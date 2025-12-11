<?php

namespace App\Support;

/**
 * Role Manager Support Class
 *
 * Centralized management of user roles and their properties.
 * This class provides constants, methods, and utilities for handling user roles consistently across the application.
 *
 * Actual Database Structure:
 * 1    sys_admin
 * 2    admin
 * 3    support
 * 4    instructor
 * 5    student
 * 6    guest
 */
class RoleManager
{
    // Role ID Constants (matching actual database)
    public const SYS_ADMIN_ID = 1;
    public const ADMIN_ID = 2;
    public const SUPPORT_ID = 3;
    public const INSTRUCTOR_ID = 4;
    public const STUDENT_ID = 5;
    public const GUEST_ID = 6;

    // Role Name Constants (matching database values)
    public const SYS_ADMIN_NAME = 'sys_admin';
    public const ADMIN_NAME = 'admin';
    public const SUPPORT_NAME = 'support';
    public const INSTRUCTOR_NAME = 'instructor';
    public const STUDENT_NAME = 'student';
    public const GUEST_NAME = 'guest';

    // Role Display Names (user-friendly names)
    public const SYS_ADMIN_DISPLAY = 'Sys Admin';
    public const ADMIN_DISPLAY = 'Administrator';
    public const SUPPORT_DISPLAY = 'Support';
    public const INSTRUCTOR_DISPLAY = 'Instructor';
    public const STUDENT_DISPLAY = 'Student';
    public const GUEST_DISPLAY = 'Guest';

    /**
     * Master role configuration - single source of truth
     */
    private static function getRoleConfiguration(): array
    {
        return [
            self::SYS_ADMIN_ID => [
                'name' => self::SYS_ADMIN_NAME,
                'display' => self::SYS_ADMIN_DISPLAY,
                'badge' => 'badge-danger',
                'is_admin' => true,
                'hierarchy' => 1,
                'permissions' => ['admin_access', 'user_management', 'system_settings', 'all_permissions', 'user_impersonation'],
                'can_impersonate' => true,
                'can_be_impersonated' => false,
            ],
            self::ADMIN_ID => [
                'name' => self::ADMIN_NAME,
                'display' => self::ADMIN_DISPLAY,
                'badge' => 'badge-primary',
                'is_admin' => true,
                'hierarchy' => 2,
                'permissions' => ['admin_access', 'user_management', 'content_management'],
                'can_impersonate' => false,
                'can_be_impersonated' => true,
            ],
            self::SUPPORT_ID => [
                'name' => self::SUPPORT_NAME,
                'display' => self::SUPPORT_DISPLAY,
                'badge' => 'badge-warning',
                'is_admin' => true,
                'hierarchy' => 3,
                'permissions' => ['admin_access', 'support_tickets', 'user_assistance'],
                'can_impersonate' => false,
                'can_be_impersonated' => true,
            ],
            self::INSTRUCTOR_ID => [
                'name' => self::INSTRUCTOR_NAME,
                'display' => self::INSTRUCTOR_DISPLAY,
                'badge' => 'badge-info',
                'is_admin' => true,
                'hierarchy' => 4,
                'permissions' => ['admin_access', 'course_management', 'student_management'],
                'can_impersonate' => false,
                'can_be_impersonated' => true,
            ],
            self::STUDENT_ID => [
                'name' => self::STUDENT_NAME,
                'display' => self::STUDENT_DISPLAY,
                'badge' => 'badge-secondary',
                'is_admin' => false,
                'hierarchy' => 5,
                'permissions' => [],
                'can_impersonate' => false,
                'can_be_impersonated' => true,
            ],
            self::GUEST_ID => [
                'name' => self::GUEST_NAME,
                'display' => self::GUEST_DISPLAY,
                'badge' => 'badge-light',
                'is_admin' => false,
                'hierarchy' => 6,
                'permissions' => [],
                'can_impersonate' => false,
                'can_be_impersonated' => true,
            ],
        ];
    }

    /**
     * Get all admin role IDs (roles that have admin access)
     */
    public static function getAdminRoleIds(): array
    {
        return array_keys(array_filter(self::getRoleConfiguration(), fn($role) => $role['is_admin']));
    }

    /**
     * Get all admin role names (roles that have admin access)
     */
    public static function getAdminRoleNames(): array
    {
        $adminRoles = array_filter(self::getRoleConfiguration(), fn($role) => $role['is_admin']);
        return array_column($adminRoles, 'name');
    }

    /**
     * Get role display names mapped to their database names
     */
    public static function getRoleDisplayMap(): array
    {
        return array_column(self::getRoleConfiguration(), 'display', 'name');
    }

    /**
     * Get role options for dropdowns (admin roles only)
     */
    public static function getAdminRoleOptions(): array
    {
        $options = ['' => 'All Roles'];
        $adminRoles = array_filter(self::getRoleConfiguration(), fn($role) => $role['is_admin']);

        foreach ($adminRoles as $roleConfig) {
            $options[$roleConfig['name']] = $roleConfig['display'];
        }

        return $options;
    }

