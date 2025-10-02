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
     * Display course payment selection page
     */
    public function coursePayment(\App\Models\Course $course)
    {
        // Check if course is active
        if (!$course->is_active) {
            abort(404, 'Course not found');
        }

        // Check if user is already enrolled
        if (auth()->user()->ActiveCourseAuths->firstWhere('course_id', $course->id)) {
            return redirect()->route('courses.show', $course->id)
                           ->with('warning', 'You are already enrolled in this course.');
        }

        // Prepare content data
        $content = array_merge([
            'title' => 'Enroll in ' . $course->title . ' - Select Payment Method',
            'description' => 'Choose your payment method to enroll in ' . $course->title,
            'keywords' => 'payment, course enrollment, stripe, paypal, secure checkout'
        ], self::renderPageMeta('course-payment'));

        // Check which payment gateways are enabled
        $paymentConfig = [
            'stripe_enabled' => !empty(setting('payment.stripe_public_key')) && !empty(setting('payment.stripe_secret_key')),
            'paypal_enabled' => !empty(setting('payment.paypal_client_id')) && !empty(setting('payment.paypal_client_secret')),
        ];

        return view('frontend.payments.course', compact('content', 'course', 'paymentConfig'));
    }

    /**
     * Display the payment processing page
     */
    public function payflowpro(Payment $payment)
    {
        // Check if payment exists and belongs to current user
        if (!$payment || $payment->order->user_id !== auth()->id()) {
            abort(404, 'Payment not found');
        }

        // Check if payment is already completed
        if ($payment->status === 'completed') {
            return redirect()->route('courses.show', $payment->order->course_id)
                           ->with('success', 'This payment has already been completed.');
        }

        // Get course information
        $course = $payment->order->course;

        // Prepare content data
        $content = array_merge([
            'title' => 'Payment Processing - ' . $course->title,
            'description' => 'Complete your payment for ' . $course->title,
            'keywords' => 'payment, course enrollment, secure checkout'
        ], self::renderPageMeta('payment-processing'));

        // Check which payment gateways are enabled
        $paymentConfig = [
            'stripe_enabled' => !empty(setting('payment.stripe_public_key')) && !empty(setting('payment.stripe_secret_key')),
            'paypal_enabled' => !empty(setting('payment.paypal_client_id')) && !empty(setting('payment.paypal_client_secret')),
        ];

        return view('frontend.payments.payflowpro', compact('content', 'payment', 'course', 'paymentConfig'));
    }

    /**
     * Process Stripe payment
     */
    public function processStripe(Request $request, Payment $payment)
    {
        // TODO: Implement Stripe payment processing
        // This would integrate with Stripe API to process the payment

        return back()->with('info', 'Stripe payment processing coming soon!');
    }

    /**
     * Process PayPal payment
     */
    public function processPaypal(Request $request, Payment $payment)
    {
        // TODO: Implement PayPal payment processing
        // This would integrate with PayPal API to process the payment

        return back()->with('info', 'PayPal payment processing coming soon!');
    }

    /**
     * Handle payment success callback
     */
    public function success(Payment $payment)
    {
        // Update payment status
        $payment->update(['status' => 'completed']);

        // Create course enrollment
        $courseAuth = CourseAuth::create([
            'user_id' => auth()->id(),
            'course_id' => $payment->order->course_id,
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('account.index')
                       ->with('success', 'Payment successful! You are now enrolled in the course.');
    }

    /**
     * Handle payment cancellation
     */
    public function cancel(Payment $payment)
    {
        return redirect()->route('courses.show', $payment->order->course_id)
                       ->with('warning', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * Process Stripe payment for course enrollment
     */
    public function processCourseStripe(Request $request, \App\Models\Course $course)
    {
        // Check if user is already enrolled
        if (auth()->user()->ActiveCourseAuths->firstWhere('course_id', $course->id)) {
            return redirect()->route('courses.show', $course->id)
                           ->with('warning', 'You are already enrolled in this course.');
        }

        try {
            // Create order and payment through enrollment controller
            $enrollmentController = new \App\Http\Controllers\Web\EnrollmentController();
            $order = $enrollmentController->GetOrder($course);
            $payment = $enrollmentController->GetPayment($order);

            // TODO: Integrate with actual Stripe payment processing
            // For now, simulate successful payment
            $payment->update([
                'status' => 'completed',
                'transaction_id' => 'stripe_test_' . uniqid(),
                'processed_at' => now()
            ]);

            // Create course enrollment
            CourseAuth::create([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'is_active' => true,
                'enrolled_at' => now(),
            ]);

            return redirect()->route('courses.show', $course->id)
                           ->with('success', 'Payment successful! You are now enrolled in ' . $course->title);

        } catch (\Exception $e) {
            Log::error('Course Stripe payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process PayPal payment for course enrollment
     */
    public function processCoursePaypal(Request $request, \App\Models\Course $course)
    {
        // Check if user is already enrolled
        if (auth()->user()->ActiveCourseAuths->firstWhere('course_id', $course->id)) {
            return redirect()->route('courses.show', $course->id)
                           ->with('warning', 'You are already enrolled in this course.');
        }

        try {
            // Create order and payment through enrollment controller
            $enrollmentController = new \App\Http\Controllers\Web\EnrollmentController();
            $order = $enrollmentController->GetOrder($course);
            $payment = $enrollmentController->GetPayment($order);

            // TODO: Integrate with actual PayPal payment processing
            // For now, simulate successful payment
            $payment->update([
                'status' => 'completed',
                'transaction_id' => 'paypal_test_' . uniqid(),
                'processed_at' => now()
            ]);

            // Create course enrollment
            CourseAuth::create([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'is_active' => true,
                'enrolled_at' => now(),
            ]);

            return redirect()->route('courses.show', $course->id)
                           ->with('success', 'Payment successful via PayPal! You are now enrolled in ' . $course->title);

        } catch (\Exception $e) {
            Log::error('Course PayPal payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }
}
