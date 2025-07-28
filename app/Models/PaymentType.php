<?php

namespace App\Models;

/**
 * @file PaymentType.php
 * @brief Model for payment_types table.
 * @details This model represents a payment type in the system, including attributes like name and relationships to orders.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\Order;
use App\Traits\StaticModel;


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

    protected $fillable     = [];  // static model


    public function __toString()
    {
        return $this->name;
    }


    //
    // relationships
    //


    public function Orders()
    {
        return $this->hasMany(Orders::class, 'payment_type_id');
    }
}
