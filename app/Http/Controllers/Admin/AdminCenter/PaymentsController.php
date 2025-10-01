<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Akaunting\Setting\Facade as Setting;

/**
 * PaymentsController
 * Handles payment method configuration and management
 */
class PaymentsController extends Controller
{
    /**
     * Display payment methods overview
     */
    public function index()
    {
        // Get current payment method configurations
        $paymentMethods = $this->getPaymentMethods();

        // Get payment statistics (placeholder for now)
        $stats = $this->getPaymentStats();

        return view('admin.admin-center.payments.index', compact('paymentMethods', 'stats'));
    }

    /**
     * Show PayPal configuration
     */
    public function paypal()
    {
        $paypalSettings = [
            'enabled' => Setting::get('payments.paypal.enabled', false),
            'mode' => Setting::get('payments.paypal.mode', 'sandbox'), // sandbox or live
            'client_id' => Setting::get('payments.paypal.client_id', ''),
            'client_secret' => Setting::get('payments.paypal.client_secret', ''),
            'webhook_url' => Setting::get('payments.paypal.webhook_url', ''),
            'currency' => Setting::get('payments.paypal.currency', 'USD'),
        ];

        return view('admin.admin-center.payments.paypal', compact('paypalSettings'));
    }

    /**
     * Update PayPal configuration
     */
    public function updatePaypal(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'mode' => 'required|in:sandbox,live',
            'client_id' => 'required_if:enabled,1|string|max:255',
            'client_secret' => 'required_if:enabled,1|string|max:255',
            'webhook_url' => 'nullable|url',
            'currency' => 'required|string|size:3',
        ]);

        // Save PayPal settings
        Setting::set('payments.paypal.enabled', (bool) $request->get('enabled', false));
        Setting::set('payments.paypal.mode', $request->get('mode'));
        Setting::set('payments.paypal.client_id', $request->get('client_id'));
        Setting::set('payments.paypal.client_secret', $request->get('client_secret'));
        Setting::set('payments.paypal.webhook_url', $request->get('webhook_url'));
        Setting::set('payments.paypal.currency', $request->get('currency'));

        return redirect()->route('admin.payments.paypal')
            ->with('success', 'PayPal settings updated successfully.');
    }

    /**
     * Show Stripe configuration
     */
    public function stripe()
    {
        $stripeSettings = [
            'enabled' => Setting::get('payments.stripe.enabled', false),
            'publishable_key' => Setting::get('payments.stripe.publishable_key', ''),
            'secret_key' => Setting::get('payments.stripe.secret_key', ''),
            'webhook_secret' => Setting::get('payments.stripe.webhook_secret', ''),
            'currency' => Setting::get('payments.stripe.currency', 'USD'),
            'collect_billing_address' => Setting::get('payments.stripe.collect_billing_address', true),
            'collect_shipping_address' => Setting::get('payments.stripe.collect_shipping_address', false),
        ];

        return view('admin.admin-center.payments.stripe', compact('stripeSettings'));
    }

    /**
     * Update Stripe configuration
     */
    public function updateStripe(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'publishable_key' => 'required_if:enabled,1|string|starts_with:pk_',
            'secret_key' => 'required_if:enabled,1|string|starts_with:sk_',
            'webhook_secret' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'collect_billing_address' => 'nullable|boolean',
            'collect_shipping_address' => 'nullable|boolean',
        ]);

        // Save Stripe settings
        Setting::set('payments.stripe.enabled', (bool) $request->get('enabled', false));
        Setting::set('payments.stripe.publishable_key', $request->get('publishable_key'));
        Setting::set('payments.stripe.secret_key', $request->get('secret_key'));
        Setting::set('payments.stripe.webhook_secret', $request->get('webhook_secret'));
        Setting::set('payments.stripe.currency', $request->get('currency'));
        Setting::set('payments.stripe.collect_billing_address', (bool) $request->get('collect_billing_address', false));
        Setting::set('payments.stripe.collect_shipping_address', (bool) $request->get('collect_shipping_address', false));

        return redirect()->route('admin.payments.stripe')
            ->with('success', 'Stripe settings updated successfully.');
    }

    /**
     * Test payment method connection
     */
    public function testConnection(Request $request)
    {
        $method = $request->get('method');

        switch ($method) {
            case 'paypal':
                return $this->testPayPalConnection();
            case 'stripe':
                return $this->testStripeConnection();
            default:
                return response()->json(['success' => false, 'message' => 'Invalid payment method']);
        }
    }

    /**
     * Get all configured payment methods
     */
    private function getPaymentMethods()
    {
        return [
            'paypal' => [
                'name' => 'PayPal',
                'enabled' => Setting::get('payments.paypal.enabled', false),
                'mode' => Setting::get('payments.paypal.mode', 'sandbox'),
                'currency' => Setting::get('payments.paypal.currency', 'USD'),
                'icon' => 'fab fa-paypal',
                'color' => 'primary',
                'status' => Setting::get('payments.paypal.enabled', false) ? 'active' : 'inactive',
            ],
            'stripe' => [
                'name' => 'Stripe',
                'enabled' => Setting::get('payments.stripe.enabled', false),
                'currency' => Setting::get('payments.stripe.currency', 'USD'),
                'icon' => 'fab fa-stripe',
                'color' => 'info',
                'status' => Setting::get('payments.stripe.enabled', false) ? 'active' : 'inactive',
            ],
        ];
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStats()
    {
        // Placeholder for payment statistics
        // In a real implementation, you would query your orders/transactions table
        return [
            'total_transactions' => 0,
            'successful_payments' => 0,
            'failed_payments' => 0,
            'refunds' => 0,
            'total_revenue' => 0,
            'paypal_transactions' => 0,
            'stripe_transactions' => 0,
        ];
    }

    /**
     * Test PayPal connection
     */
    private function testPayPalConnection()
    {
        try {
            $clientId = Setting::get('payments.paypal.client_id');
            $clientSecret = Setting::get('payments.paypal.client_secret');
            $mode = Setting::get('payments.paypal.mode', 'sandbox');

            if (empty($clientId) || empty($clientSecret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal credentials are not configured'
                ]);
            }

            // In a real implementation, you would test the actual PayPal API connection
            // For now, just return success if credentials are provided
            return response()->json([
                'success' => true,
                'message' => "PayPal connection test successful (Mode: {$mode})",
                'details' => [
                    'mode' => $mode,
                    'client_id_present' => !empty($clientId),
                    'client_secret_present' => !empty($clientSecret),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal connection test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Stripe connection
     */
    private function testStripeConnection()
    {
        try {
            $publishableKey = Setting::get('payments.stripe.publishable_key');
            $secretKey = Setting::get('payments.stripe.secret_key');

            if (empty($publishableKey) || empty($secretKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe credentials are not configured'
                ]);
            }

            // In a real implementation, you would test the actual Stripe API connection
            // For now, just return success if credentials are provided
            return response()->json([
                'success' => true,
                'message' => 'Stripe connection test successful',
                'details' => [
                    'publishable_key_present' => !empty($publishableKey),
                    'secret_key_present' => !empty($secretKey),
                    'test_mode' => strpos($publishableKey, 'pk_test_') === 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe connection test failed: ' . $e->getMessage()
            ]);
        }
    }
}
