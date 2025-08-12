<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Admin;

class MediaManagerUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $instructorUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different roles
        $this->adminUser = Admin::factory()->create([
            'role_id' => 1, // System Admin
            'email' => 'admin@test.com'
        ]);

        $this->instructorUser = Admin::factory()->create([
            'role_id' => 2, // Instructor
            'email' => 'instructor@test.com'
        ]);

        $this->regularUser = User::factory()->create([
            'role_id' => 3, // Regular user
            'email' => 'user@test.com'
        ]);

        // Set up fake storage disks
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media_s3');

        // Create required directory structure
        Storage::disk('public')->makeDirectory('media');
        Storage::disk('local')->makeDirectory('media');
    }

    /** @test */
    public function admin_can_upload_file_to_public_disk()
    {
        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify file was uploaded to correct location
        $this->assertTrue(Storage::disk('public')->exists('media/' . $file->hashName()));
    }

    /** @test */
    public function admin_can_upload_file_to_private_disk()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 'local',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify file was uploaded to private storage
        $this->assertTrue(Storage::disk('local')->exists('media/' . $file->hashName()));
    }

    /** @test */
    public function admin_can_upload_multiple_files()
    {
        $files = [
            UploadedFile::fake()->image('image1.jpg', 800, 600),
            UploadedFile::fake()->image('image2.png', 1024, 768),
            UploadedFile::fake()->create('document.pdf', 1024)
        ];

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => $files,
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify all files were uploaded
        foreach ($files as $file) {
            $this->assertTrue(Storage::disk('public')->exists('media/' . $file->hashName()));
        }
    }

    /** @test */
    public function instructor_can_upload_to_public_and_private_disks()
    {
        $publicFile = UploadedFile::fake()->image('public-image.jpg', 800, 600);
        $privateFile = UploadedFile::fake()->create('private-doc.pdf', 1024);

        // Test public disk upload
        $response = $this->actingAs($this->instructorUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$publicFile],
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $this->assertTrue(Storage::disk('public')->exists('media/' . $publicFile->hashName()));

        // Test private disk upload
        $response = $this->actingAs($this->instructorUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$privateFile],
                             'disk' => 'local',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $this->assertTrue(Storage::disk('local')->exists('media/' . $privateFile->hashName()));
    }

    /** @test */
    public function admin_can_upload_to_s3_archive()
    {
        $file = UploadedFile::fake()->create('archive-file.zip', 2048);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 's3',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify file was uploaded to S3 storage
        $this->assertTrue(Storage::disk('media_s3')->exists('media/' . $file->hashName()));
    }

    /** @test */
    public function upload_validates_file_size_limits()
    {
        // Create a file that exceeds the limit (assuming 25MB max)
        $largeFile = UploadedFile::fake()->create('large-file.zip', 26 * 1024); // 26MB

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$largeFile],
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('files.0');
    }

    /** @test */
    public function upload_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('files');
    }

    /** @test */
    public function upload_to_subdirectory_creates_path()
    {
        $file = UploadedFile::fake()->image('subfolder-image.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 'public',
                             'path' => '/images/gallery',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify file was uploaded to subdirectory
        $this->assertTrue(Storage::disk('public')->exists('media/images/gallery/' . $file->hashName()));
    }

    /** @test */
    public function upload_handles_duplicate_filenames()
    {
        $file1 = UploadedFile::fake()->image('test.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('test.jpg', 1024, 768);

        // Upload first file
        $response1 = $this->actingAs($this->adminUser, 'admin')
                          ->post('/admin/media-manager/upload', [
                              'files' => [$file1],
                              'disk' => 'public',
                              'path' => '/',
                              '_token' => csrf_token()
                          ]);

        $response1->assertStatus(200);

        // Upload second file with same name
        $response2 = $this->actingAs($this->adminUser, 'admin')
                          ->post('/admin/media-manager/upload', [
                              'files' => [$file2],
                              'disk' => 'public',
                              'path' => '/',
                              '_token' => csrf_token()
                          ]);

        $response2->assertStatus(200);

        // Both files should exist (with different hashed names)
        $this->assertTrue(Storage::disk('public')->exists('media/' . $file1->hashName()));
        $this->assertTrue(Storage::disk('public')->exists('media/' . $file2->hashName()));
    }

    /** @test */
    public function upload_returns_proper_success_response()
    {
        $file = UploadedFile::fake()->image('response-test.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 'public',
                             'path' => '/',
                             '_token' => csrf_token()
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'files' => [
                '*' => [
                    'name',
                    'path',
                    'size',
                    'type'
                ]
            ]
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_upload()
    {
        $file = UploadedFile::fake()->image('unauthorized.jpg', 800, 600);

        $response = $this->post('/admin/media-manager/upload', [
            'files' => [$file],
            'disk' => 'public',
            'path' => '/',
            '_token' => csrf_token()
        ]);

        $response->assertStatus(302); // Redirect to login
    }

    /** @test */
    public function csrf_protection_prevents_upload_without_token()
    {
        $file = UploadedFile::fake()->image('csrf-test.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->post('/admin/media-manager/upload', [
                             'files' => [$file],
                             'disk' => 'public',
                             'path' => '/'
                             // No CSRF token
                         ]);

        $response->assertStatus(419); // CSRF token mismatch
    }
}
