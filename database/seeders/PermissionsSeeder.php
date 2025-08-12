<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create course-related permissions
        $permissions = [
            // Course permissions
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'courses.archive',
            'courses.restore',

            // Course management permissions
            'course-management.access',
            'course-management.full-access',

            // Student-specific permissions
            'courses.view-own',
            'courses.enroll',

            // Instructor-specific permissions
            'courses.view-assigned',
            'courses.manage-assigned',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get existing roles from the roles table and sync with Spatie
        $existingRoles = Role::all();

        foreach ($existingRoles as $role) {
            // Create corresponding Spatie role with guard_name
            $spatieRole = SpatieRole::firstOrCreate([
                'name' => $role->name,
                'guard_name' => 'web'
            ]);

            // Assign permissions based on role type
            $this->assignPermissionsByRole($spatieRole, $role);
        }
    }

    /**
     * Assign permissions based on role type
     */
    private function assignPermissionsByRole(SpatieRole $spatieRole, Role $role): void
    {
        // Define role constants as used in RoleManager
        $SYS_ADMIN_ID = 1;
        $ADMIN_ID = 2;
        $INST_ADMIN_ID = 3;
        $INSTRUCTOR_ID = 4;
        $STUDENT_ID = 5;

        switch ($role->id) {
            case $SYS_ADMIN_ID:
                // System Admin gets all permissions
                $spatieRole->givePermissionTo(Permission::all());
                break;

            case $ADMIN_ID:
                // Admin gets most permissions except system-level ones
                $spatieRole->givePermissionTo([
                    'courses.view',
                    'courses.create',
                    'courses.edit',
                    'courses.delete',
                    'courses.archive',
                    'courses.restore',
                    'course-management.access',
                    'course-management.full-access'
                ]);
                break;

            case $INST_ADMIN_ID:
                // Institution Admin gets course management permissions
                $spatieRole->givePermissionTo([
                    'courses.view',
                    'courses.create',
                    'courses.edit',
                    'courses.archive',
                    'course-management.access'
                ]);
                break;

            case $INSTRUCTOR_ID:
                // Instructor gets limited permissions
                $spatieRole->givePermissionTo([
                    'courses.view-assigned',
                    'courses.manage-assigned',
                    'course-management.access'
                ]);
                break;

            case $STUDENT_ID:
                // Student gets very limited permissions
                $spatieRole->givePermissionTo([
                    'courses.view-own',
                    'courses.enroll'
                ]);
                break;
        }
    }
}
