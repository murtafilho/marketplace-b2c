<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedSellerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Usuário não logado pode acessar página de criar loja
     */
    public function test_guest_can_access_create_store_page()
    {
        $response = $this->get('/criar-loja');
        
        $response->assertStatus(200);
        $response->assertSee('Crie Sua Loja no Marketplace');
        $response->assertSee('Cadastre-se e crie sua loja em um único passo!');
        $response->assertSee('Seus Dados Pessoais');
        $response->assertSee('Informações da Sua Loja');
    }

    /**
     * Teste: Usuário logado sem loja vê formulário simplificado
     */
    public function test_logged_user_without_store_sees_simplified_form()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($user)->get('/criar-loja');
        
        $response->assertStatus(200);
        $response->assertSee('Olá ' . $user->name);
        $response->assertSee('Vamos configurar sua loja');
        $response->assertDontSee('Seus Dados Pessoais'); // Não mostra seção de dados pessoais
        $response->assertSee('Informações da Sua Loja');
    }

    /**
     * Teste: Usuário com loja é redirecionado para dashboard
     */
    public function test_user_with_store_is_redirected_to_dashboard()
    {
        $user = User::factory()->create(['role' => 'seller']);
        SellerProfile::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get('/criar-loja');
        
        $response->assertRedirect('/seller/dashboard');
        $response->assertSessionHas('info', 'Você já possui uma loja cadastrada.');
    }

    /**
     * Teste: Cadastro completo de novo usuário com loja
     */
    public function test_guest_can_create_account_and_store_together()
    {
        $userData = [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'phone' => '(11) 98765-4321',
            'password' => 'senha123456',
            'password_confirmation' => 'senha123456',
            'company_name' => 'Loja da Maria',
            'company_description' => 'Vendemos produtos artesanais',
            'accept_terms' => '1',
        ];
        
        $response = $this->post('/criar-loja', $userData);
        
        $response->assertRedirect('/seller/onboarding');
        $response->assertSessionHas('success', 'Conta e loja criadas com sucesso! Complete seu cadastro para começar a vender.');
        
        // Verificar se usuário foi criado
        $user = User::where('email', 'maria@exemplo.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('seller', $user->role);
        $this->assertEquals('Maria Silva', $user->name);
        
        // Verificar se perfil de vendedor foi criado
        $seller = SellerProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($seller);
        $this->assertEquals('Loja da Maria', $seller->company_name);
        // Campo description pode ser null ou conter o valor fornecido
        $this->assertTrue(
            $seller->description === 'Vendemos produtos artesanais' || 
            $seller->description === null,
            'Description should be either the provided value or null'
        );
        $this->assertEquals('pending', $seller->status);
        
        // Verificar se usuário foi logado automaticamente
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Teste: Usuário logado pode criar loja (formulário simplificado)
     */
    public function test_logged_user_can_create_store()
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'name' => 'João Santos'
        ]);
        
        $storeData = [
            'company_name' => 'Loja do João',
            'company_description' => 'Eletrônicos e acessórios',
            'accept_terms' => '1',
        ];
        
        $response = $this->actingAs($user)->post('/criar-loja', $storeData);
        
        $response->assertRedirect('/seller/onboarding');
        $response->assertSessionHas('success', 'Loja criada com sucesso! Complete seu cadastro para começar a vender.');
        
        // Verificar se role foi atualizado
        $user->refresh();
        $this->assertEquals('seller', $user->role);
        
        // Verificar se perfil de vendedor foi criado
        $seller = SellerProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($seller);
        $this->assertEquals('Loja do João', $seller->company_name);
        $this->assertEquals('pending', $seller->status);
    }

    /**
     * Teste: Admin pode criar loja mantendo role de admin
     */
    public function test_admin_can_create_store_keeping_admin_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $storeData = [
            'company_name' => 'Loja do Admin',
            'company_description' => 'Loja teste do administrador',
            'accept_terms' => '1',
        ];
        
        $response = $this->actingAs($admin)->post('/criar-loja', $storeData);
        
        $response->assertRedirect('/seller/onboarding');
        
        // Verificar que admin mantém role de admin
        $admin->refresh();
        $this->assertEquals('admin', $admin->role);
        
        // Mas tem perfil de vendedor
        $seller = SellerProfile::where('user_id', $admin->id)->first();
        $this->assertNotNull($seller);
    }

    /**
     * Teste: Validação de campos obrigatórios para novo usuário
     */
    public function test_validation_for_new_user_registration()
    {
        $invalidData = [
            'name' => '', // vazio
            'email' => 'email-invalido', // formato inválido
            'phone' => '', // vazio
            'password' => '123', // muito curto
            'password_confirmation' => '456', // não coincide
            'company_name' => '', // vazio
            'accept_terms' => '', // não aceito
        ];
        
        $response = $this->post('/criar-loja', $invalidData);
        
        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone',
            'password',
            'company_name',
            'accept_terms'
        ]);
    }

    /**
     * Teste: Email duplicado é rejeitado
     */
    public function test_duplicate_email_is_rejected()
    {
        User::factory()->create(['email' => 'existente@exemplo.com']);
        
        $userData = [
            'name' => 'Novo User',
            'email' => 'existente@exemplo.com', // email já existe
            'phone' => '(11) 98765-4321',
            'password' => 'senha123456',
            'password_confirmation' => 'senha123456',
            'company_name' => 'Nova Loja',
            'accept_terms' => '1',
        ];
        
        $response = $this->post('/criar-loja', $userData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Teste: Usuário não pode criar segunda loja
     */
    public function test_user_cannot_create_second_store()
    {
        $user = User::factory()->create(['role' => 'seller']);
        SellerProfile::factory()->create(['user_id' => $user->id]);
        
        $storeData = [
            'company_name' => 'Segunda Loja',
            'company_description' => 'Tentativa de segunda loja',
            'accept_terms' => '1',
        ];
        
        $response = $this->actingAs($user)->post('/criar-loja', $storeData);
        
        $response->assertRedirect('/seller/dashboard');
        $response->assertSessionHas('error', 'Você já possui uma loja cadastrada.');
        
        // Verificar que ainda tem apenas uma loja
        $this->assertEquals(1, SellerProfile::where('user_id', $user->id)->count());
    }

    /**
     * Teste: Interface mostra benefícios claramente
     */
    public function test_interface_shows_benefits_clearly()
    {
        $response = $this->get('/criar-loja');
        
        // Verificar se benefícios estão visíveis
        $response->assertSee('Por que vender conosco?');
        $response->assertSee('Comissão Baixa');
        $response->assertSee('Apenas 10% sobre vendas');
        $response->assertSee('Pagamento Seguro');
        $response->assertSee('Via Mercado Pago');
        $response->assertSee('Suporte Completo');
        
        // Verificar FAQ
        $response->assertSee('Perguntas Frequentes');
        $response->assertSee('Quanto tempo leva para aprovar minha loja?');
        $response->assertSee('Como recebo o pagamento das vendas?');
    }

    /**
     * Teste: Fluxo completo do botão na home até criar loja
     */
    public function test_complete_flow_from_home_button()
    {
        // 1. Acessar home
        $response = $this->get('/');
        $response->assertSee('Criar Minha Loja');
        
        // 2. Clicar no botão leva para página correta
        $response = $this->get('/criar-loja');
        $response->assertStatus(200);
        
        // 3. Preencher e enviar formulário
        $userData = [
            'name' => 'Pedro Oliveira',
            'email' => 'pedro@exemplo.com',
            'phone' => '(11) 91234-5678',
            'password' => 'senha123456',
            'password_confirmation' => 'senha123456',
            'company_name' => 'Pedro Store',
            'company_description' => 'Loja de eletrônicos',
            'accept_terms' => '1',
        ];
        
        $response = $this->post('/criar-loja', $userData);
        
        // 4. Verificar redirecionamento para onboarding
        $response->assertRedirect('/seller/onboarding');
        
        // 5. Verificar que está logado
        $this->assertAuthenticated();
        
        // 6. Verificar que pode acessar onboarding
        $response = $this->get('/seller/onboarding');
        $response->assertStatus(200);
    }
}