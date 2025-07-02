<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Order;
use KKP\Laravel\ModelTraits\StaticModel;


class PaymentType extends Model
{

    use StaticModel;


    protected $table        = 'payment_types';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    //
    // NOTE: this table does not have an id sequence
    //

    protected $casts        = [

        'id'                => 'integer',

        'is_active'         => 'boolean',
        'name'              => 'string',

        'model_class'       => 'string',
        'controller_class'  => 'string',

    ];

    protected $fillable     = [ ];  // static model


    public function __toString() { return $this->name; }


    //
    // relationships
    //


    public function Orders()
    {
        return $this->hasMany( Orders::class, 'payment_type_id' );
    }


}
