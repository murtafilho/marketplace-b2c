<?php
/**
 * Arquivo: tests/Feature/AdminSellerManagementTest.php  
 * Descrição: Teste do sistema de gestão de vendedores pelo admin
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSellerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_sellers_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $seller1 = User::factory()->create(['role' => 'seller', 'name' => 'Vendedor 1']);
        $seller1->sellerProfile()->create([
            'company_name' => 'Empresa 1',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
        ]);

        $seller2 = User::factory()->create(['role' => 'seller', 'name' => 'Vendedor 2']);
        $seller2->sellerProfile()->create([
            'company_name' => 'Empresa 2', 
            'status' => 'approved',
            'commission_rate' => 15.0,
        ]);

        $response = $this->actingAs($admin)->get('/admin/sellers');

        $response->assertStatus(200);
        $response->assertSee('Gestão de Vendedores');
        $response->assertSee('Vendedor 1');
        $response->assertSee('Vendedor 2');
        $response->assertSee('Empresa 1');
        $response->assertSee('Empresa 2');
        $response->assertSee('Pendente Aprovação');
        $response->assertSee('Aprovados');
    }

    public function test_non_admin_cannot_access_sellers_list(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);

        $customerResponse = $this->actingAs($customer)->get('/admin/sellers');
        $sellerResponse = $this->actingAs($seller)->get('/admin/sellers');

        $customerResponse->assertStatus(302);
        $sellerResponse->assertStatus(302);
    }

    public function test_admin_can_view_seller_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create([
            'role' => 'seller',
            'name' => 'João Vendedor',
            'email' => 'joao@example.com'
        ]);
        
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Loja do João',
            'status' => 'pending_approval',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-01',
            'commission_rate' => 12.0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get("/admin/sellers/{$profile->id}");

        $response->assertStatus(200);
        $response->assertSee('Loja do João');
        $response->assertSee('João Vendedor');
        $response->assertSee('joao@example.com');
        $response->assertSee('CPF: 123.456.789-01');
        $response->assertSee('12.0%');
        $response->assertSee('Aprovar Vendedor');
        $response->assertSee('Rejeitar Vendedor');
    }

    public function test_admin_can_approve_seller(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/sellers/{$profile->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('approved', $profile->status);
        $this->assertNotNull($profile->approved_at);
    }

    public function test_admin_can_reject_seller(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/sellers/{$profile->id}/reject", [
            'rejection_reason' => 'Documentos incompletos'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('rejected', $profile->status);
        $this->assertEquals('Documentos incompletos', $profile->rejection_reason);
        $this->assertNotNull($profile->rejected_at);
    }

    public function test_admin_cannot_approve_non_pending_seller(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved', // Already approved
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/sellers/{$profile->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $profile->refresh();
        $this->assertEquals('approved', $profile->status); // Status unchanged
    }

    public function test_admin_can_suspend_seller(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller', 'is_active' => true]);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/sellers/{$profile->id}/suspend");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile->refresh();
        $seller->refresh();
        
        $this->assertEquals('suspended', $profile->status);
        $this->assertFalse($seller->is_active);
    }

    public function test_admin_can_update_commission_rate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->post("/admin/sellers/{$profile->id}/commission", [
            'commission_rate' => 15.5
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals(15.5, $profile->commission_rate);
    }

    public function test_admin_can_filter_sellers_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $pendingSeller = User::factory()->create(['role' => 'seller', 'name' => 'Pending Seller']);
        $pendingSeller->sellerProfile()->create([
            'company_name' => 'Pending Business',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
        ]);

        $approvedSeller = User::factory()->create(['role' => 'seller', 'name' => 'Approved Seller']);
        $approvedSeller->sellerProfile()->create([
            'company_name' => 'Approved Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->get('/admin/sellers?status=pending_approval');

        $response->assertStatus(200);
        $response->assertSee('Pending Seller');
        $response->assertDontSee('Approved Seller');
    }

    public function test_admin_can_search_sellers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $seller1 = User::factory()->create(['role' => 'seller', 'name' => 'João Silva']);
        $seller1->sellerProfile()->create([
            'company_name' => 'Loja do João',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $seller2 = User::factory()->create(['role' => 'seller', 'name' => 'Maria Santos']);
        $seller2->sellerProfile()->create([
            'company_name' => 'Maria Store',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($admin)->get('/admin/sellers?search=João');

        $response->assertStatus(200);
        $response->assertSee('João Silva');
        $response->assertSee('Loja do João');
        $response->assertDontSee('Maria Santos');
    }
}