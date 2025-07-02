<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use KKP\Laravel\ModelTraits\PgTimestamps;


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

    protected $guarded      = [ ]; // all fillable

    public function __toString() { return $this->browser; }


    //
    // relationships
    //


    public function User()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }


}
