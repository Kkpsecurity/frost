<?php

namespace App\Http\Controllers\Frontend\Courses;

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


    public function AutoPayFlowPro(Course $Course): RedirectResponse
    {
        // Debug logging to track enrollment attempts
        Log::info('Enrollment attempt for course ' . $Course->id . ' by user ' . Auth::id());

        // Check if user is already enrolled - warn but allow re-enrollment (for renewals/prepay)
        $existingEnrollment = Auth::user()->ActiveCourseAuths->firstWhere('course_id', $Course->id);
        if ($existingEnrollment) {
            Log::info('User ' . Auth::id() . ' already has active enrollment for course ' . $Course->id . ' - allowing re-enrollment for renewal/prepay');
            // Store warning to show after payment page loads
            session()->flash('enrollment_warning', 'Note: You already have an active enrollment for this course. This purchase will extend or renew your access.');
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

            // Redirect to checkout page where user can select payment method
            // Don't create payment yet - let them choose payment method first
            Log::info('Redirecting to checkout page for order ' . $Order->id);
            return redirect()->route('checkout.show', $Order->id);
        } catch (\Exception $e) {
            Log::error('Enrollment error for course ' . $Course->id . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('courses.show', $Course->id)
                ->with('error', 'There was an error processing your enrollment. Please try again or contact support.');
        }
    }


    public function TestAutoPayFlowPro(Course $Course): array
    {

        $Order   = $this->GetOrder($Course);
        $Payment = $this->GetPayment($Order);

        return [$Order, $Payment];
    }



    ##############################
    ###                        ###
    ###   get / create Order   ###
    ###                        ###
    ##############################


    public static function GetOrder(Course $Course): Order
    {

        if ($Order = PaymentQueries::GetIncompleteOrder($Course)) {
            return $Order;
        }


        if (app()->environment('production')) {

            return Order::create([

                'user_id'           => Auth::id(),
                'course_id'         => $Course->id,
                'payment_type_id'   => RCache::PaymentTypes('PayFlowPro', 'name')->id,
                'course_price'      => $Course->price,
                'total_price'       => $Course->price,

            ])->refresh();
        }


        //
        // devel mode
        //


        $develPath = storage_path('devel');
        if (!is_dir($develPath)) {
            mkdir($develPath, 0755, true);
        }

        $filename = $develPath . DIRECTORY_SEPARATOR . 'last_order_id';
        if (!file_exists($filename)) {
            file_put_contents($filename, '0');
        }

        $order_id = (int) file_get_contents($filename) + 1;
        file_put_contents($filename, (string) $order_id, LOCK_EX);

        return Order::forceCreate([

            'id'                => $order_id,
            'user_id'           => Auth::id(),
            'course_id'         => $Course->id,
            'payment_type_id'   => RCache::PaymentTypes('PayFlowPro', 'name')->id,
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
        if ($Payment = $Order->payments()->first()) {
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
