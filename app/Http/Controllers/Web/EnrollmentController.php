<?php

namespace App\Http\Controllers\Web;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

use App\RCache;
use App\Classes\PaymentQueries;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\Order;
use App\Models\Payments\PaymentModel;
use App\Models\Payments\PayFlowPro;


class EnrollmentController extends Controller
{


    public function AutoPayFlowPro( Course $Course ) : RedirectResponse
    {

        if ( Auth::user()->ActiveCourseAuths->firstWhere( 'course_id', $Course->id ) )
        {
            return back()->with( 'warning', 'You are already enrolled in this course.' );
        }

        $Order   = $this->GetOrder( $Course );
        $Payment = $this->GetPayment( $Order );

        return redirect()->route( 'payments.payflowpro', $Payment );

    }


    public function TestAutoPayFlowPro( Course $Course ) : array
    {

        $Order   = $this->GetOrder( $Course );
        $Payment = $this->GetPayment( $Order );

        return [ $Order, $Payment ];

    }



    ##############################
    ###                        ###
    ###   get / create Order   ###
    ###                        ###
    ##############################


    public static function GetOrder( Course $Course ) : Order
    {

        if ( $Order = PaymentQueries::GetIncompleteOrder( $Course ) )
        {
            return $Order;
        }


        if ( app()->environment( 'production' ) )
        {

            return Order::create([

                'user_id'           => Auth::id(),
                'course_id'         => $Course->id,
                'payment_type_id'   => RCache::PaymentTypes( 'PayFlowPro', 'name' )->id,
                'course_price'      => $Course->price,
                'total_price'       => $Course->price,

            ])->refresh();

        }


        //
        // devel mode
        //


        $filename = storage_path( 'devel/last_order_id' );
        $order_id = (int) file_get_contents( $filename ) + 1;
        file_put_contents( $filename, $order_id );

        return Order::forceCreate([

            'id'                => $order_id,
            'user_id'           => Auth::id(),
            'course_id'         => $Course->id,
            'payment_type_id'   => RCache::PaymentTypes( 'PayFlowPro', 'name' )->id,
            'course_price'      => $Course->price,
            'total_price'       => $Course->price,

        ])->refresh();

        // don't reset sequence!

    }



    ################################
    ###                          ###
    ###   get / create Payment   ###
    ###                          ###
    ################################


    public  function GetPayment( Order $Order ) : PaymentModel
    {

        if ( $Payment = $Order->GetPayment() )
        {
            return $Payment;
        }

        return PayFlowPro::forceCreate([

            'id'            => $Order->id,
            'order_id'      => $Order->id,
            'total_price'   => $Order->total_price,
            'pp_is_sandbox' => ( ! app()->environment( 'production' ) )

        ])->refresh();

    }


}
