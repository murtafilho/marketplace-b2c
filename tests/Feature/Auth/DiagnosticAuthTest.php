<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DiagnosticAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_attempt_directly(): void
    {
        // Criar usuário diretamente
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '123456789',
            'is_active' => true,
        ]);

        // Testar Auth::attempt diretamente
        $result = Auth::attempt(['email' => 'test@example.com', 'password' => 'password']);
        
        $this->assertTrue($result, 'Auth::attempt should return true');
        $this->assertTrue(Auth::check(), 'User should be authenticated after Auth::attempt');
        $this->assertEquals($user->id, Auth::id(), 'Authenticated user should be the correct user');
    }

    public function test_user_factory_password(): void
    {
        // Testar se o factory está criando senhas corretas
        $user = User::factory()->admin()->create();
        
        $this->assertTrue(Hash::check('password', $user->password), 'Factory should create users with password "password"');
    }

    public function test_login_with_real_http_call(): void
    {
        $user = User::factory()->admin()->create();
        
        // Primeiro obter o token CSRF fazendo GET na página de login
        $loginPage = $this->get('/login');
        $loginPage->assertStatus(200);
        
        // Fazer requisição HTTP real para /login COM token CSRF
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            '_token' => csrf_token(),
        ]);
        
        // Verificar resposta
        dump('Response status: ' . $response->getStatusCode());
        dump('Response headers: ', $response->headers->all());
        dump('Session data: ', session()->all());
        dump('Auth check: ', Auth::check());
        dump('Auth user: ', Auth::user());
        
        // Assertions básicas
        $response->assertStatus(302); // Deve ser redirect
        $this->assertTrue(Auth::check(), 'User should be authenticated');
    }
}