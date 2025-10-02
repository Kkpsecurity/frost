<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

use App\Services\RCache;
use App\Classes\PaymentQueries;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\Order;
use App\Models\Payment;


class EnrollmentController extends Controller
{


    public function AutoPayFlowPro( Course $Course ) : RedirectResponse
    {
        // Debug logging to track enrollment attempts
        Log::info('Enrollment attempt for course ' . $Course->id . ' by user ' . Auth::id());

        // Check if user is already enrolled
        if ( Auth::user()->ActiveCourseAuths->firstWhere( 'course_id', $Course->id ) )
        {
            Log::info('User ' . Auth::id() . ' already enrolled in course ' . $Course->id);
            return redirect()->route('courses.show', $Course->id)
                ->with('warning', 'You are already enrolled in this course.');
        }

        // Check if course is active
        if (!$Course->is_active) {
            Log::warning('Attempted enrollment in inactive course ' . $Course->id);
            return redirect()->route('courses.list')
                ->with('error', 'This course is not currently available for enrollment.');
        }

        try {
            Log::info('Creating order for course ' . $Course->id);
            $Order = $this->GetOrder($Course);
            Log::info('Order created with ID: ' . $Order->id);

            $Payment = $this->GetPayment($Order);
            Log::info('Payment created with ID: ' . $Payment->id);

            Log::info('Redirecting to payment gateway for payment ' . $Payment->id);
            return redirect()->route('payments.payflowpro', $Payment);
        } catch (\Exception $e) {
            Log::error('Enrollment error for course ' . $Course->id . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('courses.show', $Course->id)
                ->with('error', 'There was an error processing your enrollment. Please try again or contact support.');
        }
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


    public function GetPayment(Order $Order): Payment
    {
        // Check if payment already exists for this order
        if ($Payment = $Order->payments()->first())
        {
            return $Payment;
        }

        // Create new payment record
        return Payment::create([
            'order_id' => $Order->id,
            'payment_method' => 'payflowpro',
            'gateway' => 'payflowpro',
            'amount' => $Order->total_price,
            'currency' => 'USD',
            'status' => 'pending'
        ]);
    }


}
