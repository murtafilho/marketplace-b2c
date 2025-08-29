<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        // Criar usuário admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Criar alguns dados para testar estatísticas
        $seller = SellerProfile::factory()->create(['status' => 'approved']);
        $pendingSeller = SellerProfile::factory()->create(['status' => 'pending_approval']);
        
        $category = Category::factory()->create();
        Product::factory()->create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'status' => 'active'
        ]);

        // Acessar dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('stats');
        $response->assertViewHas('recent_sellers');
        $response->assertViewHas('recent_orders');
        $response->assertViewHas('recent_activities');
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        // Criar usuário customer
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get('/admin/dashboard');

        $response->assertStatus(302); // Redirect to login
    }

    public function test_dashboard_displays_correct_statistics()
    {
        // Criar usuário admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Criar dados de teste
        User::factory(3)->create(['role' => 'customer']);
        
        $seller1 = SellerProfile::factory()->create(['status' => 'approved']);
        $seller2 = SellerProfile::factory()->create(['status' => 'pending_approval']);
        
        $category = Category::factory()->create();
        Product::factory(2)->create([
            'seller_id' => $seller1->id,
            'category_id' => $category->id,
            'status' => 'active'
        ]);

        // Acessar dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        
        // Verificar se as estatísticas estão sendo passadas
        $stats = $response->viewData('stats');
        
        $this->assertGreaterThanOrEqual(4, $stats['users_total']); // At least 3 customers + 1 admin (may include protected users)
        $this->assertEquals(1, $stats['sellers_approved']);
        $this->assertEquals(1, $stats['sellers_pending']);
        $this->assertEquals(2, $stats['products_active']);
    }

    public function test_dashboard_shows_pending_sellers()
    {
        // Criar usuário admin
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Criar vendedores pendentes
        $pendingSeller = SellerProfile::factory()->create([
            'status' => 'pending_approval',
            'submitted_at' => now()
        ]);

        // Acessar dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee($pendingSeller->user->name);
        $response->assertSee('Aguardando Aprovação');
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
}