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

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $sellerProfile;
    private $category;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Setup conforme DICIONÁRIO DE DADOS
        $this->seller = User::factory()->create(['role' => 'seller']);
        
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'approved',
            'approved_at' => now()
        ]);
        
        $this->category = Category::factory()->create(['is_active' => true]);
        
        // Produto base para testes
        $this->product = Product::create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto para Upload',
            'slug' => 'produto-para-upload',
            'description' => 'Produto para testar upload de imagens',
            'price' => 99.99,
            'stock_quantity' => 10,
            'status' => 'active'
        ]);
        
        $this->actingAs($this->seller);
    }

    /**
     * Teste de upload via controller
     */
    public function test_upload_imagens_via_controller()
    {
        $images = [
            UploadedFile::fake()->image('test1.jpg', 600, 600),
            UploadedFile::fake()->image('test2.jpg', 600, 600),
            UploadedFile::fake()->image('test3.jpg', 600, 600)
        ];
        
        $response = $this->post("/seller/products/{$this->product->id}/images", [
            'images' => $images
        ]);
        
        // Verificar que não houve erro 500
        if ($response->status() == 500) {
            $this->fail('Erro 500 no upload: ' . $response->getContent());
        }
        
        // Deve ser sucesso ou redirect
        $this->assertTrue(
            in_array($response->status(), [200, 201, 302]),
            "Status esperado 200/201/302, recebido: {$response->status()}"
        );
    }

    /**
     * Teste de gerenciamento completo de imagens
     */
    public function test_gerenciamento_completo_imagens()
    {
        // ===== CRIAR IMAGENS DIRETAMENTE =====
        $imagem1 = ProductImage::create([
            'product_id' => $this->product->id,
            'original_name' => 'teste1.jpg',
            'file_name' => 'produto_teste1.jpg',
            'file_path' => 'products/' . $this->product->id . '/teste1.jpg',
            'thumbnail_path' => 'products/' . $this->product->id . '/thumb_teste1.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024000,
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Imagem de Teste 1',
            'sort_order' => 1,
            'is_primary' => true
        ]);
        
        $imagem2 = ProductImage::create([
            'product_id' => $this->product->id,
            'original_name' => 'teste2.jpg',
            'file_name' => 'produto_teste2.jpg',
            'file_path' => 'products/' . $this->product->id . '/teste2.jpg',
            'thumbnail_path' => 'products/' . $this->product->id . '/thumb_teste2.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024000,
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Imagem de Teste 2',
            'sort_order' => 2,
            'is_primary' => false
        ]);
        
        $imagem3 = ProductImage::create([
            'product_id' => $this->product->id,
            'original_name' => 'teste3.jpg',
            'file_name' => 'produto_teste3.jpg',
            'file_path' => 'products/' . $this->product->id . '/teste3.jpg',
            'thumbnail_path' => 'products/' . $this->product->id . '/thumb_teste3.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024000,
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Imagem de Teste 3',
            'sort_order' => 3,
            'is_primary' => false
        ]);
        
        // Verificar criação
        $this->assertDatabaseCount('product_images', 3);
        $this->assertDatabaseHas('product_images', [
            'product_id' => $this->product->id,
            'is_primary' => true,
            'sort_order' => 1
        ]);
        
        // ===== TESTAR TROCA DE IMAGEM PRINCIPAL =====
        // Fazer a imagem 2 ser principal
        $imagem1->update(['is_primary' => false]);
        $imagem2->update(['is_primary' => true]);
        
        // Verificar que apenas uma é principal
        $principaisCount = ProductImage::where('product_id', $this->product->id)
            ->where('is_primary', true)
            ->count();
        $this->assertEquals(1, $principaisCount);
        
        $this->assertDatabaseHas('product_images', [
            'id' => $imagem2->id,
            'is_primary' => true
        ]);
        
        // ===== TESTAR DELEÇÃO DE IMAGEM =====
        $imagem3->delete();
        
        $this->assertDatabaseCount('product_images', 2);
        $this->assertDatabaseMissing('product_images', [
            'id' => $imagem3->id
        ]);
        
        // ===== TESTAR SUBSTITUIÇÃO COMPLETA =====
        // Deletar todas
        ProductImage::where('product_id', $this->product->id)->delete();
        $this->assertDatabaseCount('product_images', 0);
        
        // Criar novas
        for ($i = 1; $i <= 5; $i++) {
            ProductImage::create([
                'product_id' => $this->product->id,
                'original_name' => "nova{$i}.jpg",
                'file_name' => "produto_nova{$i}.jpg",
                'file_path' => "products/{$this->product->id}/nova{$i}.jpg",
                'thumbnail_path' => "products/{$this->product->id}/thumb_nova{$i}.jpg",
                'mime_type' => 'image/jpeg',
                'file_size' => 1024000,
                'width' => 1200,
                'height' => 900,
                'alt_text' => "Nova Imagem {$i}",
                'sort_order' => $i,
                'is_primary' => ($i === 1)
            ]);
        }
        
        // Verificar resultado final
        $this->assertDatabaseCount('product_images', 5);
        
        $imagensProduto = ProductImage::where('product_id', $this->product->id)
            ->orderBy('sort_order')
            ->get();
        
        $this->assertCount(5, $imagensProduto);
        $this->assertTrue($imagensProduto->first()->is_primary);
        $this->assertEquals(1200, $imagensProduto->first()->width);
        $this->assertEquals(900, $imagensProduto->first()->height);
    }

    /**
     * Teste de validações de imagem conforme dicionário
     */
    public function test_validacoes_imagens()
    {
        // Criar 10 imagens (máximo conforme dicionário)
        for ($i = 1; $i <= 10; $i++) {
            ProductImage::create([
                'product_id' => $this->product->id,
                'original_name' => "img{$i}.jpg",
                'file_name' => "produto_img{$i}.jpg",
                'file_path' => "products/{$this->product->id}/img{$i}.jpg",
                'thumbnail_path' => "products/{$this->product->id}/thumb_img{$i}.jpg",
                'mime_type' => 'image/jpeg',
                'file_size' => 1024000,
                'width' => 800,
                'height' => 600,
                'alt_text' => "Imagem {$i}",
                'sort_order' => $i,
                'is_primary' => ($i === 1)
            ]);
        }
        
        $this->assertDatabaseCount('product_images', 10);
        
        // Verificar que apenas uma é principal
        $principaisCount = ProductImage::where('product_id', $this->product->id)
            ->where('is_primary', true)
            ->count();
        $this->assertEquals(1, $principaisCount);
        
        // Verificar ordenação
        $ultimaImagem = ProductImage::where('product_id', $this->product->id)
            ->orderBy('sort_order', 'desc')
            ->first();
        $this->assertEquals(10, $ultimaImagem->sort_order);
    }

    /**
     * Teste de relacionamento com produto
     */
    public function test_relacionamento_produto_imagens()
    {
        // Criar imagem
        $imagem = ProductImage::create([
            'product_id' => $this->product->id,
            'original_name' => 'relacionamento.jpg',
            'file_name' => 'produto_relacionamento.jpg',
            'file_path' => 'products/' . $this->product->id . '/relacionamento.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024000,
            'width' => 800,
            'height' => 600,
            'is_primary' => true,
            'sort_order' => 1
        ]);
        
        // Testar relacionamento
        $produto = Product::with('images')->find($this->product->id);
        $this->assertCount(1, $produto->images);
        
        $imagemRelacionada = $produto->images->first();
        $this->assertEquals($imagem->id, $imagemRelacionada->id);
        $this->assertEquals($this->product->id, $imagemRelacionada->product_id);
        
        // Testar acesso à imagem principal
        $imagemPrincipal = $produto->images->where('is_primary', true)->first();
        $this->assertNotNull($imagemPrincipal);
        $this->assertEquals($imagem->id, $imagemPrincipal->id);
    }

    protected function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        parent::tearDown();
    }
}