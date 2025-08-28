<?php
/**
 * Arquivo: tests/Feature/UserRoleTest.php
 * Descrição: Teste dos roles de usuário do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_as_customer_by_default(): void
    {
        $user = User::factory()->create();
        
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isSeller());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_can_be_created_as_seller(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        
        $this->assertTrue($user->isSeller());
        $this->assertFalse($user->isCustomer());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_can_be_created_as_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isSeller());
        $this->assertFalse($user->isCustomer());
    }

    public function test_user_scopes_work_correctly(): void
    {
        // Criar usuários de diferentes tipos
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'seller']);
        User::factory()->create(['role' => 'customer']);
        User::factory()->create(['role' => 'customer']);

        $this->assertEquals(1, User::admins()->count());
        $this->assertEquals(1, User::sellers()->count());
        $this->assertEquals(2, User::customers()->count());
    }
}
