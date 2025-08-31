<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestImageUploadController extends Controller
{
    /**
     * Exibir página de teste de upload de imagens
     */
    public function index(): View
    {
        // Buscar alguns produtos para teste
        $products = Product::select('id', 'name', 'description', 'price')
            ->limit(20)
            ->get()
            ->map(function ($product) {
                $product->formatted_price = 'R$ ' . number_format($product->price, 2, ',', '.');
                return $product;
            });
        
        return view('test.image-upload', compact('products'));
    }
    
    /**
     * API para obter dados de um produto específico
     */
    public function getProduct(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'formatted_price' => 'R$ ' . number_format($product->price, 2, ',', '.'),
            'has_images' => $product->hasImages(),
            'primary_image_url' => $product->primary_image_url,
            'images_count' => $product->getMedia('gallery')->count()
        ]);
    }
}