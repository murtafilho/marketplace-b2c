<?php
/**
 * Arquivo: app/Models/Conversation.php
 * Descrição: Model para conversas entre compradores e vendedores
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_id',
        'seller_id',
        'product_id',
        'order_id',
        'subject',
        'status',
        'priority',
        'last_message_at',
        'last_message_by',
        'unread_customer',
        'unread_seller',
        'metadata'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
        'unread_customer' => 'integer',
        'unread_seller' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversation) {
            if (!$conversation->uuid) {
                $conversation->uuid = Str::uuid()->toString();
            }
        });
    }

    // Relacionamentos
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function sellerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function deliveryAgreements(): HasMany
    {
        return $this->hasMany(DeliveryAgreement::class);
    }

    public function activeDeliveryAgreement(): HasOne
    {
        return $this->hasOne(DeliveryAgreement::class)
            ->where('status', 'accepted')
            ->latestOfMany();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeUnreadForCustomer($query)
    {
        return $query->where('unread_customer', '>', 0);
    }

    public function scopeUnreadForSeller($query)
    {
        return $query->where('unread_seller', '>', 0);
    }

    public function scopeWithUnreadCount($query, $userId, $userType = 'customer')
    {
        $field = $userType === 'seller' ? 'unread_seller' : 'unread_customer';
        return $query->where($field, '>', 0);
    }

    // Helper Methods
    public function markAsReadFor($userType)
    {
        $field = $userType === 'seller' ? 'unread_seller' : 'unread_customer';
        $this->update([$field => 0]);
        
        // Marcar mensagens como lidas
        $this->messages()
            ->where('sender_type', '!=', $userType)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function incrementUnreadFor($userType)
    {
        $field = $userType === 'seller' ? 'unread_seller' : 'unread_customer';
        $this->increment($field);
    }

    public function getOtherParticipant($userId)
    {
        return $this->customer_id == $userId 
            ? $this->sellerUser 
            : $this->customer;
    }

    public function hasParticipant($userId)
    {
        return $this->customer_id == $userId || $this->seller_id == $userId;
    }

    public function canBeAccessedBy($userId)
    {
        return $this->hasParticipant($userId);
    }

    // Atributos calculados
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getHasUnreadMessagesAttribute(): bool
    {
        return $this->unread_customer > 0 || $this->unread_seller > 0;
    }

    public function getFormattedLastMessageAtAttribute(): string
    {
        if (!$this->last_message_at) {
            return 'Nunca';
        }

        $diff = $this->last_message_at->diffForHumans();
        return str_replace(['há', 'atrás'], '', $diff);
    }
}
