<?php

/**
 * ⚠️ TEMPORARY TEST CONTROLLER - TO BE REMOVED ⚠️
 *
 * Created: February 10, 2026
 * Purpose: Stub controller for enrollment flow testing
 *
 * DO NOT USE IN PRODUCTION
 *
 * See: docs/tasks/TEMPORARY_TEST_FILES.md for removal instructions
 *
 * This controller should be replaced with proper payment gateway integration.
 * Archived full implementation available at:
 * app/Http/Controllers/Archived/Web_Payments/PayFlowProController.php
 *
 * Missing implementations:
 * - Payment gateway API integration (PayFlowPro/Stripe)
 * - Secure token generation and validation
 * - Transaction amount verification
 * - Webhook handlers
 * - Payment notification dispatching
 * - Error handling and logging
 * - Security validations
 */

namespace App\Http\Controllers\Frontend\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
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
     * Process PayFlowPro payment
     */
    public function processPayFlowPro(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        // TODO: Implement actual PayFlowPro processing
        Log::info('Processing PayFlowPro payment', ['payment_id' => $payment->id, 'data' => $request->all()]);

        // For now, mark as completed
        $payment->update([
            'status' => 'completed',
            'transaction_id' => 'TEST_' . time(),
        ]);

        $payment->order->update(['status' => 'paid']);

        return redirect()->route('order.completed', $payment->order)
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

        try {
            // Update payment record
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $validated['payment_intent_id'],
                'gateway_response' => json_encode([
                    'payment_intent' => $validated['payment_intent_id'],
                    'payment_method' => $validated['payment_method'] ?? null,
                    'completed_at' => now()->toIso8601String(),
                ]),
            ]);

            // Update order status
            $payment->order->update(['status' => 'paid']);

            Log::info('Stripe payment confirmed', [
                'payment_id' => $payment->id,
                'transaction_id' => $validated['payment_intent_id'],
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('order.completed', $payment->order),
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe payment confirmation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
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
     * Process PayPal payment
     */
    public function processPayPal(Request $request, Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        // TODO: Implement actual PayPal processing
        Log::info('Processing PayPal payment', ['payment_id' => $payment->id, 'data' => $request->all()]);

        // For now, mark as completed
        $payment->update([
            'status' => 'completed',
            'transaction_id' => 'PAYPAL_' . time(),
        ]);

        $payment->order->update(['status' => 'paid']);

        return redirect()->route('order.completed', $payment->order)
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Handle payment return/callback
     */
    public function handleReturn(Request $request, Payment $payment)
    {
        // TODO: Implement actual payment processing
        Log::info('Payment return received', ['payment_id' => $payment->id, 'data' => $request->all()]);

        return redirect()->route('order.completed', $payment->order);
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
