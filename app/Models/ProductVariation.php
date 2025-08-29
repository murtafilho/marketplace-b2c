<?php
/**
 * Arquivo: app/Models/ProductVariation.php
 * Descrição: Model para variações de produtos
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',           // Corrigido: era variation_name
        'value',          // Corrigido: era variation_value
        'price_adjustment', // Corrigido: era price
        'stock_quantity',
        'sku_suffix',     // Corrigido: era sku
        'weight_adjustment',
        'sort_order',
        'is_active',
        'meta_data'
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'weight_adjustment' => 'decimal:3',
        'stock_quantity' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'meta_data' => 'array'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getFinalPriceAttribute(): float
    {
        // Preço final = preço do produto + ajuste da variação
        return $this->product->price + $this->price_adjustment;
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->final_price, 2, ',', '.');
    }
    
    public function getFullSkuAttribute(): string
    {
        // SKU completo = SKU do produto + sufixo da variação
        return $this->product->sku . ($this->sku_suffix ? '-' . $this->sku_suffix : '');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}