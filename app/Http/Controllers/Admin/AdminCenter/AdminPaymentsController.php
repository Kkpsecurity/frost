<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Akaunting\Setting\Facade as Setting;

/**
 * AdminPaymentsController
 * Handles payment method configuration and management
 */
class AdminPaymentsController extends Controller
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
        $config = [
            'enabled' => Setting::get('payments.paypal.enabled', false),
            'environment' => Setting::get('payments.paypal.mode', 'sandbox'), // sandbox or live
            'client_id' => Setting::get('payments.paypal.client_id', ''),
            'client_secret' => Setting::get('payments.paypal.client_secret', ''),
            'webhook_url' => Setting::get('payments.paypal.webhook_url', ''),
            'connection_status' => Setting::get('payments.paypal.connection_status', 'Not Tested'),
            'last_test' => Setting::get('payments.paypal.last_test', null)
        ];

        return view('admin.admin-center.payments.paypal', compact('config'));
    }

    /**
     * Update PayPal configuration
     */
    public function updatePaypal(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'environment' => 'required|in:sandbox,live',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'webhook_url' => 'nullable|url',
        ]);

        // Save PayPal settings
        Setting::set('payments.paypal.enabled', (bool) $request->get('enabled', false));
        Setting::set('payments.paypal.mode', $request->get('environment'));
        Setting::set('payments.paypal.client_id', $request->get('client_id'));
        Setting::set('payments.paypal.client_secret', $request->get('client_secret'));
        Setting::set('payments.paypal.webhook_url', $request->get('webhook_url'));

        return redirect()->route('admin.payments.paypal')
            ->with('success', 'PayPal settings updated successfully.');
    }

    /**
     * Show Stripe configuration
     */
    public function stripe()
    {
        $config = [
            'enabled' => Setting::get('payments.stripe.enabled', false),
            'environment' => Setting::get('payments.stripe.environment', 'test'),
            'test_publishable_key' => Setting::get('payments.stripe.test_publishable_key', ''),
            'test_secret_key' => Setting::get('payments.stripe.test_secret_key', ''),
            'live_publishable_key' => Setting::get('payments.stripe.live_publishable_key', ''),
            'live_secret_key' => Setting::get('payments.stripe.live_secret_key', ''),
            'webhook_endpoint' => Setting::get('payments.stripe.webhook_endpoint', ''),
            'webhook_secret' => Setting::get('payments.stripe.webhook_secret', ''),
            'connection_status' => Setting::get('payments.stripe.connection_status', 'Not Tested'),
            'last_test' => Setting::get('payments.stripe.last_test', null)
        ];

        return view('admin.admin-center.payments.stripe', compact('config'));
    }

    /**
     * Update Stripe configuration
     */
    public function updateStripe(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|boolean',
            'environment' => 'required|in:test,live',
            'test_publishable_key' => 'nullable|string|starts_with:pk_test_',
            'test_secret_key' => 'nullable|string|starts_with:sk_test_',
            'live_publishable_key' => 'nullable|string|starts_with:pk_live_',
            'live_secret_key' => 'nullable|string|starts_with:sk_live_',
            'webhook_endpoint' => 'nullable|url',
            'webhook_secret' => 'nullable|string|starts_with:whsec_',
        ]);

        // Save Stripe settings
        Setting::set('payments.stripe.enabled', (bool) $request->get('enabled', false));
        Setting::set('payments.stripe.environment', $request->get('environment'));
        Setting::set('payments.stripe.test_publishable_key', $request->get('test_publishable_key'));
        Setting::set('payments.stripe.test_secret_key', $request->get('test_secret_key'));
        Setting::set('payments.stripe.live_publishable_key', $request->get('live_publishable_key'));
        Setting::set('payments.stripe.live_secret_key', $request->get('live_secret_key'));
        Setting::set('payments.stripe.webhook_endpoint', $request->get('webhook_endpoint'));
        Setting::set('payments.stripe.webhook_secret', $request->get('webhook_secret'));

        return redirect()->route('admin.payments.stripe')
            ->with('success', 'Stripe settings updated successfully.');
    }

    /**
     * Test payment method connection
     */
    public function testConnection(Request $request)
    {
        $method = $request->input('method');

        try {
            switch ($method) {
                case 'paypal':
                    return $this->testPayPalConnection();
                case 'stripe':
                    return $this->testStripeConnection();
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid payment method']);
            }
        } catch (\Exception $e) {
            // Update failed connection status
            if ($method === 'paypal') {
                Setting::set('payments.paypal.connection_status', 'Failed');
                Setting::set('payments.paypal.last_test', now()->format('Y-m-d H:i:s'));
            } elseif ($method === 'stripe') {
                Setting::set('payments.stripe.connection_status', 'Failed');
                Setting::set('payments.stripe.last_test', now()->format('Y-m-d H:i:s'));
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'error_details' => [
                    'error' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
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
     * Test PayPal connection with real API
     */
    private function testPayPalConnection()
    {
        // Get PayPal configuration
        $clientId = Setting::get('payments.paypal.client_id');
        $clientSecret = Setting::get('payments.paypal.client_secret');
        $mode = Setting::get('payments.paypal.mode', 'sandbox');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('PayPal credentials not configured. Please enter Client ID and Client Secret.');
        }

        // PayPal API endpoint
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Get access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception('CURL error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error_description'])
                ? $errorData['error_description']
                : 'PayPal API authentication failed. HTTP Code: ' . $httpCode;
            throw new \Exception($errorMessage);
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            throw new \Exception('Failed to get PayPal access token. Invalid response from PayPal API.');
        }

        // Update connection status
        Setting::set('payments.paypal.connection_status', 'Connected');
        Setting::set('payments.paypal.last_test', now()->format('Y-m-d H:i:s'));

        return response()->json([
            'success' => true,
            'message' => 'PayPal connection successful! API authentication completed.',
            'details' => [
                'Environment' => ucfirst($mode),
                'Token Type' => $data['token_type'] ?? 'Bearer',
                'Expires In' => ($data['expires_in'] ?? 0) . ' seconds',
                'API Version' => 'v1',
                'Scope' => $data['scope'] ?? 'Default'
            ]
        ]);
    }    /**
     * Test Stripe connection
     */
    private function testStripeConnection()
    {
        // Get Stripe configuration
        $environment = Setting::get('payments.stripe.environment', 'test');
        $secretKey = $environment === 'test'
            ? Setting::get('payments.stripe.test_secret_key')
            : Setting::get('payments.stripe.live_secret_key');

        if (empty($secretKey)) {
            throw new \Exception('Stripe secret key not configured for ' . $environment . ' environment. Please enter your ' . $environment . ' secret key.');
        }

        // Validate secret key format
        $expectedPrefix = $environment === 'test' ? 'sk_test_' : 'sk_live_';
        if (!str_starts_with($secretKey, $expectedPrefix)) {
            throw new \Exception('Invalid Stripe secret key format. ' . ucfirst($environment) . ' keys should start with "' . $expectedPrefix . '"');
        }

        // Initialize Stripe
        \Stripe\Stripe::setApiKey($secretKey);

        // Test API call - retrieve account info
        $account = \Stripe\Account::retrieve();

        // Update connection status
        Setting::set('payments.stripe.connection_status', 'Connected');
        Setting::set('payments.stripe.last_test', now()->format('Y-m-d H:i:s'));

        return response()->json([
            'success' => true,
            'message' => 'Stripe connection successful! Account details retrieved.',
            'details' => [
                'Environment' => ucfirst($environment) . ' Mode',
                'Account ID' => $account->id,
                'Country' => strtoupper($account->country),
                'Currency' => strtoupper($account->default_currency),
                'Business Type' => ucfirst($account->business_type ?? 'individual'),
                'Charges Enabled' => $account->charges_enabled ? 'Yes' : 'No',
                'Payouts Enabled' => $account->payouts_enabled ? 'Yes' : 'No',
                'Details Submitted' => $account->details_submitted ? 'Yes' : 'No'
            ]
        ]);
    }
}
