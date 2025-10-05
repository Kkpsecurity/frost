<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\CourseAuth;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display course payment selection page - GUEST CHECKOUT ENABLED
     */
    public function coursePayment(\App\Models\Course $course)
    {
        // Check if course is active
        if (!$course->is_active) {
            abort(404, 'Course not found');
        }

        // TEMPORARILY DISABLED - Let payment page load regardless of enrollment status
        // if (auth()->check()) {
        //     $enrolled = auth()->user()->ActiveCourseAuths->firstWhere('course_id', $course->id);
        //     if ($enrolled) {
        //         return redirect()->route('courses.show', $course->id)
        //             ->with('warning', 'You are already enrolled in this course.');
        //     }
        // }

        // Prepare content data
        $content = array_merge([
            'title' => 'Enroll in ' . $course->title . ' - Select Payment Method',
            'description' => 'Choose your payment method to enroll in ' . $course->title,
            'keywords' => 'payment, course enrollment, stripe, paypal, secure checkout'
        ], self::renderPageMeta('course-payment'));

        // Check which payment gateways are enabled - TEMPORARILY ENABLE FOR TESTING
        $paymentConfig = [
            'stripe_enabled' => true, // Temporarily enabled for testing
            'paypal_enabled' => true, // Temporarily enabled for testing
        ];

        // Pass guest checkout flag
        $isGuest = !auth()->check();

        return view('frontend.payments.course', compact('content', 'course', 'paymentConfig', 'isGuest'));
    }

    /**
     * Debug course payment information
     */
    public function debugCoursePayment(\App\Models\Course $course)
    {
        $debug = [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'price' => $course->price,
                'is_active' => $course->is_active,
            ],
            'authentication' => [
                'is_authenticated' => auth()->check(),
                'guest_checkout' => !auth()->check(),
            ],
            'payment_config' => [
                'stripe_enabled' => !empty(setting('payment.stripe_public_key')) && !empty(setting('payment.stripe_secret_key')),
                'paypal_enabled' => !empty(setting('payment.paypal_client_id')) && !empty(setting('payment.paypal_client_secret')),
            ],
        ];

        if (auth()->check()) {
            $user = auth()->user();
            $enrolled = $user->ActiveCourseAuths->firstWhere('course_id', $course->id);

            $debug['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'active_enrollments_count' => $user->ActiveCourseAuths->count(),
            ];

            $debug['enrollment_check'] = [
                'is_enrolled' => $enrolled ? true : false,
                'will_redirect' => $enrolled ? true : false,
            ];
        }

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Process Stripe payment for course enrollment - REDIRECT TO STRIPE
     */
    public function processCourseStripe(Request $request, \App\Models\Course $course)
    {
        try {
            // Store course and user info in session for when they return
            session([
                'payment_course_id' => $course->id,
                'payment_method' => 'stripe',
                'payment_amount' => $course->price,
                'payment_user_data' => $request->all()
            ]);

            // Create a Stripe payment simulation page
            return view('frontend.payments.stripe-checkout', [
                'course' => $course,
                'amount' => $course->price,
                'currency' => 'USD',
                'return_url' => route('courses.show', $course->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Course Stripe payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process PayPal payment for course enrollment - REDIRECT TO PAYPAL
     */
    public function processCoursePaypal(Request $request, \App\Models\Course $course)
    {
        try {
            // Store course and user info in session for when they return
            session([
                'payment_course_id' => $course->id,
                'payment_method' => 'paypal',
                'payment_amount' => $course->price,
                'payment_user_data' => $request->all()
            ]);

            // Create a PayPal payment simulation page
            return view('frontend.payments.paypal-checkout', [
                'course' => $course,
                'amount' => $course->price,
                'currency' => 'USD',
                'return_url' => route('courses.show', $course->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Course PayPal payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Handle payment success simulation from checkout pages
     */
    public function successSimulation(Request $request)
    {
        $courseId = $request->input('course_id');
        $paymentMethod = $request->input('payment_method');
        $amount = $request->input('amount');
        $returnUrl = $request->input('return_url');

        // Store success data in session
        session([
            'payment_success' => true,
            'payment_course_id' => $courseId,
            'payment_method' => $paymentMethod,
            'payment_amount' => $amount
        ]);

        // Create success simulation view
        return view('frontend.payments.success-simulation', [
            'course_id' => $courseId,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'return_url' => $returnUrl,
            'email' => $request->input('email'),
            'transaction_id' => 'SIM_' . strtoupper($paymentMethod) . '_' . time()
        ]);
    }
}
