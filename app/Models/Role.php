<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use KKP\Laravel\ModelTraits\StaticModel;


class Role extends Model
{

    use StaticModel;


    protected $table        = 'roles';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    //
    // NOTE: this table does not have an id sequence
    //

    protected $casts        = [

        'id'                => 'integer',
        'name'              => 'string',  // 16

    ];

    protected $fillable     = [ ];  // static model


    public function __toString() { return $this->name; }


    //
    // relationships
    //


    public function Users()
    {
        return $this->hasMany( User::class, 'role_id' );
    }


}
