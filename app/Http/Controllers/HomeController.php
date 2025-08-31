<?php
/**
 * Arquivo: app/Http/Controllers/HomeController.php
 * Descrição: Controller para a página inicial do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial do marketplace
     */
    public function index()
    {
        // Buscar produtos em destaque (produtos marcados como featured)
        $featuredProducts = Product::where('status', 'active')
            ->where('featured', true)
            ->with(['seller.user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Se não houver produtos em destaque, pegar os mais recentes
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('status', 'active')
                ->with(['seller.user', 'category'])
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();
        }

        // Buscar categorias principais com contagem de produtos das subcategorias
        $mainCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->withCount(['products' => function($subQuery) {
                    $subQuery->where('status', 'active');
                }]);
            }])
            ->orderBy('sort_order')
            ->get();
            
        // Calcular total de produtos para cada categoria principal
        $mainCategories->each(function($category) {
            $category->products_count = $category->children->sum('products_count');
        });

        // Buscar produtos mais vendidos/populares (ordenados por views_count)
        $popularProducts = Product::where('status', 'active')
            ->with(['seller.user', 'category'])
            ->orderBy('views_count', 'desc')
            ->orderBy('sales_count', 'desc')
            ->limit(12)
            ->get();

        // Estatísticas do sistema
        $stats = [
            'total_products' => Product::where('status', 'active')->count(),
            'total_sellers' => \App\Models\SellerProfile::where('status', 'approved')->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'total_customers' => \App\Models\User::where('role', 'customer')->count(),
        ];

        return view('home', compact(
            'featuredProducts', 
            'mainCategories', 
            'popularProducts', 
            'stats'
        ));
    }

    /**
     * Busca produtos baseado na query do usuário
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categorySlug = $request->get('categoria');
        
        $products = Product::where('status', 'active')
            ->with(['seller.user', 'category', 'images']);

        // Filtrar por busca textual
        if (!empty($query)) {
            $products->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('short_description', 'like', '%' . $query . '%');
            });
        }

        // Filtrar por categoria
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $products->where('category_id', $category->id);
            }
        }

        $products = $products->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('search', compact('products', 'query', 'categories', 'categorySlug'));
    }

    /**
     * Exibe produtos de uma categoria específica
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('status', 'active')
            ->where('category_id', $category->id)
            ->with(['seller.user', 'category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('category', compact('category', 'products'));
    }

    /**
     * Exibe detalhes de um produto específico
     */
    public function product($id)
    {
        $product = Product::where('status', 'active')
            ->with(['seller.user', 'category', 'images'])
            ->findOrFail($id);

        // Incrementar visualizações
        $product->increment('views_count');

        // Produtos relacionados (mesma categoria)
        $relatedProducts = Product::where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['seller.user', 'images'])
            ->limit(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }
}
