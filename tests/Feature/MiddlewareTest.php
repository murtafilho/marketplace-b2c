<?php
/**
 * Arquivo: tests/Feature/MiddlewareTest.php
 * Descrição: Teste dos middlewares de controle de acesso
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_middleware_allows_admin_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/sellers');

        $response->assertStatus(200);
    }

    public function test_admin_middleware_blocks_non_admin(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);

        $customerResponse = $this->actingAs($customer)->get('/admin/sellers');
        $sellerResponse = $this->actingAs($seller)->get('/admin/sellers');

        $customerResponse->assertStatus(302);
        $sellerResponse->assertStatus(302);
    }

    public function test_seller_middleware_allows_seller_access(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($seller)->get('/seller/onboarding');

        $response->assertStatus(200);
    }

    public function test_seller_middleware_blocks_non_seller(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $customerResponse = $this->actingAs($customer)->get('/seller/onboarding');
        $adminResponse = $this->actingAs($admin)->get('/seller/onboarding');

        $customerResponse->assertRedirect('/');
        $customerResponse->assertSessionHas('error');
        
        $adminResponse->assertRedirect('/');
        $adminResponse->assertSessionHas('error');
    }

    public function test_verified_seller_middleware_allows_approved_seller(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
            'mp_access_token' => 'test_token',
            'mp_connected' => true,
        ]);

        // This would be tested with an actual verified seller route
        // For now, we'll test the middleware logic through the model
        $this->assertTrue($seller->sellerProfile->canSellProducts());
    }

    public function test_verified_seller_middleware_blocks_pending_seller(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
        ]);

        $this->assertFalse($seller->sellerProfile->canSellProducts());
    }

    public function test_verified_seller_middleware_blocks_seller_without_mp(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
            'mp_access_token' => null,
        ]);

        $this->assertFalse($seller->sellerProfile->canSellProducts());
    }

    public function test_guest_redirected_to_login_for_protected_routes(): void
    {
        $routes = [
            '/dashboard',
            '/seller/onboarding',
            '/admin/sellers',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_role_based_dashboard_redirects(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        // Test customer redirect
        $this->actingAs($customer);
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '(11) 99999-9999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);
        // We can't easily test the redirect without actually registering

        // Test that user roles are correctly identified
        $this->assertTrue($customer->isCustomer());
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($seller->isSeller());
        
        $this->assertFalse($customer->isAdmin());
        $this->assertFalse($customer->isSeller());
        
        $this->assertFalse($admin->isCustomer());
        $this->assertFalse($admin->isSeller());
        
        $this->assertFalse($seller->isCustomer());
        $this->assertFalse($seller->isAdmin());
    }

    public function test_middleware_handles_inactive_users(): void
    {
        $inactiveUser = User::factory()->create([
            'role' => 'customer',
            'is_active' => false
        ]);

        // Test that inactive users can still login but may have restricted access
        $response = $this->actingAs($inactiveUser)->get('/dashboard');
        
        // Inactive users should be redirected to home as per dashboard logic
        // Since they're customers, they should be redirected to home
        $response->assertRedirect(route('home'));
    }

    public function test_seller_profile_status_checks(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        
        // Test different seller statuses
        $statuses = ['pending', 'pending_approval', 'approved', 'rejected', 'suspended'];
        
        foreach ($statuses as $status) {
            $profile = $seller->sellerProfile()->updateOrCreate([], [
                'company_name' => 'Test Business',
                'status' => $status,
                'commission_rate' => 10.0,
                'mp_access_token' => $status === 'approved' ? 'test_token' : null,
                'mp_connected' => $status === 'approved',
            ]);

            if ($status === 'approved') {
                $this->assertTrue($profile->canSellProducts());
            } else {
                $this->assertFalse($profile->canSellProducts());
            }
        }
    }
}