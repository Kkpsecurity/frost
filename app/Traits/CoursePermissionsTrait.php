<?php

namespace App\Traits;

use App\Support\RoleManager;
use App\Services\PermissionIntegrationService;

/**
 * Course Permissions Trait
 *
 * Provides centralized permission checking for course-related operations
 * using both the existing RoleManager system and Spatie Permissions for granular control.
 */
trait CoursePermissionsTrait
{
    /**
     * Check if the current user can manage courses (create, edit, archive/restore)
     *
     * @return bool
     */
    protected function canManageCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'course-management.full-access');
    }

    /**
     * Check if the current user can delete courses
     *
     * @return bool
     */
    protected function canDeleteCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'courses.delete');
    }

    /**
     * Check if the current user can view course details
     *
     * @return bool
     */
    protected function canViewCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'courses.view') ||
               $permissionService->userHasPermission($user, 'course-management.access');
    }

    /**
     * Check if the current user can create courses
     *
     * @return bool
     */
    protected function canCreateCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'courses.create');
    }

    /**
     * Check if the current user can edit courses
     *
     * @return bool
     */
    protected function canEditCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'courses.edit');
    }

    /**
     * Check if the current user can archive/restore courses
     *
     * @return bool
     */
    protected function canArchiveCourses(): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        $permissionService = app(PermissionIntegrationService::class);
        return $permissionService->userHasPermission($user, 'courses.archive');
    }

    /**
     * Get all course permissions for the current user
     *
     * @return array
     */
    protected function getCoursePermissions(): array
    {
        return [
            'can_view' => $this->canViewCourses(),
            'can_create' => $this->canCreateCourses(),
            'can_edit' => $this->canEditCourses(),
            'can_delete' => $this->canDeleteCourses(),
            'can_manage' => $this->canManageCourses(),
            'can_archive' => $this->canArchiveCourses(),
        ];
    }
}
