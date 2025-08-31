<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'total_amount',
        'total_items',
        'shipping_data',
        'coupon_data',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_data' => 'array',
        'coupon_data' => 'array',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function clearCart(): void
    {
        $this->items()->delete();
        $this->update([
            'total_amount' => 0,
            'total_items' => 0,
            'shipping_data' => null,
            'coupon_data' => null,
        ]);
    }
}