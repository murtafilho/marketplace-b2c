<?php
/**
 * Arquivo: tests/Feature/SellerOnboardingTest.php
 * Descrição: Teste do processo de onboarding de vendedores
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_seller_can_access_onboarding_page(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($seller)->get('/seller/onboarding');

        $response->assertStatus(200);
        $response->assertSee('Completar Cadastro de Vendedor');
        $response->assertSee('Test Business');
    }

    public function test_customer_cannot_access_seller_onboarding(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/seller/onboarding');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_seller_can_complete_onboarding(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'phone' => '(11) 88888-8888'
        ]);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        $addressProof = UploadedFile::fake()->create('address_proof.pdf', 1000, 'application/pdf');
        $identityProof = UploadedFile::fake()->create('identity_proof.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($seller)->post('/seller/onboarding', [
            'company_name' => 'Empresa Teste',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-01',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'bank_name' => 'Banco do Brasil',
            'bank_account' => '1234-123456-7',
            'address_proof' => $addressProof,
            'identity_proof' => $identityProof,
        ]);

        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('pending_approval', $profile->status);
        $this->assertEquals('Empresa Teste', $profile->company_name);
        $this->assertEquals('cpf', $profile->document_type);
        $this->assertEquals('123.456.789-01', $profile->document_number);
        $this->assertNotNull($profile->submitted_at);
        
        Storage::disk('public')->assertExists($profile->address_proof_path);
        Storage::disk('public')->assertExists($profile->identity_proof_path);
    }

    public function test_onboarding_validates_required_fields(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($seller)->post('/seller/onboarding', [
            'company_name' => '',
            'document_type' => 'invalid',
            'document_number' => '',
            'phone' => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors([
            'company_name',
            'document_type',
            'document_number',
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'bank_name',
            'bank_account',
            'address_proof',
            'identity_proof',
        ]);
    }

    public function test_seller_can_view_pending_status(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $profile = $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'pending_approval',
            'commission_rate' => 10.0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($seller)->get('/seller/pending');

        $response->assertStatus(200);
        $response->assertSee('Conta Pendente de Aprovação');
        $response->assertSee('Aguardando Aprovação');
        $response->assertSee('Test Business');
    }

    public function test_approved_seller_is_redirected_from_onboarding(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'approved',
            'commission_rate' => 10.0,
        ]);

        $response = $this->actingAs($seller)->get('/seller/onboarding');

        $response->assertRedirect(route('seller.dashboard'));
    }

    public function test_rejected_seller_can_see_rejection_reason(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $seller->sellerProfile()->create([
            'company_name' => 'Test Business',
            'status' => 'rejected',
            'commission_rate' => 10.0,
            'rejection_reason' => 'Documentos inválidos',
            'rejected_at' => now(),
        ]);

        $response = $this->actingAs($seller)->get('/seller/pending');

        $response->assertStatus(200);
        $response->assertSee('Documentos inválidos');
        $response->assertSee('Atualizar Cadastro');
    }
}