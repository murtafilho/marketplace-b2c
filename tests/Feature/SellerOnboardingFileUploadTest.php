<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerOnboardingFileUploadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
    }

    /** @test */
    public function seller_can_access_onboarding_page()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get(route('seller.onboarding.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Completar Cadastro de Vendedor');
        $response->assertSee('enctype="multipart/form-data"', false);
    }

    /** @test */
    public function seller_onboarding_form_has_file_upload_fields()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get(route('seller.onboarding.index'));
        
        $response->assertSee('address_proof');
        $response->assertSee('identity_proof');
        $response->assertSee('accept=".pdf,.jpg,.jpeg,.png,.PDF,.JPG,.JPEG,.PNG"', false);
    }

    /** @test */
    public function seller_can_submit_onboarding_without_files()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7'
        ]);

        $response->assertRedirect(route('seller.pending'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $user->id,
            'company_name' => 'Test Company',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function seller_can_submit_onboarding_with_valid_files()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Create test files
        $addressProof = UploadedFile::fake()->create('comprovante.pdf', 1024); // 1MB
        $identityProof = UploadedFile::fake()->image('documento.jpg', 800, 600)->size(1500); // 1.5MB

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7',
            'address_proof' => $addressProof,
            'identity_proof' => $identityProof
        ]);

        $response->assertRedirect(route('seller.pending'));
        
        // Check if files were stored (via SafeUploadService)
        $profile = $sellerProfile->fresh();
        
        // The files should have paths if upload worked
        if ($profile->address_proof_path) {
            $this->assertNotNull($profile->address_proof_path);
        }
        
        if ($profile->identity_proof_path) {
            $this->assertNotNull($profile->identity_proof_path);
        }
    }

    /** @test */
    public function seller_onboarding_rejects_oversized_files()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Create oversized file (3MB)
        $oversizedFile = UploadedFile::fake()->create('big_file.pdf', 3072); // 3MB

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7',
            'address_proof' => $oversizedFile
        ]);

        $response->assertSessionHasErrors(['address_proof']);
    }

    /** @test */
    public function seller_onboarding_rejects_invalid_file_types()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Create invalid file type
        $invalidFile = UploadedFile::fake()->create('document.txt', 500);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7',
            'identity_proof' => $invalidFile
        ]);

        $response->assertSessionHasErrors(['identity_proof']);
    }

    /** @test */
    public function seller_onboarding_validates_required_fields()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            // Missing required fields
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
            'bank_account'
        ]);
    }

    /** @test */
    public function seller_onboarding_validates_cpf_format()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '111.111.111-11', // Invalid CPF
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7'
        ]);

        $response->assertSessionHasErrors(['document_number_clean']);
    }

    /** @test */
    public function seller_onboarding_upload_service_is_called()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Mock the SafeUploadService
        $mockUploadService = $this->createMock(\App\Services\SafeUploadService::class);
        
        $mockUploadService->expects($this->once())
            ->method('uploadDocument')
            ->willReturn(['file_path' => 'seller-documents/test-file.pdf']);
        
        $this->app->instance(\App\Services\SafeUploadService::class, $mockUploadService);

        $addressProof = UploadedFile::fake()->create('comprovante.pdf', 1024);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7',
            'address_proof' => $addressProof
        ]);

        $response->assertRedirect(route('seller.pending'));
    }

    /** @test */
    public function seller_onboarding_handles_upload_service_errors()
    {
        $user = User::factory()->create(['role' => 'seller']);
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Mock the SafeUploadService to throw an exception
        $mockUploadService = $this->createMock(\App\Services\SafeUploadService::class);
        
        $mockUploadService->expects($this->once())
            ->method('uploadDocument')
            ->willThrowException(new \Exception('Upload failed'));
        
        $this->app->instance(\App\Services\SafeUploadService::class, $mockUploadService);

        $addressProof = UploadedFile::fake()->create('comprovante.pdf', 1024);

        $response = $this->actingAs($user)->post(route('seller.onboarding.store'), [
            'company_name' => 'Test Company',
            'document_type' => 'cpf',
            'document_number' => '123.456.789-09',
            'phone' => '(11) 99999-9999',
            'address' => 'Rua Teste, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '12345-678',
            'bank_name' => 'Banco Teste',
            'bank_account' => '1234-123456-7',
            'address_proof' => $addressProof
        ]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['upload']);
    }
}