<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class MediaManagerEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->adminUser = User::factory()->create([
            'role_id' => 2, // Admin role
            'email' => 'admin@test.com'
        ]);

        // Set up fake storage
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media_s3');
    }

    /** @test */
    public function test_media_manager_index_page_loads()
    {
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->get('/admin/media-manager');

        $response->assertStatus(200);
        $response->assertViewIs('admin.admin-center.media.index');
    }

    /** @test */
    public function test_list_files_endpoint()
    {
        // Create test directory structure
        Storage::disk('public')->makeDirectory('media');
        Storage::disk('public')->makeDirectory('media/images');
        Storage::disk('public')->put('media/images/test.jpg', 'fake content');

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->get('/admin/media-manager/files?disk=public&path=/');

        if ($response->status() !== 200) {
            echo "Response status: " . $response->status() . "\n";
            echo "Response content first 1000 chars:\n";
            echo substr($response->getContent(), 0, 1000) . "\n";
        }

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'files',
            'directories',
            'current_path',
            'disk',
            'disk_status'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('public', $responseData['disk']);
    }

    /** @test */
    public function test_upload_endpoint_media_manager_format()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'disk' => 'public',
                             'folder' => 'images',
                             'files' => [$file]
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'files'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertNotEmpty($responseData['files']);

        // Verify file path format
        $uploadedFile = $responseData['files'][0];
        $this->assertStringStartsWith('media/images/', $uploadedFile['path']);
    }

    /** @test */
    public function test_upload_validation_rules()
    {
        // Test invalid file type for images folder
        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'disk' => 'public',
                             'folder' => 'images',
                             'files' => [$file]
                         ]);

        $response->assertStatus(422);
        $responseData = $response->json();
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('validation', strtolower($responseData['message']));
    }

    /** @test */
    public function test_upload_file_size_validation()
    {
        // Create file larger than images folder limit (10MB)
        $file = UploadedFile::fake()->image('large.jpg')->size(15000); // 15MB

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'disk' => 'public',
                             'folder' => 'images',
                             'files' => [$file]
                         ]);

        $response->assertStatus(422);
        $responseData = $response->json();
        $this->assertFalse($responseData['success']);
    }

    /** @test */
    public function test_create_folder_endpoint()
    {
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/create-folder', [
                             'disk' => 'public',
                             'path' => '/media',
                             'name' => 'test-folder'
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        // Verify folder was created
        $this->assertTrue(Storage::disk('public')->exists('media/test-folder'));
    }

    /** @test */
    public function test_delete_file_endpoint()
    {
        // Create a test file first
        Storage::disk('public')->put('test-file.txt', 'test content');
        $this->assertTrue(Storage::disk('public')->exists('test-file.txt'));

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->delete('/admin/media-manager/delete/test-file.txt', [
                             'disk' => 'public'
                         ]);

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);

        // Verify file was deleted
        $this->assertFalse(Storage::disk('public')->exists('test-file.txt'));
    }

    /** @test */
    public function test_disk_statuses_endpoint()
    {
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->get('/admin/media-manager/disk-statuses');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'disk_statuses' => [
                'public',
                'local',
                's3'
            ]
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertTrue($responseData['disk_statuses']['public']['connected']);
        $this->assertTrue($responseData['disk_statuses']['local']['connected']);
    }

    /** @test */
    public function test_unauthorized_access_denied()
    {
        // Test without authentication
        $response = $this->get('/admin/media-manager');
        $response->assertRedirect(); // Should redirect to login

        // Test API endpoints without auth
        $response = $this->get('/admin/media-manager/files');
        $response->assertStatus(302); // Redirect to login

        $response = $this->post('/admin/media-manager/upload');
        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function test_different_folder_upload_paths()
    {
        $testCases = [
            'images' => UploadedFile::fake()->image('test.jpg'),
            'documents' => UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf'),
            'assets' => UploadedFile::fake()->create('test.css', 100, 'text/css'),
            'validations' => UploadedFile::fake()->image('headshot.jpg')
        ];

        foreach ($testCases as $folder => $file) {
            $response = $this->actingAs($this->adminUser, 'admin')
                             ->post('/admin/media-manager/upload', [
                                 'disk' => 'public',
                                 'folder' => $folder,
                                 'files' => [$file]
                             ]);

            $response->assertStatus(200);
            $responseData = $response->json();
            $this->assertTrue($responseData['success'], "Upload failed for folder: {$folder}");

            // Verify correct path format
            $uploadedFile = $responseData['files'][0];
            $this->assertStringStartsWith("media/{$folder}/", $uploadedFile['path']);
            $this->assertEquals($folder, $uploadedFile['folder']);
        }
    }

    /** @test */
    public function test_invalid_disk_access()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->withHeaders(['Accept' => 'application/json'])
                         ->post('/admin/media-manager/upload', [
                             'disk' => 'invalid_disk',
                             'folder' => 'images',
                             'files' => [$file]
                         ]);

        $response->assertStatus(422);
        $responseData = $response->json();
        $this->assertFalse($responseData['success']);
    }    /** @test */
    public function test_placeholder_endpoints_return_not_implemented()
    {
        // Test download endpoint (placeholder)
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->get('/admin/media-manager/download/test-file.jpg');
        $response->assertStatus(501);

        // Test archive endpoint (placeholder)
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/archive/test-file.jpg');
        $response->assertStatus(501);

        // Test file details endpoint (placeholder)
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->get('/admin/media-manager/file/test-file.jpg');
        $response->assertStatus(501);
    }

    protected function tearDown(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media_s3');

        parent::tearDown();
    }
}
