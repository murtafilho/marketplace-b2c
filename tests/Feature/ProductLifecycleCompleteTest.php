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

class ProductLifecycleCompleteTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $sellerProfile;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Criar vendedor com perfil aprovado - CONFORME DICIONÁRIO DE DADOS
        $this->seller = User::factory()->create([
            'role' => 'seller',
            'email' => 'vendedor@test.com'
        ]);
        
        // seller_profiles conforme dicionário
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'company_name' => 'Loja Teste Completa',
            'document_type' => 'CNPJ',
            'document_number' => '12.345.678/0001-90',
            'status' => 'approved',
            'approved_at' => now(),
            'commission_rate' => 10.00,
            'product_limit' => 100
        ]);
        
        // categories conforme dicionário
        $this->category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);
        
        $this->actingAs($this->seller);
    }

    /**
     * TESTE COMPLETO DO CICLO DE VIDA DO PRODUTO
     * Seguindo rigorosamente o DICIONÁRIO DE DADOS
     */
    public function test_ciclo_completo_produto_vendedor()
    {
        // ========================================
        // ETAPA 1: CADASTRAR PRODUTO
        // ========================================
        
        // Criar produto conforme dicionário - tabela products
        $produto = Product::create([
            'seller_id' => $this->sellerProfile->id, // FK para seller_profiles.id
            'category_id' => $this->category->id,
            'name' => 'Notebook Gamer Teste',
            'slug' => 'notebook-gamer-teste',
            'description' => 'Notebook potente para jogos com RTX 4060',
            'short_description' => 'Notebook gaming de alta performance',
            'price' => 5999.99,
            'compare_at_price' => 6999.99,
            'cost' => 4500.00,
            'stock_quantity' => 10, // NÃO 'quantity'
            'stock_status' => 'in_stock',
            'sku' => 'NTB-GAMER-001',
            'barcode' => '7898765432100',
            'weight' => 2.5,
            'length' => 35.0,
            'width' => 25.0,
            'height' => 3.0,
            'status' => 'active', // NÃO 'is_active'
            'featured' => true,
            'digital' => false,
            'meta_title' => 'Notebook Gamer RTX 4060 - Melhor Preço',
            'meta_description' => 'Compre notebook gamer com RTX 4060',
            'meta_keywords' => 'notebook, gamer, rtx 4060',
            'views_count' => 0,
            'sales_count' => 0,
            'rating_average' => 0.00,
            'rating_count' => 0,
            'published_at' => now()
        ]);
        
        // Verificar produto criado
        $this->assertDatabaseHas('products', [
            'id' => $produto->id,
            'seller_id' => $this->sellerProfile->id,
            'status' => 'active',
            'stock_quantity' => 10
        ]);
        
        // ========================================
        // ETAPA 2: ADICIONAR IMAGENS
        // ========================================
        
        $imagem1 = UploadedFile::fake()->image('notebook-frente.jpg', 800, 600);
        $imagem2 = UploadedFile::fake()->image('notebook-lado.jpg', 800, 600);
        $imagem3 = UploadedFile::fake()->image('notebook-teclado.jpg', 800, 600);
        
        // Criar imagens conforme dicionário - tabela product_images
        $imagemPrincipal = ProductImage::create([
            'product_id' => $produto->id,
            'original_name' => $imagem1->getClientOriginalName(),
            'file_name' => 'notebook_' . uniqid() . '.jpg',
            'file_path' => 'products/' . $produto->id . '/notebook_frente.jpg',
            'thumbnail_path' => 'products/' . $produto->id . '/thumb_notebook_frente.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $imagem1->getSize(),
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Notebook Gamer Frente',
            'sort_order' => 1,
            'is_primary' => true // Primeira imagem é principal
        ]);
        
        ProductImage::create([
            'product_id' => $produto->id,
            'original_name' => $imagem2->getClientOriginalName(),
            'file_name' => 'notebook_' . uniqid() . '.jpg',
            'file_path' => 'products/' . $produto->id . '/notebook_lado.jpg',
            'thumbnail_path' => 'products/' . $produto->id . '/thumb_notebook_lado.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $imagem2->getSize(),
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Notebook Gamer Lateral',
            'sort_order' => 2,
            'is_primary' => false
        ]);
        
        ProductImage::create([
            'product_id' => $produto->id,
            'original_name' => $imagem3->getClientOriginalName(),
            'file_name' => 'notebook_' . uniqid() . '.jpg',
            'file_path' => 'products/' . $produto->id . '/notebook_teclado.jpg',
            'thumbnail_path' => 'products/' . $produto->id . '/thumb_notebook_teclado.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $imagem3->getSize(),
            'width' => 800,
            'height' => 600,
            'alt_text' => 'Notebook Gamer Teclado',
            'sort_order' => 3,
            'is_primary' => false
        ]);
        
        // Verificar imagens criadas
        $this->assertDatabaseCount('product_images', 3);
        $this->assertDatabaseHas('product_images', [
            'product_id' => $produto->id,
            'is_primary' => true
        ]);
        
        // ========================================
        // ETAPA 3: VERIFICAR PRODUTO NA HOME (ATIVO)
        // ========================================
        
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste');
        $response->assertSee('5.999,99'); // Formato brasileiro
        
        // Verificar na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste');
        
        // ========================================
        // ETAPA 4: DESATIVAR PRODUTO
        // ========================================
        
        // Atualizar status para 'inactive' (conforme dicionário)
        $produto->update(['status' => 'inactive']);
        
        // Verificar status atualizado
        $this->assertDatabaseHas('products', [
            'id' => $produto->id,
            'status' => 'inactive'
        ]);
        
        // ========================================
        // ETAPA 5: VERIFICAR QUE NÃO APARECE NA HOME
        // ========================================
        
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('Notebook Gamer Teste');
        
        // Verificar que não aparece na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertDontSee('Notebook Gamer Teste');
        
        // Mas ainda aparece no painel do vendedor
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste');
        
        // ========================================
        // ETAPA 6: TROCAR IMAGENS
        // ========================================
        
        // Deletar imagens antigas
        ProductImage::where('product_id', $produto->id)->delete();
        $this->assertDatabaseCount('product_images', 0);
        
        // Adicionar novas imagens
        $novaImagem1 = UploadedFile::fake()->image('nova-frente.jpg', 1200, 900);
        $novaImagem2 = UploadedFile::fake()->image('nova-traseira.jpg', 1200, 900);
        $novaImagem3 = UploadedFile::fake()->image('nova-caixa.jpg', 1200, 900);
        $novaImagem4 = UploadedFile::fake()->image('nova-acessorios.jpg', 1200, 900);
        
        // Criar novas imagens
        ProductImage::create([
            'product_id' => $produto->id,
            'original_name' => $novaImagem1->getClientOriginalName(),
            'file_name' => 'nova_' . uniqid() . '.jpg',
            'file_path' => 'products/' . $produto->id . '/nova_frente.jpg',
            'thumbnail_path' => 'products/' . $produto->id . '/thumb_nova_frente.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $novaImagem1->getSize(),
            'width' => 1200,
            'height' => 900,
            'alt_text' => 'Nova Imagem Principal',
            'sort_order' => 1,
            'is_primary' => true
        ]);
        
        for ($i = 2; $i <= 4; $i++) {
            $img = ${'novaImagem' . $i};
            ProductImage::create([
                'product_id' => $produto->id,
                'original_name' => $img->getClientOriginalName(),
                'file_name' => 'nova_' . uniqid() . '.jpg',
                'file_path' => 'products/' . $produto->id . '/nova_img_' . $i . '.jpg',
                'thumbnail_path' => 'products/' . $produto->id . '/thumb_nova_img_' . $i . '.jpg',
                'mime_type' => 'image/jpeg',
                'file_size' => $img->getSize(),
                'width' => 1200,
                'height' => 900,
                'alt_text' => 'Nova Imagem ' . $i,
                'sort_order' => $i,
                'is_primary' => false
            ]);
        }
        
        // Verificar novas imagens
        $this->assertDatabaseCount('product_images', 4);
        
        // ========================================
        // ETAPA 7: REATIVAR PRODUTO
        // ========================================
        
        // Reativar produto mudando status para 'active'
        $produto->update(['status' => 'active']);
        
        // Atualizar alguns dados do produto
        $produto->update([
            'name' => 'Notebook Gamer Teste Atualizado',
            'price' => 5499.99,
            'stock_quantity' => 15
        ]);
        
        // Verificar atualizações
        $this->assertDatabaseHas('products', [
            'id' => $produto->id,
            'status' => 'active',
            'name' => 'Notebook Gamer Teste Atualizado',
            'price' => 5499.99,
            'stock_quantity' => 15
        ]);
        
        // ========================================
        // ETAPA 8: VERIFICAR PRODUTO ATUALIZADO NA HOME
        // ========================================
        
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste Atualizado');
        $response->assertSee('5.499,99'); // Novo preço
        
        // Verificar na busca
        $response = $this->get('/buscar?q=notebook');
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste Atualizado');
        
        // Verificar página individual do produto
        $response = $this->get("/produto/{$produto->id}");
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer Teste Atualizado');
        $response->assertSee('5.499,99');
        
        // ========================================
        // VERIFICAÇÕES FINAIS
        // ========================================
        
        // Verificar integridade dos dados conforme dicionário
        $produtoFinal = Product::find($produto->id);
        $this->assertEquals($this->sellerProfile->id, $produtoFinal->seller_id);
        $this->assertEquals($this->category->id, $produtoFinal->category_id);
        $this->assertEquals('active', $produtoFinal->status);
        $this->assertEquals(15, $produtoFinal->stock_quantity);
        $this->assertCount(4, $produtoFinal->images);
        
        // Verificar que apenas uma imagem é principal
        $imagensPrincipais = ProductImage::where('product_id', $produto->id)
            ->where('is_primary', true)
            ->count();
        $this->assertEquals(1, $imagensPrincipais);
    }

    /**
     * Teste de visibilidade baseada em status
     * Conforme DICIONÁRIO: status = 'active', 'draft', 'inactive'
     */
    public function test_visibilidade_produto_por_status()
    {
        // Produto ATIVO - deve aparecer
        $produtoAtivo = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Visível',
            'status' => 'active', // CORRETO conforme dicionário
            'stock_quantity' => 10
        ]);
        
        // Produto INATIVO - não deve aparecer
        $produtoInativo = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Invisível',
            'status' => 'inactive', // CORRETO conforme dicionário
            'stock_quantity' => 10
        ]);
        
        // Produto RASCUNHO - não deve aparecer
        $produtoRascunho = Product::factory()->create([
            'seller_id' => $this->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Produto Rascunho',
            'status' => 'draft', // CORRETO conforme dicionário
            'stock_quantity' => 10
        ]);
        
        // Testar home
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Produto Visível');
        $response->assertDontSee('Produto Invisível');
        $response->assertDontSee('Produto Rascunho');
        
        // Vendedor vê todos no painel
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Produto Visível');
        $response->assertSee('Produto Invisível');
        $response->assertSee('Produto Rascunho');
    }

    protected function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        parent::tearDown();
    }
}