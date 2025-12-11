<?php

namespace App\Models;

/**
 * @file InstLicense.php
 * @brief Model for inst_licenses table.
 * @details This model represents an institutional license in the system, including attributes like user ID, license key, and expiration date.
 */

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\User;
use App\Helpers\TextTk;


class InstLicense extends Model
{

    protected $table        = 'inst_licenses';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'user_id'           => 'integer',
        'license'           => 'string',   // 16
        'expires_at'        => 'date',

    ];

    protected $guarded      = ['id'];


    //
    // relationships
    //


    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    //
    // incoming data filters
    //


    public function setLicenseAttribute($value)
    {
        $this->attributes['license'] = TextTk::Sanitize(strtoupper(str_replace(' ', '', $value)));
    }


    //
    // cache queries
    //


    public function GetUser(): User
    {
        return RCache::User($this->user_id);
    }


    //
    // helpers
    //


    public function ExpiresAt(): string
    {
        return Carbon::parse($this->expires_at)->isoFormat('MM/DD/YYYY');
    }

    public function IsExpired(): bool
    {
        return Carbon::now()->gt(Carbon::parse($this->expires_at));
    }
}
