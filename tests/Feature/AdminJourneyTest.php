<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $sellers;
    protected $categories;
    protected $products;
    protected $orders;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário administrador
        $this->admin = User::factory()->create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
            'role' => 'admin'
        ]);

        // Criar vendedores com diferentes status
        $this->sellers = collect([
            User::factory()->create([
                'name' => 'Vendedor Pendente',
                'email' => 'vendedor1@teste.com',
                'role' => 'seller'
            ]),
            User::factory()->create([
                'name' => 'Vendedor Aprovado',
                'email' => 'vendedor2@teste.com',
                'role' => 'seller'
            ]),
            User::factory()->create([
                'name' => 'Vendedor Rejeitado',
                'email' => 'vendedor3@teste.com',
                'role' => 'seller'
            ])
        ]);

        // Criar perfis de vendedores
        SellerProfile::factory()->create([
            'user_id' => $this->sellers[0]->id,
            'status' => 'pending_approval',
            'company_name' => 'Loja Pendente'
        ]);

        SellerProfile::factory()->create([
            'user_id' => $this->sellers[1]->id,
            'status' => 'approved',
            'company_name' => 'Loja Aprovada',
            'approved_at' => now()
        ]);

        SellerProfile::factory()->create([
            'user_id' => $this->sellers[2]->id,
            'status' => 'rejected',
            'company_name' => 'Loja Rejeitada',
            'rejection_reason' => 'Documentação inválida'
        ]);

        // Criar categorias
        $this->categories = Category::factory(5)->create([
            'is_active' => true
        ]);

        // Criar produtos
        $approvedSeller = $this->sellers[1]->sellerProfile;
        $this->products = Product::factory(10)->create([
            'seller_id' => $approvedSeller->id,
            'category_id' => $this->categories->random()->id,
            'status' => 'active'
        ]);

        // Criar pedidos
        $customer = User::factory()->create(['role' => 'customer']);
        $this->orders = Order::factory(5)->create([
            'user_id' => $customer->id,
            'status' => 'pending'
        ]);

        // Criar itens dos pedidos
        foreach ($this->orders as $order) {
            OrderItem::factory(2)->create([
                'order_id' => $order->id,
                'product_id' => $this->products->random()->id
            ]);
        }
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Total de Vendedores');
        $response->assertSee('Total de Produtos');
        $response->assertSee('Pedidos Pendentes');
        $response->assertSee('Receita Total');
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
        $response->assertSee('Gerenciar Vendedores');
        $response->assertSee('Loja Pendente');
        $response->assertSee('Loja Aprovada');
        $response->assertSee('Loja Rejeitada');
        $response->assertSee('pending_approval');
        $response->assertSee('approved');
        $response->assertSee('rejected');
    }

    public function test_admin_can_filter_sellers_by_status()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/sellers?status=pending_approval');

        $response->assertStatus(200);
        $response->assertSee('Loja Pendente');
        $response->assertDontSee('Loja Aprovada');
    }

    public function test_admin_can_search_sellers()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/sellers?search=Pendente');

        $response->assertStatus(200);
        $response->assertSee('Loja Pendente');
        $response->assertDontSee('Loja Aprovada');
    }

    public function test_admin_can_view_seller_details()
    {
        $seller = $this->sellers[0]->sellerProfile;
        
        $response = $this->actingAs($this->admin)
                         ->get("/admin/sellers/{$seller->id}");

        $response->assertStatus(200);
        $response->assertSee($seller->company_name);
        $response->assertSee($seller->user->email);
        $response->assertSee('pending_approval');
        $response->assertSee('Aprovar');
        $response->assertSee('Rejeitar');
    }

    public function test_admin_can_approve_seller()
    {
        $seller = $this->sellers[0]->sellerProfile;
        
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$seller->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals('approved', $seller->status);
        $this->assertNotNull($seller->approved_at);
    }

    public function test_admin_can_reject_seller()
    {
        $seller = $this->sellers[0]->sellerProfile;
        
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$seller->id}/reject", [
                             'rejection_reason' => 'Documentos incompletos'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals('rejected', $seller->status);
        $this->assertEquals('Documentos incompletos', $seller->rejection_reason);
    }

    public function test_admin_can_suspend_approved_seller()
    {
        $seller = $this->sellers[1]->sellerProfile; // Aprovado
        
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$seller->id}/suspend");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals('suspended', $seller->status);
    }

    public function test_admin_can_update_seller_commission()
    {
        $seller = $this->sellers[1]->sellerProfile;
        
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$seller->id}/commission", [
                             'commission_rate' => 15.5
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $seller->refresh();
        $this->assertEquals(15.5, $seller->commission_rate);
    }

    public function test_admin_cannot_approve_already_approved_seller()
    {
        $seller = $this->sellers[1]->sellerProfile; // Já aprovado
        
        $response = $this->actingAs($this->admin)
                         ->post("/admin/sellers/{$seller->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_view_categories_list()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/categories');

        $response->assertStatus(200);
        $response->assertSee('Gerenciar Categorias');
        
        foreach ($this->categories->take(3) as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_admin_can_create_new_category()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/categories/create');

        $response->assertStatus(200);
        $response->assertSee('Nova Categoria');
        $response->assertSee('Nome da Categoria');
        $response->assertSee('Descrição');
    }

    public function test_admin_can_store_new_category()
    {
        $categoryData = [
            'name' => 'Nova Categoria Teste',
            'slug' => 'nova-categoria-teste',
            'description' => 'Descrição da nova categoria',
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
                         ->post('/admin/categories', $categoryData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Nova Categoria Teste',
            'slug' => 'nova-categoria-teste',
            'is_active' => true
        ]);
    }

    public function test_admin_can_edit_category()
    {
        $category = $this->categories->first();
        
        $response = $this->actingAs($this->admin)
                         ->get("/admin/categories/{$category->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Editar Categoria');
        $response->assertSee($category->name);
    }

    public function test_admin_can_update_category()
    {
        $category = $this->categories->first();
        
        $updateData = [
            'name' => 'Categoria Atualizada',
            'description' => 'Nova descrição',
            'is_active' => false
        ];

        $response = $this->actingAs($this->admin)
                         ->put("/admin/categories/{$category->id}", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertEquals('Categoria Atualizada', $category->name);
        $this->assertEquals(false, $category->is_active);
    }

    public function test_admin_can_toggle_category_status()
    {
        $category = $this->categories->first();
        $originalStatus = $category->is_active;
        
        $response = $this->actingAs($this->admin)
                         ->patch("/admin/categories/{$category->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertNotEquals($originalStatus, $category->is_active);
    }

    public function test_admin_can_delete_category()
    {
        $category = $this->categories->first();
        
        $response = $this->actingAs($this->admin)
                         ->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    public function test_admin_can_access_reports_dashboard()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertSee('Relatórios');
        $response->assertSee('Financeiro');
        $response->assertSee('Vendedores');
        $response->assertSee('Produtos');
    }

    public function test_admin_can_view_financial_reports()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports/financial');

        $response->assertStatus(200);
        $response->assertSee('Relatório Financeiro');
        $response->assertSee('Total de Pedidos');
        $response->assertSee('Receita Total');
        $response->assertSee('Comissões');
    }

    public function test_admin_can_filter_financial_reports_by_date()
    {
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $response = $this->actingAs($this->admin)
                         ->get("/admin/reports/financial?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);
        $response->assertSee('Relatório Financeiro');
    }

    public function test_admin_can_view_sellers_reports()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports/sellers');

        $response->assertStatus(200);
        $response->assertSee('Relatório de Vendedores');
        $response->assertSee('Top Vendedores');
    }

    public function test_admin_can_view_products_reports()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports/products');

        $response->assertStatus(200);
        $response->assertSee('Relatório de Produtos');
        $response->assertSee('Produtos Mais Vendidos');
    }

    public function test_admin_can_export_financial_report()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/reports/export?type=financial&format=csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_upload_category_image()
    {
        Storage::fake('public');
        
        $category = $this->categories->first();
        $file = UploadedFile::fake()->image('category.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/media/upload', [
                             'file' => $file,
                             'type' => 'category',
                             'entity_id' => $category->id
                         ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('categories/' . $file->hashName());
    }

    public function test_admin_cannot_upload_invalid_file_type()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('malicious.exe', 1024);

        $response = $this->actingAs($this->admin)
                         ->post('/admin/media/upload', [
                             'file' => $file,
                             'type' => 'category'
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_admin_can_view_system_stats()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
        
        // Verificar se as estatísticas são exibidas
        $response->assertSee(count($this->sellers)); // Total de vendedores
        $response->assertSee(count($this->products)); // Total de produtos
        $response->assertSee(count($this->orders)); // Total de pedidos
    }

    public function test_admin_can_manage_layout_settings()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/layout');

        $response->assertStatus(200);
        $response->assertSee('Configurações de Layout');
        $response->assertSee('Logo');
        $response->assertSee('Cores');
        $response->assertSee('Banner Principal');
    }

    public function test_admin_can_update_layout_settings()
    {
        $response = $this->actingAs($this->admin)
                         ->post('/admin/layout/update', [
                             'site_name' => 'Novo Nome do Site',
                             'primary_color' => '#3b82f6',
                             'show_banner' => true,
                             'banner_text' => 'Bem-vindos ao marketplace!'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('layout_settings', [
            'key' => 'site_name',
            'value' => 'Novo Nome do Site'
        ]);
    }

    public function test_complete_admin_journey()
    {
        // 1. Login como admin e acessar dashboard
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // 2. Visualizar vendedores pendentes
        $response = $this->get('/admin/sellers?status=pending_approval');
        $response->assertStatus(200);

        // 3. Aprovar um vendedor
        $pendingSeller = $this->sellers[0]->sellerProfile;
        $response = $this->post("/admin/sellers/{$pendingSeller->id}/approve");
        $response->assertRedirect();

        // 4. Criar nova categoria
        $response = $this->post('/admin/categories', [
            'name' => 'Categoria Admin Test',
            'slug' => 'categoria-admin-test',
            'description' => 'Categoria criada pelo admin',
            'is_active' => true
        ]);
        $response->assertRedirect();

        // 5. Visualizar relatórios
        $response = $this->get('/admin/reports/financial');
        $response->assertStatus(200);

        // 6. Exportar relatório
        $response = $this->get('/admin/reports/export?type=financial&format=csv');
        $response->assertStatus(200);

        // Verificar se todas as ações foram executadas com sucesso
        $pendingSeller->refresh();
        $this->assertEquals('approved', $pendingSeller->status);

        $this->assertDatabaseHas('categories', [
            'name' => 'Categoria Admin Test',
            'is_active' => true
        ]);
    }

    public function test_admin_dashboard_shows_recent_activity()
    {
        $response = $this->actingAs($this->admin)
                         ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Atividade Recente');
        $response->assertSee('Vendedores Pendentes');
        $response->assertSee('Produtos Recentes');
    }

    public function test_admin_can_bulk_approve_sellers()
    {
        $pendingSellers = SellerProfile::where('status', 'pending_approval')->pluck('id')->toArray();
        
        $response = $this->actingAs($this->admin)
                         ->post('/admin/sellers/bulk-approve', [
                             'seller_ids' => $pendingSellers
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($pendingSellers as $sellerId) {
            $this->assertDatabaseHas('seller_profiles', [
                'id' => $sellerId,
                'status' => 'approved'
            ]);
        }
    }
}