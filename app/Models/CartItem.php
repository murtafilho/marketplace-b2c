<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cartItem) {
            $cartItem->total_price = $cartItem->unit_price * $cartItem->quantity;
        });

        static::saved(function ($cartItem) {
            $cartItem->cart->refresh();
            $totalAmount = $cartItem->cart->items->sum('total_price');
            $cartItem->cart->update(['total_amount' => $totalAmount]);
        });

        static::deleted(function ($cartItem) {
            $cartItem->cart->refresh();
            $totalAmount = $cartItem->cart->items->sum('total_price');
            $cartItem->cart->update(['total_amount' => $totalAmount]);
        });
    }

    public function increaseQuantity(int $amount = 1): void
    {
        $this->update(['quantity' => $this->quantity + $amount]);
    }

    public function decreaseQuantity(int $amount = 1): void
    {
        $newQuantity = max(0, $this->quantity - $amount);
        
        if ($newQuantity === 0) {
            $this->delete();
        } else {
            $this->update(['quantity' => $newQuantity]);
        }
    }
}