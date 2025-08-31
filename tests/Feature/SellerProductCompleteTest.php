<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerProductCompleteTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $sellerProfile;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Criar vendedor com perfil aprovado
        $this->seller = User::factory()->create([
            'role' => 'seller',
            'email' => 'seller@test.com',
            'password' => bcrypt('password123')
        ]);
        
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'company_name' => 'Loja de Teste',
            'status' => 'approved',
            'approved_at' => now()
        ]);
        
        // Criar categoria para os produtos
        $this->category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);
        
        $this->actingAs($this->seller);
    }

    public function test_complete_product_lifecycle_with_images()
    {
        // ===== ETAPA 1: CADASTRAR NOVO PRODUTO =====
        $this->assertDatabaseCount('products', 0);
        
        // Acessar formulário de criação
        $response = $this->get('/seller/products/create');
        $response->assertStatus(200);
        $response->assertSee('Novo Produto');
        
        // Dados do novo produto
        $productData = [
            'name' => 'Smartphone Teste X1',
            'slug' => 'smartphone-teste-x1',
            'description' => 'Um smartphone de teste com recursos incríveis',
            'price' => 1999.99,
            'compare_at_price' => 2499.99,
            'category_id' => $this->category->id,
            'sku' => 'SMART-X1-001',
            'barcode' => '7891234567890',
            'stock_quantity' => 50,
            'weight' => 150,
            'width' => 7,
            'height' => 15,
            'status' => 'active',
            'featured' => true,
            'meta_title' => 'Smartphone Teste X1 - Melhor Preço',
            'meta_description' => 'Compre o Smartphone Teste X1 com desconto',
            'meta_keywords' => 'smartphone, teste, x1, celular'
        ];
        
        // Criar o produto
        $response = $this->post('/seller/products', $productData);
        $response->assertRedirect('/seller/products');
        $response->assertSessionHas('success');
        
        // Verificar produto criado
        $this->assertDatabaseCount('products', 1);
        $product = Product::first();
        $this->assertEquals('Smartphone Teste X1', $product->name);
        $this->assertEquals(1999.99, $product->price);
        $this->assertEquals('active', $product->status);
        
        // ===== ETAPA 2: ADICIONAR IMAGENS AO PRODUTO =====
        $image1 = UploadedFile::fake()->image('produto-frente.jpg', 800, 800);
        $image2 = UploadedFile::fake()->image('produto-verso.jpg', 800, 800);
        $image3 = UploadedFile::fake()->image('produto-lateral.jpg', 800, 800);
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => [$image1, $image2, $image3]
        ]);
        
        // Verificar imagens salvas
        $this->assertDatabaseCount('product_images', 3);
        $images = ProductImage::where('product_id', $product->id)->get();
        $this->assertCount(3, $images);
        
        // Verificar que a primeira imagem é principal
        $primaryImage = $images->where('is_primary', true)->first();
        $this->assertNotNull($primaryImage);
        
        // ===== ETAPA 3: VERIFICAR PRODUTO ATIVO NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1');
        $response->assertSee('R$ 1.999,99');
        
        // Verificar na busca
        $response = $this->get('/buscar?q=smartphone');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1');
        
        // ===== ETAPA 4: DESATIVAR O PRODUTO =====
        $response = $this->patch("/seller/products/{$product->id}/toggle-status");
        $response->assertRedirect();
        
        // Verificar produto desativado
        $product->refresh();
        $this->assertEquals('inactive', $product->status);
        
        // ===== ETAPA 5: VERIFICAR QUE PRODUTO NÃO APARECE NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Smartphone Teste X1');
        
        // Verificar que não aparece na busca
        $response = $this->get('/buscar?q=smartphone');
        $response->assertStatus(200);
        $response->assertDontSee('Smartphone Teste X1');
        
        // Verificar que ainda aparece no painel do vendedor
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1');
        $response->assertSee('Inativo'); // Status deve mostrar inativo
        
        // ===== ETAPA 6: EDITAR PRODUTO E TROCAR IMAGENS =====
        $response = $this->get("/seller/products/{$product->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1');
        
        // Deletar imagens antigas
        foreach ($images as $image) {
            $response = $this->delete("/seller/products/images/{$image->id}");
        }
        
        $this->assertDatabaseCount('product_images', 0);
        
        // Adicionar novas imagens
        $newImage1 = UploadedFile::fake()->image('nova-frente.jpg', 1000, 1000);
        $newImage2 = UploadedFile::fake()->image('nova-verso.jpg', 1000, 1000);
        $newImage3 = UploadedFile::fake()->image('nova-lateral.jpg', 1000, 1000);
        $newImage4 = UploadedFile::fake()->image('nova-caixa.jpg', 1000, 1000);
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => [$newImage1, $newImage2, $newImage3, $newImage4]
        ]);
        
        // Verificar novas imagens
        $this->assertDatabaseCount('product_images', 4);
        $newImages = ProductImage::where('product_id', $product->id)->get();
        $this->assertCount(4, $newImages);
        
        // Atualizar dados do produto
        $updatedData = [
            'name' => 'Smartphone Teste X1 Pro',
            'slug' => 'smartphone-teste-x1-pro',
            'description' => 'Versão atualizada do smartphone com melhorias',
            'price' => 2299.99,
            'compare_at_price' => 2799.99,
            'category_id' => $this->category->id,
            'stock_quantity' => 100,
            'status' => 'inactive' // Manter desativado por enquanto
        ];
        
        $response = $this->put("/seller/products/{$product->id}", $updatedData);
        $response->assertRedirect("/seller/products/{$product->id}/edit");
        $response->assertSessionHas('success');
        
        // Verificar atualizações
        $product->refresh();
        $this->assertEquals('Smartphone Teste X1 Pro', $product->name);
        $this->assertEquals(2299.99, $product->price);
        $this->assertEquals('inactive', $product->status);
        
        // ===== ETAPA 7: REATIVAR O PRODUTO =====
        $response = $this->patch("/seller/products/{$product->id}/toggle-status");
        $response->assertRedirect();
        
        // Verificar produto reativado
        $product->refresh();
        $this->assertEquals('active', $product->status);
        
        // ===== ETAPA 8: VERIFICAR PRODUTO ATUALIZADO NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1 Pro');
        $response->assertSee('R$ 2.299,99');
        $response->assertDontSee('Smartphone Teste X1'); // Nome antigo não deve aparecer
        
        // Verificar na busca com novo nome
        $response = $this->get('/buscar?q=pro');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1 Pro');
        
        // Verificar página individual do produto
        $response = $this->get("/produto/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1 Pro');
        $response->assertSee('Versão atualizada do smartphone com melhorias');
        $response->assertSee('R$ 2.299,99');
        
        // ===== ETAPA 9: TESTAR DUPLICAÇÃO DO PRODUTO =====
        $response = $this->post("/seller/products/{$product->id}/duplicate");
        $response->assertRedirect();
        
        // Verificar produto duplicado
        $this->assertDatabaseCount('products', 2);
        $duplicatedProduct = Product::where('id', '!=', $product->id)->first();
        $this->assertStringContainsString('Smartphone Teste X1 Pro', $duplicatedProduct->name);
        $this->assertStringContainsString('(Cópia)', $duplicatedProduct->name);
        $this->assertFalse($duplicatedProduct->is_active); // Duplicado deve iniciar desativado
        
        // ===== ETAPA 10: TESTAR ATUALIZAÇÃO DE INVENTÁRIO =====
        $response = $this->patch("/seller/products/{$product->id}/inventory", [
            'stock_quantity' => 25,
            'track_inventory' => true,
            'allow_backorder' => false
        ]);
        $response->assertRedirect();
        
        $product->refresh();
        $this->assertEquals(25, $product->quantity);
        
        // ===== ETAPA 11: VERIFICAR FILTROS NO PAINEL DO VENDEDOR =====
        // Filtrar por ativos
        $response = $this->get('/seller/products?status=active');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1 Pro');
        
        // Filtrar por inativos (deve mostrar o duplicado)
        $response = $this->get('/seller/products?status=inactive');
        $response->assertStatus(200);
        $response->assertSee('(Cópia)');
        
        // Buscar por nome
        $response = $this->get('/seller/products?search=pro');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Teste X1 Pro');
        
        // ===== ETAPA 12: TESTAR BULK UPDATE =====
        $productIds = Product::pluck('id')->toArray();
        
        $response = $this->patch('/seller/products/bulk-update', [
            'product_ids' => $productIds,
            'action' => 'deactivate'
        ]);
        
        // Verificar todos desativados
        $activeCount = Product::where('is_active', true)->count();
        $this->assertEquals(0, $activeCount);
        
        // Reativar em massa
        $response = $this->patch('/seller/products/bulk-update', [
            'product_ids' => $productIds,
            'action' => 'activate'
        ]);
        
        // Verificar todos ativados
        $activeCount = Product::where('is_active', true)->count();
        $this->assertEquals(2, $activeCount);
        
        // ===== VERIFICAÇÕES FINAIS =====
        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseCount('product_images', 4); // Apenas o produto principal tem imagens
        
        // Verificar integridade dos dados
        $finalProduct = Product::find($product->id);
        $this->assertEquals($this->sellerProfile->id, $finalProduct->seller_id);
        $this->assertEquals($this->category->id, $finalProduct->category_id);
        $this->assertEquals('active', $finalProduct->status);
        $this->assertCount(4, $finalProduct->images);
    }

    public function test_product_visibility_based_on_status()
    {
        // Criar produto ativo
        $activeProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Ativo',
            'status' => 'active',
            'stock_quantity' => 10
        ]);
        
        // Criar produto inativo
        $inactiveProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Inativo',
            'status' => 'inactive',
            'stock_quantity' => 10
        ]);
        
        // Criar produto sem estoque
        $outOfStockProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Sem Estoque',
            'status' => 'active',
            'stock_quantity' => 0
        ]);
        
        // Testar visibilidade na home
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Produto Ativo');
        $response->assertDontSee('Produto Inativo');
        $response->assertSee('Produto Sem Estoque'); // Deve aparecer mas com indicação de falta
        
        // Testar busca
        $response = $this->get('/buscar?q=produto');
        $response->assertStatus(200);
        $response->assertSee('Produto Ativo');
        $response->assertDontSee('Produto Inativo');
        
        // Testar categoria
        $response = $this->get('/categoria/eletronicos');
        $response->assertStatus(200);
        $response->assertSee('Produto Ativo');
        $response->assertDontSee('Produto Inativo');
        
        // Vendedor deve ver todos no painel
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Produto Ativo');
        $response->assertSee('Produto Inativo');
        $response->assertSee('Produto Sem Estoque');
    }

    public function test_product_image_management()
    {
        // Criar produto
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto com Imagens'
        ]);
        
        // Adicionar primeira leva de imagens
        $images = [
            UploadedFile::fake()->image('img1.jpg', 600, 600),
            UploadedFile::fake()->image('img2.jpg', 600, 600),
            UploadedFile::fake()->image('img3.jpg', 600, 600)
        ];
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => $images
        ]);
        
        $this->assertDatabaseCount('product_images', 3);
        
        // Verificar imagem principal
        $primaryImage = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->first();
        $this->assertNotNull($primaryImage);
        
        // Trocar imagem principal
        $secondImage = ProductImage::where('product_id', $product->id)
            ->where('is_primary', false)
            ->first();
        
        $response = $this->patch("/seller/products/images/{$secondImage->id}/set-primary");
        
        // Verificar troca
        $primaryImage->refresh();
        $secondImage->refresh();
        $this->assertFalse($primaryImage->is_primary);
        $this->assertTrue($secondImage->is_primary);
        
        // Deletar uma imagem
        $response = $this->delete("/seller/products/images/{$secondImage->id}");
        
        $this->assertDatabaseCount('product_images', 2);
        
        // Verificar que outra imagem se tornou principal
        $newPrimary = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->first();
        $this->assertNotNull($newPrimary);
        
        // Adicionar mais imagens até o limite
        $moreImages = [];
        for ($i = 0; $i < 8; $i++) {
            $moreImages[] = UploadedFile::fake()->image("extra{$i}.jpg", 600, 600);
        }
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => $moreImages
        ]);
        
        $this->assertDatabaseCount('product_images', 10); // 2 + 8 = 10 (limite)
        
        // Tentar adicionar mais uma (deve falhar)
        $extraImage = UploadedFile::fake()->image('extra-limit.jpg', 600, 600);
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => [$extraImage]
        ]);
        
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('product_images', 10); // Ainda 10
    }

    public function test_product_seo_and_metadata()
    {
        // Criar produto com SEO
        $productData = [
            'name' => 'Produto SEO Otimizado',
            'slug' => 'produto-seo-otimizado',
            'description' => 'Descrição para SEO',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock_quantity' => 10,
            'meta_title' => 'Compre Produto SEO Otimizado | Melhor Preço',
            'meta_description' => 'Produto SEO Otimizado com entrega rápida e garantia',
            'meta_keywords' => 'produto, seo, otimizado, comprar'
        ];
        
        $response = $this->post('/seller/products', $productData);
        $response->assertRedirect();
        
        $product = Product::where('slug', 'produto-seo-otimizado')->first();
        
        // Verificar página do produto tem meta tags
        $response = $this->get("/produto/{$product->id}");
        $response->assertStatus(200);
        
        // Verificar que o slug funciona
        $response = $this->get("/produto/{$product->slug}");
        $response->assertStatus(200);
        $response->assertSee('Produto SEO Otimizado');
        
        // Atualizar SEO
        $response = $this->put("/seller/products/{$product->id}", [
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'stock_quantity' => $product->quantity,
            'meta_title' => 'Novo Título SEO',
            'meta_description' => 'Nova descrição SEO',
            'meta_keywords' => 'novas, palavras, chave'
        ]);
        
        $product->refresh();
        $this->assertEquals('Novo Título SEO', $product->meta_title);
        $this->assertEquals('Nova descrição SEO', $product->meta_description);
    }

    protected function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        parent::tearDown();
    }
}