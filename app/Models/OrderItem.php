<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_order_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_snapshot' => 'array',
    ];

    public function subOrder(): BelongsTo
    {
        return $this->belongsTo(SubOrder::class);
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

        static::creating(function ($orderItem) {
            // Salvar snapshot do produto no momento da compra
            if ($orderItem->product) {
                $orderItem->product_snapshot = [
                    'name' => $orderItem->product->name,
                    'description' => $orderItem->product->description,
                    'sku' => $orderItem->product->sku,
                    'brand' => $orderItem->product->brand,
                    'weight' => $orderItem->product->weight,
                    'dimensions' => $orderItem->product->dimensions,
                ];

                // Adicionar dados da variação se existir
                if ($orderItem->productVariation) {
                    $orderItem->product_snapshot['variation'] = [
                        'name' => $orderItem->productVariation->variation_name,
                        'value' => $orderItem->productVariation->variation_value,
                        'price' => $orderItem->productVariation->price,
                        'sku' => $orderItem->productVariation->sku,
                    ];
                }
            }

            // Calcular preço total
            $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
        });

        static::saved(function ($orderItem) {
            // Atualizar totais do sub-pedido
            $subOrder = $orderItem->subOrder;
            if ($subOrder) {
                $subtotalAmount = $subOrder->items->sum('total_price');
                $totalAmount = $subtotalAmount + $subOrder->shipping_amount + $subOrder->tax_amount;
                
                $subOrder->update([
                    'subtotal_amount' => $subtotalAmount,
                    'total_amount' => $totalAmount,
                ]);
            }
        });
    }

    public function getProductNameAttribute(): string
    {
        return $this->product_snapshot['name'] ?? $this->product?->name ?? 'Produto não disponível';
    }

    public function getVariationNameAttribute(): ?string
    {
        return $this->product_snapshot['variation']['name'] ?? $this->productVariation?->variation_name;
    }

    public function getVariationValueAttribute(): ?string
    {
        return $this->product_snapshot['variation']['value'] ?? $this->productVariation?->variation_value;
    }
}