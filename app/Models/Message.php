<?php
/**
 * Arquivo: app/Models/Message.php
 * Descrição: Model para mensagens do sistema de comunicação
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageNotification;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
        'type',
        'attachments',
        'delivery_info',
        'is_read',
        'read_at',
        'is_edited',
        'edited_at',
        'status'
    ];

    protected $casts = [
        'attachments' => 'array',
        'delivery_info' => 'array',
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'read_at' => 'datetime',
        'edited_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Atualizar informações da conversa
            $conversation = $message->conversation;
            $conversation->last_message_at = $message->created_at;
            $conversation->last_message_by = $message->sender_id;
            
            // Incrementar contador de não lidas
            if ($message->sender_type === 'customer') {
                $conversation->increment('unread_seller');
            } elseif ($message->sender_type === 'seller') {
                $conversation->increment('unread_customer');
            }
            
            $conversation->save();
            
            // Enviar email de notificação (apenas mensagens de usuários, não sistema)
            if ($message->sender_type !== 'system' && $message->type === 'text') {
                try {
                    // Determinar o destinatário
                    $recipient = null;
                    if ($message->sender_type === 'customer') {
                        // Enviar para o vendedor
                        $recipient = $conversation->sellerUser;
                    } else {
                        // Enviar para o cliente
                        $recipient = $conversation->customer;
                    }
                    
                    if ($recipient && $recipient->email) {
                        Mail::to($recipient->email)->send(new NewMessageNotification($message, $recipient));
                    }
                } catch (\Exception $e) {
                    // Log do erro mas não interrompe o processo
                    \Log::error('Erro ao enviar email de notificação: ' . $e->getMessage());
                }
            }
        });
    }

    // Relacionamentos
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeTextMessages($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeDeliveryProposals($query)
    {
        return $query->where('type', 'delivery_proposal');
    }

    public function scopeSystemMessages($query)
    {
        return $query->where('type', 'system');
    }

    // Helper Methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
                'status' => 'read'
            ]);
        }
    }

    public function markAsDelivered()
    {
        if ($this->status === 'sent') {
            $this->update(['status' => 'delivered']);
        }
    }

    public function editContent($newContent)
    {
        $this->update([
            'content' => $newContent,
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    public function isFromCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }

    public function isFromSeller(): bool
    {
        return $this->sender_type === 'seller';
    }

    public function isSystemMessage(): bool
    {
        return $this->sender_type === 'system' || $this->type === 'system';
    }

    public function isDeliveryProposal(): bool
    {
        return $this->type === 'delivery_proposal';
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    // Atributos calculados
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDateAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return 'Hoje';
        } elseif ($this->created_at->isYesterday()) {
            return 'Ontem';
        } else {
            return $this->created_at->format('d/m/Y');
        }
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->created_at->diffInMinutes(now()) < 5;
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->isSystemMessage()) {
            return 'Sistema';
        }
        
        return $this->sender->name ?? 'Usuário Desconhecido';
    }

    public function getAttachmentCountAttribute(): int
    {
        return is_array($this->attachments) ? count($this->attachments) : 0;
    }
}
