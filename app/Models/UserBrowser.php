<?php

namespace App\Models;

/**
 * @file UserBrowser.php
 * @brief Model for user_browsers table.
 * @details This model represents a user's browser information, including the user ID and browser string.
 * It provides a relationship to the User model and overrides the __toString method to return the browser string.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Traits\PgTimestamps;


class UserBrowser extends Model
{

    use PgTimestamps;


    protected $table        = 'user_browsers';
    protected $primaryKey   = 'user_id';
    public    $timestamps   = true;
    const     CREATED_AT    = null;

    protected $casts        = [

        'user_id'           => 'integer',
        'browser'           => 'string',
        'updated_at'        => 'timestamp',

    ];

    protected $guarded      = []; // all fillable

    public function __toString()
    {
        return $this->browser;
    }


    //
    // relationships
    //


    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
