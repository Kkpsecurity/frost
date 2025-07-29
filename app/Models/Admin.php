<?php

namespace App\Models;

/**
 * @file Admin.php
 * @brief Admin model facade for frontend isolation.
 * @details This model acts as a facade to isolate frontend from backend implementation.
 * Admins are actually stored in the users table with role_id <= 4 (System Admin, Admin, Instructor, Support).
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use App\Notifications\AdminResetPasswordNotification;
use App\Traits\AvatarTrait;
use App\Support\RoleManager;

class Admin extends User
{
    use HasFactory, Notifiable, AvatarTrait;

    /**
     * Override the table to use users table
     */
    protected $table = 'users';

    /**
     * Boot method to automatically filter only admin users
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically scope all queries to admin users only
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->whereIn('role_id', RoleManager::getAdminRoleIds());
        });

        // When creating new admin, set role_id to default admin level
        static::creating(function ($admin) {
            if (!isset($admin->role_id)) {
                $admin->role_id = RoleManager::getDefaultAdminRoleId();
            }
        });
    }

    /**
     * Override fillable to include admin-specific fields
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'is_active',
        'avatar',
        'use_gravatar',
        'email_opt_in',
        'email_verified_at',
        'role_id',
    ];

    /**
     * Admin-specific attributes
     */
    protected $attributes = [
        'is_active' => true,
        'role_id' => RoleManager::ADMIN_ID, // Default admin level
        'email_opt_in' => false,
    ];

    /**
     * Always return true for admin check since this model only represents admins
     */
    public function isAdmin(): bool
    {
        return true;
    }

    /**
     * Get admin's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->fname . ' ' . $this->lname);
    }

    /**
     * Check if admin has specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->Role && $this->Role->name === $roleName;
    }

    /**
     * Check if admin has higher or equal privileges than given role
     */
    public function hasHigherOrEqualPrivileges(int $roleId): bool
    {
        return RoleManager::hasHigherOrEqualPrivileges($this->role_id, $roleId);
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayNameAttribute(): string
    {
        return $this->Role ? RoleManager::getDisplayName($this->Role->name) : 'N/A';
    }

    /**
     * Get role badge class for UI
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return $this->Role ? RoleManager::getRoleBadgeClass($this->Role->name) : 'badge-light';
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    /**
     * Get AdminLTE compatible image URL
     */
    public function adminlte_image(): string
    {
        return $this->getAvatar('small');
    }

    /**
     * Get AdminLTE compatible profile URL
     */
    public function adminlte_profile_url(): string
    {
        // For now, return dashboard URL since we don't have a profile page yet
        return '/admin/dashboard';
    }

    /**
     * Get AdminLTE compatible description (role)
     */
    public function adminlte_desc(): string
    {
        return $this->getRoleDisplayNameAttribute();
    }
}
