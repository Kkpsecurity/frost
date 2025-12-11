<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission;

/**
 * Permission Integration Service
 *
 * Bridges the existing RoleManager system with Spatie Permissions
 * to provide granular permission control while maintaining backward compatibility
 */
class PermissionIntegrationService
{
    /**
     * Sync user with Spatie role based on their role_id
     */
    public function syncUserRole(User $user): void
    {
        // Get the user's current role
        $role = Role::find($user->role_id);

        if (!$role) {
            return;
        }

        // Find or create corresponding Spatie role
        $spatieRole = SpatieRole::where('name', $role->name)->first();

        if ($spatieRole) {
            // Remove all existing roles and assign the correct one
            $user->syncRoles([$spatieRole]);
        }
    }

    /**
     * Sync all users with their Spatie roles
     */
    public function syncAllUsers(): void
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->syncUserRole($user);
            }
        });
    }

    /**
     * Check if user has permission (combines RoleManager and Spatie)
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        // First check RoleManager-based permissions for backward compatibility
        if ($this->hasRoleManagerPermission($user, $permission)) {
            return true;
        }

        // Then check Spatie permissions for granular control
        return $user->hasPermissionTo($permission);
    }

    /**
     * Check RoleManager-based permissions for backward compatibility
     */
    private function hasRoleManagerPermission(User $user, string $permission): bool
    {
        // Map permissions to RoleManager methods
        $rolePermissions = [
            'courses.view' => $user->IsAnyAdmin(),
            'courses.create' => $user->IsAdministrator(),
            'courses.edit' => $user->IsAdministrator(),
            'courses.delete' => $user->IsAdministrator(),
            'courses.archive' => $user->IsAdministrator(),
            'courses.restore' => $user->IsAdministrator(),
            'course-management.access' => $user->IsAnyAdmin(),
            'course-management.full-access' => $user->IsAdministrator(),
        ];

        return $rolePermissions[$permission] ?? false;
    }

    /**
     * Get all permissions for a user (RoleManager + Spatie)
     */
    public function getUserPermissions(User $user): array
    {
        $permissions = [];

        // Add RoleManager-based permissions
        if ($user->IsSysAdmin()) {
            $permissions[] = 'system.admin';
        }

        if ($user->IsAdministrator()) {
            $permissions = array_merge($permissions, [
                'courses.view',
                'courses.create',
                'courses.edit',
                'courses.delete',
                'courses.archive',
                'courses.restore',
                'course-management.full-access'
            ]);
        }

        if ($user->IsAnyAdmin()) {
            $permissions = array_merge($permissions, [
                'course-management.access'
            ]);
        }

        // Add Spatie permissions
        $spatiePermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $permissions = array_merge($permissions, $spatiePermissions);

        return array_unique($permissions);
    }

    /**
     * Create a new permission
     */
    public function createPermission(string $name, string $guardName = 'web'): Permission
    {
        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => $guardName
        ]);
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(string $roleName, string $permissionName): bool
    {
        $role = SpatieRole::where('name', $roleName)->first();
        $permission = Permission::where('name', $permissionName)->first();

        if ($role && $permission) {
            $role->givePermissionTo($permission);
            return true;
        }

        return false;
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(string $roleName, string $permissionName): bool
    {
        $role = SpatieRole::where('name', $roleName)->first();
        $permission = Permission::where('name', $permissionName)->first();

        if ($role && $permission) {
            $role->revokePermissionTo($permission);
            return true;
        }

        return false;
    }
}
