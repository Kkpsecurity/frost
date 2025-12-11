<?php

namespace App\Models;

/**
 * @file ZoomCreds.php
 * @brief Model for zoom_creds table.
 * @details This model represents Zoom credentials, including email, password, passcode, and status.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Services\RCache;
use App\Traits\NoString;
use App\Traits\Observable;


class ZoomCreds extends Model
{

    use Observable, NoString;

    protected $table        = 'zoom_creds';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'zoom_email'        => 'string',
        'zoom_password'     => 'string',
        'zoom_passcode'     => 'string',
        #'zoom_password'     => 'encrypted',
        #'zoom_passcode'     => 'encryped',

        'zoom_status'       => 'string',   // 'disabled' | 'enabled'

        'pmi'               => 'string',
        'use_pmi'           => 'boolean',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [

        'zoom_status' => 'disabled',
        'use_pmi'     => true,

    ];


    //
    // relationships
    //


    public function Users()
    {
        return $this->hasMany(User::class, 'zoom_creds_id');
    }
}
