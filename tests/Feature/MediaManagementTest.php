<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_access_media_gallery()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/media');

        $response->assertStatus(200);
        $response->assertViewIs('admin.media.index');
    }

    public function test_non_admin_cannot_access_media_gallery()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get('/admin/media');

        $response->assertStatus(403);
    }

    public function test_can_upload_valid_image()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $response = $this->actingAs($admin)
            ->post('/admin/media/upload', [
                'files' => [$file],
                'directory' => 'uploads',
                'quality' => 85,
                'optimize' => true
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'results'
        ]);

        // Verificar se o arquivo foi armazenado
        Storage::disk('public')->assertExists('uploads/' . $file->hashName());
    }

    public function test_rejects_invalid_file_types()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->create('malicious.php', 100, 'text/php');

        $response = $this->actingAs($admin)
            ->post('/admin/media/validate', [
                'files' => [$file]
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertFalse($data['validation_results'][0]['valid']);
        $this->assertStringContainsString('Tipo de arquivo não permitido', 
            implode(' ', $data['validation_results'][0]['errors']));
    }

    public function test_can_validate_files_before_upload()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $validFile = UploadedFile::fake()->image('valid.jpg', 800, 600);
        $invalidFile = UploadedFile::fake()->create('invalid.txt', 100);

        $response = $this->actingAs($admin)
            ->post('/admin/media/validate', [
                'files' => [$validFile, $invalidFile]
            ]);

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertTrue($data['success']);
        $this->assertEquals(2, $data['validation_summary']['total_files']);
        $this->assertEquals(1, $data['validation_summary']['valid_files']);
        $this->assertEquals(1, $data['validation_summary']['invalid_files']);
    }

    public function test_can_create_directory()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/admin/media/create-directory', [
                'name' => 'test-folder',
                'parent' => ''
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Diretório criado com sucesso',
            'path' => 'test-folder'
        ]);

        Storage::disk('public')->assertDirectoryExists('test-folder');
    }

    public function test_rejects_invalid_directory_names()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/admin/media/create-directory', [
                'name' => 'invalid@name#',
                'parent' => ''
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_can_search_files()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar alguns arquivos de teste
        $file1 = UploadedFile::fake()->image('product-image.jpg');
        $file2 = UploadedFile::fake()->image('banner-home.jpg');
        
        Storage::disk('public')->put('uploads/product-image.jpg', $file1->getContent());
        Storage::disk('public')->put('uploads/banner-home.jpg', $file2->getContent());

        $response = $this->actingAs($admin)
            ->get('/admin/media/search?query=product&type=images');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'files',
            'total'
        ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals(1, $data['total']);
        $this->assertStringContainsString('product', $data['files'][0]['name']);
    }

    public function test_can_optimize_image()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->image('test.jpg', 1200, 800);
        
        // Primeiro fazer upload
        Storage::disk('public')->put('uploads/test.jpg', $file->getContent());
        $encodedPath = base64_encode('uploads/test.jpg');

        $response = $this->actingAs($admin)
            ->post('/admin/media/optimize', [
                'path' => $encodedPath,
                'preset' => 'web',
                'quality' => 80
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('otimizada com sucesso', $data['message']);
    }

    public function test_can_delete_file()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->image('to-delete.jpg');
        
        Storage::disk('public')->put('uploads/to-delete.jpg', $file->getContent());
        $encodedPath = base64_encode('uploads/to-delete.jpg');

        $response = $this->actingAs($admin)
            ->delete('/admin/media/delete', [
                'paths' => [$encodedPath]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        Storage::disk('public')->assertMissing('uploads/to-delete.jpg');
    }

    public function test_file_size_limits_are_enforced()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $largeFile = UploadedFile::fake()->create('large.jpg', 25 * 1024); // 25MB

        $response = $this->actingAs($admin)
            ->post('/admin/media/validate', [
                'files' => [$largeFile]
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertFalse($data['validation_results'][0]['valid']);
        $this->assertStringContainsString('muito grande', 
            implode(' ', $data['validation_results'][0]['errors']));
    }

    public function test_can_get_optimization_stats()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar alguns arquivos
        $file1 = UploadedFile::fake()->image('img1.jpg');
        $file2 = UploadedFile::fake()->image('img2.png');
        
        Storage::disk('public')->put('uploads/img1.jpg', $file1->getContent());
        Storage::disk('public')->put('uploads/img2.png', $file2->getContent());

        $response = $this->actingAs($admin)
            ->get('/admin/media/stats?directory=uploads');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'stats' => [
                'total_files',
                'total_size',
                'human_size',
                'formats',
                'average_size'
            ]
        ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals(2, $data['stats']['total_files']);
        $this->assertArrayHasKey('jpg', $data['stats']['formats']);
        $this->assertArrayHasKey('png', $data['stats']['formats']);
    }

    public function test_batch_operations_work_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar múltiplos arquivos
        $files = [
            UploadedFile::fake()->image('img1.jpg', 800, 600),
            UploadedFile::fake()->image('img2.jpg', 1000, 700),
        ];

        foreach ($files as $index => $file) {
            Storage::disk('public')->put("uploads/img{$index}.jpg", $file->getContent());
        }

        $encodedPaths = [
            base64_encode('uploads/img0.jpg'),
            base64_encode('uploads/img1.jpg'),
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/media/batch-optimize', [
                'paths' => $encodedPaths,
                'preset' => 'web'
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertTrue($data['success']);
        $this->assertEquals(2, $data['results']['total_files']);
        $this->assertEquals(2, $data['results']['successful']);
        $this->assertEquals(0, $data['results']['failed']);
    }
}