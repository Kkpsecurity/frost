<?php

namespace App\Models;

/**
 * @file UserPref.php
 * @brief Model for user_prefs table.
 * @details This model represents user preferences, including attributes like user ID, preference name,
 * and preference value. It provides methods for sanitizing input and retrieving the associated user.
 */

use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\User;
use App\Helpers\TextTk;
use App\Traits\HasCompositePrimaryKey;


class UserPref extends Model
{

    use HasCompositePrimaryKey;


    const ALLOW_HTML_KEY    = false;
    const ALLOW_HTML_VALUE  = false;


    protected $table        = 'user_prefs';
    protected $primaryKey   = [ 'user_id', 'pref_name' ];
    public    $timestamps   = false;

    protected $casts        = [

        'user_id'           => 'integer',
        'pref_name'         => 'string',   // 64
        'pref_value'        => 'string',   // 255

    ];

    protected $guarded      = [ ];


    public function __toString() { return $this->pref_value; }


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


    public function setPrefNameAttribute( $value )
    {
        $sanitizeFlag = ( self::ALLOW_HTML_KEY ? TextTk::SANITIZE_NO_STRIPTAGS : null );
        $this->attributes[ 'pref_name' ] = TextTk::Sanitize( $value, $sanitizeFlag );
    }

    public function setPrefValueAttribute( $value )
    {
        $sanitizeFlag = ( self::ALLOW_HTML_VALUE ? TextTk::SANITIZE_NO_STRIPTAGS : null );
        $this->attributes[ 'pref_value' ] = TextTk::Sanitize( $value, $sanitizeFlag );
    }


    //
    // cache queries
    //


    public function GetUser() : User
    {
        return RCache::Users( $this->user_id );
    }


}
