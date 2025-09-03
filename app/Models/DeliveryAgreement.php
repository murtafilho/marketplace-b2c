<?php
/**
 * Arquivo: app/Models/DeliveryAgreement.php
 * Descrição: Model para acordos de entrega entre compradores e vendedores
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sub_order_id',
        'proposed_by',
        'type',
        'description',
        'details',
        'delivery_fee',
        'estimated_date',
        'estimated_time',
        'status',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'completed_at',
        'completion_proof'
    ];

    protected $casts = [
        'details' => 'array',
        'completion_proof' => 'array',
        'delivery_fee' => 'decimal:2',
        'estimated_date' => 'date',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relacionamentos
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function subOrder(): BelongsTo
    {
        return $this->belongsTo(SubOrder::class);
    }

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['proposed', 'negotiating']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForSubOrder($query, $subOrderId)
    {
        return $query->where('sub_order_id', $subOrderId);
    }

    // Helper Methods
    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);

        // Criar mensagem do sistema
        Message::create([
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->proposed_by,
            'sender_type' => 'system',
            'type' => 'system',
            'content' => "Acordo de entrega aceito: {$this->getTypeLabel()} - {$this->description}",
            'delivery_info' => [
                'agreement_id' => $this->id,
                'type' => $this->type,
                'fee' => $this->delivery_fee,
                'date' => $this->estimated_date,
                'time' => $this->estimated_time
            ]
        ]);

        // Atualizar sub_order com informações de entrega
        $this->subOrder->update([
            'shipping_method' => [
                'type' => $this->type,
                'agreement_id' => $this->id,
                'description' => $this->description,
                'fee' => $this->delivery_fee
            ]
        ]);
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason
        ]);

        // Criar mensagem do sistema
        Message::create([
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->proposed_by,
            'sender_type' => 'system',
            'type' => 'system',
            'content' => "Acordo de entrega rejeitado" . ($reason ? ": {$reason}" : "")
        ]);
    }

    public function markAsCompleted($proof = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_proof' => $proof
        ]);

        // Criar mensagem do sistema
        Message::create([
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->proposed_by,
            'sender_type' => 'system',
            'type' => 'system',
            'content' => "Entrega concluída com sucesso!"
        ]);

        // Atualizar status do sub_order
        $this->subOrder->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    // Getters
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'pickup' => 'Retirada no Local',
            'meet_location' => 'Encontro em Local Combinado',
            'custom_delivery' => 'Entrega Personalizada',
            'correios' => 'Correios',
            'transportadora' => 'Transportadora',
            default => 'Outro'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'proposed' => 'Proposto',
            'negotiating' => 'Em Negociação',
            'accepted' => 'Aceito',
            'rejected' => 'Rejeitado',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'proposed' => 'yellow',
            'negotiating' => 'blue',
            'accepted' => 'green',
            'rejected' => 'red',
            'completed' => 'green',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    // Verificações
    public function isProposed(): bool
    {
        return $this->status === 'proposed';
    }

    public function isNegotiating(): bool
    {
        return $this->status === 'negotiating';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['proposed', 'negotiating']);
    }

    public function canBeAccepted(): bool
    {
        return in_array($this->status, ['proposed', 'negotiating']);
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'accepted';
    }
}
