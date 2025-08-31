<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_customer_user(): void
    {
        $userData = [
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '123456789',
            'is_active' => true,
        ];

        $user = User::create($userData);

        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => 'customer',
            'is_active' => true,
        ]);
        
        $this->assertEquals('customer', $user->role);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function can_create_seller_user_with_profile(): void
    {
        $user = User::factory()->seller()->create();
        
        $this->assertEquals('seller', $user->role);
        $this->assertInstanceOf(SellerProfile::class, $user->sellerProfile);
        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function can_create_admin_user(): void
    {
        $user = User::factory()->admin()->create();
        
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function user_factory_creates_correct_passwords(): void
    {
        $user = User::factory()->create();
        
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /** @test */
    public function can_check_user_roles(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->seller()->create();
        $admin = User::factory()->admin()->create();
        
        // Verificar se métodos de role existem e funcionam
        $this->assertEquals('customer', $customer->role);
        $this->assertEquals('seller', $seller->role);
        $this->assertEquals('admin', $admin->role);
        
        // Se existirem métodos helper para roles
        if (method_exists($customer, 'isCustomer')) {
            $this->assertTrue($customer->isCustomer());
            $this->assertFalse($customer->isSeller());
            $this->assertFalse($customer->isAdmin());
        }
        
        if (method_exists($seller, 'isSeller')) {
            $this->assertTrue($seller->isSeller());
            $this->assertFalse($seller->isCustomer());
            $this->assertFalse($seller->isAdmin());
        }
        
        if (method_exists($admin, 'isAdmin')) {
            $this->assertTrue($admin->isAdmin());
            $this->assertFalse($admin->isCustomer());
            $this->assertFalse($admin->isSeller());
        }
    }

    /** @test */
    public function user_can_be_activated_and_deactivated(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        
        $this->assertFalse($user->is_active);
        
        $user->update(['is_active' => true]);
        
        $this->assertTrue($user->refresh()->is_active);
    }

    /** @test */
    public function user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'unique@example.com']);
    }

    /** @test */
    public function user_has_required_fillable_fields(): void
    {
        $user = new User();
        
        $expectedFillable = [
            'name', 'email', 'password', 'role', 
            'phone', 'is_active', 'email_verified_at'
        ];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /** @test */
    public function user_password_is_hidden_in_array(): void
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function user_has_correct_casts(): void
    {
        $user = new User();
        $casts = $user->getCasts();
        
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    /** @test */
    public function user_uses_soft_deletes(): void
    {
        $user = User::factory()->create();
        
        $user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertNotNull($user->refresh()->deleted_at);
    }

    /** @test */
    public function seller_profile_relationship_works(): void
    {
        $seller = User::factory()->seller()->create();
        
        $this->assertInstanceOf(SellerProfile::class, $seller->sellerProfile);
        $this->assertEquals($seller->id, $seller->sellerProfile->user_id);
        
        // Testar relationship inverso
        $this->assertInstanceOf(User::class, $seller->sellerProfile->user);
        $this->assertEquals($seller->id, $seller->sellerProfile->user->id);
    }

    /** @test */
    public function customer_does_not_have_seller_profile(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        $this->assertNull($customer->sellerProfile);
    }

    /** @test */
    public function admin_does_not_have_seller_profile(): void
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertNull($admin->sellerProfile);
    }
}