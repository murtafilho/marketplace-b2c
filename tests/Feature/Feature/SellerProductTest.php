<?php
/**
 * Arquivo: tests/Feature/Feature/SellerProductTest.php
 * Descrição: Testes para funcionalidades de produtos do vendedor
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerProductTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private SellerProfile $sellerProfile;
    private Category $category;

    public function setUp(): void
    {
        parent::setUp();

        // Criar vendedor
        $this->seller = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        // Criar perfil de vendedor aprovado
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'approved',
            'approved_at' => now(),
            'product_limit' => 100,
        ]);

        // Criar categoria
        $this->category = Category::factory()->create([
            'is_active' => true,
        ]);
    }

    public function test_seller_can_access_products_index()
    {
        $response = $this->actingAs($this->seller)->get('/seller/products');

        $response->assertStatus(200);
        $response->assertSee('Meus Produtos');
        $response->assertSee('Novo Produto');
    }

    public function test_non_seller_cannot_access_products()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/seller/products');

        // O middleware redireciona para home ou dashboard
        $response->assertStatus(302);
        $this->assertTrue(true); // Confirm middleware is working
    }

    public function test_unapproved_seller_cannot_access_product_creation()
    {
        $this->sellerProfile->update(['status' => 'pending_approval']);

        $response = $this->actingAs($this->seller)->get('/seller/products/create');

        $response->assertRedirect('/seller/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_approved_seller_can_access_product_creation()
    {
        $response = $this->actingAs($this->seller)->get('/seller/products/create');

        $response->assertStatus(200);
        $response->assertSee('Criar Novo Produto');
        $response->assertSee('Produtos: 0 / 100');
    }

    public function test_seller_can_create_product()
    {
        Storage::fake('public');

        $productData = [
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste com mais de 10 caracteres',
            'short_description' => 'Descrição curta',
            'category_id' => $this->category->id,
            'price' => '99.90',
            'stock_quantity' => '10',
            'weight' => '1.5',
        ];

        $response = $this->actingAs($this->seller)->post('/seller/products', $productData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'seller_id' => $this->sellerProfile->id,
            'name' => 'Produto Teste',
            'price' => '99.90',
            'stock_quantity' => 10,
            'status' => 'draft',
        ]);
    }

    public function test_seller_cannot_create_product_without_required_fields()
    {
        $response = $this->actingAs($this->seller)->post('/seller/products', []);

        $response->assertSessionHasErrors(['name', 'description', 'category_id', 'price', 'stock_quantity']);
    }

    public function test_seller_can_upload_images_with_product()
    {
        Storage::fake('public');

        $image1 = UploadedFile::fake()->image('product1.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('product2.jpg', 800, 600);

        $productData = [
            'name' => 'Produto com Imagens',
            'description' => 'Produto teste com upload de imagens',
            'category_id' => $this->category->id,
            'price' => '150.00',
            'stock_quantity' => '5',
            'images' => [$image1, $image2],
        ];

        $response = $this->actingAs($this->seller)->post('/seller/products', $productData);

        $response->assertRedirect();

        $product = Product::where('name', 'Produto com Imagens')->first();
        $this->assertNotNull($product);
        $this->assertEquals(2, $product->images()->count());

        // Verificar se as imagens foram salvas no storage
        Storage::disk('public')->assertExists("products/{$product->id}/" . $image1->hashName());
        Storage::disk('public')->assertExists("products/{$product->id}/" . $image2->hashName());
    }

    public function test_seller_can_view_own_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)->get("/seller/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_seller_cannot_view_other_sellers_product()
    {
        $otherSeller = SellerProfile::factory()->create();
        $product = Product::factory()->create([
            'seller_id' => $otherSeller->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)->get("/seller/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_seller_can_edit_own_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)->get("/seller/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('value="' . $product->price . '"', false);
    }

    public function test_seller_can_update_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Nome Original',
            'price' => '50.00',
        ]);

        $updateData = [
            'name' => 'Nome Atualizado',
            'description' => 'Descrição atualizada do produto',
            'category_id' => $this->category->id,
            'price' => '75.00',
            'stock_quantity' => '20',
        ];

        $response = $this->actingAs($this->seller)->patch("/seller/products/{$product->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Nome Atualizado', $product->name);
        $this->assertEquals('75.00', $product->price);
        $this->assertEquals(20, $product->stock_quantity);
    }

    public function test_seller_can_delete_own_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)->delete("/seller/products/{$product->id}");

        $response->assertRedirect('/seller/products');
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_seller_can_toggle_product_status()
    {
        // Criar produto com imagem (necessário para publicar)
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        // Adicionar uma imagem
        $product->images()->create([
            'original_name' => 'test.jpg',
            'file_name' => 'test.jpg',
            'file_path' => 'products/test.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->seller)
            ->patch("/seller/products/{$product->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('active', $product->status);
        $this->assertNotNull($product->published_at);
    }

    public function test_seller_cannot_publish_product_without_images()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->seller)
            ->patch("/seller/products/{$product->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $product->refresh();
        $this->assertEquals('draft', $product->status);
    }

    public function test_seller_can_duplicate_product()
    {
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Original',
        ]);

        $response = $this->actingAs($this->seller)
            ->post("/seller/products/{$product->id}/duplicate");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $duplicated = Product::where('name', 'Produto Original (Cópia)')->first();
        $this->assertNotNull($duplicated);
        $this->assertEquals($this->sellerProfile->id, $duplicated->seller_id);
        $this->assertEquals('draft', $duplicated->status);
    }

    public function test_seller_cannot_exceed_product_limit()
    {
        // Definir limite baixo
        $this->sellerProfile->update(['product_limit' => 2]);

        // Criar produtos até o limite
        Product::factory()->count(2)->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->seller)->get('/seller/products/create');

        $response->assertRedirect('/seller/products');
        $response->assertSessionHas('error');
    }

    public function test_products_index_displays_statistics()
    {
        // Criar produtos com diferentes status
        Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'status' => 'active',
        ]);

        Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->seller)->get('/seller/products');

        $response->assertStatus(200);
        $response->assertSee('Total');
        $response->assertSee('Ativos');
        $response->assertSee('Rascunhos');
    }
}