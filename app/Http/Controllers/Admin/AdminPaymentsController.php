<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Akaunting\Setting\Facade as Setting;

/**
 * AdminPaymentsController
 * Manages Stripe and PayPal gateway configuration from the admin panel.
 */
class AdminPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Payment settings overview
     */
    public function index(): View
    {
        return view('admin.payments.index', [
            'pageTitle'       => 'Payment Settings',
            'stripeConfigured' => !empty(setting('payments.stripe.test_secret_key'))
                || !empty(setting('payments.stripe.live_secret_key')),
            'paypalConfigured' => !empty(setting('payments.paypal.client_id')),
        ]);
    }

    /**
     * Stripe configuration form
     */
    public function stripe(): View
    {
        return view('admin.payments.stripe', [
            'pageTitle'          => 'Stripe Configuration',
            'testPublishableKey' => setting('payments.stripe.test_publishable_key') ?? '',
            'livePublishableKey' => setting('payments.stripe.live_publishable_key') ?? '',
            'hasTestSecretKey'   => !empty(setting('payments.stripe.test_secret_key')),
            'hasLiveSecretKey'   => !empty(setting('payments.stripe.live_secret_key')),
            'mode'               => setting('payments.stripe.mode') ?? 'test',
        ]);
    }

    /**
     * Save Stripe configuration
     */
    public function updateStripe(Request $request): RedirectResponse
    {
        $request->validate([
            'test_publishable_key' => ['nullable', 'string', 'max:255', 'regex:/^pk_test_/'],
            'test_secret_key'      => ['nullable', 'string', 'max:255', 'regex:/^sk_test_/'],
            'live_publishable_key' => ['nullable', 'string', 'max:255', 'regex:/^pk_live_/'],
            'live_secret_key'      => ['nullable', 'string', 'max:255', 'regex:/^sk_live_/'],
            'mode'                 => ['required', 'in:test,live'],
        ]);

        Setting::set('payments.stripe.mode', $request->input('mode'));
        Setting::set('payments.stripe.test_publishable_key', $request->input('test_publishable_key', ''));
        Setting::set('payments.stripe.live_publishable_key', $request->input('live_publishable_key', ''));

        // Only overwrite secret keys if a new value is provided
        if ($request->filled('test_secret_key')) {
            Setting::set('payments.stripe.test_secret_key', $request->input('test_secret_key'));
        }
        if ($request->filled('live_secret_key')) {
            Setting::set('payments.stripe.live_secret_key', $request->input('live_secret_key'));
        }

        return redirect()->route('admin.payments.stripe')
            ->with('success', 'Stripe configuration saved.');
    }

    /**
     * PayPal configuration form
     */
    public function paypal(): View
    {
        return view('admin.payments.paypal', [
            'pageTitle'       => 'PayPal Configuration',
            'hasClientId'     => !empty(setting('payments.paypal.client_id')),
            'hasClientSecret' => !empty(setting('payments.paypal.client_secret')),
            'mode'            => setting('payments.paypal.mode') ?? 'sandbox',
        ]);
    }

    /**
     * Save PayPal configuration
     */
    public function updatePayPal(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id'     => ['nullable', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string', 'max:255'],
            'mode'          => ['required', 'in:sandbox,live'],
        ]);

        Setting::set('payments.paypal.mode', $request->input('mode'));

        if ($request->filled('client_id')) {
            Setting::set('payments.paypal.client_id', $request->input('client_id'));
        }
        if ($request->filled('client_secret')) {
            Setting::set('payments.paypal.client_secret', $request->input('client_secret'));
        }

        return redirect()->route('admin.payments.paypal')
            ->with('success', 'PayPal configuration saved.');
    }

    /**
     * Test gateway connectivity using the stored secret key
     */
    public function testConnection(Request $request): JsonResponse
    {
        $request->validate([
            'gateway' => ['required', 'in:stripe,paypal'],
        ]);

        if ($request->input('gateway') === 'stripe') {
            $mode      = setting('payments.stripe.mode') ?? 'test';
            $secretKey = $mode === 'live'
                ? setting('payments.stripe.live_secret_key')
                : setting('payments.stripe.test_secret_key');

            if (empty($secretKey)) {
                return response()->json(['success' => false, 'message' => 'No Stripe secret key configured.']);
            }

            try {
                \Stripe\Stripe::setApiKey($secretKey);
                \Stripe\Balance::retrieve();
                return response()->json(['success' => true, 'message' => 'Stripe connection successful.']);
            } catch (\Stripe\Exception\AuthenticationException $e) {
                return response()->json(['success' => false, 'message' => 'Invalid Stripe key: ' . $e->getMessage()]);
            } catch (\Exception $e) {
                Log::warning('Stripe connection test failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'PayPal connection test not yet implemented.']);
    }
}
