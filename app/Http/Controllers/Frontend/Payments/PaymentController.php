<?php

/**
 * PaymentController
 *
 * Handles payment gateway return callbacks, Stripe confirmation, and order completion.
 *
 * Active gateway: PayFlowPro (PayPal Payflow Pro)
 * Secondary gateway: Stripe (wired and ready; not yet live)
 *
 * Payment flow:
 *   PayFlowPro: POST /payments/{payment}/return → handleReturn()
 *   Stripe:     POST /payments/stripe/{payment}/confirm → confirmStripe()
 */

namespace App\Http\Controllers\Frontend\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Events\Payment\PaymentCompleted;
use App\Events\Payment\PaymentFailed;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Show PayFlowPro payment form
     */
    public function showPayFlowPro(Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        $content = self::renderPageMeta('PayFlowPro Payment');
        $order = $payment->order;
        $course = $order->course;

        Log::info("Showing PayFlowPro payment form for payment: {$payment->id}");

        // TODO: Implement actual PayFlowPro integration
        // For now, show a test payment page
        return view('frontend.payments.payflowpro', compact('content', 'payment', 'order', 'course'));
    }

    /**
     * Process PayFlowPro payment (dev/test form handler)
     */
    public function processPayFlowPro(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        Log::info('ProcessPayFlowPro (test handler) called', ['payment_id' => $payment->id]);

        $payment->update([
            'status'         => 'completed',
            'transaction_id' => 'TEST_' . time(),
            'processed_at'   => now(),
        ]);

        $order = $payment->order;
        $order->SetCompleted();

        event(new PaymentCompleted($order->fresh(), $payment->fresh()));

        return redirect()->route('order.completed', $order)
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Show Stripe payment form
     */
    public function showStripe(Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        $content = self::renderPageMeta('Stripe Payment');
        $order = $payment->order;
        $course = $order->course;

        Log::info("Showing Stripe payment form for payment: {$payment->id}");

        return view('frontend.payments.stripe', compact('content', 'payment', 'order', 'course'));
    }

    /**
     * Create Stripe PaymentIntent
     */
    public function createStripeIntent(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // TODO: Replace with actual Stripe API call
            // For now, return a mock client secret in proper Stripe format
            // Format: pi_{alphanumeric}_secret_{alphanumeric}
            $piId = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(16))), 0, 24);
            $secretPart = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(24))), 0, 32);
            $clientSecret = 'pi_' . $piId . '_secret_' . $secretPart;

            Log::info('Stripe PaymentIntent created (mock)', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'client_secret_format' => 'pi_xxx_secret_xxx',
            ]);

            return response()->json([
                'clientSecret' => $clientSecret,
            ]);

            /*
            // Production implementation:
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $payment->amount * 100, // Amount in cents
                'currency' => strtolower($payment->currency),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'user_id' => auth()->id(),
                ],
                'description' => 'Course enrollment: ' . $payment->order->course->name,
                'receipt_email' => $request->input('email'),
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
            */
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to initialize payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Confirm Stripe payment
     */
    public function confirmStripe(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method' => 'nullable|string',
        ]);

        $order = $payment->order;

        try {
            $payment->update([
                'status'           => 'completed',
                'transaction_id'   => $validated['payment_intent_id'],
                'gateway_response' => [
                    'payment_intent' => $validated['payment_intent_id'],
                    'payment_method' => $validated['payment_method'] ?? null,
                ],
                'processed_at' => now(),
            ]);

            $order->SetCompleted();

            event(new PaymentCompleted($order->fresh(), $payment->fresh()));

            Log::info('Stripe payment confirmed', [
                'payment_id'     => $payment->id,
                'transaction_id' => $validated['payment_intent_id'],
                'order_id'       => $order->id,
            ]);

            return response()->json([
                'success'      => true,
                'redirect_url' => route('order.completed', $order),
            ]);
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);

            event(new PaymentFailed($order, $payment->fresh(), $e->getMessage()));

            Log::error('Stripe payment confirmation failed', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to confirm payment. Please contact support.',
            ], 500);
        }
    }

    /**
     * Show PayPal payment form
     */
    public function showPayPal(Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        $content = self::renderPageMeta('PayPal Payment');
        $order = $payment->order;
        $course = $order->course;

        Log::info("Showing PayPal payment form for payment: {$payment->id}");

        // TODO: Implement actual PayPal integration
        return view('frontend.payments.paypal', compact('content', 'payment', 'order', 'course'));
    }

    /**
     * Process PayPal payment (stub — awaiting PayPal Checkout integration)
     */
    public function processPayPal(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        Log::info('ProcessPayPal called', ['payment_id' => $payment->id, 'data' => $request->all()]);

        $payment->update([
            'status'         => 'completed',
            'transaction_id' => 'PAYPAL_' . time(),
            'processed_at'   => now(),
        ]);

        $order = $payment->order;
        $order->SetCompleted();

        event(new PaymentCompleted($order->fresh(), $payment->fresh()));

        return redirect()->route('order.completed', $order)
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Handle PayFlowPro return callback
     *
     * PayPal posts RESULT, PNREF, PPREF, RESPMSG (and other fields) to this route.
     * RESULT=0 means approved; anything else is a decline or error.
     */
    public function handleReturn(Request $request, Payment $payment)
    {
        $result  = (int) $request->input('RESULT', -1);
        $pnref   = $request->input('PNREF');
        $ppref   = $request->input('PPREF');
        $respmsg = $request->input('RESPMSG', 'Unknown error');

        $order = $payment->order;

        Log::info('PayFlowPro return received', [
            'payment_id' => $payment->id,
            'order_id'   => $order->id,
            'RESULT'     => $result,
            'PNREF'      => $pnref,
            'RESPMSG'    => $respmsg,
        ]);

        if ($result === 0) {

            // Payment approved
            $payment->update([
                'status'           => 'completed',
                'transaction_id'   => $pnref,
                'gateway_response' => [
                    'RESULT'  => $result,
                    'PNREF'   => $pnref,
                    'PPREF'   => $ppref,
                    'RESPMSG' => $respmsg,
                ],
                'processed_at' => now(),
            ]);

            $order->SetCompleted();

            event(new PaymentCompleted($order->fresh(), $payment->fresh()));

            return redirect()->route('order.completed', $order)
                ->with('success', 'Payment completed successfully!');
        }

        // Payment declined or errored
        $payment->update([
            'status'           => 'failed',
            'gateway_response' => [
                'RESULT'  => $result,
                'PNREF'   => $pnref,
                'RESPMSG' => $respmsg,
            ],
        ]);

        event(new PaymentFailed($order, $payment->fresh(), $respmsg));

        Log::warning('PayFlowPro payment declined', [
            'payment_id' => $payment->id,
            'order_id'   => $order->id,
            'RESULT'     => $result,
            'RESPMSG'    => $respmsg,
        ]);

        return redirect()->route('courses.enroll', $order->course_id)
            ->with('error', 'Payment was not completed: ' . $respmsg);
    }

    /**
     * Get payment token (AJAX)
     */
    public function getToken(Payment $payment)
    {
        // TODO: Implement actual token generation
        return response()->json([
            'token' => 'test_token',
            'token_id' => 'test_token_id'
        ]);
    }

    /**
     * Show order completed page
     */
    public function orderCompleted(Order $order)
    {
        // Verify user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order');
        }

        $content = self::renderPageMeta('Order Completed');
        $course = $order->course;

        return view('frontend.orders.completed', compact('content', 'order', 'course'));
    }
}
