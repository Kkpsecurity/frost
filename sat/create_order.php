<?php

use App\RCache;
use App\Models\Payments\PayFlowPro as Payment;
use App\Models\Order;



#DB::statement( 'TRUNCATE payment_payflowpro, orders RESTART IDENTITY' );


if ( ! $Order = Order::whereNull( 'completed_at' )->first() )
{


    $filename = storage_path( 'devel/last_order_id' );
    $order_id = (int) file_get_contents( $filename ) + 1;
    file_put_contents( $filename, $order_id );


    $Course = RCache::Courses( 1 );

    $Order = Order::forceCreate([

        'id'                => $order_id,
        'user_id'           => auth()->id(),
        'course_id'         => $Course->id,
        'payment_type_id'   => RCache::PaymentTypes( 'PayFlowPro', 'name' )->id,
        'course_price'      => $Course->price,
        'total_price'       => $Course->price,

    ])->refresh();

}



if ( ! $Payment = Payment::where( 'order_id', $Order->id )->latest()->first() )
{

    $Payment = Payment::forceCreate([

        'id'            => $Order->id,
        'order_id'      => $Order->id,
        'total_price'   => $Order->total_price,
        'pp_is_sandbox' => true,

    ])->refresh();

}
