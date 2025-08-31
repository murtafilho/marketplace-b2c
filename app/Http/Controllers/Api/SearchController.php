<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'message' => 'Digite pelo menos 2 caracteres para buscar'
            ]);
        }

        $products = Product::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with(['seller', 'category'])
            ->limit(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'formatted_price' => 'R$ ' . number_format($product->price, 2, ',', '.'),
                    'seller' => $product->seller->business_name ?? $product->seller->name,
                    'category' => $product->category->name,
                    'image' => $product->images->first()?->image_path ?? null,
                    'url' => route('products.show', $product->id)
                ];
            });

        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->limit(4)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'products_count' => $category->products()->where('status', 'active')->count(),
                    'url' => route('products.category', $category->slug)
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'has_results' => $products->count() > 0 || $categories->count() > 0
        ]);
    }
}