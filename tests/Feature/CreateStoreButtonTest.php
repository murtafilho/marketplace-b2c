<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateStoreButtonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Botão "Criar Minha Loja" aparece para usuário não logado
     */
    public function test_create_store_button_shows_for_guest_users()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Criar Minha Loja');
        // Para usuários não logados, o botão leva para registro com parâmetro de seller
        $response->assertSee('register?role=seller');
    }

    /**
     * Teste: Botão "Criar Minha Loja" aparece para customer sem loja
     */
    public function test_create_store_button_shows_for_customer_without_store()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($user)->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Criar Minha Loja');
        $response->assertDontSee('Administrar Loja');
    }

    /**
     * Teste: Botão "Criar Minha Loja" aparece para admin sem loja
     */
    public function test_create_store_button_shows_for_admin_without_store()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Criar Minha Loja');
        $response->assertSee('Administrar'); // Botão de admin também aparece
        $response->assertDontSee('Administrar Loja');
    }

    /**
     * Teste: Botão "Administrar Loja" aparece para usuário COM loja
     */
    public function test_manage_store_button_shows_for_user_with_store()
    {
        $user = User::factory()->create(['role' => 'seller']);
        
        // Criar perfil de vendedor
        SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved'
        ]);
        
        $response = $this->actingAs($user)->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Administrar Loja');
        $response->assertDontSee('Criar Minha Loja');
    }

    /**
     * Teste: Admin com loja vê "Administrar Loja" em vez de "Criar Minha Loja"
     */
    public function test_admin_with_store_sees_manage_button()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Admin também tem uma loja
        SellerProfile::factory()->create([
            'user_id' => $admin->id,
            'status' => 'approved'
        ]);
        
        $response = $this->actingAs($admin)->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Administrar'); // Botão admin
        $response->assertSee('Administrar Loja'); // Botão da loja
        $response->assertDontSee('Criar Minha Loja');
    }

    /**
     * Teste: Apenas uma loja por usuário - botão some após criar loja
     */
    public function test_user_can_only_have_one_store()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        // Antes de ter loja
        $response = $this->actingAs($user)->get('/');
        $response->assertSee('Criar Minha Loja');
        
        // Criar loja para o usuário
        $response = $this->actingAs($user)->post('/become-seller');
        $user->refresh();
        $this->assertEquals('seller', $user->role);
        
        // Criar perfil de vendedor
        SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
        
        // Depois de ter loja - botão mudou
        $response = $this->actingAs($user)->get('/');
        $response->assertSee('Administrar Loja');
        $response->assertDontSee('Criar Minha Loja');
    }

    /**
     * Teste: Botão criar loja funciona para usuário customer
     */
    public function test_create_store_button_works_for_customer()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($user)->post('/become-seller');
        
        $response->assertRedirect('/seller/onboarding');
        
        $user->refresh();
        $this->assertEquals('seller', $user->role);
    }

    /**
     * Teste: Botão criar loja funciona para admin sem loja
     */
    public function test_create_store_button_works_for_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->post('/become-seller');
        
        // Admin mantém role de admin mas cria perfil de vendedor
        $response->assertRedirect('/seller/onboarding');
        
        $admin->refresh();
        // Admin continua sendo admin (não muda para seller)
        $this->assertEquals('admin', $admin->role);
    }

    /**
     * Teste: Não pode criar segunda loja
     */
    public function test_cannot_create_second_store()
    {
        $user = User::factory()->create(['role' => 'seller']);
        
        // Já tem loja
        SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved'
        ]);
        
        // Tentar criar segunda loja deve falhar
        $response = $this->actingAs($user)->post('/become-seller');
        
        // Deve redirecionar ou retornar erro
        $response->assertRedirect();
        
        // Verificar que ainda tem apenas uma loja
        $this->assertEquals(1, SellerProfile::where('user_id', $user->id)->count());
    }
}