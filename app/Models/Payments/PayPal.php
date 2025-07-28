<?php

namespace App\Models\Payments;

use App\Models\Payments\PaymentModel;


class PayPal extends PaymentModel
{

    protected $table        = 'payment_paypal';

    protected $casts        = [

        'id'                => 'integer',
        'order_id'          => 'integer',
        'uuid'              => 'string',

        'total_price'       => 'decimal:2',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'completed_at'      => 'timestamp',

        'refunded_at'       => 'timestamp',
        'refunded_by'       => 'integer',

    ];
}
