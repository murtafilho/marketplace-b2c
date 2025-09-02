<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        // URLs de imagens de placeholder do Unsplash para diferentes categorias
        $placeholderImages = [
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1574080661461-67348a2d0515?w=400&h=400&fit=crop',
            'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=400&fit=crop'
        ];
        
        Product::all()->each(function ($product, $index) use ($placeholderImages) {
            // Usar uma imagem diferente para cada produto
            $imageUrl = $placeholderImages[$index % count($placeholderImages)];
            
            ProductImage::create([
                'product_id' => $product->id,
                'original_name' => 'placeholder_' . $product->id . '.jpg',
                'file_name' => 'placeholder_' . $product->id . '.jpg',
                'file_path' => 'products/placeholder_' . $product->id . '.jpg',
                'mime_type' => 'image/jpeg',
                'file_size' => 50000,
                'width' => 400,
                'height' => 400,
                'alt_text' => $product->name,
                'sort_order' => 1,
                'is_primary' => true
            ]);
            
            // Criar uma segunda imagem para alguns produtos
            if ($product->id % 2 == 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'original_name' => 'placeholder_' . $product->id . '_2.jpg',
                    'file_name' => 'placeholder_' . $product->id . '_2.jpg',
                    'file_path' => 'products/placeholder_' . $product->id . '_2.jpg',
                    'mime_type' => 'image/jpeg',
                    'file_size' => 45000,
                    'width' => 400,
                    'height' => 400,
                    'alt_text' => $product->name . ' - Imagem 2',
                    'sort_order' => 2,
                    'is_primary' => false
                ]);
            }
        });
        
        echo "Imagens criadas para " . Product::count() . " produtos!\n";
    }
}