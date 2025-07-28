<?php

declare(strict_types=1);

namespace App\Classes\Utilities;

/**
 * @file PaymentQueries.php
 * @brief Class for handling payment-related queries.
 * @details Provides methods to retrieve incomplete orders and manage payment data.
 */

use Illuminate\Support\Facades\Auth;

use App\Services\RCache;

use App\Models\Order;
use App\Models\Course;

class PaymentQueries
{


    public static function GetIncompleteOrder(Course $Course): ?Order
    {

        abort_unless(Auth::check(), 401);

        $Order = Order::where('user_id',   Auth::id())
            ->where('course_id', $Course->id)
            ->whereNull('completed_at')
            ->latest('updated_at')
            ->first();

        //
        // touch Order and Payments for housekeeping
        //

        if ($Order) {
            $Order->pgtouch();
            $Order->AllPayments()->each(function ($Payment) {
                $Payment->pgtouch();
            });
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
