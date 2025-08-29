<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProtectedUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test protected users restoration and functionality
     */
    public function test_protected_users_restoration_and_login(): void
    {
        echo "\n🔒 TESTANDO RESTAURAÇÃO E LOGIN DOS USUÁRIOS PROTEGIDOS\n";
        echo str_repeat("=", 60) . "\n";

        // 1. Executar o comando para criar usuários protegidos
        echo "\n1. 🔧 Executando comando de restauração...\n";
        $this->artisan('marketplace:ensure-protected-users')
             ->expectsOutput('🔒 VERIFICANDO USUÁRIOS PROTEGIDOS DO MARKETPLACE...')
             ->assertExitCode(0);
        echo "   ✅ Comando executado com sucesso\n";

        // 2. Verificar se todos os usuários foram criados
        echo "\n2. 👥 Verificando usuários criados...\n";
        
        // Recarregar usuários após o comando
        $admin = User::where('email', 'admin@marketplace.com')->first();
        $seller = User::where('email', 'tech@marketplace.com')->first();
        $customer = User::where('email', 'cliente@marketplace.com')->first();

        $this->assertNotNull($admin, 'Admin user should exist');
        $this->assertNotNull($seller, 'Seller user should exist');
        $this->assertNotNull($customer, 'Customer user should exist');

        echo "   ✅ Admin: {$admin->name} ({$admin->email})\n";
        echo "   ✅ Seller: {$seller->name} ({$seller->email})\n";
        echo "   ✅ Customer: {$customer->name} ({$customer->email})\n";

        // 3. Verificar roles
        echo "\n3. 🏷️ Verificando roles...\n";
        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('seller', $seller->role);
        $this->assertEquals('customer', $customer->role);
        echo "   ✅ Todos os roles estão corretos\n";

        // 4. Verificar emails verificados
        echo "\n4. 📧 Verificando emails verificados...\n";
        $this->assertNotNull($admin->email_verified_at);
        $this->assertNotNull($seller->email_verified_at);
        $this->assertNotNull($customer->email_verified_at);
        echo "   ✅ Todos os emails estão verificados\n";

        // 5. Verificar perfil do vendedor
        echo "\n5. 🏪 Verificando perfil do vendedor...\n";
        $sellerProfile = $seller->sellerProfile;
        $this->assertNotNull($sellerProfile, 'Seller should have a profile');
        $this->assertEquals('approved', $sellerProfile->status);
        $this->assertEquals('Tech Store Brasil', $sellerProfile->company_name);
        $this->assertNotNull($sellerProfile->approved_at);
        echo "   ✅ Perfil do vendedor criado e aprovado\n";
        echo "   ✅ Empresa: {$sellerProfile->company_name}\n";
        echo "   ✅ Status: {$sellerProfile->status}\n";

        // 6. Testar logins
        echo "\n6. 🔐 Testando logins...\n";
        
        // Login do admin
        $adminLoginResponse = $this->post('/login', [
            'email' => 'admin@marketplace.com',
            'password' => 'admin123'
        ]);
        $adminLoginResponse->assertRedirect('/dashboard');
        echo "   ✅ Login do admin funcionando\n";

        // Logout para testar próximo
        $this->post('/logout');

        // Login do seller
        $sellerLoginResponse = $this->post('/login', [
            'email' => 'tech@marketplace.com', 
            'password' => 'seller123'
        ]);
        $sellerLoginResponse->assertRedirect('/dashboard');
        echo "   ✅ Login do seller funcionando\n";

        // Logout para testar próximo
        $this->post('/logout');

        // Login do customer
        $customerLoginResponse = $this->post('/login', [
            'email' => 'cliente@marketplace.com',
            'password' => 'cliente123'
        ]);
        $customerLoginResponse->assertRedirect('/dashboard');
        echo "   ✅ Login do customer funcionando\n";

        // 7. Testar acessos específicos
        echo "\n7. 🎯 Testando acessos específicos...\n";

        // Admin dashboard
        $adminDashResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $adminDashResponse->assertStatus(200);
        echo "   ✅ Admin pode acessar dashboard administrativo\n";

        // Seller dashboard
        $sellerDashResponse = $this->actingAs($seller)->get('/seller/dashboard');
        $sellerDashResponse->assertStatus(200);
        echo "   ✅ Seller pode acessar dashboard do vendedor\n";

        // Customer não deve acessar admin
        $customerAdminResponse = $this->actingAs($customer)->get('/admin/dashboard');
        $customerAdminResponse->assertStatus(302); // Redirect porque não é admin
        echo "   ✅ Customer não consegue acessar área admin (correto)\n";

        // 8. Verificar comando de verificação
        echo "\n8. ✔️ Testando comando de verificação...\n";
        $this->artisan('marketplace:ensure-protected-users --verify')
             ->expectsOutput('✅ Todos os usuários protegidos estão presentes!')
             ->assertExitCode(0);
        echo "   ✅ Comando de verificação funcionando\n";

        // 9. Estatísticas finais
        echo "\n9. 📊 Estatísticas finais...\n";
        $totalUsers = User::count();
        $totalSellers = SellerProfile::count();
        echo "   • Total de usuários: {$totalUsers}\n";
        echo "   • Total de vendedores: {$totalSellers}\n";
        echo "   • Usuários protegidos: 3/3 ✅\n";

        echo "\n🎉 TODOS OS USUÁRIOS PROTEGIDOS RESTAURADOS E FUNCIONAIS!\n";
        echo str_repeat("=", 60) . "\n";

        // Assertions finais
        $this->assertEquals(3, $totalUsers, 'Should have exactly 3 protected users');
        $this->assertEquals(1, $totalSellers, 'Should have exactly 1 seller profile');
    }

    /**
     * Test that protected users survive database operations
     */
    public function test_protected_users_survive_multiple_operations(): void
    {
        echo "\n🛡️ TESTANDO RESISTÊNCIA DOS USUÁRIOS PROTEGIDOS\n";
        echo str_repeat("=", 50) . "\n";

        // 1. Criar usuários protegidos
        $this->artisan('marketplace:ensure-protected-users')->assertExitCode(0);
        echo "   ✅ Usuários protegidos criados\n";

        // 2. Executar comando múltiplas vezes
        for ($i = 1; $i <= 3; $i++) {
            echo "\n   🔄 Execução #{$i}...\n";
            $this->artisan('marketplace:ensure-protected-users')->assertExitCode(0);
        }

        // 3. Verificar se ainda existem e são únicos
        $adminCount = User::where('email', 'admin@marketplace.com')->count();
        $sellerCount = User::where('email', 'tech@marketplace.com')->count();
        $customerCount = User::where('email', 'cliente@marketplace.com')->count();

        $this->assertEquals(1, $adminCount, 'Should have exactly 1 admin');
        $this->assertEquals(1, $sellerCount, 'Should have exactly 1 seller');
        $this->assertEquals(1, $customerCount, 'Should have exactly 1 customer');

        echo "   ✅ Usuários permanecem únicos após múltiplas execuções\n";

        // 4. Verificar comando de verificação
        $this->artisan('marketplace:ensure-protected-users --verify')
             ->assertExitCode(0);

        echo "\n🎯 USUÁRIOS PROTEGIDOS SÃO RESISTENTES A OPERAÇÕES MÚLTIPLAS!\n";
    }
}