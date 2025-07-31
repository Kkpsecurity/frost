<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MediaPermission;

class MediaManagerPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions
        MediaPermission::truncate();

        // Define permission sets
        $permissions = [
            // Admin permissions
            [
                'role' => 'admin',
                'disk' => 'public',
                'permissions' => ['view', 'upload', 'delete', 'move', 'archive']
            ],
            [
                'role' => 'admin',
                'disk' => 'local',
                'permissions' => ['view', 'upload', 'delete', 'move', 'archive']
            ],
            [
                'role' => 'admin',
                'disk' => 'media_s3',
                'permissions' => ['view', 'upload', 'delete', 'move', 'archive']
            ],

            // Staff permissions
            [
                'role' => 'staff',
                'disk' => 'public',
                'permissions' => ['view', 'upload']
            ],
            [
                'role' => 'staff',
                'disk' => 'local',
                'permissions' => ['view', 'upload', 'delete']
            ],
            [
                'role' => 'staff',
                'disk' => 'media_s3',
                'permissions' => ['view']
            ],

            // Student permissions
            [
                'role' => 'student',
                'disk' => 'public',
                'permissions' => ['view', 'upload']
            ],
            [
                'role' => 'student',
                'disk' => 'local',
                'permissions' => []
            ],
            [
                'role' => 'student',
                'disk' => 'media_s3',
                'permissions' => []
            ],

            // Instructor permissions (if you have instructors)
            [
                'role' => 'instructor',
                'disk' => 'public',
                'permissions' => ['view', 'upload', 'delete']
            ],
            [
                'role' => 'instructor',
                'disk' => 'local',
                'permissions' => ['view', 'upload']
            ],
            [
                'role' => 'instructor',
                'disk' => 'media_s3',
                'permissions' => ['view']
            ],
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            MediaPermission::create($permission);
        }

        $this->command->info('Media Manager permissions seeded successfully.');
    }
}
