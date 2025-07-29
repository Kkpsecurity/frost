<?php

namespace App\Support;

/**
 * Role Manager Support Class
 *
 * Centralized management of user roles and their properties.
 * This class provides constants, methods, and utilities for handling user roles consistently across the application.
 *
 * Actual Database Structure:
 * 1    SysAdmin
 * 2    Administrator
 * 3    Support
 * 4    Instructor
 * 5    Student
 * 6    Guest
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
    public const SYS_ADMIN_NAME = 'SysAdmin';
    public const ADMIN_NAME = 'Administrator';
    public const INSTRUCTOR_NAME = 'Instructor';
    public const SUPPORT_NAME = 'Support';
    public const STUDENT_NAME = 'Student';
    public const GUEST_NAME = 'Guest';

    // Role Display Names
    public const SYS_ADMIN_DISPLAY = 'System Admin';
    public const ADMIN_DISPLAY = 'Administrator';
    public const INSTRUCTOR_DISPLAY = 'Instructor';
    public const SUPPORT_DISPLAY = 'Support';
    public const STUDENT_DISPLAY = 'Student';
    public const GUEST_DISPLAY = 'Guest';

    /**
     * Get all admin role IDs (roles that have admin access)
     */
    public static function getAdminRoleIds(): array
    {
        return [
            self::SYS_ADMIN_ID,     // 1 - SysAdmin
            self::ADMIN_ID,         // 2 - Administrator
            self::SUPPORT_ID,       // 3 - Support
            self::INSTRUCTOR_ID,    // 4 - Instructor
        ];
    }

    /**
     * Get all admin role names (roles that have admin access)
     */
    public static function getAdminRoleNames(): array
    {
        return [
            self::SYS_ADMIN_NAME,   // sys_admin
            self::ADMIN_NAME,       // admin
            self::SUPPORT_NAME,     // support
            self::INSTRUCTOR_NAME,  // instructor
        ];
    }

    /**
     * Get role display names mapped to their database names
     */
    public static function getRoleDisplayMap(): array
    {
        return [
            self::SYS_ADMIN_NAME => self::SYS_ADMIN_DISPLAY,
            self::ADMIN_NAME => self::ADMIN_DISPLAY,
            self::INSTRUCTOR_NAME => self::INSTRUCTOR_DISPLAY,
            self::SUPPORT_NAME => self::SUPPORT_DISPLAY,
            self::STUDENT_NAME => self::STUDENT_DISPLAY,
            self::GUEST_NAME => self::GUEST_DISPLAY,
        ];
    }

    /**
     * Get role options for dropdowns (admin roles only)
     */
    public static function getAdminRoleOptions(): array
    {
        return [
            '' => 'All Roles',
            self::SYS_ADMIN_NAME => self::SYS_ADMIN_DISPLAY,
            self::ADMIN_NAME => self::ADMIN_DISPLAY,
            self::SUPPORT_NAME => self::SUPPORT_DISPLAY,
            self::INSTRUCTOR_NAME => self::INSTRUCTOR_DISPLAY,
        ];
    }

    /**
     * Get role options for creation forms
     */
    public static function getCreationRoleOptions(): array
    {
        return [
            self::SYS_ADMIN_ID => self::SYS_ADMIN_DISPLAY,
            self::ADMIN_ID => self::ADMIN_DISPLAY,
            self::SUPPORT_ID => self::SUPPORT_DISPLAY,
            self::INSTRUCTOR_ID => self::INSTRUCTOR_DISPLAY,
        ];
    }

    /**
     * Check if a role ID is an admin role
     */
    public static function isAdminRole(int $roleId): bool
    {
        return in_array($roleId, self::getAdminRoleIds());
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
        $roleMap = [
            self::SYS_ADMIN_NAME => self::SYS_ADMIN_ID,     // sys_admin => 1
            self::ADMIN_NAME => self::ADMIN_ID,             // admin => 2
            self::SUPPORT_NAME => self::SUPPORT_ID,         // support => 3
            self::INSTRUCTOR_NAME => self::INSTRUCTOR_ID,   // instructor => 4
            self::STUDENT_NAME => self::STUDENT_ID,         // student => 5
            self::GUEST_NAME => self::GUEST_ID,             // guest => 6
        ];

        return $roleMap[$roleName] ?? null;
    }

    /**
     * Get role name by role ID
     */
    public static function getRoleName(int $roleId): ?string
    {
        $roleMap = [
            self::SYS_ADMIN_ID => self::SYS_ADMIN_NAME,     // 1 => sys_admin
            self::ADMIN_ID => self::ADMIN_NAME,             // 2 => admin
            self::SUPPORT_ID => self::SUPPORT_NAME,         // 3 => support
            self::INSTRUCTOR_ID => self::INSTRUCTOR_NAME,   // 4 => instructor
            self::STUDENT_ID => self::STUDENT_NAME,         // 5 => student
            self::GUEST_ID => self::GUEST_NAME,             // 6 => guest
        ];

        return $roleMap[$roleId] ?? null;
    }

    /**
     * Get the maximum admin role ID (used for scoping)
     */
    public static function getMaxAdminRoleId(): int
    {
        return self::INSTRUCTOR_ID; // 4 is the highest admin role
    }

    /**
     * Get default role ID for new admin users
     */
    public static function getDefaultAdminRoleId(): int
    {
        return self::ADMIN_ID; // 2 - Administrator
    }

    /**
     * Get role hierarchy levels (lower number = higher privilege)
     */
    public static function getRoleHierarchy(): array
    {
        return [
            self::SYS_ADMIN_ID => 1,    // 1 - SysAdmin (highest)
            self::ADMIN_ID => 2,        // 2 - Administrator
            self::SUPPORT_ID => 3,      // 3 - Support
            self::INSTRUCTOR_ID => 4,   // 4 - Instructor
            self::STUDENT_ID => 5,      // 5 - Student
            self::GUEST_ID => 6,        // 6 - Guest (lowest)
        ];
    }

    /**
     * Check if role A has higher or equal privileges than role B
     */
    public static function hasHigherOrEqualPrivileges(int $roleIdA, int $roleIdB): bool
    {
        $hierarchy = self::getRoleHierarchy();
        $levelA = $hierarchy[$roleIdA] ?? PHP_INT_MAX;
        $levelB = $hierarchy[$roleIdB] ?? PHP_INT_MAX;

        return $levelA <= $levelB;
    }

        /**
     * Get role badge class for UI display
     */
    public static function getRoleBadgeClass(string $roleName): string
    {
        return match ($roleName) {
            self::SYS_ADMIN_NAME => 'badge-danger',    // Red for SysAdmin
            self::ADMIN_NAME => 'badge-primary',       // Blue for Administrator
            self::SUPPORT_NAME => 'badge-warning',     // Yellow for Support
            self::INSTRUCTOR_NAME => 'badge-info',     // Light Blue for Instructor
            self::STUDENT_NAME => 'badge-secondary',   // Gray for Student
            default => 'badge-light',
        };
    }

    /**
     * Get permissions for a role (can be extended later)
     */
    public static function getRolePermissions(string $roleName): array
    {
        return match ($roleName) {
            self::SYS_ADMIN_NAME => [
                'admin_access',
                'user_management',
                'system_settings',
                'all_permissions',
                'user_impersonation',
            ],
            self::ADMIN_NAME => [
                'admin_access',
                'user_management',
                'content_management',
            ],
            self::SUPPORT_NAME => [
                'admin_access',
                'support_tickets',
                'user_assistance',
            ],
            self::INSTRUCTOR_NAME => [
                'admin_access',
                'course_management',
                'student_management',
            ],
            default => [],
        };
    }

    /**
     * Check if a user can impersonate other users (sys admin only)
     */
    public static function canImpersonate(string $roleName): bool
    {
        return $roleName === self::SYS_ADMIN_NAME;
    }

    /**
     * Check if a user can be impersonated (all roles except sys admin)
     */
    public static function canBeImpersonated(string $roleName): bool
    {
        return $roleName !== self::SYS_ADMIN_NAME;
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
     */
    public static function formatDate($date, string $format = 'medium_date'): ?string
    {
        if (!$date) {
            return null;
        }

        $formats = self::getDateFormats();
        $dateFormat = $formats[$format] ?? $format;

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format($dateFormat);
    }
}
