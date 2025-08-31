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

class SellerJourneySimpleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_complete_seller_journey()
    {
        // 1. Criar vendedor e perfil diretamente no banco
        $seller = User::factory()->create([
            'role' => 'seller',
            'name' => 'João Silva',
            'email' => 'joao.silva@example.com',
        ]);
        
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Silva Comércio Ltda',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'phone' => '(11) 98765-4321',
            'address' => 'Rua das Flores, 123, São Paulo, SP',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'business_description' => 'Comércio de produtos eletrônicos',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        // 2. Verificar que vendedor pendente não acessa dashboard completo
        $this->actingAs($seller);
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('seller.pending');

        // 3. Admin aprova o vendedor
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertStatus(302); // redirect back
        
        $sellerProfile->refresh();
        $this->assertEquals('approved', $sellerProfile->status);
        $this->assertNotNull($sellerProfile->approved_at);

        // 4. Vendedor aprovado acessa dashboard
        $this->actingAs($seller);
        $seller->refresh(); // Refresh para obter status atualizado
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('seller.dashboard');

        // 5. Vendedor pode gerenciar produtos
        $category = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'description' => 'Produtos eletrônicos diversos',
        ]);

        $response = $this->get('/seller/products');
        $response->assertStatus(200);
        $response->assertViewIs('seller.products.index');

        // 6. Criar produto diretamente no banco (simplificado)
        $product = Product::create([
            'seller_id' => $sellerProfile->id,
            'category_id' => $category->id,
            'name' => 'Smartphone XYZ',
            'slug' => 'smartphone-xyz',
            'description' => 'Smartphone com ótima qualidade',
            'price' => 999.90,
            'compare_at_price' => 1299.90,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->assertNotNull($product);
        $this->assertEquals($sellerProfile->id, $product->seller_id);

        // 7. Teste de rejeição
        $rejectedSeller = User::factory()->create(['role' => 'seller']);
        $rejectedProfile = SellerProfile::create([
            'user_id' => $rejectedSeller->id,
            'company_name' => 'Empresa Rejeitada',
            'document_type' => 'cnpj',
            'document_number' => '98.765.432/0001-10',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        $response = $this->post("/admin/sellers/{$rejectedProfile->id}/reject", [
            'rejection_reason' => 'Documentação incompleta',
        ]);

        $rejectedProfile->refresh();
        $this->assertEquals('rejected', $rejectedProfile->status);
        $this->assertEquals('Documentação incompleta', $rejectedProfile->rejection_reason);

        // 8. Vendedor rejeitado vê página de rejeição
        $this->actingAs($rejectedSeller);
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('seller.rejected');

        // 9. Teste de suspensão
        $this->actingAs($admin);
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/suspend");
        
        $sellerProfile->refresh();
        $this->assertEquals('suspended', $sellerProfile->status);
    }

    public function test_admin_can_manage_seller_commission()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Test Company',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $this->actingAs($admin);
        
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/commission", [
            'commission_rate' => 15.5,
        ]);

        $response->assertStatus(302);
        
        $sellerProfile->refresh();
        $this->assertEquals(15.5, $sellerProfile->commission_rate);
    }

    public function test_middleware_authorization()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'company_name' => 'Test Company',
            'document_type' => 'cnpj',
            'document_number' => '12.345.678/0001-90',
            'status' => 'pending',
        ]);

        // Não sellers não podem acessar rotas admin
        $this->actingAs($seller);
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/'); // middleware admin

        $this->actingAs($customer);
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/'); // middleware admin

        // Guests são redirecionados para login
        auth()->logout();
        $response = $this->post("/admin/sellers/{$sellerProfile->id}/approve");
        $response->assertRedirect('/login');
    }
}