    /**
     * Get role options for creation forms (using role IDs as keys)
     */
    public static function getCreationRoleOptions(): array
    {
        $options = [];
        $adminRoles = array_filter(self::getRoleConfiguration(), fn($role) => $role['is_admin']);

        foreach ($adminRoles as $roleId => $roleConfig) {
            $options[$roleId] = $roleConfig['display'];
        }

        return $options;
    }

    /**
     * Check if a role ID is an admin role
     */
    public static function isAdminRole(int $roleId): bool
    {
        $config = self::getRoleConfiguration();
        return $config[$roleId]['is_admin'] ?? false;
    }

    /**
     * Check if a role name is an admin role
     */
    public static function isAdminRoleName(string $roleName): bool
    {
        return in_array($roleName, self::getAdminRoleNames());
    }

    /**
     * Get display name for a role
     */
    public static function getDisplayName(string $roleName): string
    {
        $map = self::getRoleDisplayMap();
        return $map[$roleName] ?? ucfirst($roleName);
    }

    /**
     * Get role ID by role name
     */
    public static function getRoleId(string $roleName): ?int
    {
        foreach (self::getRoleConfiguration() as $roleId => $config) {
            if ($config['name'] === $roleName) {
                return $roleId;
            }
        }
        return null;
    }

    /**
     * Get role name by role ID
     */
    public static function getRoleName(int $roleId): ?string
    {
        $config = self::getRoleConfiguration();
        return $config[$roleId]['name'] ?? null;
    }

        /**
     * Get the maximum admin role ID (used for scoping)
     */
    public static function getMaxAdminRoleId(): int
    {
        return max(self::getAdminRoleIds());
    }

    /**
     * Get default role ID for new admin users
     */
    public static function getDefaultAdminRoleId(): int
    {
        return self::ADMIN_ID; // 2 - Administrator
    }

    /**
     * Get default role ID for new support users
     */
    public static function getDefaultSupportRoleId(): int
    {
        return self::SUPPORT_ID; // 3 - Support
    }

    /**
     * Get role hierarchy levels (lower number = higher privilege)
     */
    public static function getRoleHierarchy(): array
    {
        return array_column(self::getRoleConfiguration(), 'hierarchy');
    }

    /**
     * Check if role A has higher or equal privileges than role B
     */
    public static function hasHigherOrEqualPrivileges(int $roleIdA, int $roleIdB): bool
    {
        $config = self::getRoleConfiguration();
        $levelA = $config[$roleIdA]['hierarchy'] ?? PHP_INT_MAX;
        $levelB = $config[$roleIdB]['hierarchy'] ?? PHP_INT_MAX;

        return $levelA <= $levelB;
    }

    /**
     * Get role badge class for UI display
     */
    public static function getRoleBadgeClass(string $roleName): string
    {
        foreach (self::getRoleConfiguration() as $config) {
            if ($config['name'] === $roleName) {
                return $config['badge'];
            }
        }
        return 'badge-light';
    }

    /**
     * Get permissions for a role
     */
    public static function getRolePermissions(string $roleName): array
    {
        foreach (self::getRoleConfiguration() as $config) {
            if ($config['name'] === $roleName) {
                return $config['permissions'];
            }
        }
        return [];
    }

    /**
     * Check if a user can impersonate other users
     */
    public static function canImpersonate(string $roleName): bool
    {
        foreach (self::getRoleConfiguration() as $config) {
            if ($config['name'] === $roleName) {
                return $config['can_impersonate'];
            }
        }
        return false;
    }

    /**
     * Check if a user can be impersonated
     */
    public static function canBeImpersonated(string $roleName): bool
    {
        foreach (self::getRoleConfiguration() as $config) {
            if ($config['name'] === $roleName) {
                return $config['can_be_impersonated'];
            }
        }
        return true;
    }

    /**
     * Get available date formats
     *
     * @return array
     */
    public static function getDateFormats(): array
    {
        return config('define.date_formats', [
            'short_date' => 'm/d/Y',
            'medium_date' => 'M j, Y',
            'long_date' => 'F j, Y',
            'datetime_short' => 'm/d/Y g:i A',
            'datetime_medium' => 'M j, Y g:i A',
            'datetime_long' => 'F j, Y g:i A',
            'time_only' => 'g:i A',
            'date_only' => 'M j, Y',
        ]);
    }

    /**
     * Format a date using US format patterns
     *
     * @param mixed $date Date to format (Carbon instance, string, etc.)
     * @param string $format Format type from available date formats
     * @param bool $forceEasternTime Force conversion to Eastern time (for class schedules)
     * @return string|null Formatted date string or null if date is empty
     */
    public static function formatDate($date, string $format = 'medium_date', bool $forceEasternTime = false): ?string
    {
        if (!$date) {
            return null;
        }

        $formats = self::getDateFormats();
        $dateFormat = $formats[$format] ?? $format;

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        // Convert to Eastern time for class schedules to ensure consistency
        if ($forceEasternTime) {
            $date = $date->setTimezone('America/New_York');
            // Add timezone indicator to the format if it's a time format
            if (str_contains($dateFormat, 'g:i A') || str_contains($dateFormat, 'H:i')) {
                $dateFormat .= ' T'; // Add timezone abbreviation (EDT/EST)
            }
        }

        return $date->format($dateFormat);
    }
}
