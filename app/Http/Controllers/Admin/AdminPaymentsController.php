<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPaymentsController extends Controller
{
    public function index()
    {
        // Get payment methods configuration
        $paymentMethods = [
            'paypal' => [
                'name' => 'PayPal',
                'enabled' => setting('payment.paypal.enabled', false),
                'configured' => $this->isPayPalConfigured(),
                'icon' => 'fab fa-paypal',
                'color' => 'primary',
                'description' => 'Accept payments through PayPal'
            ],
            'stripe' => [
                'name' => 'Stripe',
                'enabled' => setting('payment.stripe.enabled', false),
                'configured' => $this->isStripeConfigured(),
                'icon' => 'fab fa-stripe',
                'color' => 'info',
                'description' => 'Accept credit/debit card payments'
            ]
        ];

        return view('admin.admin-center.payments.index', compact('paymentMethods'));
    }

    public function paypal()
    {
        $config = [
            'enabled' => setting('payment.paypal.enabled', false),
            'environment' => setting('payment.paypal.environment', 'sandbox'),
            'client_id' => setting('payment.paypal.client_id', ''),
            'client_secret' => setting('payment.paypal.client_secret', ''),
            'webhook_url' => setting('payment.paypal.webhook_url', ''),
            'connection_status' => setting('payment.paypal.connection_status', 'Not Tested'),
            'last_test' => setting('payment.paypal.last_test', null)
        ];

        return view('admin.admin-center.payments.paypal', compact('config'));
    }

    public function stripe()
    {
        $config = [
            'enabled' => setting('payment.stripe.enabled', false),
            'environment' => setting('payment.stripe.environment', 'test'),
            'test_publishable_key' => setting('payment.stripe.test_publishable_key', ''),
            'test_secret_key' => setting('payment.stripe.test_secret_key', ''),
            'live_publishable_key' => setting('payment.stripe.live_publishable_key', ''),
            'live_secret_key' => setting('payment.stripe.live_secret_key', ''),
            'webhook_endpoint' => setting('payment.stripe.webhook_endpoint', ''),
            'webhook_secret' => setting('payment.stripe.webhook_secret', ''),
            'connection_status' => setting('payment.stripe.connection_status', 'Not Tested'),
            'last_test' => setting('payment.stripe.last_test', null)
        ];

        return view('admin.admin-center.payments.stripe', compact('config'));
    }

    public function updatePayPal(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'environment' => 'required|in:sandbox,live',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'webhook_url' => 'nullable|url'
        ]);

        // Save PayPal settings
        setting([
            'payment.paypal.enabled' => $request->boolean('enabled'),
            'payment.paypal.environment' => $request->environment,
            'payment.paypal.client_id' => $request->client_id,
            'payment.paypal.client_secret' => $request->client_secret,
            'payment.paypal.webhook_url' => $request->webhook_url
        ])->save();

        return redirect()->route('admin.payments.paypal')
            ->with('success', 'PayPal configuration updated successfully!');
    }

    public function updateStripe(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'environment' => 'required|in:test,live',
            'test_publishable_key' => 'nullable|string|starts_with:pk_test_',
            'test_secret_key' => 'nullable|string|starts_with:sk_test_',
            'live_publishable_key' => 'nullable|string|starts_with:pk_live_',
            'live_secret_key' => 'nullable|string|starts_with:sk_live_',
            'webhook_endpoint' => 'nullable|url',
            'webhook_secret' => 'nullable|string|starts_with:whsec_'
        ]);

        // Save Stripe settings
        setting([
            'payment.stripe.enabled' => $request->boolean('enabled'),
            'payment.stripe.environment' => $request->environment,
            'payment.stripe.test_publishable_key' => $request->test_publishable_key,
            'payment.stripe.test_secret_key' => $request->test_secret_key,
            'payment.stripe.live_publishable_key' => $request->live_publishable_key,
            'payment.stripe.live_secret_key' => $request->live_secret_key,
            'payment.stripe.webhook_endpoint' => $request->webhook_endpoint,
            'payment.stripe.webhook_secret' => $request->webhook_secret
        ])->save();

        return redirect()->route('admin.payments.stripe')
            ->with('success', 'Stripe configuration updated successfully!');
    }

    public function testConnection(Request $request)
    {
        $method = $request->input('method');

        try {
            if ($method === 'paypal') {
                return $this->testPayPalConnection();
            } elseif ($method === 'stripe') {
                return $this->testStripeConnection();
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method'
            ], 400);

        } catch (\Exception $e) {
            // Update failed connection status
            if ($method === 'paypal') {
                setting([
                    'payment.paypal.connection_status' => 'Failed',
                    'payment.paypal.last_test' => now()->format('Y-m-d H:i:s')
                ])->save();
            } elseif ($method === 'stripe') {
                setting([
                    'payment.stripe.connection_status' => 'Failed',
                    'payment.stripe.last_test' => now()->format('Y-m-d H:i:s')
                ])->save();
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

    private function testPayPalConnection()
    {
        // Get PayPal configuration
        $environment = setting('payment.paypal.environment', 'sandbox');
        $clientId = setting('payment.paypal.client_id');
        $clientSecret = setting('payment.paypal.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('PayPal credentials not configured. Please enter Client ID and Client Secret.');
        }

        // PayPal API endpoint
        $baseUrl = $environment === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

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
        setting([
            'payment.paypal.connection_status' => 'Connected',
            'payment.paypal.last_test' => now()->format('Y-m-d H:i:s')
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'PayPal connection successful! API authentication completed.',
            'details' => [
                'Environment' => ucfirst($environment),
                'Token Type' => $data['token_type'] ?? 'Bearer',
                'Expires In' => ($data['expires_in'] ?? 0) . ' seconds',
                'API Version' => 'v1',
                'Scope' => $data['scope'] ?? 'Default'
            ]
        ]);
    }

    private function testStripeConnection()
    {
        // Get Stripe configuration
        $environment = setting('payment.stripe.environment', 'test');
        $secretKey = $environment === 'test'
            ? setting('payment.stripe.test_secret_key')
            : setting('payment.stripe.live_secret_key');

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
        setting([
            'payment.stripe.connection_status' => 'Connected',
            'payment.stripe.last_test' => now()->format('Y-m-d H:i:s')
        ])->save();

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

    private function isPayPalConfigured()
    {
        return !empty(setting('payment.paypal.client_id')) &&
               !empty(setting('payment.paypal.client_secret'));
    }

    private function isStripeConfigured()
    {
        $environment = setting('payment.stripe.environment', 'test');

        if ($environment === 'test') {
            return !empty(setting('payment.stripe.test_secret_key'));
        } else {
            return !empty(setting('payment.stripe.live_secret_key'));
        }
    }
}
