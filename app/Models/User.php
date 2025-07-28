<?php

namespace App\Models;

/**
 * @file User.php
 * @brief Model for users table.
 * @details This model represents a user in the system, including attributes like name, email, and role.
 */

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Carbon;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_active',
        'role_id',
        'lname',
        'fname',
        'email',
        'password',
        'avatar',
        'use_gravatar',
        'student_info',
        'email_opt_in',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'is_active' => true,
        'role_id' => 5, // default: student
        'email_opt_in' => false,
    ];

    /**
     * The attributes that are guarded against mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'is_active',
        'role_id',
        'zoom_creds_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'role_id' => 'integer',
        'lname' => 'string',
        'fname' => 'string',
        'email' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'remember_token' => 'string',
        'avatar' => 'string',
        'use_gravatar' => 'boolean',
        'student_info' => 'array', // Cast JSON to array
        'email_opt_in' => 'boolean',
    ];

    /**
     * Get the user's preferences.
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(UserPref::class, 'user_id');
    }

    /**
     * Get a specific user preference value.
     */
    public function getPreference(string $prefName, string $default = null): ?string
    {
        $pref = $this->preferences()->where('pref_name', $prefName)->first();
        return $pref ? $pref->pref_value : $default;
    }

    /**
     * Set a user preference.
     */
    public function setPreference(string $prefName, string $prefValue): UserPref
    {
        return $this->preferences()->updateOrCreate(
            ['pref_name' => $prefName],
            ['pref_value' => $prefValue]
        );
    }
}
