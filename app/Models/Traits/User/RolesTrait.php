<?php

namespace App\Models\Traits\User;

use App\Support\RoleManager;

/**
 * @file RolesTrait.php
 * @brief Trait for managing user roles.
 * @details This trait provides methods to check user roles and return the appropriate dashboard URL.
 */

trait RolesTrait
{
    /**
     * Check if user is System Administrator
     */
    public function IsSysAdmin(): bool
    {
        return $this->role_id == RoleManager::SYS_ADMIN_ID;
    }

    /**
     * Check if user is Administrator or higher
     * Returns true if user is Administrator, Support, or SysAdmin.
     * Returns false if user is Instructor, Student, or Guest.
     * @return bool
     * @see RoleManager::ADMIN_ID for the Administrator role ID
     */
    public function IsAdministrator(): bool
    {
        return $this->role_id <= RoleManager::ADMIN_ID;
    }

    /**
     * Check if user is Support or higher
     * Returns true if user is Support, Administrator, or SysAdmin.
     * Returns false if user is Instructor, Student, or Guest.
     * @return bool
     * @see RoleManager::SUPPORT_ID for the Support role ID
     */
    public function IsSupport(): bool
    {
        return $this->role_id <= RoleManager::SUPPORT_ID;
    }

    /**
     * Check if user is Instructor or higher
     * Returns true if user is Instructor, Support, Administrator, or SysAdmin.
     * Returns false if user is Student or Guest.
     * @return bool
     * @see RoleManager::INSTRUCTOR_ID for the Instructor role ID
     */
    public function IsInstructor(): bool
    {
        return $this->role_id <= RoleManager::INSTRUCTOR_ID;
    }

    /**
     * Check if user is Student
     * Returns true if user is Student.
     * Returns false if user is SysAdmin, Administrator, Support, or Instructor.
     * @return bool
     * @see RoleManager::STUDENT_ID for the Student role ID
     */
    public function IsStudent(): bool
    {
        return $this->role_id == RoleManager::STUDENT_ID;
    }

    /**
     * Check if user is Guest
     *
     * Returns true if user is Guest.
     * Returns false if user is SysAdmin, Administrator, Support, Instructor, or Student.
     * @return bool
     * @see RoleManager::GUEST_ID for the Guest role ID
     */
    public function IsGuest(): bool
    {
        return $this->role_id == RoleManager::GUEST_ID;
    }

    /**
     * Check if user has any admin role
     * Returns true if user is SysAdmin, Administrator, or Support.
     * Returns false if user is Instructor, Student, or Guest.
     * @return bool
     * @see RoleManager::SYS_ADMIN_ID for the SysAdmin role ID
     * @see RoleManager::ADMIN_ID for the Administrator role ID
     */
    public function IsAnyAdmin(): bool
    {
        return RoleManager::isAdminRole($this->role_id);
    }

    /**
     * Get the role name (database key) for this user
     * Returns: SysAdmin, Administrator, Support, Instructor, Student, Guest
     */
    public function getRoleName(): ?string
    {
        return RoleManager::getRoleName($this->role_id);
    }

    /**
     * Get the role display name for this user
     * Returns: Sys Admin, Administrator, Support, Instructor, Student, Guest
     */
    public function getRoleDisplayName(): ?string
    {
        $roleName = $this->getRoleName();
        return $roleName ? RoleManager::getDisplayName($roleName) : null;
    }

    /**
     * Get the role badge class for UI display
     */
    public function getRoleBadgeClass(): string
    {
        $roleName = $this->getRoleName();
        return $roleName ? RoleManager::getRoleBadgeClass($roleName) : 'badge-light';
    }

    /**
     * Check if this user can impersonate other users (role-based)
     */
    public function canImpersonateByRole(): bool
    {
        $roleName = $this->getRoleName();
        return $roleName ? RoleManager::canImpersonate($roleName) : false;
    }

    /**
     * Check if this user can be impersonated by role (role-based)
     */
    public function canBeImpersonatedByRole(): bool
    {
        $roleName = $this->getRoleName();
        return $roleName ? RoleManager::canBeImpersonated($roleName) : true;
    }

    /**
     * Get permissions for this user's role
     */
    public function getRolePermissions(): array
    {
        $roleName = $this->getRoleName();
        return $roleName ? RoleManager::getRolePermissions($roleName) : [];
    }

    /**
     * Check if user has higher or equal privileges than another role
     */
    public function hasHigherOrEqualPrivileges(int $otherRoleId): bool
    {
        return RoleManager::hasHigherOrEqualPrivileges($this->role_id, $otherRoleId);
    }


    /**
     * Get the appropriate dashboard route for this user's role
     */
    public function Dashboard(): string
    {
        switch ($this->role_id) {
            case RoleManager::SYS_ADMIN_ID:      // 1 - SysAdmin
            case RoleManager::ADMIN_ID:          // 2 - Administrator
            case RoleManager::SUPPORT_ID:        // 3 - Support
                return route('admin.dashboard');
            case RoleManager::INSTRUCTOR_ID:     // 4 - Instructor
                return route('admin.instructors.dashboard');
            default:                             // 5 - Student, 6 - Guest
                return route('classroom.dashboard');
        }
    }
}
