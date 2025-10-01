<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use PageMetaDataTrait;

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
        $courseAuth = auth()->user()->courseAuths()->create([
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
}
