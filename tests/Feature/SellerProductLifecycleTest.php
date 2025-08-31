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

class SellerProductLifecycleTest extends TestCase
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
            'email' => 'seller@test.com'
        ]);
        
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'company_name' => 'Loja de Teste',
            'status' => 'approved',
            'approved_at' => now()
        ]);
        
        // Criar categoria
        $this->category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);
        
        $this->actingAs($this->seller);
    }

    /**
     * Teste completo do ciclo de vida do produto:
     * 1. Cadastrar produto
     * 2. Adicionar imagens
     * 3. Verificar que aparece na home
     * 4. Desativar produto
     * 5. Verificar que NÃO aparece na home/busca
     * 6. Trocar imagens
     * 7. Reativar produto
     * 8. Verificar que aparece novamente na home
     */
    public function test_complete_product_lifecycle()
    {
        // ===== ETAPA 1: CADASTRAR PRODUTO =====
        $productData = [
            'name' => 'Notebook Gamer X1',
            'slug' => 'notebook-gamer-x1',
            'description' => 'Notebook potente para jogos',
            'short_description' => 'Notebook gamer de alta performance',
            'price' => 4999.99,
            'compare_at_price' => 5999.99,
            'category_id' => $this->category->id,
            'sku' => 'NTB-GMR-X1',
            'stock_quantity' => 10,
            'weight' => 2.5,
            'width' => 35,
            'height' => 25,
            'length' => 3
        ];
        
        // Criar produto via POST
        $response = $this->post('/seller/products', $productData);
        $response->assertRedirect(); // Aceitar qualquer redirect
        
        // Verificar produto criado
        $product = Product::where('slug', 'notebook-gamer-x1')->first();
        $this->assertNotNull($product);
        $this->assertEquals('active', $product->status);
        $this->assertEquals(4999.99, $product->price);
        
        // ===== ETAPA 2: ADICIONAR IMAGENS =====
        $images = [
            UploadedFile::fake()->image('notebook1.jpg', 800, 600),
            UploadedFile::fake()->image('notebook2.jpg', 800, 600),
            UploadedFile::fake()->image('notebook3.jpg', 800, 600)
        ];
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => $images
        ]);
        
        // Verificar imagens criadas
        $productImages = ProductImage::where('product_id', $product->id)->get();
        $this->assertCount(3, $productImages);
        
        // ===== ETAPA 3: VERIFICAR PRODUTO NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        $response->assertSee('4.999,99'); // Formato brasileiro
        
        // Verificar na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        
        // ===== ETAPA 4: DESATIVAR PRODUTO =====
        $response = $this->patch("/seller/products/{$product->id}/toggle-status");
        $response->assertRedirect();
        
        $product->refresh();
        $this->assertEquals('draft', $product->status); // Toggle muda para draft
        
        // ===== ETAPA 5: VERIFICAR QUE NÃO APARECE NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Notebook Gamer X1');
        
        // Verificar que não aparece na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertDontSee('Notebook Gamer X1');
        
        // Mas ainda aparece no painel do vendedor
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        
        // ===== ETAPA 6: TROCAR IMAGENS =====
        // Deletar imagens antigas
        foreach ($productImages as $image) {
            $response = $this->delete("/seller/products/images/{$image->id}");
        }
        
        // Verificar que foram deletadas
        $this->assertDatabaseCount('product_images', 0);
        
        // Adicionar novas imagens
        $newImages = [
            UploadedFile::fake()->image('new1.jpg', 1000, 800),
            UploadedFile::fake()->image('new2.jpg', 1000, 800),
            UploadedFile::fake()->image('new3.jpg', 1000, 800),
            UploadedFile::fake()->image('new4.jpg', 1000, 800)
        ];
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => $newImages
        ]);
        
        // Verificar novas imagens
        $newProductImages = ProductImage::where('product_id', $product->id)->get();
        $this->assertCount(4, $newProductImages);
        
        // ===== ETAPA 7: REATIVAR PRODUTO =====
        $response = $this->patch("/seller/products/{$product->id}/toggle-status");
        $response->assertRedirect();
        
        $product->refresh();
        $this->assertEquals('active', $product->status);
        
        // ===== ETAPA 8: VERIFICAR QUE APARECE NOVAMENTE NA HOME =====
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        
        // Verificar na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        
        // Verificar página individual do produto
        $response = $this->get("/produto/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer X1');
        $response->assertSee('Notebook potente para jogos');
        
        // ===== VERIFICAÇÕES FINAIS =====
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Notebook Gamer X1',
            'status' => 'active',
            'seller_id' => $this->sellerProfile->id
        ]);
        
        $this->assertDatabaseCount('product_images', 4);
    }

    /**
     * Teste simplificado de visibilidade baseada em status
     */
    public function test_product_visibility_by_status()
    {
        // Criar produto ativo
        $activeProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Visível',
            'status' => 'active',
            'stock_quantity' => 10
        ]);
        
        // Criar produto inativo
        $inactiveProduct = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Invisível',
            'status' => 'draft',
            'stock_quantity' => 10
        ]);
        
        // Testar home
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Produto Visível');
        $response->assertDontSee('Produto Invisível');
        
        // Vendedor vê todos
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Produto Visível');
        $response->assertSee('Produto Invisível');
    }

    /**
     * Teste de upload e gerenciamento de imagens
     */
    public function test_image_upload_and_management()
    {
        // Criar produto
        $product = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto com Imagens',
            'status' => 'active'
        ]);
        
        // Upload de múltiplas imagens
        $images = [
            UploadedFile::fake()->image('img1.jpg', 600, 600),
            UploadedFile::fake()->image('img2.jpg', 600, 600),
            UploadedFile::fake()->image('img3.jpg', 600, 600)
        ];
        
        $response = $this->post("/seller/products/{$product->id}/images", [
            'images' => $images
        ]);
        
        // Verificar upload
        $this->assertDatabaseCount('product_images', 3);
        
        // Verificar primeira imagem é principal
        $primaryImage = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->first();
        $this->assertNotNull($primaryImage);
        
        // Deletar uma imagem
        $response = $this->delete("/seller/products/images/{$primaryImage->id}");
        
        // Verificar que foi deletada
        $this->assertDatabaseCount('product_images', 2);
        
        // Verificar que outra imagem virou principal
        $newPrimary = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->first();
        $this->assertNotNull($newPrimary);
        $this->assertNotEquals($primaryImage->id, $newPrimary->id);
    }

    protected function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        parent::tearDown();
    }
}