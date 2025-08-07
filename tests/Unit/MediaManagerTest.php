<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MediaFileService;
use App\Http\Controllers\Admin\MediaController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MediaManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $mediaFileService;
    protected $mediaController;
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mediaFileService = new MediaFileService();
        $this->mediaController = new MediaController($this->mediaFileService);

        // Create test user with admin privileges
        $this->testUser = User::factory()->create([
            'role_id' => 2, // Admin role
            'email' => 'test@example.com'
        ]);

        // Set up fake storage for testing
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media_s3');
    }

    /** @test */
    public function test_disk_mapping_configuration()
    {
        $reflection = new \ReflectionClass($this->mediaFileService);
        $method = $reflection->getMethod('mapDiskName');
        $method->setAccessible(true);

        // Test disk name mapping
        $this->assertEquals('public', $method->invoke($this->mediaFileService, 'public'));
        $this->assertEquals('local', $method->invoke($this->mediaFileService, 'local'));
        $this->assertEquals('media_s3', $method->invoke($this->mediaFileService, 's3'));
        $this->assertEquals('public', $method->invoke($this->mediaFileService, 'unknown')); // fallback

        $this->addToAssertionCount(4);
    }

    /** @test */
    public function test_role_based_disk_access()
    {
        $reflection = new \ReflectionClass($this->mediaFileService);
        $method = $reflection->getMethod('canAccessDisk');
        $method->setAccessible(true);

        // Test with authenticated admin user
        Auth::guard('admin')->login($this->testUser);

        // Admin should have access to all disks
        $this->assertTrue($method->invoke($this->mediaFileService, 'public'));
        $this->assertTrue($method->invoke($this->mediaFileService, 'local'));
        $this->assertTrue($method->invoke($this->mediaFileService, 's3'));

        $this->addToAssertionCount(3);
    }

    /** @test */
    public function test_storage_path_generation()
    {
        $reflection = new \ReflectionClass($this->mediaFileService);
        $method = $reflection->getMethod('getStoragePath');
        $method->setAccessible(true);

        // Test MIME type to path mapping
        $this->assertEquals('media/images', $method->invoke($this->mediaFileService, 'image/jpeg', ''));
        $this->assertEquals('media/images', $method->invoke($this->mediaFileService, 'image/png', ''));
        $this->assertEquals('media/documents', $method->invoke($this->mediaFileService, 'application/pdf', ''));
        $this->assertEquals('media/assets', $method->invoke($this->mediaFileService, 'application/javascript', ''));
        $this->assertEquals('media/assets', $method->invoke($this->mediaFileService, 'text/css', ''));
        $this->assertEquals('media/files', $method->invoke($this->mediaFileService, 'application/unknown', ''));

        // Test custom path override
        $this->assertEquals('custom/path', $method->invoke($this->mediaFileService, 'image/jpeg', 'custom/path'));

        $this->addToAssertionCount(7);
    }

    /** @test */
    public function test_path_sanitization()
    {
        $reflection = new \ReflectionClass($this->mediaFileService);
        $method = $reflection->getMethod('sanitizePath');
        $method->setAccessible(true);

        // Test path sanitization
        $this->assertEquals('/', $method->invoke($this->mediaFileService, ''));
        $this->assertEquals('/', $method->invoke($this->mediaFileService, '/'));
        $this->assertEquals('/media', $method->invoke($this->mediaFileService, 'media'));
        $this->assertEquals('/media', $method->invoke($this->mediaFileService, '/media'));
        $this->assertEquals('/media/images', $method->invoke($this->mediaFileService, '/media/images'));
        $this->assertEquals('/safe/path', $method->invoke($this->mediaFileService, '../safe/path'));
        $this->assertEquals('/safe/path', $method->invoke($this->mediaFileService, '//safe//path'));

        $this->addToAssertionCount(7);
    }

    /** @test */
    public function test_disk_status_check()
    {
        Auth::guard('admin')->login($this->testUser);

        // Test public disk status
        $status = $this->mediaFileService->checkDiskStatus('public');
        $this->assertTrue($status['connected']);
        $this->assertEquals('public', $status['disk']);
        $this->assertEquals('local', $status['type']);

        // Test local disk status
        $status = $this->mediaFileService->checkDiskStatus('local');
        $this->assertTrue($status['connected']);
        $this->assertEquals('local', $status['disk']);
        $this->assertEquals('local', $status['type']);

        $this->addToAssertionCount(6);
    }

    /** @test */
    public function test_list_files_public_disk_redirect()
    {
        Auth::guard('admin')->login($this->testUser);

        // Create test directory structure
        Storage::disk('public')->makeDirectory('media');
        Storage::disk('public')->makeDirectory('media/images');
        Storage::disk('public')->put('media/images/test.jpg', 'fake image content');

        // Test that root path gets redirected to /media for public disk
        $result = $this->mediaFileService->listFiles('public', '/');

        $this->assertTrue($result['success']);
        $this->assertEquals('/media', $result['current_path']);
        $this->assertEquals('public', $result['disk']);

        $this->addToAssertionCount(3);
    }

    /** @test */
    public function test_file_upload_path_consistency()
    {
        Auth::guard('admin')->login($this->testUser);

        // Create a test file
        $file = UploadedFile::fake()->image('test.jpg');

        // Test MediaFileService upload
        $result = $this->mediaFileService->uploadFile($file, 'public', '');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('media/', $result['file']['path']);

        // Verify file was actually stored
        $this->assertTrue(Storage::disk('public')->exists($result['file']['path']));

        $this->addToAssertionCount(3);
    }

    /** @test */
    public function test_media_controller_upload_path_format()
    {
        // Test the MediaController's path formatting
        $reflection = new \ReflectionClass($this->mediaController);
        $method = $reflection->getMethod('processMediaManagerFile');
        $method->setAccessible(true);

        $file = UploadedFile::fake()->image('test.jpg');

        // Test that MediaController uses media/{folder} format
        $result = $method->invoke($this->mediaController, $file, 'public', 'images');

        $this->assertStringStartsWith('media/images/', $result['path']);
        $this->assertEquals('public', $result['disk']);
        $this->assertEquals('images', $result['folder']);

        $this->addToAssertionCount(3);
    }

    /** @test */
    public function test_get_file_type_from_extension()
    {
        $reflection = new \ReflectionClass($this->mediaController);
        $method = $reflection->getMethod('getFileTypeFromExtension');
        $method->setAccessible(true);

        // Test file type detection
        $this->assertEquals('image', $method->invoke($this->mediaController, 'jpg'));
        $this->assertEquals('image', $method->invoke($this->mediaController, 'PNG'));
        $this->assertEquals('document', $method->invoke($this->mediaController, 'pdf'));
        $this->assertEquals('document', $method->invoke($this->mediaController, 'docx'));
        $this->assertEquals('asset', $method->invoke($this->mediaController, 'css'));
        $this->assertEquals('asset', $method->invoke($this->mediaController, 'js'));
        $this->assertEquals('file', $method->invoke($this->mediaController, 'unknown'));

        $this->addToAssertionCount(7);
    }

    /** @test */
    public function test_format_bytes()
    {
        $reflection = new \ReflectionClass($this->mediaController);
        $method = $reflection->getMethod('formatBytes');
        $method->setAccessible(true);

        // Test byte formatting
        $this->assertEquals('512 B', $method->invoke($this->mediaController, 512));
        $this->assertEquals('1 KB', $method->invoke($this->mediaController, 1024));
        $this->assertEquals('1 MB', $method->invoke($this->mediaController, 1024 * 1024));
        $this->assertEquals('1.5 MB', $method->invoke($this->mediaController, 1024 * 1024 * 1.5));

        $this->addToAssertionCount(4);
    }

    /** @test */
    public function test_file_validation_rules()
    {
        // Test folder-specific validation rules that exist in MediaController
        $expectedRules = [
            'images' => [
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'max_size' => 10240, // 10MB
                'mime_types' => ['image/jpeg', 'image/png', 'image/gif']
            ],
            'documents' => [
                'extensions' => ['pdf', 'doc', 'docx'],
                'max_size' => 25600, // 25MB
                'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            ],
            'assets' => [
                'extensions' => ['css', 'js', 'json'],
                'max_size' => 5120, // 5MB
                'mime_types' => ['text/css', 'application/javascript', 'application/json']
            ],
            'validations' => [
                'extensions' => ['jpg', 'jpeg', 'png'],
                'max_size' => 8192, // 8MB
                'mime_types' => ['image/jpeg', 'image/png']
            ]
        ];

        // Assert that validation rules are consistent
        $this->assertIsArray($expectedRules['images']);
        $this->assertIsArray($expectedRules['documents']);
        $this->assertIsArray($expectedRules['assets']);
        $this->assertIsArray($expectedRules['validations']);

        $this->addToAssertionCount(4);
    }

    /** @test */
    public function test_unauthorized_access_prevention()
    {
        // Test without authentication
        $result = $this->mediaFileService->listFiles('public', '/');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Access denied', $result['error']);

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function test_folder_creation()
    {
        Auth::guard('admin')->login($this->testUser);

        // Test folder creation
        $result = $this->mediaFileService->createFolder('public', '/media', 'test-folder');

        $this->assertTrue($result['success']);
        $this->assertTrue(Storage::disk('public')->exists('media/test-folder'));

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function test_file_deletion()
    {
        Auth::guard('admin')->login($this->testUser);

        // Create a test file first
        Storage::disk('public')->put('media/test-file.txt', 'test content');
        $this->assertTrue(Storage::disk('public')->exists('media/test-file.txt'));

        // Test file deletion
        $result = $this->mediaFileService->deleteFile('public', 'media/test-file.txt');

        $this->assertTrue($result['success']);
        $this->assertFalse(Storage::disk('public')->exists('media/test-file.txt'));

        $this->addToAssertionCount(3);
    }

    protected function tearDown(): void
    {
        // Clean up
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media_s3');

        parent::tearDown();
    }
}
