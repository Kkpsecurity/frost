<?php

namespace App\Http\Controllers\Frontend\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Display checkout page with payment method selection
     */
    public function show(Order $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Check if order is already paid
        if ($order->status === 'paid') {
            return redirect()->route('order.completed', $order)
                ->with('warning', 'This order has already been paid.');
        }

        // Load course relationship
        $order->load('course');

        // Get available payment methods
        $paymentTypes = PaymentType::where('is_active', true)->get();

        return view('frontend.checkout.show', [
            'order' => $order,
            'course' => $order->course,
            'paymentTypes' => $paymentTypes,
        ]);
    }

    /**
     * Process payment method selection and create payment
     */
    public function processPayment(Request $request, Order $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Validate payment method
        $validated = $request->validate([
            'payment_method' => 'required|string|in:paypal,stripe,payflowpro',
        ]);

        try {
            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'gateway' => $validated['payment_method'],
                'amount' => $order->total,
                'currency' => 'USD',
                'status' => 'pending',
            ]);

            Log::info("Payment created with ID: {$payment->id} using method: {$validated['payment_method']}");

            // Redirect based on payment method
            switch ($validated['payment_method']) {
                case 'payflowpro':
                    return redirect()->route('payments.payflowpro', $payment);

                case 'paypal':
                    // TODO: Implement PayPal redirect
                    return redirect()->route('payments.paypal', $payment);

                case 'stripe':
                    // TODO: Implement Stripe redirect
                    return redirect()->route('payments.stripe', $payment);

                default:
                    return redirect()->route('checkout.show', $order)
                        ->with('error', 'Invalid payment method selected.');
            }
        } catch (\Exception $e) {
            Log::error("Payment creation error for order {$order->id}: " . $e->getMessage());

            return redirect()->route('checkout.show', $order)
                ->with('error', 'Unable to process payment. Please try again.');
        }
    }
}
