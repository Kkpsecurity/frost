<?php
declare(strict_types=1);

namespace App\Classes;

use Auth;

use RCache;
use App\Models\Course;
#use App\Models\CourseAuth;
use App\Models\Order;
#use App\Models\User;
#use App\Models\Payments\PayFlowPro;
#use App\Models\Payments\PayPal;


class PaymentQueries
{


    public static function GetIncompleteOrder( Course $Course ) : ?Order
    {

        abort_unless( Auth::check(), 401 );

        $Order = Order::where( 'user_id',   Auth::id()  )
                      ->where( 'course_id', $Course->id )
                  ->whereNull( 'completed_at' )
                     ->latest( 'updated_at'   )
                      ->first();

        //
        // touch Order and Payments for housekeeping
        //

        if ( $Order )
        {
            $Order->pgtouch();
            $Order->AllPayments()->each(function( $Payment ) { $Payment->pgtouch(); });
        }

        return $Order;

    }


    //
    // NOTE:
    //  get current Payment: $Order->GetPayment()
    //


    /*
    public static function GetPayFlowPro( Order $Order ) : ?PayFlowPro
    {
        return PayFlowPro::firstWhere( 'order_id', $Order->id );
    }


    public static function GetPayPal( Order $Order ) : ?PayPal
    {
        return PayPal::firstWhere( 'order_id', $Order->id );
    }
    */


}
