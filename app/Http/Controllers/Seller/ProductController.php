<?php
/**
 * Arquivo: app/Http/Controllers/Seller/ProductController.php
 * Descrição: Controller para CRUD de produtos dos vendedores
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\Seller\StoreProductRequest;
use App\Http\Requests\Seller\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the seller's products.
     */
    public function index(Request $request)
    {
        $seller = auth()->user()->sellerProfile;
        
        $query = Product::where('seller_id', $seller->id)
                       ->with(['category', 'images'])
                       ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);
        
        return view('seller.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
                             ->orderBy('name')
                             ->get();
                             
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $seller = auth()->user()->sellerProfile;
        
        // Check product limit
        $productCount = Product::where('seller_id', $seller->id)->count();
        if ($productCount >= $seller->product_limit) {
            return back()->withErrors(['limit' => 'Você atingiu o limite de ' . $seller->product_limit . ' produtos.']);
        }

        $validated = $request->validated();
        
        // Generate slug
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        $validated['seller_id'] = $seller->id;
        
        // Set published_at if status is active
        if ($validated['status'] === 'active') {
            $validated['published_at'] = now();
        }

        $product = Product::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()->route('seller.products.index')
                        ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        
        $product->load(['category', 'images', 'variations']);
        
        return view('seller.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        
        $categories = Category::where('is_active', true)
                             ->orderBy('name')
                             ->get();
        
        $product->load('images');
        
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $validated = $request->validated();
        
        // Update slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }
        
        // Set published_at if status changed to active
        if ($product->status !== 'active' && $validated['status'] === 'active') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== 'active') {
            $validated['published_at'] = null;
        }

        $product->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUploads($product, $request->file('images'));
        }

        return redirect()->route('seller.products.index')
                        ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        // Soft delete the product
        $product->delete();
        
        return redirect()->route('seller.products.index')
                        ->with('success', 'Produto removido com sucesso!');
    }

    /**
     * Toggle product status between active and inactive
     */
    public function toggleStatus(Product $product)
    {
        $this->authorize('update', $product);
        
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        
        $product->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'active' ? now() : null
        ]);

        $message = $newStatus === 'active' 
                 ? 'Produto ativado com sucesso!' 
                 : 'Produto desativado com sucesso!';
        
        return back()->with('success', $message);
    }

    /**
     * Duplicate a product
     */
    public function duplicate(Product $product)
    {
        $this->authorize('view', $product);
        
        $seller = auth()->user()->sellerProfile;
        
        // Check product limit
        $productCount = Product::where('seller_id', $seller->id)->count();
        if ($productCount >= $seller->product_limit) {
            return back()->withErrors(['limit' => 'Você atingiu o limite de ' . $seller->product_limit . ' produtos.']);
        }

        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Cópia)';
        $newProduct->slug = $this->generateUniqueSlug($newProduct->name);
        $newProduct->status = 'draft';
        $newProduct->published_at = null;
        $newProduct->views_count = 0;
        $newProduct->sales_count = 0;
        $newProduct->rating_average = 0;
        $newProduct->rating_count = 0;
        $newProduct->save();

        return redirect()->route('seller.products.edit', $newProduct)
                        ->with('success', 'Produto duplicado com sucesso! Faça as alterações necessárias.');
    }

    /**
     * Generate unique slug for product
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Handle product image uploads
     */
    private function handleImageUploads(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            if ($index >= 5) break; // Max 5 images per product
            
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');
            
            $product->images()->create([
                'filename' => $filename,
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $index + 1,
                'is_primary' => $index === 0
            ]);
        }
    }
}