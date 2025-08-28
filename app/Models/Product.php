<?php
/**
 * Arquivo: app/Models/Product.php
 * Descrição: Model para produtos do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'cost',
        'sku',
        'barcode',
        'stock_quantity',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'featured',
        'digital',
        'downloadable_files',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'sales_count',
        'rating_average',
        'rating_count',
        'published_at',
        'brand',
        'model',
        'warranty_months',
        'tags',
        'attributes',
        'dimensions',
        'shipping_class'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'featured' => 'boolean',
        'digital' => 'boolean',
        'downloadable_files' => 'array',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'published_at' => 'datetime',
        'warranty_months' => 'integer',
        'tags' => 'array',
        'attributes' => 'array',
        'dimensions' => 'array'
    ];

    protected $dates = [
        'published_at'
    ];

    // Relationships
    public function seller(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock')
                    ->where('stock_quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    // Accessors & Mutators
    public function getFormattedPriceAttribute()
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function getIsOnSaleAttribute()
    {
        return $this->compare_at_price > $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale) return 0;
        
        return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }
}
