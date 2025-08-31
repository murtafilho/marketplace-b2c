<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'active')
            ->with(['seller', 'category', 'images']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderByDesc('views_count');
                break;
            case 'rating':
                $query->orderByDesc('rating_average');
                break;
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('shop.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Only show active products
        if ($product->status !== 'active') {
            abort(404);
        }

        // Increment views count
        $product->increment('views_count');

        // Load relationships
        $product->load(['seller', 'category', 'images', 'variations']);

        // Related products (same category)
        $relatedProducts = Product::where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['seller', 'images'])
            ->limit(4)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category.
     */
    public function category(Category $category)
    {
        // Only show active categories
        if (!$category->is_active) {
            abort(404);
        }

        $products = Product::where('status', 'active')
            ->where('category_id', $category->id)
            ->with(['seller', 'category', 'images'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('shop.products.category', compact('category', 'products'));
    }
}
