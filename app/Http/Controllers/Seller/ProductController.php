<?php
/**
 * Arquivo: app/Http/Controllers/Seller/ProductController.php
 * Descrição: Controller para CRUD de produtos do vendedor
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the seller's products.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->sellerProfile;
        
        if (!$seller || !$seller->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Você precisa ter sua conta aprovada para gerenciar produtos.');
        }

        $query = Product::where('seller_id', $seller->id)
            ->with(['category', 'images'])
            ->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(12);

        $stats = [
            'total' => $seller->products()->count(),
            'active' => $seller->products()->where('status', 'active')->count(),
            'draft' => $seller->products()->where('status', 'draft')->count(),
            'inactive' => $seller->products()->where('status', 'inactive')->count(),
        ];

        return view('seller.products.index', compact('products', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $seller = auth()->user()->sellerProfile;
        
        if (!$seller || !$seller->isApproved()) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Você precisa ter sua conta aprovada para criar produtos.');
        }

        // Verificar limite de produtos
        $currentCount = $seller->products()->count();
        if ($currentCount >= $seller->product_limit) {
            return redirect()->route('seller.products.index')
                ->with('error', "Você atingiu o limite de {$seller->product_limit} produtos. Entre em contato conosco para aumentar seu limite.");
        }

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('seller.products.create', compact('categories', 'currentCount', 'seller'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $seller = auth()->user()->sellerProfile;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'compare_at_price' => 'nullable|numeric|min:0.01|max:999999.99|gt:price',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0|max:999.999',
            'length' => 'nullable|numeric|min:0|max:999.99',
            'width' => 'nullable|numeric|min:0|max:999.99',
            'height' => 'nullable|numeric|min:0|max:999.99',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Verificar limite novamente
        $currentCount = $seller->products()->count();
        if ($currentCount >= $seller->product_limit) {
            return back()->with('error', 'Limite de produtos atingido.');
        }

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'cost' => $request->cost,
            'sku' => $request->sku ?: 'SKU-' . strtoupper(Str::random(8)),
            'barcode' => $request->barcode,
            'stock_quantity' => $request->stock_quantity,
            'stock_status' => $request->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'status' => 'draft',
            'featured' => false,
            'digital' => false,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'published_at' => null,
        ]);

        // Upload de imagens
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()->route('seller.products.show', $product)
            ->with('success', 'Produto criado com sucesso! Publique quando estiver pronto.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $this->authorizeProduct($product);
        
        $product->load(['category', 'images', 'variations']);
        
        return view('seller.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $product->load('images');
        
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'compare_at_price' => 'nullable|numeric|min:0.01|max:999999.99|gt:price',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0|max:999.999',
            'length' => 'nullable|numeric|min:0|max:999.99',
            'width' => 'nullable|numeric|min:0|max:999.99',
            'height' => 'nullable|numeric|min:0|max:999.99',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'cost' => $request->cost,
            'sku' => $request->sku ?: $product->sku,
            'barcode' => $request->barcode,
            'stock_quantity' => $request->stock_quantity,
            'stock_status' => $request->stock_quantity > 0 ? 'in_stock' : 'out_of_stock',
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        // Upload de novas imagens
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()->route('seller.products.show', $product)
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        
        // Soft delete
        $product->delete();
        
        return redirect()->route('seller.products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Toggle product status between draft/active
     */
    public function toggleStatus(Product $product)
    {
        $this->authorizeProduct($product);
        
        if ($product->status === 'draft') {
            // Validar se produto pode ser ativado
            if (!$product->images()->exists()) {
                return back()->with('error', 'Adicione pelo menos uma imagem antes de publicar o produto.');
            }
            
            $product->update([
                'status' => 'active',
                'published_at' => now(),
            ]);
            
            return back()->with('success', 'Produto publicado com sucesso!');
        } else {
            $product->update([
                'status' => 'draft',
                'published_at' => null,
            ]);
            
            return back()->with('success', 'Produto despublicado.');
        }
    }

    /**
     * Duplicate a product
     */
    public function duplicate(Product $product)
    {
        $this->authorizeProduct($product);
        
        $seller = auth()->user()->sellerProfile;
        
        // Verificar limite
        $currentCount = $seller->products()->count();
        if ($currentCount >= $seller->product_limit) {
            return back()->with('error', 'Limite de produtos atingido.');
        }
        
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Cópia)';
        $newProduct->slug = Str::slug($newProduct->name) . '-' . Str::random(6);
        $newProduct->sku = 'SKU-' . strtoupper(Str::random(8));
        $newProduct->status = 'draft';
        $newProduct->published_at = null;
        $newProduct->sales_count = 0;
        $newProduct->views_count = 0;
        $newProduct->save();
        
        // Copiar imagens
        foreach ($product->images as $image) {
            ProductImage::create([
                'product_id' => $newProduct->id,
                'original_name' => $image->original_name,
                'file_name' => $image->file_name,
                'file_path' => $image->file_path,
                'mime_type' => $image->mime_type,
                'file_size' => $image->file_size,
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
            ]);
        }
        
        return redirect()->route('seller.products.edit', $newProduct)
            ->with('success', 'Produto duplicado com sucesso!');
    }

    /**
     * Delete product image
     */
    public function deleteImage(ProductImage $image)
    {
        $this->authorizeProduct($image->product);
        
        // Delete file
        if (Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }
        
        $image->delete();
        
        return back()->with('success', 'Imagem removida com sucesso!');
    }

    /**
     * Handle image uploads
     */
    private function handleImageUploads(Product $product, array $images)
    {
        $sortOrder = $product->images()->max('sort_order') ?? 0;
        $isPrimary = !$product->images()->exists();
        
        foreach ($images as $image) {
            $path = $image->store('products/' . $product->id, 'public');
            
            ProductImage::create([
                'product_id' => $product->id,
                'original_name' => $image->getClientOriginalName(),
                'file_name' => basename($path),
                'file_path' => $path,
                'mime_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'alt_text' => $product->name,
                'sort_order' => ++$sortOrder,
                'is_primary' => $isPrimary,
            ]);
            
            $isPrimary = false;
        }
    }

    /**
     * Authorize that seller owns the product
     */
    private function authorizeProduct(Product $product)
    {
        $seller = auth()->user()->sellerProfile;
        
        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Você não tem permissão para acessar este produto.');
        }
    }
}