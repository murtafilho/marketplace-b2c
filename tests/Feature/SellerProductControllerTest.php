<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $user;
    private $category;

    public function setUp(): void
    {
        parent::setUp();

        // Criar usuário vendedor aprovado
        $this->user = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        $this->seller = SellerProfile::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Criar categoria para testes
        $this->category = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true,
        ]);
    }

    public function test_approved_seller_can_access_products_index()
    {
        $response = $this->actingAs($this->user)->get('/seller/products');

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.index');
    }

    public function test_approved_seller_can_access_products_create_form()
    {
        $response = $this->actingAs($this->user)->get('/seller/products/create');

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.create');
        $response->assertViewHas('categories');
    }

    public function test_approved_seller_can_create_product()
    {
        Storage::fake('public');

        $productData = [
            'name' => 'Smartphone Teste',
            'description' => 'Descrição do produto',
            'price' => 999.99,
            'stock_quantity' => 10,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)
            ->post('/seller/products', $productData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('products', [
            'name' => 'Smartphone Teste',
            'seller_id' => $this->seller->id,
        ]);
    }

    public function test_seller_can_view_own_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/seller/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertViewIs('seller.products.show');
        $response->assertViewHas('product');
    }

    public function test_seller_cannot_view_other_seller_product()
    {
        $otherSeller = SellerProfile::factory()->create(['status' => 'approved']);
        $product = Product::factory()->create([
            'seller_id' => $otherSeller->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/seller/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_pending_seller_cannot_access_products()
    {
        $this->seller->update(['status' => 'pending']);

        $response = $this->actingAs($this->user)->get('/seller/products');

        $response->assertRedirect('/seller/dashboard');
    }

    public function test_seller_can_toggle_product_status()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        // Adicionar imagem para poder ativar
        $product->images()->create([
            'original_name' => 'test.jpg',
            'file_name' => 'test.jpg',
            'file_path' => 'products/test.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'alt_text' => 'Test',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->patch("/seller/products/{$product->id}/toggle-status");

        $response->assertRedirect();
        
        $product->refresh();
        $this->assertEquals('active', $product->status);
    }
}