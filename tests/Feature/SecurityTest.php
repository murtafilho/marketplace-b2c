<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_protection_on_forms()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(419);
    }

    public function test_login_rate_limiting()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_password_strength_requirements()
    {
        $response = $this->withSession(['_token' => 'test'])
            ->post('/register', [
                '_token' => 'test',
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '123',
                'password_confirmation' => '123',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_authenticated_routes_protection()
    {
        $protectedRoutes = [
            '/dashboard',
            '/seller/dashboard',
            '/admin/dashboard',
            '/seller/onboarding',
            '/shop/cart',
            '/shop/checkout'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->status() === 302 || $response->status() === 401,
                "Route {$route} should be protected but returned status: " . $response->status()
            );
        }
    }

    public function test_admin_role_protection()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_seller_middleware_protection()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/seller/dashboard');
        
        $response->assertRedirect('/seller/onboarding');
    }

    public function test_verified_seller_middleware()
    {
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get('/seller/dashboard');
        
        $response->assertRedirect('/seller/onboarding/pending');
    }

    public function test_xss_protection_in_forms()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test'])
            ->post('/seller/onboarding', [
                '_token' => 'test',
                'company_name' => '<script>alert("xss")</script>',
                'cnpj' => '12345678000195',
                'phone' => '11999999999',
                'address' => 'Test Address',
                'city' => 'Test City',
                'state' => 'SP',
                'postal_code' => '12345-678',
                'bank_account' => '123456',
                'bank_agency' => '1234',
                'pix_key' => 'test@example.com',
            ]);

        $this->assertDatabaseMissing('seller_profiles', [
            'company_name' => '<script>alert("xss")</script>'
        ]);
    }

    public function test_sql_injection_protection()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/api/search?q=' . urlencode("'; DROP TABLE users; --"));

        $response->assertStatus(404);
        
        $this->assertTrue(User::count() > 0, 'Users table should still exist');
    }

    public function test_session_fixation_protection()
    {
        $sessionId = Session::getId();
        
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);

        $response = $this->withSession(['_token' => 'test'])
            ->post('/login', [
                '_token' => 'test',
                'email' => $user->email,
                'password' => 'password',
            ]);

        $newSessionId = Session::getId();
        
        $this->assertNotEquals($sessionId, $newSessionId);
    }

    public function test_secure_headers_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_file_upload_security()
    {
        $user = User::factory()->create();

        $maliciousFile = \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 1024);

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test'])
            ->post('/admin/media/upload', [
                '_token' => 'test',
                'file' => $maliciousFile,
            ]);

        $response->assertSessionHasErrors(['file']);
    }

    public function test_environment_variables_not_exposed()
    {
        $response = $this->get('/.env');
        $response->assertStatus(404);

        $response = $this->get('/config');
        $response->assertStatus(404);
    }

    public function test_debug_mode_disabled_in_production()
    {
        config(['app.debug' => false]);
        
        $response = $this->get('/non-existent-route');
        
        $content = $response->getContent();
        $this->assertStringNotContainsString('Laravel\\Foundation\\', $content);
        $this->assertStringNotContainsString('Stack trace:', $content);
    }

    public function test_mass_assignment_protection()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test'])
            ->post('/seller/onboarding', [
                '_token' => 'test',
                'company_name' => 'Test Company',
                'cnpj' => '12345678000195',
                'status' => 'approved',
                'user_id' => 999,
            ]);

        $this->assertDatabaseMissing('seller_profiles', [
            'status' => 'approved',
            'user_id' => 999
        ]);
    }

    public function test_api_rate_limiting()
    {
        for ($i = 0; $i < 61; $i++) {
            $response = $this->get('/api/categories');
            if ($i === 60) {
                $response->assertStatus(429);
            }
        }
    }
}