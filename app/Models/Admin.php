<?php

namespace App\Models;

/**
 * @file Admin.php
 * @brief Admin model facade for frontend isolation.
 * @details This model acts as a facade to isolate frontend from backend implementation.
 * Admins are actually stored in the users table with role_id < 4 (1=Super Admin, 2=Admin, 3=Support).
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\AdminResetPasswordNotification;

class Admin extends User
{
    use HasFactory, Notifiable;

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

        // Automatically scope all queries to admin users only (role_id < 3: System + Regular admins)
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->where('role_id', '<', 3);
        });

        // When creating new admin, set role_id to 2 (default admin level)
        static::creating(function ($admin) {
            if (!isset($admin->role_id)) {
                $admin->role_id = 2;
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
    ];

    /**
     * Admin-specific attributes
     */
    protected $attributes = [
        'is_active' => true,
        'role_id' => 2, // Always admin (not sys_admin)
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
     * Admin-specific scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Send the password reset notification with admin-specific URL.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
}
