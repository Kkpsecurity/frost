<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function media_manager_loads_without_errors()
    {
        // Create a test admin user
        $admin = Admin::factory()->create([
            'role_id' => 2, // Admin role
            'email' => 'test@example.com'
        ]);

        // Authenticate as admin
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/admin-center/media');

        $response->assertStatus(200);
        $response->assertViewIs('admin.admin-center.media.index');
    }

    /** @test */
    public function disk_status_endpoint_works()
    {
        // Create a test admin user
        $admin = Admin::factory()->create([
            'role_id' => 1, // System Admin role
            'email' => 'sysadmin@example.com'
        ]);

        // Test disk status endpoint
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.media-manager.disk-statuses'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'disk_statuses' => [
                'public' => [
                    'connected',
                    'message',
                    'disk',
                    'type'
                ]
            ]
        ]);
    }

    /** @test */
    public function storage_settings_page_loads()
    {
        // Create a test admin user
        $admin = Admin::factory()->create([
            'role_id' => 1, // System Admin role
            'email' => 'sysadmin@example.com'
        ]);

        // Test storage settings page
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/admin-center/settings/storage');

        $response->assertStatus(200);
        $response->assertViewIs('admin.admin-center.settings.show');
    }

    /** @test */
    public function files_listing_respects_role_permissions()
    {
        // Test Instructor role (should access public and local)
        $instructor = Admin::factory()->create([
            'role_id' => 4, // Instructor role
            'email' => 'instructor@example.com'
        ]);

        // Test public disk access
        $response = $this->actingAs($instructor, 'admin')
            ->get(route('admin.media-manager.files', ['disk' => 'public']));

        $response->assertStatus(200);

        // Test local disk access
        $response = $this->actingAs($instructor, 'admin')
            ->get(route('admin.media-manager.files', ['disk' => 'local']));

        $response->assertStatus(200);

        // Test S3 disk access (should be denied)
        $response = $this->actingAs($instructor, 'admin')
            ->get(route('admin.media-manager.files', ['disk' => 's3']));

        $response->assertStatus(403);
    }
}
