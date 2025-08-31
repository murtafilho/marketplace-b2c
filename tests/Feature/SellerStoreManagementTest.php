<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerStoreManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $sellerProfile;
    protected $categories;
    protected $products;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar vendedor
        $this->seller = User::factory()->create([
            'name' => 'Vendedor Loja',
            'email' => 'loja@teste.com',
            'role' => 'seller'
        ]);

        // Criar perfil do vendedor aprovado
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'approved',
            'company_name' => 'Minha Loja Teste',
            'document_type' => 'cnpj',
            'document_number' => '12345678000195',
            'phone' => '11999999999',
            'address' => 'Rua da Loja, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'commission_rate' => 10.0,
            'product_limit' => 100
        ]);

        // Criar categorias
        $this->categories = Category::factory(5)->create([
            'is_active' => true
        ]);

        // Criar alguns produtos existentes
        $this->products = Product::factory(3)->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->categories->random()->id,
            'status' => 'active'
        ]);
    }

    public function test_seller_can_access_dashboard()
    {
        $response = $this->actingAs($this->seller)
                         ->get('/seller/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee($this->seller->name);
        $response->assertSee('Total de Produtos');
        $response->assertSee('Vendas do Mês');
        $response->assertSee('Receita do Mês');
    }

    public function test_unapproved_seller_cannot_access_dashboard()
    {
        // Criar vendedor não aprovado
        $unapprovedSeller = User::factory()->create(['role' => 'seller']);
        SellerProfile::factory()->create([
            'user_id' => $unapprovedSeller->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($unapprovedSeller)
                         ->get('/seller/dashboard');

        $response->assertRedirect('/seller/onboarding/pending');
    }

    public function test_seller_can_view_products_list()
    {
        $response = $this->actingAs($this->seller)
                         ->get('/seller/products');

        $response->assertStatus(200);
        $response->assertSee('Meus Produtos');
        $response->assertSee('Novo Produto');
        
        foreach ($this->products->take(2) as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_seller_can_filter_products_by_status()
    {
        // Criar produto inativo
        $inactiveProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->categories->first()->id,
            'name' => 'Produto Inativo',
            'status' => 'inactive'
        ]);

        $response = $this->actingAs($this->seller)
                         ->get('/seller/products?status=inactive');

        $response->assertStatus(200);
        $response->assertSee('Produto Inativo');
        $response->assertDontSee($this->products->first()->name);
    }

    public function test_seller_can_search_products()
    {
        $searchProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->categories->first()->id,
            'name' => 'Produto Único Especial'
        ]);

        $response = $this->actingAs($this->seller)
                         ->get('/seller/products?search=Único');

        $response->assertStatus(200);
        $response->assertSee('Produto Único Especial');
    }

    public function test_seller_can_access_product_creation_form()
    {
        $response = $this->actingAs($this->seller)
                         ->get('/seller/products/create');

        $response->assertStatus(200);
        $response->assertSee('Novo Produto');
        $response->assertSee('Nome do Produto');
        $response->assertSee('Categoria');
        $response->assertSee('Preço');
        $response->assertSee('Quantidade em Estoque');
        $response->assertSee('Descrição');
        
        // Verificar se as categorias estão disponíveis
        foreach ($this->categories->take(3) as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_seller_can_create_new_product()
    {
        $productData = [
            'name' => 'Produto Novo Teste',
            'slug' => 'produto-novo-teste',
            'category_id' => $this->categories->first()->id,
            'description' => 'Descrição do produto novo',
            'short_description' => 'Descrição curta',
            'price' => 299.99,
            'compare_at_price' => 399.99,
            'cost' => 150.00,
            'sku' => 'PROD-001',
            'barcode' => '1234567890123',
            'stock_quantity' => 50,
            'weight' => 1.5,
            'length' => 20,
            'width' => 15,
            'height' => 10,
            'status' => 'active',
            'meta_title' => 'Produto Novo - SEO',
            'meta_description' => 'Meta descrição do produto'
        ];

        $response = $this->actingAs($this->seller)
                         ->post('/seller/products', $productData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'seller_id' => $this->sellerProfile->id,
            'name' => 'Produto Novo Teste',
            'price' => 299.99,
            'sku' => 'PROD-001',
            'stock_quantity' => 50
        ]);
    }

    public function test_seller_cannot_exceed_product_limit()
    {
        // Reduzir o limite para testar
        $this->sellerProfile->update(['product_limit' => 3]);

        $productData = [
            'name' => 'Produto Excedente',
            'category_id' => $this->categories->first()->id,
            'price' => 99.99,
            'stock_quantity' => 10
        ];

        $response = $this->actingAs($this->seller)
                         ->post('/seller/products', $productData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('products', [
            'name' => 'Produto Excedente'
        ]);
    }

    public function test_seller_can_view_product_details()
    {
        $product = $this->products->first();
        
        $response = $this->actingAs($this->seller)
                         ->get("/seller/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->description);
        $response->assertSee('R$ ' . number_format($product->price, 2, ',', '.'));
        $response->assertSee('Estoque: ' . $product->stock_quantity);
        $response->assertSee('Editar');
        $response->assertSee('Duplicar');
    }

    public function test_seller_can_edit_product()
    {
        $product = $this->products->first();
        
        $response = $this->actingAs($this->seller)
                         ->get("/seller/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Editar Produto');
        $response->assertSee($product->name);
        $response->assertSee($product->description);
    }

    public function test_seller_can_update_product()
    {
        $product = $this->products->first();
        
        $updateData = [
            'name' => 'Produto Atualizado',
            'price' => 399.99,
            'stock_quantity' => 25,
            'description' => 'Nova descrição do produto',
            'status' => 'inactive'
        ];

        $response = $this->actingAs($this->seller)
                         ->put("/seller/products/{$product->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Produto Atualizado', $product->name);
        $this->assertEquals(399.99, $product->price);
        $this->assertEquals(25, $product->stock_quantity);
        $this->assertEquals('inactive', $product->status);
    }

    public function test_seller_can_toggle_product_status()
    {
        $product = $this->products->first();
        $originalStatus = $product->status;
        
        $response = $this->actingAs($this->seller)
                         ->patch("/seller/products/{$product->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $expectedStatus = $originalStatus === 'active' ? 'inactive' : 'active';
        $this->assertEquals($expectedStatus, $product->status);
    }

    public function test_seller_can_duplicate_product()
    {
        $product = $this->products->first();
        
        $response = $this->actingAs($this->seller)
                         ->post("/seller/products/{$product->id}/duplicate");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'seller_id' => $this->sellerProfile->id,
            'name' => $product->name . ' - Cópia',
            'price' => $product->price,
            'status' => 'draft'
        ]);
    }

    public function test_seller_can_upload_product_images()
    {
        $product = $this->products->first();
        
        // Criar imagem fake para teste - ajustar tamanho para 5MB máximo
        $file = UploadedFile::fake()->image('produto.jpg', 800, 600)->size(1024); // 1MB

        $response = $this->actingAs($this->seller)
                         ->post("/seller/products/{$product->id}/images", [
                             'images' => [$file]
                         ]);

        // Debug em caso de erro
        if ($response->status() !== 200) {
            dump('Response Status: ' . $response->status());
            dump('Response Content: ' . $response->content());
        }

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        // Verificar se a imagem foi salva no banco
        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'is_primary' => true
        ]);
        
        // Verificar se o arquivo existe fisicamente
        $productImage = $product->images()->first();
        $this->assertTrue(file_exists(storage_path('app/public/' . $productImage->file_path)));
    }

    public function test_seller_can_delete_product_image()
    {
        Storage::fake('public');
        
        $product = $this->products->first();
        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
            'file_path' => 'products/test-image.jpg'
        ]);

        $response = $this->actingAs($this->seller)
                         ->delete("/seller/products/images/{$image->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('product_images', [
            'id' => $image->id
        ]);
    }

    public function test_seller_can_update_store_profile()
    {
        $updateData = [
            'company_name' => 'Nova Nome da Loja',
            'phone' => '11888888888',
            'address' => 'Nova Rua, 456',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'postal_code' => '20000-000'
        ];

        $response = $this->actingAs($this->seller)
                         ->put('/seller/profile', $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->sellerProfile->refresh();
        $this->assertEquals('Nova Nome da Loja', $this->sellerProfile->company_name);
        $this->assertEquals('11888888888', $this->sellerProfile->phone);
        $this->assertEquals('Rio de Janeiro', $this->sellerProfile->city);
    }

    public function test_seller_can_view_sales_statistics()
    {
        $response = $this->actingAs($this->seller)
                         ->get('/seller/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total de Produtos');
        $response->assertSee(count($this->products)); // Verificar estatísticas
        $response->assertSee('Produtos Ativos');
        $response->assertSee('Rascunhos');
    }

    public function test_seller_can_manage_product_inventory()
    {
        $product = $this->products->first();
        $originalStock = $product->stock_quantity;
        
        $response = $this->actingAs($this->seller)
                         ->patch("/seller/products/{$product->id}/inventory", [
                             'stock_quantity' => $originalStock + 10,
                             'stock_status' => 'in_stock'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals($originalStock + 10, $product->stock_quantity);
        $this->assertEquals('in_stock', $product->stock_status);
    }

    public function test_seller_cannot_access_other_seller_products()
    {
        // Criar outro vendedor
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $otherSellerProfile = SellerProfile::factory()->create([
            'user_id' => $otherSeller->id,
            'status' => 'approved'
        ]);
        
        $otherProduct = Product::factory()->create([
            'seller_id' => $otherSellerProfile->id,
            'category_id' => $this->categories->first()->id
        ]);

        $response = $this->actingAs($this->seller)
                         ->get("/seller/products/{$otherProduct->id}");

        $response->assertStatus(403);
    }

    public function test_seller_can_bulk_update_products()
    {
        $productIds = $this->products->pluck('id')->toArray();
        
        $response = $this->actingAs($this->seller)
                         ->patch('/seller/products/bulk-update', [
                             'product_ids' => $productIds,
                             'action' => 'activate'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($this->products as $product) {
            $product->refresh();
            $this->assertEquals('active', $product->status);
        }
    }

    public function test_seller_product_validation_works()
    {
        $invalidData = [
            'name' => '', // Nome obrigatório
            'price' => -10, // Preço negativo
            'stock_quantity' => -5 // Estoque negativo
        ];

        $response = $this->actingAs($this->seller)
                         ->post('/seller/products', $invalidData);

        $response->assertSessionHasErrors(['name', 'price', 'stock_quantity', 'category_id']);
    }

    public function test_complete_seller_product_workflow()
    {
        Storage::fake('public');
        
        // 1. Acessar dashboard
        $response = $this->actingAs($this->seller)->get('/seller/dashboard');
        $response->assertStatus(200);

        // 2. Acessar lista de produtos
        $response = $this->get('/seller/products');
        $response->assertStatus(200);

        // 3. Criar novo produto
        $productData = [
            'name' => 'Produto Workflow',
            'category_id' => $this->categories->first()->id,
            'description' => 'Produto criado no workflow completo',
            'price' => 199.99,
            'stock_quantity' => 20,
            'sku' => 'WF-001',
            'status' => 'active'
        ];

        $response = $this->post('/seller/products', $productData);
        $response->assertRedirect();

        $product = Product::where('name', 'Produto Workflow')->first();
        $this->assertNotNull($product);

        // 4. Upload de imagem
        $file = UploadedFile::fake()->image('workflow.jpg');
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => [$file]
        ]);
        $response->assertRedirect();

        // 5. Editar produto
        $response = $this->put("/seller/products/{$product->id}", [
            'name' => 'Produto Workflow Editado',
            'price' => 249.99,
            'stock_quantity' => 15
        ]);
        $response->assertRedirect();

        // 6. Alternar status
        $response = $this->patch("/seller/products/{$product->id}/toggle-status");
        $response->assertRedirect();

        // 7. Duplicar produto
        $response = $this->post("/seller/products/{$product->id}/duplicate");
        $response->assertRedirect();

        // Verificar resultados finais
        $product->refresh();
        $this->assertEquals('Produto Workflow Editado', $product->name);
        $this->assertEquals(249.99, $product->price);
        
        $this->assertDatabaseHas('products', [
            'name' => 'Produto Workflow Editado - Cópia',
            'seller_id' => $this->sellerProfile->id
        ]);

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id
        ]);
    }
}