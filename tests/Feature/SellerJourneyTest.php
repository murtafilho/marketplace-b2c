<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerJourneyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_seller_registration_and_complete_journey()
    {
        // 1. CADASTRO INICIAL DO VENDEDOR
        $response = $this->post('/register', [
            'name' => 'João Silva',
            'email' => 'joao.silva@example.com',
            'phone' => '(11) 99999-9999',
            'role' => 'seller',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        
        $seller = User::where('email', 'joao.silva@example.com')->first();
        $this->assertEquals('seller', $seller->role);
        $this->assertNull($seller->sellerProfile);

        // 2. PROCESSO DE ONBOARDING
        $this->actingAs($seller);
        
        // Acessar página de onboarding
        $response = $this->get('/seller/onboarding');
        $response->assertStatus(200);
        $response->assertViewIs('seller.onboarding.index');

        // Submeter dados de onboarding
        $documentFile = UploadedFile::fake()->create('documento.pdf', 1024, 'application/pdf');
        $addressProofFile = UploadedFile::fake()->create('comprovante.pdf', 1024, 'application/pdf');
        
        $response = $this->post('/seller/onboarding', [
            'company_name' => 'Silva Comércio Ltda',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'phone' => '(11) 98765-4321',
            'address' => 'Rua das Flores, 123, São Paulo, SP, 01234-567',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'business_description' => 'Comércio de produtos eletrônicos e acessórios',
            'document_file' => $documentFile,
            'address_proof_file' => $addressProofFile,
        ]);

        $response->assertRedirect('/seller/onboarding/pending');
        
        // Verificar criação do perfil do vendedor
        $seller->refresh();
        $this->assertNotNull($seller->sellerProfile);
        $this->assertEquals('pending', $seller->sellerProfile->status);
        $this->assertEquals('Silva Comércio Ltda', $seller->sellerProfile->company_name);

        // 3. STATUS DE AGUARDO - PÁGINA PENDING
        $response = $this->get('/seller/onboarding/pending');
        $response->assertStatus(200);
        $response->assertViewIs('seller.onboarding.pending');
        $response->assertSee('Aguardando Aprovação');

        // Tentar acessar dashboard - deve ser redirecionado
        $response = $this->get('/seller/dashboard');
        $response->assertRedirect('/seller/onboarding/pending');

        // 4. APROVAÇÃO PELO ADMIN
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Admin visualiza lista de vendedores
        $response = $this->get('/admin/sellers');
        $response->assertStatus(200);
        $response->assertSee('Silva Comércio Ltda');

        // Admin visualiza detalhes do vendedor
        $response = $this->get("/admin/sellers/{$seller->sellerProfile->id}");
        $response->assertStatus(200);
        $response->assertSee('Aguardando Aprovação');

        // Admin aprova o vendedor
        $response = $this->post("/admin/sellers/{$seller->sellerProfile->id}/approve");
        $response->assertRedirect("/admin/sellers/{$seller->sellerProfile->id}");
        
        $seller->sellerProfile->refresh();
        $this->assertEquals('approved', $seller->sellerProfile->status);
        $this->assertNotNull($seller->sellerProfile->approved_at);

        // 5. ACESSO AO DASHBOARD DO VENDEDOR
        $this->actingAs($seller);
        
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('seller.dashboard');
        $response->assertSee('Dashboard do Vendedor');

        // Não deve mais ser redirecionado para pending
        $response = $this->get('/seller/onboarding/pending');
        $response->assertRedirect('/seller/dashboard');

        // 6. GESTÃO DE PRODUTOS
        // Criar categoria para teste
        $category = Category::create([
            'name' => 'Eletrônicos',
            'description' => 'Produtos eletrônicos diversos',
        ]);

        // Acessar página de produtos
        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertViewIs('seller.products.index');

        // Criar novo produto
        $productImage = UploadedFile::fake()->image('produto.jpg', 800, 600);
        
        $response = $this->get('/seller/products/create');
        $response->assertStatus(200);

        $response = $this->post('/seller/products', [
            'name' => 'Smartphone XYZ',
            'description' => 'Smartphone com ótima qualidade e preço acessível',
            'price' => 999.90,
            'compare_at_price' => 1299.90,
            'stock_quantity' => 10,
            'category_id' => $category->id,
            'is_active' => true,
            'images' => [$productImage],
        ]);

        $response->assertRedirect('/seller/products');
        
        // Verificar criação do produto
        $product = Product::where('name', 'Smartphone XYZ')->first();
        $this->assertNotNull($product);
        $this->assertEquals($seller->sellerProfile->id, $product->seller_id);
        $this->assertEquals(999.90, $product->price);
        $this->assertEquals(10, $product->stock_quantity);

        // 7. TESTE DE REJEIÇÃO (novo vendedor)
        $rejectedSeller = User::factory()->create(['role' => 'seller']);
        SellerProfile::create([
            'user_id' => $rejectedSeller->id,
            'company_name' => 'Empresa Rejeitada',
            'document_type' => 'cnpj',
            'document_number' => '98.765.432/0001-10',
            'phone' => '(11) 88888-8888',
            'address' => 'Rua Teste, 456',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'business_description' => 'Teste de rejeição',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        
        $response = $this->post("/admin/sellers/{$rejectedSeller->sellerProfile->id}/reject", [
            'rejection_reason' => 'Documentação incompleta',
        ]);

        $rejectedSeller->sellerProfile->refresh();
        $this->assertEquals('rejected', $rejectedSeller->sellerProfile->status);
        $this->assertEquals('Documentação incompleta', $rejectedSeller->sellerProfile->rejection_reason);
        $this->assertNotNull($rejectedSeller->sellerProfile->rejected_at);

        // Vendedor rejeitado visualiza página de rejeição
        $this->actingAs($rejectedSeller);
        
        $response = $this->get('/seller/onboarding/pending');
        $response->assertStatus(200);
        $response->assertSee('Cadastro Rejeitado');
        $response->assertSee('Documentação incompleta');

        // 8. TESTE DE SUSPENSÃO
        $this->actingAs($admin);
        
        $response = $this->post("/admin/sellers/{$seller->sellerProfile->id}/suspend");
        $response->assertRedirect("/admin/sellers/{$seller->sellerProfile->id}");
        
        $seller->sellerProfile->refresh();
        $this->assertEquals('suspended', $seller->sellerProfile->status);

        // Vendedor suspenso não deve acessar dashboard
        $this->actingAs($seller);
        
        $response = $this->get('/seller/dashboard');
        $response->assertRedirect('/seller/onboarding/pending');
    }

    public function test_seller_cannot_access_dashboard_before_approval()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        
        $this->actingAs($seller);
        
        // Sem perfil de vendedor - deve redirecionar para onboarding
        $response = $this->get('/seller/dashboard');
        $response->assertRedirect('/seller/onboarding');

        // Com perfil pendente
        SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Teste Empresa',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'status' => 'pending',
        ]);

        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('seller.pending');
    }

    public function test_only_admin_can_approve_reject_suspend_sellers()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Teste Empresa',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'status' => 'pending',
        ]);

        // Seller não pode aprovar - middleware redireciona para home
        $this->actingAs($seller);
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/');

        // Customer não pode aprovar - middleware redireciona para home  
        $this->actingAs($customer);
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/');

        // Guest não pode aprovar - middleware auth redireciona para login
        auth()->logout();
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/login');
    }

    public function test_commission_rate_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Teste Empresa',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $this->actingAs($admin);
        
        // Atualizar taxa de comissão
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/commission", [
            'commission_rate' => 15.5,
        ]);

        $response->assertStatus(302); // redirect back
        
        $sellerProfile->refresh();
        $this->assertEquals(15.5, $sellerProfile->commission_rate);
    }
}