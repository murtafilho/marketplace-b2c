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
use App\Services\SafeUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $uploadService;

    public function __construct(SafeUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

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
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            
            // Campos opcionais com defaults
            'short_description' => 'nullable|string|max:500',
            'compare_at_price' => 'nullable|numeric|min:0.01|max:999999.99',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0|max:999.999',
            'length' => 'nullable|numeric|min:0|max:999.99',
            'width' => 'nullable|numeric|min:0|max:999.99',
            'height' => 'nullable|numeric|min:0|max:999.99',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'warranty_months' => 'nullable|integer|min:0|max:120',
            'status' => 'nullable|in:draft,active',
            
            // Imagens 
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            
            // SEO
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
            'short_description' => $request->short_description ?: '',
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'cost' => $request->cost,
            'sku' => $request->sku ?: 'SKU-' . strtoupper(Str::random(8)),
            'barcode' => $request->barcode ?: '',
            'stock_quantity' => $request->stock_quantity ?: 1,
            'stock_status' => ($request->stock_quantity ?: 1) > 0 ? 'in_stock' : 'out_of_stock',
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'brand' => $request->brand ?: '',
            'model' => $request->model ?: '',
            'warranty_months' => $request->warranty_months,
            'status' => $request->status ?: 'draft',
            'featured' => false,
            'digital' => false,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: '',
            'meta_keywords' => $request->meta_keywords ?: '',
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
        $newProduct->name = $product->name . ' - Cópia';
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
     * Upload images to product
     */
    public function uploadImages(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        
        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120'
        ]);
        
        try {
            DB::beginTransaction();
            
            $sortOrder = $product->images()->max('sort_order') ?? 0;
            $isPrimary = !$product->images()->exists();
            
            foreach ($request->file('images') as $image) {
                try {
                    $uploaded = $this->uploadService->uploadProductImage($image, $product->id);
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'original_name' => $uploaded['original_name'],
                        'file_name' => $uploaded['file_name'],
                        'file_path' => $uploaded['file_path'],
                        'thumbnail_path' => $uploaded['thumbnail_path'] ?? $uploaded['file_path'],
                        'mime_type' => $uploaded['metadata']['mime_type'] ?? $uploaded['mime_type'],
                        'file_size' => $uploaded['metadata']['size'] ?? $uploaded['size'],
                        'width' => $uploaded['metadata']['width'] ?? 0,
                        'height' => $uploaded['metadata']['height'] ?? 0,
                        'alt_text' => $product->name,
                        'sort_order' => ++$sortOrder,
                        'is_primary' => $isPrimary,
                    ]);
                    
                    $isPrimary = false;
                    
                } catch (\Exception $e) {
                    \Log::error('Erro no upload de imagem do produto: ' . $e->getMessage());
                    throw new \Exception('Erro no upload da imagem: ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Imagens enviadas com sucesso!',
                'images' => $product->images()->latest()->get()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar imagens: ' . $e->getMessage()
            ], 422);
        }
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
     * Update product inventory
     */
    public function updateInventory(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock,limited'
        ]);
        
        $product->update([
            'stock_quantity' => $request->stock_quantity,
            'stock_status' => $request->stock_status
        ]);
        
        return back()->with('success', 'Estoque atualizado com sucesso!');
    }

    /**
     * Bulk update products
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);
        
        $seller = auth()->user()->sellerProfile;
        $products = Product::whereIn('id', $request->product_ids)
                          ->where('seller_id', $seller->id)
                          ->get();
        
        if ($products->count() !== count($request->product_ids)) {
            return back()->with('error', 'Alguns produtos não foram encontrados.');
        }
        
        switch ($request->action) {
            case 'activate':
                $products->each(function ($product) {
                    $product->update(['status' => 'active']);
                });
                $message = 'Produtos ativados com sucesso!';
                break;
                
            case 'deactivate':
                $products->each(function ($product) {
                    $product->update(['status' => 'inactive']);
                });
                $message = 'Produtos desativados com sucesso!';
                break;
                
            case 'delete':
                $products->each(function ($product) {
                    $product->delete();
                });
                $message = 'Produtos excluídos com sucesso!';
                break;
        }
        
        return back()->with('success', $message);
    }

    /**
     * Handle image uploads
     */
    private function handleImageUploads(Product $product, array $images)
    {
        $sortOrder = $product->images()->max('sort_order') ?? 0;
        $isPrimary = !$product->images()->exists();
        
        foreach ($images as $image) {
            try {
                $uploaded = $this->uploadService->uploadProductImage($image, $product->id);
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'original_name' => $uploaded['original_name'],
                    'file_name' => $uploaded['file_name'],
                    'file_path' => $uploaded['file_path'],
                    'thumbnail_path' => $uploaded['thumbnail_path'] ?? $uploaded['file_path'],
                    'mime_type' => $uploaded['metadata']['mime_type'] ?? $uploaded['mime_type'],
                    'file_size' => $uploaded['metadata']['size'] ?? $uploaded['size'],
                    'width' => $uploaded['metadata']['width'] ?? 0,
                    'height' => $uploaded['metadata']['height'] ?? 0,
                    'alt_text' => $product->name,
                    'sort_order' => ++$sortOrder,
                    'is_primary' => $isPrimary,
                ]);
                
                $isPrimary = false;
                
            } catch (\Exception $e) {
                \Log::error('Erro no upload de imagem do produto: ' . $e->getMessage());
                throw new \Exception('Erro no upload da imagem: ' . $e->getMessage());
            }
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