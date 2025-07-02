<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\User;


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

    protected $guarded      = [ 'id' ];


    //
    // relationships
    //


    public function User()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }


    //
    // incoming data filters
    //


    public function setLicenseAttribute( $value )
    {
        $this->attributes[ 'license' ] = TextTk::Sanitize( strtoupper( str_replace( ' ', '', $value ) ) );
    }


    //
    // cache queries
    //


    public function GetUser() : User
    {
        return RCache::User( $this->user_id );
    }


    //
    // helpers
    //


    public function ExpiresAt() : string
    {
        return Carbon::parse( $this->expires_at )->isoFormat( 'MM/DD/YYYY' );
    }

    public function IsExpired() : bool
    {
        return Carbon::now()->gt( Carbon::parse( $this->expires_at ) );
    }


}
