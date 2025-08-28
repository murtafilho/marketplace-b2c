<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerShippingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'carrier',
        'service_code',
        'base_price',
        'price_per_kg',
        'free_shipping_threshold',
        'estimated_days_min',
        'estimated_days_max',
        'dimensions_limit',
        'weight_limit',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days_min' => 'integer',
        'estimated_days_max' => 'integer',
        'dimensions_limit' => 'array',
        'weight_limit' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function calculateShippingCost(float $weight, float $orderValue = 0): float
    {
        // Se houver frete grátis e o pedido atingir o limite
        if ($this->free_shipping_threshold && $orderValue >= $this->free_shipping_threshold) {
            return 0.00;
        }

        // Calcular frete baseado no peso
        $shippingCost = $this->base_price;
        
        if ($weight > 0 && $this->price_per_kg > 0) {
            $shippingCost += $weight * $this->price_per_kg;
        }

        return round($shippingCost, 2);
    }

    public function isAvailableForWeight(float $weight): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->weight_limit === null || $weight <= $this->weight_limit;
    }

    public function isAvailableForDimensions(array $dimensions): bool
    {
        if (!$this->is_active || !$this->dimensions_limit) {
            return $this->is_active;
        }

        $limit = $this->dimensions_limit;
        
        return (
            (!isset($limit['length']) || $dimensions['length'] <= $limit['length']) &&
            (!isset($limit['width']) || $dimensions['width'] <= $limit['width']) &&
            (!isset($limit['height']) || $dimensions['height'] <= $limit['height'])
        );
    }

    public function getEstimatedDeliveryRange(): string
    {
        if ($this->estimated_days_min === $this->estimated_days_max) {
            return $this->estimated_days_min . ' dia' . ($this->estimated_days_min > 1 ? 's' : '');
        }

        return $this->estimated_days_min . ' a ' . $this->estimated_days_max . ' dias';
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->base_price, 2, ',', '.');
    }

    public function getIsEconomicAttribute(): bool
    {
        return $this->estimated_days_min >= 7;
    }

    public function getIsExpressAttribute(): bool
    {
        return $this->estimated_days_max <= 2;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeAvailableForWeight($query, float $weight)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) use ($weight) {
                        $q->whereNull('weight_limit')
                          ->orWhere('weight_limit', '>=', $weight);
                    });
    }
}