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
     * Show the payment page
     */
    public function show(Payment $payment)
    {
        // Verify user owns this payment
        if ($payment->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to payment');
        }

        $content = self::renderPageMeta('Payment');
        $order = $payment->order;
        $course = $order->course;

        // TODO: Implement actual PayFlowPro integration
        // For now, show a test payment page

        return view('frontend.payments.test-payment', compact('content', 'payment', 'order', 'course'));
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
