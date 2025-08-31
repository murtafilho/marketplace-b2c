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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

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

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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
    
    // Media Library Configuration
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->quality(80)
            ->performOnCollections('gallery');
            
        $this->addMediaConversion('medium')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->quality(85)
            ->performOnCollections('gallery');
            
        $this->addMediaConversion('large')
            ->width(800)
            ->height(800)
            ->sharpen(10)
            ->quality(90)
            ->performOnCollections('gallery');
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile(false);
    }
    
    // Helper methods for images
    public function getPrimaryImageAttribute()
    {
        $primaryImage = $this->getMedia('gallery')
            ->filter(function ($media) {
                return $media->getCustomProperty('is_primary', false);
            })
            ->first();
            
        return $primaryImage ?: $this->getFirstMedia('gallery');
    }
    
    public function getPrimaryImageUrlAttribute()
    {
        $primaryImage = $this->primary_image;
        return $primaryImage ? $primaryImage->getUrl() : null;
    }
    
    public function getPrimaryImageThumbUrlAttribute()
    {
        $primaryImage = $this->primary_image;
        return $primaryImage ? $primaryImage->getUrl('thumb') : null;
    }
    
    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('gallery');
    }
    
    public function hasImages(): bool
    {
        return $this->getMedia('gallery')->count() > 0;
    }
}
