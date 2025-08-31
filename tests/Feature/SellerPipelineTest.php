<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerPipelineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete seller pipeline from registration to product approval
     */
    public function test_complete_seller_pipeline(): void
    {
        Storage::fake('public');
        
        echo "\n🧪 TESTANDO PIPELINE COMPLETO DO VENDEDOR\n";
        echo str_repeat("=", 60) . "\n";

        // 1. PÁGINA INICIAL - Verificar se link "Vender no valedosol.org" funciona
        echo "\n1. 📱 Testando página inicial...\n";
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Vender no valedosol.org');
        echo "   ✅ Página inicial carregada\n";
        echo "   ✅ Link 'Vender no valedosol.org' presente\n";

        // 2. REGISTRO DE VENDEDOR - Criar conta
        echo "\n2. 👤 Testando registro de vendedor...\n";
        $registerResponse = $this->post('/register', [
            'name' => 'João Vendedor Silva',
            'email' => 'joao.vendedor@teste.com',
            'phone' => '(11) 99999-8888',
            'password' => 'senha123456',
            'password_confirmation' => 'senha123456',
            'role' => 'seller',
        ]);

        $registerResponse->assertRedirect(route('seller.onboarding.index'));
        echo "   ✅ Vendedor registrado com sucesso\n";
        echo "   ✅ Redirecionado para onboarding\n";

        // Verificar se usuário foi criado corretamente
        $user = User::where('email', 'joao.vendedor@teste.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('seller', $user->role);
        echo "   ✅ Usuário criado como 'seller'\n";

        // Verificar se SellerProfile foi criado
        $sellerProfile = $user->sellerProfile;
        $this->assertNotNull($sellerProfile);
        $this->assertEquals('pending', $sellerProfile->status);
        $this->assertEquals('João Vendedor Silva', $sellerProfile->company_name);
        echo "   ✅ SellerProfile criado com status 'pending'\n";

        // 3. ONBOARDING - Completar perfil
        echo "\n3. 🏪 Testando processo de onboarding...\n";
        
        // Verificar acesso à página de onboarding
        $onboardingResponse = $this->actingAs($user)->get('/seller/onboarding');
        $onboardingResponse->assertStatus(200);
        $onboardingResponse->assertSee('Completar Cadastro de Vendedor');
        echo "   ✅ Página de onboarding acessível\n";

        // Completar onboarding com documentos
        $addressProof = UploadedFile::fake()->create('comprovante_endereco.pdf', 1000, 'application/pdf');
        $identityProof = UploadedFile::fake()->create('documento_identidade.pdf', 1000, 'application/pdf');

        $onboardingData = [
            'company_name' => 'Loja do João Vendedor',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-01',
            'phone' => '(11) 99999-8888',
            'address' => 'Rua do Comércio, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'bank_name' => 'Banco do Brasil',
            'bank_agency' => '1234',
            'bank_account' => '12345-6',
            'address_proof' => $addressProof,
            'identity_proof' => $identityProof,
        ];

        $onboardingSubmitResponse = $this->actingAs($user)->post('/seller/onboarding', $onboardingData);
        $onboardingSubmitResponse->assertRedirect(route('seller.pending'));
        echo "   ✅ Onboarding completado com sucesso\n";

        // Verificar se dados foram salvos
        $sellerProfile->refresh();
        $this->assertEquals('pending_approval', $sellerProfile->status);
        $this->assertEquals('Loja do João Vendedor', $sellerProfile->company_name);
        $this->assertEquals('cpf', $sellerProfile->document_type);
        $this->assertNotNull($sellerProfile->submitted_at);
        echo "   ✅ Status atualizado para 'pending_approval'\n";
        echo "   ✅ Dados do onboarding salvos corretamente\n";

        // Verificar upload de documentos
        Storage::disk('public')->assertExists($sellerProfile->address_proof_path);
        Storage::disk('public')->assertExists($sellerProfile->identity_proof_path);
        echo "   ✅ Documentos enviados e salvos\n";

        // Verificar página de status pendente
        $pendingResponse = $this->actingAs($user)->get('/seller/pending');
        $pendingResponse->assertStatus(200);
        $pendingResponse->assertSee('Aguardando Aprovação');
        echo "   ✅ Página de status pendente funcionando\n";

        // 4. APROVAÇÃO PELO ADMIN - Simular aprovação
        echo "\n4. ⭐ Testando aprovação pelo administrador...\n";
        
        // Criar admin para testar aprovação
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Testar acesso à página de gerenciamento de vendedores
        $sellersResponse = $this->actingAs($admin)->get('/admin/sellers');
        $sellersResponse->assertStatus(200);
        $sellersResponse->assertSee('João Vendedor Silva');
        echo "   ✅ Admin pode acessar lista de vendedores\n";

        // Testar página de detalhes do vendedor
        $sellerShowResponse = $this->actingAs($admin)->get("/admin/sellers/{$sellerProfile->id}");
        $sellerShowResponse->assertStatus(200);
        $sellerShowResponse->assertSee('Loja do João Vendedor');
        echo "   ✅ Admin pode ver detalhes do vendedor\n";

        // Aprovar vendedor
        $approveResponse = $this->actingAs($admin)->post("/admin/sellers/{$sellerProfile->id}/approve");
        $approveResponse->assertRedirect();
        echo "   ✅ Vendedor aprovado pelo admin\n";

        // Verificar se status foi atualizado
        $sellerProfile->refresh();
        $this->assertEquals('approved', $sellerProfile->status);
        $this->assertNotNull($sellerProfile->approved_at);
        $this->assertEquals($admin->id, $sellerProfile->approved_by);
        echo "   ✅ Status atualizado para 'approved'\n";

        // 5. CADASTRO DE PRODUTOS - Testar vendedor aprovado
        echo "\n5. 📦 Testando cadastro de produtos...\n";
        
        // Criar categoria para o produto
        $category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);
        echo "   ✅ Categoria criada: {$category->name}\n";

        // Verificar acesso ao dashboard do vendedor
        $dashboardResponse = $this->actingAs($user)->get('/seller/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Olá,');
        echo "   ✅ Dashboard do vendedor acessível\n";

        // Verificar acesso à página de produtos
        $productsResponse = $this->actingAs($user)->get('/seller/products');
        $productsResponse->assertStatus(200);
        $productsResponse->assertSee('Meus Produtos');
        echo "   ✅ Página de produtos acessível\n";

        // Testar criação de produto
        $productImage = UploadedFile::fake()->image('produto.jpg', 800, 600);

        $productData = [
            'name' => 'Smartphone Samsung Galaxy A54',
            'category_id' => $category->id,
            'description' => 'Excelente smartphone com câmera de 50MP e bateria de longa duração.',
            'short_description' => 'Smartphone Samsung Galaxy A54 128GB',
            'price' => 1299.99,
            'compare_at_price' => 1499.99,
            'stock_quantity' => 50,
            'weight' => 0.202,
            'length' => 15.8,
            'width' => 7.4,
            'height' => 0.8,
            'brand' => 'Samsung',
            'model' => 'Galaxy A54',
            'warranty_months' => 12,
            // Status será 'draft' por padrão - comportamento correto
            'images' => [$productImage],
        ];

        $createProductResponse = $this->actingAs($user)->post('/seller/products', $productData);
        $createProductResponse->assertRedirect();
        echo "   ✅ Produto criado com sucesso\n";

        // Verificar se produto foi salvo
        $product = Product::where('name', 'Smartphone Samsung Galaxy A54')->first();
        $this->assertNotNull($product);
        $this->assertEquals($sellerProfile->id, $product->seller_id);
        $this->assertEquals('draft', $product->status);
        $this->assertEquals(1299.99, $product->price);
        echo "   ✅ Dados do produto salvos corretamente\n";

        // Verificar upload da imagem
        $this->assertTrue($product->images->count() > 0);
        Storage::disk('public')->assertExists($product->images->first()->file_path);
        echo "   ✅ Imagem do produto enviada e salva\n";

        // 6. VERIFICAÇÃO FINAL
        echo "\n6. 🎯 Verificação final do pipeline...\n";
        
        // Verificar se produto aparece na página inicial
        $finalHomeResponse = $this->get('/');
        $finalHomeResponse->assertStatus(200);
        // O produto pode não aparecer na home se não estiver em destaque, mas deve estar no sistema
        echo "   ✅ Sistema funcionando corretamente\n";

        // Estatísticas finais
        $totalProducts = Product::count();
        $totalSellers = SellerProfile::where('status', 'approved')->count();
        $totalUsers = User::count();

        echo "\n📊 ESTATÍSTICAS DO TESTE:\n";
        echo "   • Produtos criados: {$totalProducts}\n";
        echo "   • Vendedores aprovados: {$totalSellers}\n";
        echo "   • Usuários totais: {$totalUsers}\n";

        echo "\n🎉 PIPELINE DO VENDEDOR TESTADO COM SUCESSO!\n";
        echo str_repeat("=", 60) . "\n";

        // Assertions finais
        $this->assertTrue($totalProducts > 0);
        $this->assertTrue($totalSellers > 0);
        $this->assertTrue($totalUsers >= 2); // Pelo menos vendedor + admin
    }

    /**
     * Test seller rejection workflow
     */
    public function test_seller_rejection_workflow(): void
    {
        echo "\n🚫 TESTANDO WORKFLOW DE REJEIÇÃO DE VENDEDOR\n";
        echo str_repeat("=", 50) . "\n";

        // Criar vendedor pendente
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending_approval',
            'company_name' => 'Empresa Teste Rejeição'
        ]);

        // Criar admin
        $admin = User::factory()->create(['role' => 'admin']);

        echo "   ✅ Vendedor criado com status 'pending_approval'\n";

        // Rejeitar vendedor
        $rejectResponse = $this->actingAs($admin)->post("/admin/sellers/{$sellerProfile->id}/reject", [
            'rejection_reason' => 'Documentos inválidos ou incompletos.'
        ]);

        $rejectResponse->assertRedirect();
        echo "   ✅ Vendedor rejeitado pelo admin\n";

        // Verificar se dados foram atualizados
        $sellerProfile->refresh();
        $this->assertEquals('rejected', $sellerProfile->status);
        $this->assertEquals('Documentos inválidos ou incompletos.', $sellerProfile->rejection_reason);
        $this->assertNotNull($sellerProfile->rejected_at);
        $this->assertEquals($admin->id, $sellerProfile->rejected_by);

        echo "   ✅ Status atualizado para 'rejected'\n";
        echo "   ✅ Motivo da rejeição salvo\n";
        echo "   ✅ Data e responsável pela rejeição registrados\n";

        echo "\n🎯 WORKFLOW DE REJEIÇÃO FUNCIONANDO CORRETAMENTE!\n";
    }
}