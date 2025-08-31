<?php
/**
 * Arquivo: tests/Feature/CompleteUserJourneyTest.php
 * Descrição: Teste da jornada completa do usuário - do cadastro até gestão de produtos
 * Laravel Version: 12.x
 * Criado em: 29/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompleteUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_user_journey_from_registration_to_product_management(): void
    {
        echo "\n🎯 SIMULANDO JORNADA COMPLETA DO USUÁRIO\n";
        echo "========================================\n";

        // 1. Usuário acessa página pública
        echo "1. 🌐 Acessando página pública...\n";
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Marketplace');
        echo "   ✅ Página inicial carregada com sucesso\n";

        // 2. Usuário faz cadastro como customer inicialmente
        echo "\n2. 👤 Fazendo cadastro inicial...\n";
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'phone' => '(11) 98765-4321',
            'role' => 'customer',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ];

        $response = $this->post('/register', $userData);
        $response->assertRedirect('/dashboard');
        echo "   ✅ Cadastro realizado com sucesso\n";

        // Verificar se usuário está autenticado
        $this->assertAuthenticated();
        $user = User::where('email', 'joao@exemplo.com')->first();
        $this->assertEquals('customer', $user->role);
        echo "   ✅ Usuário autenticado como customer\n";

        // 3. Verificar se aparece botão para criar loja (usuário sem loja)
        echo "\n3. 🏪 Verificando interface para usuário SEM loja...\n";
        $response = $this->actingAs($user)->get('/');
        $response->assertSee('Criar Minha Loja');
        echo "   ✅ Botão 'Criar Minha Loja' está visível\n";

        // 4. Usuário decide criar uma loja (se torna seller)
        echo "\n4. 🛒 Criando loja (mudando para seller)...\n";
        $response = $this->actingAs($user)->post('/become-seller');
        $response->assertRedirect('/seller/onboarding');
        
        // Verificar se role mudou
        $user->refresh();
        $this->assertEquals('seller', $user->role);
        echo "   ✅ Usuário convertido para seller\n";

        // 5. Completar onboarding
        echo "\n5. 📋 Completando onboarding da loja...\n";
        Storage::fake('public');
        
        $onboardingData = [
            'company_name' => 'Loja do João',
            'document_type' => 'cnpj',
            'document_number' => '11.222.333/0001-81',
            'phone' => '(11) 98765-4321',
            'address' => 'Rua das Flores, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'bank_name' => 'Banco do Brasil',
            'bank_agency' => '1234',
            'bank_account' => '56789-0',
            'address_proof' => UploadedFile::fake()->create('comprovante.pdf', 100, 'application/pdf'),
            'identity_proof' => UploadedFile::fake()->create('identidade.pdf', 100, 'application/pdf'),
        ];

        $response = $this->actingAs($user)->post('/seller/onboarding', $onboardingData);
        $response->assertRedirect('/seller/pending');
        echo "   ✅ Onboarding completado\n";

        // 6. Simular aprovação pelo admin
        echo "\n6. ⭐ Aprovando loja pelo admin...\n";
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = $user->sellerProfile;
        
        $response = $this->actingAs($admin)->post("/admin/sellers/{$seller->id}/approve");
        $response->assertRedirect()->with('success');
        
        $seller->refresh();
        $this->assertEquals('approved', $seller->status);
        echo "   ✅ Loja aprovada pelo admin\n";

        // 7. Verificar se agora aparece botão para administrar loja
        echo "\n7. 🎛️ Verificando interface para usuário COM loja...\n";
        $response = $this->actingAs($user)->get('/');
        $response->assertSee('Administrar Loja');
        $response->assertDontSee('Criar Minha Loja');
        echo "   ✅ Botão 'Administrar Loja' está visível\n";
        echo "   ✅ Botão 'Criar Minha Loja' foi removido\n";

        // 8. Criar categoria para produtos
        echo "\n8. 📂 Criando categoria para produtos...\n";
        $category = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true,
        ]);
        echo "   ✅ Categoria 'Eletrônicos' criada\n";

        // 9. Cadastrar produtos
        echo "\n9. 📦 Cadastrando produtos na loja...\n";
        
        // Produto 1
        $product1Data = [
            'name' => 'Smartphone Premium',
            'description' => 'Smartphone top de linha com ótima qualidade',
            'short_description' => 'Smartphone premium',
            'price' => 1200.00,
            'category_id' => $category->id,
            'stock_quantity' => 10,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'sku' => 'SMART001',
            'weight' => 0.2,
        ];

        $response = $this->actingAs($user)->post('/seller/products', $product1Data);
        $response->assertRedirect();
        
        $product1 = Product::where('sku', 'SMART001')->first();
        $this->assertNotNull($product1);
        
        // Adicionar imagem fictícia para poder publicar
        $product1->images()->create([
            'original_name' => 'produto1.jpg',
            'file_name' => 'produto1.jpg',
            'file_path' => 'products/produto1.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'alt_text' => 'Smartphone Premium',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
        
        // Publicar produto 1
        $response = $this->actingAs($user)->patch("/seller/products/{$product1->id}/toggle-status");
        $product1->refresh();
        $this->assertEquals('active', $product1->status);
        echo "   ✅ Produto 1 criado: {$product1->name} - R$ {$product1->price}\n";

        // Produto 2
        $product2Data = [
            'name' => 'Notebook Gamer',
            'description' => 'Notebook para jogos com alta performance',
            'short_description' => 'Notebook gamer',
            'price' => 2500.00,
            'category_id' => $category->id,
            'stock_quantity' => 5,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'sku' => 'NOTE001',
            'weight' => 2.5,
        ];

        $response = $this->actingAs($user)->post('/seller/products', $product2Data);
        $response->assertRedirect();
        
        $product2 = Product::where('sku', 'NOTE001')->first();
        $this->assertNotNull($product2);
        
        // Adicionar imagem fictícia para poder publicar
        $product2->images()->create([
            'original_name' => 'produto2.jpg',
            'file_name' => 'produto2.jpg',
            'file_path' => 'products/produto2.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'alt_text' => 'Notebook Gamer',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
        
        // Publicar produto 2
        $response = $this->actingAs($user)->patch("/seller/products/{$product2->id}/toggle-status");
        $product2->refresh();
        $this->assertEquals('active', $product2->status);
        echo "   ✅ Produto 2 criado: {$product2->name} - R$ {$product2->price}\n";

        // 10. Acessar dashboard do vendedor
        echo "\n10. 📊 Acessando dashboard da loja...\n";
        $response = $this->actingAs($user)->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Seller Dashboard');
        // Dashboard carregado com sucesso
        echo "   ✅ Dashboard do vendedor acessado\n";

        // 11. Ir para gestão de produtos
        echo "\n11. 🛠️ Acessando gestão de produtos...\n";
        $response = $this->actingAs($user)->get('/seller/products');
        $response->assertStatus(200);
        $response->assertSee('Smartphone Premium');
        $response->assertSee('Notebook Gamer');
        echo "   ✅ Lista de produtos carregada\n";

        // 12. Inativar o primeiro produto
        echo "\n12. ❌ Inativando produto 'Smartphone Premium'...\n";
        $response = $this->actingAs($user)->patch("/seller/products/{$product1->id}/toggle-status");
        $response->assertRedirect();
        
        $product1->refresh();
        $this->assertEquals('draft', $product1->status);
        echo "   ✅ Produto inativado: {$product1->name} (status: {$product1->status})\n";

        // 13. Alterar preço do segundo produto
        echo "\n13. 💰 Alterando preço do 'Notebook Gamer'...\n";
        $updateData = [
            'name' => 'Notebook Gamer',
            'description' => 'Notebook para jogos com alta performance',
            'short_description' => 'Notebook gamer',
            'price' => 2200.00, // Reduzindo de 2500 para 2200
            'category_id' => $category->id,
            'stock_quantity' => 5,
            'stock_status' => 'in_stock',
            'status' => 'active',
            'sku' => 'NOTE001',
            'weight' => 2.5,
        ];

        $response = $this->actingAs($user)->put("/seller/products/{$product2->id}", $updateData);
        $response->assertRedirect();
        
        $product2->refresh();
        $this->assertEquals(2200.00, $product2->price);
        echo "   ✅ Preço alterado: {$product2->name} - R$ {$product2->price}\n";

        // 14. Fazer busca dos produtos para verificar as alterações
        echo "\n14. 🔍 Verificando produtos na busca pública...\n";
        
        // Buscar produtos na página pública
        $response = $this->get('/products');
        $response->assertStatus(200);
        
        // Verificar se apenas o produto ativo aparece
        $response->assertSee('Notebook Gamer');
        $response->assertSee('R$ 2.200,00'); // Novo preço
        $response->assertDontSee('Smartphone Premium'); // Produto inativo não deve aparecer
        echo "   ✅ Busca pública mostra apenas produto ativo\n";
        echo "   ✅ Novo preço R$ 2.200,00 está sendo exibido\n";
        echo "   ✅ Produto inativo não aparece na busca\n";

        // 15. Verificar busca específica por categoria
        echo "\n15. 🏷️ Verificando busca por categoria...\n";
        $response = $this->get("/products/category/{$category->id}");
        $response->assertStatus(200);
        $response->assertSee('Notebook Gamer');
        $response->assertSee('R$ 2.200,00');
        $response->assertDontSee('Smartphone Premium');
        echo "   ✅ Busca por categoria funciona corretamente\n";

        // 16. Verificação final no dashboard do vendedor
        echo "\n16. 📋 Verificação final no dashboard...\n";
        $response = $this->actingAs($user)->get('/seller/products');
        $response->assertSee('Smartphone Premium');
        $response->assertSee('Inativo'); // Status do produto inativo
        $response->assertSee('Notebook Gamer');
        $response->assertSee('Ativo'); // Status do produto ativo
        echo "   ✅ Dashboard mostra todos os produtos com status corretos\n";

        echo "\n🎉 JORNADA COMPLETA REALIZADA COM SUCESSO!\n";
        echo "==========================================\n";
        echo "✅ Cadastro inicial como customer\n";
        echo "✅ Interface mostra 'Criar Minha Loja'\n";
        echo "✅ Conversão para seller\n";
        echo "✅ Onboarding completo\n";
        echo "✅ Aprovação da loja\n";
        echo "✅ Interface mostra 'Administrar Loja'\n";
        echo "✅ Cadastro de produtos\n";
        echo "✅ Gestão de produtos (inativar/alterar preço)\n";
        echo "✅ Busca pública reflete as alterações\n";
        echo "✅ Produtos inativos não aparecem publicamente\n";
        echo "✅ Novos preços são exibidos corretamente\n";
    }
}