<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $seller;
    protected $sellerProfile;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário administrador
        $this->admin = User::factory()->create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'role' => 'admin'
        ]);

        // Criar vendedor
        $this->seller = User::factory()->create([
            'name' => 'Vendedor Teste',
            'email' => 'vendedor@teste.com',
            'role' => 'seller'
        ]);

        // Criar perfil de vendedor pendente
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'pending_approval',
            'company_name' => 'Loja Teste'
        ]);

        // Criar categoria
        $this->category = Category::factory()->create([
            'name' => 'Categoria Teste',
            'is_active' => true
        ]);
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $regularUser = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($regularUser)
                         ->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_sellers_list()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/sellers');

        $response->assertStatus(200);
        $response->assertSee('Loja Teste');
        $response->assertSee('pending_approval');
    }

    public function test_admin_can_approve_seller()
    {
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$this->sellerProfile->id}/approve");

        $response->assertRedirect();
        
        $this->sellerProfile->refresh();
        $this->assertEquals('approved', $this->sellerProfile->status);
        $this->assertNotNull($this->sellerProfile->approved_at);
    }

    public function test_admin_can_reject_seller()
    {
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$this->sellerProfile->id}/reject", [
                             'rejection_reason' => 'Documentos incompletos'
                         ]);

        $response->assertRedirect();
        
        $this->sellerProfile->refresh();
        $this->assertEquals('rejected', $this->sellerProfile->status);
        $this->assertEquals('Documentos incompletos', $this->sellerProfile->rejection_reason);
    }

    public function test_admin_can_view_categories()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/categories');

        $response->assertStatus(200);
        $response->assertSee('Categoria Teste');
    }

    public function test_admin_can_create_category()
    {
        $categoryData = [
            'name' => 'Nova Categoria',
            'slug' => 'nova-categoria',
            'description' => 'Descrição da categoria',
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', $categoryData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('categories', [
            'name' => 'Nova Categoria',
            'is_active' => true
        ]);
    }

    public function test_admin_can_update_category()
    {
        $updateData = [
            'name' => 'Categoria Atualizada',
            'description' => 'Nova descrição',
            'is_active' => false
        ];

        $response = $this->actingAs($this->admin)
                         ->put("/admin/categories/{$this->category->id}", $updateData);

        $response->assertRedirect();
        
        $this->category->refresh();
        $this->assertEquals('Categoria Atualizada', $this->category->name);
        $this->assertEquals(false, $this->category->is_active);
    }

    public function test_admin_can_toggle_category_status()
    {
        $originalStatus = $this->category->is_active;
        
        $response = $this->actingAs($this->admin)
                         ->patch("/admin/categories/{$this->category->id}/toggle-status");

        $response->assertRedirect();
        
        $this->category->refresh();
        $this->assertNotEquals($originalStatus, $this->category->is_active);
    }

    public function test_admin_can_view_reports()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertSee('Relatórios');
    }

    public function test_admin_can_view_financial_reports()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports/financial');

        $response->assertStatus(200);
        $response->assertSee('Relatório Financeiro');
    }

    public function test_admin_permissions_are_enforced()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $customer = User::factory()->create(['role' => 'customer']);
        
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/sellers',
            '/admin/categories',
            '/admin/reports'
        ];

        foreach ($adminRoutes as $route) {
            // Seller não pode acessar
            $response = $this->actingAs($seller)->get($route);
            $this->assertTrue(
                $response->status() === 403 || $response->status() === 302,
                "Seller should not access {$route}"
            );

            // Customer não pode acessar
            $response = $this->actingAs($customer)->get($route);
            $this->assertTrue(
                $response->status() === 403 || $response->status() === 302,
                "Customer should not access {$route}"
            );

            // Admin pode acessar
            $response = $this->actingAs($this->admin)->get($route);
            $this->assertTrue(
                $response->status() === 200,
                "Admin should access {$route}"
            );
        }
    }

    public function test_complete_admin_workflow()
    {
        // 1. Login como admin
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // 2. Visualizar vendedores pendentes
        $response = $this->get('/admin/sellers');
        $response->assertStatus(200);
        $response->assertSee('pending_approval');

        // 3. Aprovar vendedor
        $response = $this->post("/admin/sellers/{$this->sellerProfile->id}/approve");
        $response->assertRedirect();

        // 4. Criar nova categoria
        $response = $this->post('/admin/categories', [
            'name' => 'Categoria Workflow',
            'slug' => 'categoria-workflow',
            'description' => 'Categoria criada no workflow',
            'is_active' => true
        ]);
        $response->assertRedirect();

        // 5. Visualizar relatórios
        $response = $this->get('/admin/reports/financial');
        $response->assertStatus(200);

        // Verificar resultados
        $this->sellerProfile->refresh();
        $this->assertEquals('approved', $this->sellerProfile->status);
        
        $this->assertDatabaseHas('categories', [
            'name' => 'Categoria Workflow'
        ]);
    }
}