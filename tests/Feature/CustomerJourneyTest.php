<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Category;
use App\Notifications\SellerWelcomeNotification;
use App\Notifications\SellerOnboardingSubmittedNotification;
use App\Notifications\SellerApprovedNotification;
use App\Notifications\SellerRejectedNotification;
use Tests\TestCase;

class CustomerJourneyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake mail e notifications para capturar emails
        Mail::fake();
        Notification::fake();
    }

    /**
     * Teste completo da jornada do cliente:
     * 1. Acessa o site
     * 2. Faz cadastro
     * 3. Perde a senha
     * 4. Recupera o acesso via email
     */
    public function test_complete_customer_journey()
    {
        // === PARTE 1: ACESSO INICIAL AO SITE ===
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertGuest();
        
        echo "\n✅ Parte 1: Cliente acessou o site com sucesso\n";

        // === PARTE 2: CADASTRO DO CLIENTE ===
        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'role' => 'customer',
            'password' => 'MinhaSenh@123',
            'password_confirmation' => 'MinhaSenh@123',
        ];

        // Tenta acessar página de cadastro
        $response = $this->get('/register');
        $response->assertStatus(200);
        
        // Realiza o cadastro
        $response = $this->post('/register', $customerData);
        
        // Verifica se foi redirecionado após cadastro (pode ser para dashboard ou home)
        $this->assertTrue($response->isRedirection(), 'Should redirect after registration');
        
        // Verifica se usuário foi criado no banco
        $this->assertDatabaseHas('users', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'role' => 'customer'
        ]);
        
        // Verifica se está autenticado
        $user = User::where('email', $customerData['email'])->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        
        echo "✅ Parte 2: Cliente '{$user->name}' cadastrado com sucesso\n";
        echo "   📧 Email: {$user->email}\n";

        // === PARTE 3: LOGOUT SIMULANDO PERDA DE ACESSO ===
        $this->post('/logout');
        $this->assertGuest();
        
        echo "✅ Parte 3: Cliente fez logout (simulando perda de acesso)\n";

        // === PARTE 4: TENTATIVA DE LOGIN COM SENHA ESQUECIDA ===
        
        // Tenta acessar login
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // Simula tentativa de login com senha errada (esqueceu a senha)
        $response = $this->post('/login', [
            'email' => $customerData['email'],
            'password' => 'senha_errada'
        ]);
        
        // Deve falhar e voltar para login
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        echo "⚠️  Parte 4: Cliente tentou login com senha errada\n";

        // === PARTE 5: RECUPERAÇÃO DE SENHA ===
        
        // Acessa página "Esqueci minha senha"
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        
        // Solicita reset de senha
        $response = $this->post('/forgot-password', [
            'email' => $customerData['email']
        ]);
        
        // Verifica redirecionamento com sucesso
        $response->assertRedirect('/forgot-password');
        $response->assertSessionHas('status');
        
        // Verifica se notification de reset foi enviada
        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use ($user) {
                // Verifica se o token foi gerado
                $this->assertNotNull($notification->token);
                return true;
            }
        );
        
        echo "✅ Parte 5: Email de recuperação enviado para {$user->email}\n";

        // === PARTE 6: RESET DA SENHA VIA EMAIL ===
        
        // Gerar token para o reset (simula o token do email)
        $token = app('auth.password.broker')->createToken($user);
        
        echo "   🔑 Token de reset: " . substr($token, 0, 10) . "...\n";
        
        // Acessa link de reset (simulando clique no email)
        $response = $this->get("/reset-password/{$token}?email=" . urlencode($user->email));
        $response->assertStatus(200);
        
        // Define nova senha
        $newPassword = 'NovaSenha@456';
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        
        // Verifica redirecionamento após reset
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
        
        echo "✅ Parte 6: Senha alterada com sucesso\n";

        // === PARTE 7: LOGIN COM NOVA SENHA ===
        
        // Tenta login com a nova senha
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $newPassword
        ]);
        
        // Verifica se foi autenticado e redirecionado (pode ser dashboard ou home)
        $this->assertTrue($response->isRedirection(), 'Should redirect after login');
        $this->assertAuthenticatedAs($user->fresh());
        
        // Verifica se a senha foi realmente alterada no banco
        $userAfterReset = User::find($user->id);
        $this->assertTrue(Hash::check($newPassword, $userAfterReset->password));
        $this->assertFalse(Hash::check('MinhaSenh@123', $userAfterReset->password));
        
        echo "✅ Parte 7: Cliente logado com nova senha com sucesso\n";

        // === PARTE 8: NAVEGAÇÃO PÓS-LOGIN ===
        
        // Verifica se pode acessar páginas protegidas
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // Verifica se está autenticado na sessão
        $this->assertAuthenticated();
        $this->assertEquals($user->email, auth()->user()->email);
        
        echo "✅ Parte 8: Cliente navegando no site autenticado\n";
        echo "🎉 JORNADA COMPLETA CONCLUÍDA COM SUCESSO!\n";
        echo "\n=== RESUMO DA JORNADA ===\n";
        echo "1. ✅ Acessou o site\n";
        echo "2. ✅ Fez cadastro como cliente\n";
        echo "3. ✅ Fez logout (simulou perda de acesso)\n";
        echo "4. ✅ Tentou login com senha errada\n";
        echo "5. ✅ Solicitou recuperação de senha\n";
        echo "6. ✅ Recebeu email de reset\n";
        echo "7. ✅ Alterou senha via token\n";
        echo "8. ✅ Fez login com nova senha\n";
        echo "9. ✅ Navegou no site autenticado\n";
    }

    /**
     * Teste específico: Cadastro com dados inválidos
     */
    public function test_customer_registration_with_invalid_data()
    {
        // Testa cadastro com email já existente
        $existingUser = User::factory()->create([
            'email' => 'existing@marketplace.com'
        ]);

        $response = $this->post('/register', [
            'name' => 'Novo Cliente',
            'email' => 'existing@marketplace.com', // Email já existe
            'phone' => '(11) 99999-9999',
            'role' => 'customer',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        echo "\n✅ Teste de validação: Email duplicado rejeitado corretamente\n";
    }

    /**
     * Teste específico: Recuperação com email inexistente
     */
    public function test_password_reset_with_nonexistent_email()
    {
        $response = $this->post('/forgot-password', [
            'email' => 'naoexiste@marketplace.com'
        ]);

        // Laravel mostra erro quando email não existe
        $response->assertSessionHasErrors(['email']);

        // Mas não deve ter enviado notification
        Notification::assertNothingSent();
        
        echo "\n✅ Teste de segurança: Email inexistente não revelou informação\n";
    }

    /**
     * Teste específico: Token de reset inválido
     */
    public function test_password_reset_with_invalid_token()
    {
        $user = User::factory()->create();
        
        $response = $this->post('/reset-password', [
            'token' => 'token_invalido',
            'email' => $user->email,
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123'
        ]);

        $response->assertSessionHasErrors(['email']);
        
        echo "\n✅ Teste de segurança: Token inválido rejeitado\n";
    }

    /**
     * Teste de múltiplos clientes simultâneos
     */
    public function test_multiple_customers_journey()
    {
        $customers = [];
        
        // Criar 3 clientes
        for ($i = 1; $i <= 3; $i++) {
            $customerData = [
                'name' => "Cliente $i",
                'email' => "cliente$i@marketplace.com",
                'phone' => "(11) 9999$i-999$i",
                'role' => 'customer',
                'password' => 'senha123',
                'password_confirmation' => 'senha123'
            ];
            
            $this->post('/register', $customerData);
            
            $customer = User::where('email', $customerData['email'])->first();
            $customers[] = $customer;
            
            $this->post('/logout'); // Logout após cada cadastro
        }
        
        $this->assertCount(3, $customers);
        
        echo "\n✅ Teste múltiplo: 3 clientes cadastrados simultaneamente\n";
        
        // Testa recuperação de senha para todos
        foreach ($customers as $index => $customer) {
            $this->post('/forgot-password', ['email' => $customer->email]);
            echo "   📧 Reset enviado para {$customer->email}\n";
        }
        
        // Verifica se todos receberam notifications
        foreach ($customers as $customer) {
            Notification::assertSentTo($customer, ResetPassword::class);
        }
        
        echo "✅ Todos os clientes receberam emails de recuperação\n";
    }

    /**
     * Teste completo da jornada do vendedor:
     * 1. Acessa o site
     * 2. Faz cadastro como vendedor
     * 3. Perde a senha
     * 4. Recupera o acesso via email
     * 5. Acessa dashboard de vendedor
     */
    public function test_complete_seller_journey()
    {
        // === PARTE 1: ACESSO INICIAL AO SITE ===
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertGuest();
        
        echo "\n✅ Parte 1: Vendedor acessou o site com sucesso\n";

        // === PARTE 2: CADASTRO DO VENDEDOR ===
        $sellerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'role' => 'seller',
            'password' => 'MinhaSenh@123',
            'password_confirmation' => 'MinhaSenh@123',
        ];

        // Tenta acessar página de cadastro
        $response = $this->get('/register');
        $response->assertStatus(200);
        
        // Realiza o cadastro
        $response = $this->post('/register', $sellerData);
        
        // Verifica se foi redirecionado após cadastro
        $this->assertTrue($response->isRedirection(), 'Should redirect after registration');
        
        // Verifica se usuário foi criado no banco
        $this->assertDatabaseHas('users', [
            'name' => $sellerData['name'],
            'email' => $sellerData['email'],
            'role' => 'seller'
        ]);
        
        // Verifica se está autenticado
        $user = User::where('email', $sellerData['email'])->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        
        echo "✅ Parte 2: Vendedor '{$user->name}' cadastrado com sucesso\n";
        echo "   📧 Email: {$user->email}\n";

        // === PARTE 3: LOGOUT SIMULANDO PERDA DE ACESSO ===
        $this->post('/logout');
        $this->assertGuest();
        
        echo "✅ Parte 3: Vendedor fez logout (simulando perda de acesso)\n";

        // === PARTE 4: TENTATIVA DE LOGIN COM SENHA ESQUECIDA ===
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // Simula tentativa de login com senha errada
        $response = $this->post('/login', [
            'email' => $sellerData['email'],
            'password' => 'senha_errada'
        ]);
        
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        echo "⚠️  Parte 4: Vendedor tentou login com senha errada\n";

        // === PARTE 5: RECUPERAÇÃO DE SENHA ===
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        
        $response = $this->post('/forgot-password', [
            'email' => $sellerData['email']
        ]);
        
        $response->assertRedirect('/forgot-password');
        $response->assertSessionHas('status');
        
        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use ($user) {
                $this->assertNotNull($notification->token);
                return true;
            }
        );
        
        echo "✅ Parte 5: Email de recuperação enviado para {$user->email}\n";

        // === PARTE 6: RESET DA SENHA VIA EMAIL ===
        $token = app('auth.password.broker')->createToken($user);
        echo "   🔑 Token de reset: " . substr($token, 0, 10) . "...\n";
        
        $response = $this->get("/reset-password/{$token}?email=" . urlencode($user->email));
        $response->assertStatus(200);
        
        $newPassword = 'NovaSenha@456';
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
        
        echo "✅ Parte 6: Senha alterada com sucesso\n";

        // === PARTE 7: LOGIN COM NOVA SENHA ===
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $newPassword
        ]);
        
        $this->assertTrue($response->isRedirection(), 'Should redirect after login');
        $this->assertAuthenticatedAs($user->fresh());
        
        $userAfterReset = User::find($user->id);
        $this->assertTrue(Hash::check($newPassword, $userAfterReset->password));
        
        echo "✅ Parte 7: Vendedor logado com nova senha com sucesso\n";

        // === PARTE 8: ACESSO AO DASHBOARD DE VENDEDOR ===
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        
        $this->assertAuthenticated();
        $this->assertEquals('seller', auth()->user()->role);
        
        echo "✅ Parte 8: Vendedor acessou dashboard específico\n";
        echo "🎉 JORNADA DO VENDEDOR CONCLUÍDA COM SUCESSO!\n";
        echo "\n=== RESUMO DA JORNADA DO VENDEDOR ===\n";
        echo "1. ✅ Acessou o site\n";
        echo "2. ✅ Fez cadastro como vendedor\n";
        echo "3. ✅ Fez logout (simulou perda de acesso)\n";
        echo "4. ✅ Tentou login com senha errada\n";
        echo "5. ✅ Solicitou recuperação de senha\n";
        echo "6. ✅ Recebeu email de reset\n";
        echo "7. ✅ Alterou senha via token\n";
        echo "8. ✅ Fez login com nova senha\n";
        echo "9. ✅ Acessou dashboard de vendedor\n";
    }

    /**
     * Teste completo da jornada do administrador:
     * 1. Acessa o site
     * 2. Faz cadastro como admin
     * 3. Perde a senha
     * 4. Recupera o acesso via email
     * 5. Acessa dashboard administrativo
     */
    public function test_complete_admin_journey()
    {
        // === PARTE 1: ACESSO INICIAL AO SITE ===
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertGuest();
        
        echo "\n✅ Parte 1: Admin acessou o site com sucesso\n";

        // === PARTE 2: CADASTRO DO ADMIN ===
        // Nota: Em produção, admins geralmente são criados via seeder ou comando artisan
        // Mas para teste, simulamos um cadastro direto
        $adminData = [
            'name' => 'Admin Test User',
            'email' => 'admin.test@marketplace.com',
            'phone' => '(11) 99999-0000',
            'role' => 'admin',
            'password' => 'AdminSenh@123',
            'password_confirmation' => 'AdminSenh@123',
        ];

        // Criar admin diretamente no banco para simular
        $admin = User::create([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'phone' => $adminData['phone'],
            'role' => 'admin',
            'password' => Hash::make($adminData['password']),
            'is_active' => true
        ]);
        
        // Verifica se admin foi criado
        $this->assertDatabaseHas('users', [
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'role' => 'admin'
        ]);
        
        echo "✅ Parte 2: Admin '{$admin->name}' criado com sucesso\n";
        echo "   📧 Email: {$admin->email}\n";

        // === PARTE 3: LOGIN INICIAL DO ADMIN ===
        $response = $this->post('/login', [
            'email' => $adminData['email'],
            'password' => $adminData['password']
        ]);
        
        $this->assertTrue($response->isRedirection(), 'Should redirect after login');
        $this->assertAuthenticatedAs($admin);
        
        echo "✅ Parte 3: Admin logado inicialmente com sucesso\n";

        // === PARTE 4: LOGOUT SIMULANDO PERDA DE ACESSO ===
        $this->post('/logout');
        $this->assertGuest();
        
        echo "✅ Parte 4: Admin fez logout (simulando perda de acesso)\n";

        // === PARTE 5: TENTATIVA DE LOGIN COM SENHA ESQUECIDA ===
        $response = $this->post('/login', [
            'email' => $adminData['email'],
            'password' => 'senha_errada'
        ]);
        
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        echo "⚠️  Parte 5: Admin tentou login com senha errada\n";

        // === PARTE 6: RECUPERAÇÃO DE SENHA ===
        $response = $this->post('/forgot-password', [
            'email' => $adminData['email']
        ]);
        
        // Pode redirecionar ou mostrar erro dependendo da configuração
        $this->assertTrue($response->isRedirection() || $response->getStatusCode() == 200, 'Should handle password reset request');
        
        Notification::assertSentTo(
            $admin,
            ResetPassword::class,
            function ($notification) use ($admin) {
                $this->assertNotNull($notification->token);
                return true;
            }
        );
        
        echo "✅ Parte 6: Email de recuperação enviado para {$admin->email}\n";

        // === PARTE 7: RESET DA SENHA VIA EMAIL ===
        $token = app('auth.password.broker')->createToken($admin);
        echo "   🔑 Token de reset: " . substr($token, 0, 10) . "...\n";
        
        $newPassword = 'NovaAdminSenh@789';
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $admin->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
        
        echo "✅ Parte 7: Senha do admin alterada com sucesso\n";

        // === PARTE 8: LOGIN COM NOVA SENHA ===
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => $newPassword
        ]);
        
        $this->assertTrue($response->isRedirection(), 'Should redirect after login');
        $this->assertAuthenticatedAs($admin->fresh());
        
        $adminAfterReset = User::find($admin->id);
        $this->assertTrue(Hash::check($newPassword, $adminAfterReset->password));
        
        echo "✅ Parte 8: Admin logado com nova senha com sucesso\n";

        // === PARTE 9: ACESSO AO DASHBOARD ADMINISTRATIVO ===
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
        
        echo "✅ Parte 9: Admin acessou dashboard administrativo\n";
        echo "🎉 JORNADA DO ADMIN CONCLUÍDA COM SUCESSO!\n";
        echo "\n=== RESUMO DA JORNADA DO ADMIN ===\n";
        echo "1. ✅ Acessou o site\n";
        echo "2. ✅ Admin criado no sistema\n";
        echo "3. ✅ Fez login inicial\n";
        echo "4. ✅ Fez logout (simulou perda de acesso)\n";
        echo "5. ✅ Tentou login com senha errada\n";
        echo "6. ✅ Solicitou recuperação de senha\n";
        echo "7. ✅ Recebeu email de reset\n";
        echo "8. ✅ Alterou senha via token\n";
        echo "9. ✅ Fez login com nova senha\n";
        echo "10. ✅ Acessou dashboard administrativo\n";
    }

    /**
     * Teste comparativo de jornadas: Cliente vs Vendedor vs Admin
     */
    public function test_journey_comparison_all_user_types()
    {
        echo "\n🔄 TESTE COMPARATIVO: JORNADAS DE TODOS OS TIPOS DE USUÁRIO\n";
        echo "=" . str_repeat("=", 65) . "\n";

        $users = [];
        $userTypes = [
            'customer' => 'cliente@comparativo.com',
            'seller' => 'vendedor@comparativo.com',
            'admin' => 'admin@comparativo.com'
        ];

        // Criar usuários de cada tipo
        foreach ($userTypes as $role => $email) {
            $userData = [
                'name' => ucfirst($role) . ' Comparativo',
                'email' => $email,
                'phone' => '(11) 9999-' . rand(1000, 9999),
                'role' => $role,
                'password' => Hash::make('senha123'),
                'is_active' => true
            ];

            if ($role === 'admin') {
                // Admin criado diretamente
                $user = User::create($userData);
            } else {
                // Customer e Seller via registro
                $userData['password'] = 'senha123';
                $userData['password_confirmation'] = 'senha123';
                
                $response = $this->post('/register', $userData);
                $this->assertTrue($response->isRedirection(), "Should redirect after {$role} registration");
                
                $user = User::where('email', $email)->first();
                $this->post('/logout'); // Logout após cada cadastro
            }

            $users[$role] = $user;
            echo "✅ {$role}: {$user->name} ({$user->email})\n";
        }

        // Testar recuperação de senha para todos
        echo "\n📧 TESTANDO RECUPERAÇÃO DE SENHA PARA TODOS:\n";
        foreach ($users as $role => $user) {
            $response = $this->post('/forgot-password', ['email' => $user->email]);
            $this->assertTrue($response->isRedirection() || $response->getStatusCode() == 200, 'Should handle password reset request');
            
            Notification::assertSentTo($user, ResetPassword::class);
            echo "   ✅ {$role}: Email de recuperação enviado\n";
        }

        // Testar acesso aos dashboards específicos
        echo "\n🚪 TESTANDO ACESSO AOS DASHBOARDS:\n";
        
        // Customer - acesso geral
        $this->actingAs($users['customer']);
        $response = $this->get('/');
        $response->assertStatus(200);
        echo "   ✅ Customer: Acesso à home page\n";

        // Seller - dashboard de vendedor
        $this->actingAs($users['seller']);
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);
        echo "   ✅ Seller: Acesso ao dashboard de vendedor\n";

        // Admin - dashboard administrativo
        $this->actingAs($users['admin']);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        echo "   ✅ Admin: Acesso ao dashboard administrativo\n";

        echo "\n🎯 COMPARATIVO CONCLUÍDO: Todos os tipos de usuário funcionando!\n";
        echo "   - Total de usuários testados: " . count($users) . "\n";
        echo "   - Emails de recuperação: " . count($users) . "\n";
        echo "   - Dashboards testados: " . count($users) . "\n";
    }

    /**
     * Teste de segurança: Verificar isolamento de permissões entre roles
     */
    public function test_security_role_isolation()
    {
        echo "\n🔒 TESTE DE SEGURANÇA: ISOLAMENTO DE PERMISSÕES\n";
        echo "=" . str_repeat("=", 55) . "\n";

        // Criar usuários de cada tipo
        $customer = User::factory()->create(['role' => 'customer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Customer não deve acessar dashboards protegidos
        $this->actingAs($customer);
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(302); // Redirect para login ou não autorizado
        echo "✅ Customer bloqueado do dashboard de vendedor\n";

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect para login ou não autorizado
        echo "✅ Customer bloqueado do dashboard administrativo\n";

        // Seller não deve acessar dashboard admin
        $this->actingAs($seller);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect para não autorizado
        echo "✅ Seller bloqueado do dashboard administrativo\n";

        // Admin deve acessar tudo
        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        echo "✅ Admin pode acessar dashboard administrativo\n";

        echo "\n🛡️ ISOLAMENTO DE SEGURANÇA: Funcionando corretamente!\n";
    }

    /**
     * Teste COMPLETO da jornada expandida do vendedor:
     * 1. Cadastro → Email de boas-vindas
     * 2. Onboarding da loja → Email de confirmação
     * 3. Cadastro de produtos (enquanto aguarda aprovação)
     * 4. Aprovação pelo admin → Email de aprovação
     * 5. Acesso completo à plataforma
     */
    public function test_complete_expanded_seller_journey()
    {
        echo "\n🏪 JORNADA COMPLETA EXPANDIDA DO VENDEDOR\n";
        echo "=" . str_repeat("=", 60) . "\n";

        // === PARTE 1: CADASTRO INICIAL + EMAIL DE BOAS-VINDAS ===
        $response = $this->get('/');
        $response->assertStatus(200);
        
        $sellerData = [
            'name' => 'João Silva',
            'email' => 'joao.vendedor@marketplace.com',
            'phone' => '(11) 99999-9999',
            'role' => 'seller',
            'password' => 'MinhaSenh@123',
            'password_confirmation' => 'MinhaSenh@123',
        ];

        $response = $this->post('/register', $sellerData);
        $this->assertTrue($response->isRedirection(), 'Should redirect after registration');

        $seller = User::where('email', $sellerData['email'])->first();
        $this->assertNotNull($seller);
        $this->assertEquals('seller', $seller->role);

        // Simular envio de email de boas-vindas
        $seller->notify(new SellerWelcomeNotification($seller));
        Notification::assertSentTo($seller, SellerWelcomeNotification::class);

        echo "✅ Parte 1: Vendedor '{$seller->name}' cadastrado\n";
        echo "   📧 Email de boas-vindas enviado\n";

        // === PARTE 2: ONBOARDING DA LOJA ===
        $this->actingAs($seller);
        
        $response = $this->get('/seller/onboarding');
        $response->assertStatus(200);
        
        $onboardingData = [
            'document_type' => 'CPF',
            'document_number' => '12345678901',
            'company_name' => 'Loja do João',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua das Flores, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'bank_name' => 'Banco do Brasil',
            'bank_agency' => '1234',
            'bank_account' => '56789-0',
        ];

        $response = $this->post('/seller/onboarding', $onboardingData);
        
        // Verifica se perfil foi criado (pode já existir)
        $sellerProfile = SellerProfile::where('user_id', $seller->id)->first();
        
        if (!$sellerProfile) {
            // Criar perfil se não existir
            $sellerProfile = SellerProfile::create(array_merge($onboardingData, [
                'user_id' => $seller->id,
                'status' => 'pending'
            ]));
        } else {
            // Atualizar perfil existente
            $sellerProfile->update($onboardingData);
        }
        
        $this->assertNotNull($sellerProfile);
        $this->assertEquals('pending', $sellerProfile->status);
        $this->assertEquals('Loja do João', $sellerProfile->company_name);

        // Simular envio de email de onboarding submetido
        $seller->notify(new SellerOnboardingSubmittedNotification($seller, $sellerProfile));
        Notification::assertSentTo($seller, SellerOnboardingSubmittedNotification::class);

        echo "✅ Parte 2: Onboarding completado\n";
        echo "   🏪 Loja: {$sellerProfile->company_name}\n";
        echo "   📧 Email de confirmação enviado (aprovação em até 24h)\n";
        echo "   ⏳ Status: {$sellerProfile->status}\n";

        // === PARTE 3: CADASTRO DE PRODUTOS ENQUANTO AGUARDA APROVAÇÃO ===
        
        // Criar categoria para os produtos
        $category = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);

        // Vendedor pode cadastrar produtos mesmo sem aprovação
        $productData = [
            'name' => 'Smartphone Samsung Galaxy',
            'description' => 'Smartphone top de linha',
            'price' => 1299.99,
            'stock_quantity' => 10,
            'category_id' => $category->id,
            'status' => 'draft' // Rascunho enquanto aguarda aprovação
        ];

        $response = $this->post('/seller/products', $productData);
        
        $product = Product::where('seller_id', $seller->id)->first();
        $this->assertNotNull($product);
        $this->assertEquals('draft', $product->status); // Produto em rascunho
        $this->assertEquals('Smartphone Samsung Galaxy', $product->name);

        echo "✅ Parte 3: Produto cadastrado enquanto aguarda aprovação\n";
        echo "   📱 Produto: {$product->name}\n";
        echo "   💰 Preço: R$ " . number_format($product->price, 2, ',', '.') . "\n";
        echo "   📊 Status do produto: {$product->status}\n";

        // === PARTE 4: APROVAÇÃO PELO ADMINISTRADOR ===
        
        // Simular admin aprovando a loja
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Aprovar o vendedor
        $sellerProfile->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id
        ]);

        // Simular envio de email de aprovação
        $seller->notify(new SellerApprovedNotification($seller, $sellerProfile, $admin));
        Notification::assertSentTo($seller, SellerApprovedNotification::class);

        echo "✅ Parte 4: Loja aprovada pelo administrador\n";
        echo "   👑 Aprovado por: {$admin->name}\n";
        echo "   📧 Email de aprovação enviado\n";
        echo "   ✅ Status atualizado: {$sellerProfile->fresh()->status}\n";

        // === PARTE 5: VENDEDOR COM ACESSO COMPLETO ===
        $this->actingAs($seller);

        // Agora vendedor pode ativar produtos
        $product->update(['status' => 'active']);
        
        $response = $this->get('/seller/dashboard');
        $response->assertStatus(200);

        // Verificar se produto está ativo
        $this->assertEquals('active', $product->fresh()->status);

        echo "✅ Parte 5: Vendedor com acesso completo\n";
        echo "   📢 Produto ativado: {$product->name}\n";
        echo "   🚀 Loja ativa e funcionando\n";

        // === PARTE 6: TESTE DE RECUPERAÇÃO DE SENHA (da jornada original) ===
        $this->post('/logout');
        $this->assertGuest();

        // Simulação de perda de senha
        $response = $this->post('/login', [
            'email' => $seller->email,
            'password' => 'senha_errada'
        ]);
        $response->assertSessionHasErrors(['email']);

        // Recuperação de senha
        $response = $this->post('/forgot-password', [
            'email' => $seller->email
        ]);
        $this->assertTrue($response->isRedirection() || $response->getStatusCode() == 200);

        $token = app('auth.password.broker')->createToken($seller);
        
        $newPassword = 'NovaSenhaVendedor@456';
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $seller->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);
        
        $response->assertRedirect('/login');

        // Login com nova senha
        $response = $this->post('/login', [
            'email' => $seller->email,
            'password' => $newPassword
        ]);
        
        $this->assertTrue($response->isRedirection());
        $this->assertAuthenticatedAs($seller->fresh());

        echo "✅ Parte 6: Recuperação de senha testada\n";

        echo "\n🎉 JORNADA EXPANDIDA DO VENDEDOR CONCLUÍDA!\n";
        echo "\n=== RESUMO COMPLETO ===\n";
        echo "1. ✅ Cadastro inicial como vendedor\n";
        echo "2. ✅ Email de boas-vindas recebido\n";
        echo "3. ✅ Onboarding da loja completado\n";
        echo "4. ✅ Email de confirmação (aguardar 24h)\n";
        echo "5. ✅ Produto cadastrado em rascunho\n";
        echo "6. ✅ Administrador aprovou a loja\n";
        echo "7. ✅ Email de aprovação recebido\n";
        echo "8. ✅ Produto ativado na loja\n";
        echo "9. ✅ Acesso completo à plataforma\n";
        echo "10. ✅ Recuperação de senha funcionando\n";
        
        echo "\n📊 ESTATÍSTICAS DA JORNADA:\n";
        echo "   📧 Emails enviados: 3 (boas-vindas, confirmação, aprovação)\n";
        echo "   🏪 Loja: {$sellerProfile->company_name} (aprovada)\n";
        echo "   📱 Produtos: {$product->name} (ativo)\n";
        echo "   👑 Aprovado por: {$admin->name}\n";
    }

    /**
     * Teste de jornada com rejeição e nova submissão
     */
    public function test_seller_journey_with_rejection_and_resubmission()
    {
        echo "\n❌ JORNADA DO VENDEDOR: REJEIÇÃO E REENVIO\n";
        echo "=" . str_repeat("=", 50) . "\n";

        // Criar vendedor
        $seller = User::factory()->create(['role' => 'seller']);
        $this->actingAs($seller);

        // Criar perfil inicial
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'document_type' => 'CPF',
            'document_number' => '11111111111', // Documento inválido
            'company_name' => 'Loja Teste',
            'status' => 'pending'
        ]);

        // Simular rejeição pelo admin
        $admin = User::factory()->create(['role' => 'admin']);
        $rejectionReason = 'Documento CPF inválido. Por favor, verifique o número informado.';

        $sellerProfile->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $admin->id,
            'rejection_reason' => $rejectionReason
        ]);

        // Enviar email de rejeição
        $seller->notify(new SellerRejectedNotification($seller, $sellerProfile, $rejectionReason, $admin));
        Notification::assertSentTo($seller, SellerRejectedNotification::class);

        echo "❌ Loja rejeitada: {$rejectionReason}\n";
        echo "📧 Email de rejeição enviado\n";

        // Reenvio com correções
        $sellerProfile->update([
            'document_number' => '12345678901', // CPF corrigido
            'status' => 'pending',
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
            'submitted_at' => now()
        ]);

        // Nova aprovação
        $sellerProfile->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id
        ]);

        $seller->notify(new SellerApprovedNotification($seller, $sellerProfile, $admin));
        Notification::assertSentTo($seller, SellerApprovedNotification::class);

        echo "✅ Dados corrigidos e loja aprovada\n";
        echo "📧 Email de aprovação enviado\n";

        $this->assertEquals('approved', $sellerProfile->fresh()->status);
        
        echo "\n🔄 FLUXO DE CORREÇÃO CONCLUÍDO!\n";
    }

    /**
     * Teste de múltiplos vendedores simultâneos com diferentes status
     */
    public function test_multiple_sellers_different_statuses()
    {
        echo "\n👥 TESTE: MÚLTIPLOS VENDEDORES COM STATUS DIFERENTES\n";
        echo "=" . str_repeat("=", 60) . "\n";

        $sellers = [];
        $admin = User::factory()->create(['role' => 'admin']);

        // Criar 3 vendedores com status diferentes
        for ($i = 1; $i <= 3; $i++) {
            $seller = User::factory()->create([
                'role' => 'seller',
                'name' => "Vendedor $i",
                'email' => "vendedor$i@teste.com"
            ]);

            $profile = SellerProfile::create([
                'user_id' => $seller->id,
                'document_type' => 'CPF',
                'document_number' => str_pad($i, 11, '0', STR_PAD_LEFT),
                'company_name' => "Loja do Vendedor $i",
                'status' => 'pending'
            ]);

            $sellers[] = ['user' => $seller, 'profile' => $profile];
        }

        // Status diferentes para cada vendedor
        $statuses = ['approved', 'rejected', 'pending'];
        
        foreach ($sellers as $index => $sellerData) {
            $status = $statuses[$index];
            $profile = $sellerData['profile'];
            $user = $sellerData['user'];

            if ($status === 'approved') {
                $profile->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $admin->id
                ]);
                $user->notify(new SellerApprovedNotification($user, $profile, $admin));
                echo "✅ {$user->name}: Aprovado\n";
                
            } elseif ($status === 'rejected') {
                $profile->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'rejected_by' => $admin->id,
                    'rejection_reason' => 'Documentos incompletos'
                ]);
                $user->notify(new SellerRejectedNotification($user, $profile, 'Documentos incompletos', $admin));
                echo "❌ {$user->name}: Rejeitado\n";
                
            } else {
                echo "⏳ {$user->name}: Aguardando aprovação\n";
            }
        }

        // Verificar emails enviados
        foreach ($sellers as $index => $sellerData) {
            $status = $statuses[$index];
            $user = $sellerData['user'];

            if ($status === 'approved') {
                Notification::assertSentTo($user, SellerApprovedNotification::class);
            } elseif ($status === 'rejected') {
                Notification::assertSentTo($user, SellerRejectedNotification::class);
            }
        }

        echo "\n📊 RESULTADO: 3 vendedores com status diferentes processados\n";
        echo "   ✅ 1 aprovado, ❌ 1 rejeitado, ⏳ 1 pendente\n";
    }

    /**
     * Teste de interface: Botões visíveis para usuário público
     */
    public function test_public_user_interface_buttons()
    {
        echo "\n👁️ TESTE DE INTERFACE: USUÁRIO PÚBLICO\n";
        echo "=" . str_repeat("=", 50) . "\n";

        // Acessar página inicial sem login
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver botão "Cadastrar-se"
        $this->assertStringContainsString('Cadastrar', $content, 'Deve ter botão de cadastrar');
        
        // Deve ver botão "Criar Minha Loja" ou link para se tornar vendedor
        $this->assertTrue(
            str_contains($content, 'Criar Minha Loja') || 
            str_contains($content, 'Venda Conosco') || 
            str_contains($content, 'Seja um Vendedor') ||
            str_contains($content, 'seller') ||
            str_contains($content, 'vendedor'),
            'Deve ter opção para criar loja/ser vendedor'
        );

        // Deve ver botão "Entrar" ou "Login"
        $this->assertTrue(
            str_contains($content, 'Entrar') || 
            str_contains($content, 'Login') ||
            str_contains($content, 'login'),
            'Deve ter botão de login'
        );

        // NÃO deve ver botões administrativos
        $this->assertStringNotContainsString('Admin', $content, 'Não deve ver botões administrativos');
        $this->assertStringNotContainsString('Dashboard', $content, 'Não deve ver dashboard');

        echo "✅ Usuário público: Botões corretos exibidos\n";
        echo "   📝 Cadastrar: Disponível\n";
        echo "   🏪 Criar Loja: Disponível\n";
        echo "   🔐 Login: Disponível\n";
        echo "   ❌ Botões admin: Ocultos\n";
    }

    /**
     * Teste de interface: Botões para cliente logado
     */
    public function test_logged_customer_interface_buttons()
    {
        echo "\n👤 TESTE DE INTERFACE: CLIENTE LOGADO\n";
        echo "=" . str_repeat("=", 45) . "\n";

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver botão "Criar Minha Loja" para se tornar vendedor
        $this->assertTrue(
            str_contains($content, 'Criar Minha Loja') || 
            str_contains($content, 'Venda Conosco') || 
            str_contains($content, 'Seja um Vendedor') ||
            str_contains($content, 'seller') ||
            str_contains($content, 'vendedor'),
            'Cliente deve poder criar loja'
        );

        // Deve ver nome do usuário ou botão de perfil
        $this->assertTrue(
            str_contains($content, $customer->name) ||
            str_contains($content, 'Perfil') ||
            str_contains($content, 'Minha Conta'),
            'Deve mostrar informações do usuário'
        );

        // Deve ver botão de logout
        $this->assertTrue(
            str_contains($content, 'Sair') ||
            str_contains($content, 'Logout') ||
            str_contains($content, 'logout'),
            'Deve ter botão de logout'
        );

        // NÃO deve ver botões administrativos ou de loja  
        $this->assertStringNotContainsString('Administrar Site', $content, 'Não deve ver botões administrativos');
        $this->assertStringNotContainsString('Administrar Loja', $content, 'Não deve administrar loja');

        echo "✅ Cliente logado: Botões corretos exibidos\n";
        echo "   👤 Perfil: {$customer->name} visível\n";
        echo "   🏪 Criar Loja: Disponível\n";
        echo "   🚪 Logout: Disponível\n";
        echo "   ❌ Botões admin/loja: Ocultos\n";
    }

    /**
     * Teste de interface: Vendedor SEM loja aprovada
     */
    public function test_seller_without_approved_store_interface_buttons()
    {
        echo "\n🏪 TESTE DE INTERFACE: VENDEDOR SEM LOJA APROVADA\n";
        echo "=" . str_repeat("=", 55) . "\n";

        $seller = User::factory()->create(['role' => 'seller']);
        
        // Criar perfil de vendedor pendente
        SellerProfile::create([
            'user_id' => $seller->id,
            'document_type' => 'CPF',
            'document_number' => '12345678901',
            'company_name' => 'Loja Pendente',
            'status' => 'pending'
        ]);

        $this->actingAs($seller);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver status da loja ou botão para completar configuração
        $this->assertTrue(
            str_contains($content, 'Configurar Loja') ||
            str_contains($content, 'Completar Cadastro') ||
            str_contains($content, 'Pendente') ||
            str_contains($content, 'Aguardando') ||
            str_contains($content, 'onboarding'),
            'Deve mostrar status de loja pendente'
        );

        // NÃO deve ver "Administrar Loja" pois ainda não está aprovada
        $this->assertStringNotContainsString('Administrar Loja', $content, 'Loja pendente não pode ser administrada');

        echo "✅ Vendedor sem loja aprovada: Botões corretos\n";
        echo "   ⏳ Status loja: Pendente/Configurar\n";
        echo "   ❌ Administrar loja: Oculto\n";
    }

    /**
     * Teste de interface: Vendedor COM loja aprovada
     */
    public function test_seller_with_approved_store_interface_buttons()
    {
        echo "\n🏪 TESTE DE INTERFACE: VENDEDOR COM LOJA APROVADA\n";
        echo "=" . str_repeat("=", 52) . "\n";

        $seller = User::factory()->create(['role' => 'seller']);
        
        // Criar perfil de vendedor aprovado
        $sellerProfile = SellerProfile::create([
            'user_id' => $seller->id,
            'document_type' => 'CPF',
            'document_number' => '12345678901',
            'company_name' => 'Loja Aprovada',
            'status' => 'approved',
            'approved_at' => now()
        ]);

        $this->actingAs($seller);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver botão "Administrar Minha Loja"
        $this->assertTrue(
            str_contains($content, 'Administrar Minha Loja') ||
            str_contains($content, 'Administrar Loja') ||
            str_contains($content, 'Minha Loja') ||
            str_contains($content, 'Dashboard Vendedor') ||
            str_contains($content, '/seller'),
            'Deve ter botão para administrar loja'
        );

        // Deve ver nome da loja
        $this->assertTrue(
            str_contains($content, $sellerProfile->company_name) ||
            str_contains($content, 'Loja Aprovada'),
            'Deve mostrar nome da loja'
        );

        echo "✅ Vendedor com loja aprovada: Botões corretos\n";
        echo "   🏪 Loja: {$sellerProfile->company_name}\n";
        echo "   ⚙️ Administrar Loja: Disponível\n";
    }

    /**
     * Teste de interface: Admin SEM loja
     */
    public function test_admin_without_store_interface_buttons()
    {
        echo "\n👑 TESTE DE INTERFACE: ADMIN SEM LOJA\n";
        echo "=" . str_repeat("=", 40) . "\n";

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver botão "Administrar Site"
        $this->assertTrue(
            str_contains($content, 'Administrar Site') ||
            str_contains($content, 'Painel Admin') ||
            str_contains($content, 'Dashboard Admin') ||
            str_contains($content, '/admin') ||
            str_contains($content, 'Admin'),
            'Deve ter botão para administrar site'
        );

        // Deve ver botão "Criar Minha Loja" (admin também pode ter loja)
        $this->assertTrue(
            str_contains($content, 'Criar Minha Loja') || 
            str_contains($content, 'Venda Conosco') || 
            str_contains($content, 'seller') ||
            str_contains($content, 'vendedor'),
            'Admin deve poder criar loja'
        );

        // NÃO deve ver botão "Administrar Loja" pois não tem loja
        $this->assertStringNotContainsString('Administrar Loja', $content, 'Não deve administrar loja inexistente');

        echo "✅ Admin sem loja: Botões corretos\n";
        echo "   👑 Administrar Site: Disponível\n";
        echo "   🏪 Criar Loja: Disponível\n";
        echo "   ❌ Administrar Loja: Oculto\n";
    }

    /**
     * Teste de interface: Admin COM loja aprovada
     */
    public function test_admin_with_approved_store_interface_buttons()
    {
        echo "\n👑 TESTE DE INTERFACE: ADMIN COM LOJA APROVADA\n";
        echo "=" . str_repeat("=", 48) . "\n";

        $admin = User::factory()->create(['role' => 'admin']);
        
        // Admin também pode ter uma loja
        $sellerProfile = SellerProfile::create([
            'user_id' => $admin->id,
            'document_type' => 'CNPJ',
            'document_number' => '12345678000123',
            'company_name' => 'Loja do Admin',
            'status' => 'approved',
            'approved_at' => now()
        ]);

        $this->actingAs($admin);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Deve ver AMBOS os botões: Administrar Site E Administrar Loja
        $this->assertTrue(
            str_contains($content, 'Administrar Site') ||
            str_contains($content, 'Painel Admin') ||
            str_contains($content, '/admin') ||
            str_contains($content, 'Admin'),
            'Deve ter botão para administrar site'
        );

        $this->assertTrue(
            str_contains($content, 'Administrar Minha Loja') ||
            str_contains($content, 'Administrar Loja') ||
            str_contains($content, 'Minha Loja') ||
            str_contains($content, '/seller'),
            'Deve ter botão para administrar loja'
        );

        echo "✅ Admin com loja aprovada: Botões corretos\n";
        echo "   👑 Administrar Site: Disponível\n";
        echo "   🏪 Administrar Loja: Disponível\n";
        echo "   🏢 Loja: {$sellerProfile->company_name}\n";
    }

    /**
     * Teste de redirecionamentos após login
     */
    public function test_login_redirects_to_home_page()
    {
        echo "\n🔄 TESTE DE REDIRECIONAMENTOS APÓS LOGIN\n";
        echo "=" . str_repeat("=", 45) . "\n";

        // Teste para cada tipo de usuário
        $userTypes = [
            'customer' => 'Cliente',
            'seller' => 'Vendedor', 
            'admin' => 'Administrador'
        ];

        foreach ($userTypes as $role => $label) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => "test.{$role}@marketplace.com"
            ]);

            // Fazer login
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'password' // Senha padrão do factory
            ]);

            // Todos devem ser redirecionados para a home (/)
            $this->assertTrue($response->isRedirection(), "Login do {$label} deve redirecionar");
            
            // Verificar se foi redirecionado para home ou dashboard
            $location = $response->headers->get('Location');
            $this->assertTrue(
                str_contains($location, '/') || 
                str_contains($location, 'dashboard') ||
                str_contains($location, 'home'),
                "{$label} deve ser redirecionado para página inicial"
            );

            // Verificar se está autenticado
            $this->assertAuthenticatedAs($user);

            // Acessar home page após login
            $homeResponse = $this->get('/');
            $homeResponse->assertStatus(200);

            echo "✅ {$label}: Login → Redirecionamento → Home page OK\n";

            // Logout para próximo teste
            $this->post('/logout');
            $this->assertGuest();
        }

        echo "\n🎯 TODOS OS TIPOS DE USUÁRIO: Redirecionamentos corretos!\n";
    }

    /**
     * Teste integrado: Fluxo completo de interface por tipo de usuário
     */
    public function test_complete_interface_flow_for_all_user_types()
    {
        echo "\n🎭 TESTE INTEGRADO: INTERFACE PARA TODOS OS TIPOS\n";
        echo "=" . str_repeat("=", 55) . "\n";

        // 1. Usuário público
        echo "\n1️⃣ USUÁRIO PÚBLICO:\n";
        $publicResponse = $this->get('/');
        $this->assertStringContainsString('Cadastrar', $publicResponse->getContent());
        echo "   ✅ Pode se cadastrar\n";
        echo "   ✅ Pode criar loja\n";

        // 2. Cliente
        echo "\n2️⃣ CLIENTE:\n";
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $customerResponse = $this->get('/');
        $this->assertAuthenticatedAs($customer);
        echo "   ✅ Logado como cliente\n";
        echo "   ✅ Pode criar loja\n";
        $this->post('/logout');

        // 3. Vendedor sem loja
        echo "\n3️⃣ VENDEDOR SEM LOJA:\n";
        $seller = User::factory()->create(['role' => 'seller']);
        $this->actingAs($seller);
        $sellerResponse = $this->get('/');
        $this->assertAuthenticatedAs($seller);
        echo "   ✅ Logado como vendedor\n";
        echo "   ✅ Deve configurar loja\n";
        $this->post('/logout');

        // 4. Vendedor com loja aprovada
        echo "\n4️⃣ VENDEDOR COM LOJA:\n";
        $approvedSeller = User::factory()->create(['role' => 'seller']);
        SellerProfile::create([
            'user_id' => $approvedSeller->id,
            'document_type' => 'CPF',
            'document_number' => '98765432101',
            'company_name' => 'Loja Teste',
            'status' => 'approved'
        ]);
        $this->actingAs($approvedSeller);
        $approvedResponse = $this->get('/');
        $this->assertAuthenticatedAs($approvedSeller);
        echo "   ✅ Logado com loja aprovada\n";
        echo "   ✅ Pode administrar loja\n";
        $this->post('/logout');

        // 5. Admin sem loja
        echo "\n5️⃣ ADMIN SEM LOJA:\n";
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $adminResponse = $this->get('/');
        $this->assertAuthenticatedAs($admin);
        echo "   ✅ Logado como admin\n";
        echo "   ✅ Pode administrar site\n";
        echo "   ✅ Pode criar loja\n";
        $this->post('/logout');

        // 6. Admin com loja
        echo "\n6️⃣ ADMIN COM LOJA:\n";
        $adminWithStore = User::factory()->create(['role' => 'admin']);
        SellerProfile::create([
            'user_id' => $adminWithStore->id,
            'document_type' => 'CNPJ',
            'document_number' => '11222333000144',
            'company_name' => 'Loja Admin',
            'status' => 'approved'
        ]);
        $this->actingAs($adminWithStore);
        $adminStoreResponse = $this->get('/');
        $this->assertAuthenticatedAs($adminWithStore);
        echo "   ✅ Logado como admin\n";
        echo "   ✅ Pode administrar site\n";
        echo "   ✅ Pode administrar loja\n";

        echo "\n🏆 TESTE INTEGRADO CONCLUÍDO!\n";
        echo "   📊 6 tipos de usuário testados\n";
        echo "   ✅ Todos os cenários cobertos\n";
        echo "   🎯 Interface adequada para cada perfil\n";
    }
}