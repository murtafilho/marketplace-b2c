<?php
/**
 * Arquivo: tests/Feature/MultiRoleRegistrationTest.php
 * Descrição: Teste do sistema de registro multi-roles
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_works(): void
    {
        $response = $this->post('/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '(11) 99999-9999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('dashboard'));
        
        $user = User::where('email', 'joao@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('(11) 99999-9999', $user->phone);
        $this->assertTrue($user->is_active);
        $this->assertNull($user->sellerProfile);
    }

    public function test_seller_registration_creates_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Maria Vendedora',
            'email' => 'maria@example.com',
            'phone' => '(11) 88888-8888',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'seller',
        ]);

        $response->assertRedirect(route('seller.onboarding.index'));
        
        $user = User::where('email', 'maria@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('seller', $user->role);
        $this->assertTrue($user->isSeller());
        
        $sellerProfile = $user->sellerProfile;
        $this->assertNotNull($sellerProfile);
        $this->assertEquals('pending', $sellerProfile->status);
        $this->assertEquals('Maria Vendedora', $sellerProfile->company_name);
        $this->assertEquals(10.0, $sellerProfile->commission_rate);
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => '',
            'password' => '123',
            'password_confirmation' => '456',
            'role' => 'invalid-role',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email', 
            'phone',
            'password',
            'role',
        ]);
    }

    public function test_registration_prevents_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Novo Usuario',
            'email' => 'existing@example.com',
            'phone' => '(11) 99999-9999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_form_shows_role_selection(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Comprador');
        $response->assertSee('Vendedor');
        $response->assertSee('Escolha o tipo de conta');
    }

    public function test_authenticated_user_cannot_access_register(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/dashboard');
    }
}