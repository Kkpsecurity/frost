# Payment Notifications Integration Guide

## Overview

This guide explains how to integrate the payment notification system with Stripe and PayPal webhooks for the Frost Security Training Platform.

## Payment Notification Classes

All payment notifications are located in `app/Notifications/Payment/` and implement `ShouldQueue` for async processing.

### Available Notifications

| Notification                        | Trigger                 | Channels       | User Controllable |
| ----------------------------------- | ----------------------- | -------------- | ----------------- |
| `PaymentSuccessNotification`        | Payment completed       | Database, Mail | Yes               |
| `PaymentFailedNotification`         | Payment failed          | Database, Mail | No (Critical)     |
| `PaymentPendingNotification`        | Payment processing      | Database, Mail | Yes               |
| `PaymentMethodAddedNotification`    | Payment method added    | Database, Mail | Yes               |
| `PaymentMethodRemovedNotification`  | Payment method removed  | Database, Mail | Yes               |
| `PaymentMethodExpiringNotification` | Card expiring soon      | Database, Mail | Yes               |
| `DefaultPaymentUpdatedNotification` | Default payment changed | Database, Mail | Yes               |
| `RefundInitiatedNotification`       | Refund started          | Database, Mail | No (Critical)     |
| `RefundProcessedNotification`       | Refund completed        | Database, Mail | No (Critical)     |
| `InvoiceGeneratedNotification`      | Invoice created         | Database, Mail | Yes               |
| `ReceiptEmailedNotification`        | Receipt sent            | Database       | Yes               |
| `BalanceDueNotification`            | Outstanding balance     | Database, Mail | No (Critical)     |

## Event System

### Payment Events

Located in `app/Events/Payment/`:

```php
// When payment is completed
event(new \App\Events\Payment\PaymentCompleted($order, $payment));

// When payment fails
event(new \App\Events\Payment\PaymentFailed($order, $payment, 'Reason for failure'));

// When payment is processing
event(new \App\Events\Payment\PaymentPending($order, $payment));

// When payment method is added
event(new \App\Events\Payment\PaymentMethodAdded($user, $paymentMethodArray));

// When payment method is removed
event(new \App\Events\Payment\PaymentMethodRemoved($user, $paymentMethodArray));

// When refund is processed
event(new \App\Events\Payment\RefundProcessed($order, $refundAmount));
```

### Event Listeners

All listeners are registered in `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \App\Events\Payment\PaymentCompleted::class => [
        \App\Listeners\Payment\SendPaymentSuccessNotifications::class,
    ],
    \App\Events\Payment\PaymentFailed::class => [
        \App\Listeners\Payment\SendPaymentFailedNotification::class,
    ],
    // ... additional listeners
];
```

## Stripe Webhook Integration

### 1. Create Webhook Controller

Create `app/Http/Controllers/Webhooks/StripeWebhookController.php`:

```php
<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Events\Payment\PaymentCompleted;
use App\Events\Payment\PaymentFailed;
use App\Events\Payment\RefundProcessed;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $sig_header,
                $endpoint_secret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailure($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleRefund($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe event type: ' . $event->type);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function handlePaymentSuccess($paymentIntent)
    {
        // Find payment by transaction ID
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for Stripe payment intent: ' . $paymentIntent->id);
            return;
        }

        // Update payment status
        $payment->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => $paymentIntent
        ]);

        // Update order
        $order = $payment->order;
        if ($order && !$order->completed_at) {
            $order->update(['completed_at' => now()]);
        }

        // Dispatch event - This triggers 3 notifications automatically:
        // - PaymentSuccessNotification
        // - InvoiceGeneratedNotification
        // - ReceiptEmailedNotification
        event(new PaymentCompleted($order, $payment));

        Log::info('Payment completed for order: ' . $order->id);
    }

    protected function handlePaymentFailure($paymentIntent)
    {
        // Find payment by transaction ID
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for Stripe payment intent: ' . $paymentIntent->id);
            return;
        }

        // Get failure reason
        $reason = $paymentIntent->last_payment_error->message ?? 'Payment declined';

        // Update payment status
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $paymentIntent
        ]);

        // Dispatch event - This triggers PaymentFailedNotification
        $order = $payment->order;
        event(new PaymentFailed($order, $payment, $reason));

        Log::warning('Payment failed for order: ' . $order->id . ' - Reason: ' . $reason);
    }

    protected function handleRefund($charge)
    {
        // Find payment by charge ID
        $payment = Payment::where('transaction_id', $charge->payment_intent)->first();

        if (!$payment) {
            Log::warning('Payment not found for Stripe charge: ' . $charge->id);
            return;
        }

        $order = $payment->order;
        $refundAmount = $charge->amount_refunded / 100; // Stripe uses cents

        // Update order refund status
        if (!$order->refunded_at) {
            $order->update(['refunded_at' => now()]);
        }

        // Dispatch event - This triggers RefundProcessedNotification
        event(new RefundProcessed($order, $refundAmount));

        Log::info('Refund processed for order: ' . $order->id . ' - Amount: $' . $refundAmount);
    }
}
```

### 2. Register Webhook Route

Add to `routes/web.php`:

```php
// Stripe webhook (exclude from CSRF protection)
Route::post('/webhooks/stripe', [App\Http\Controllers\Webhooks\StripeWebhookController::class, 'handle'])
    ->name('webhooks.stripe');
```

### 3. Exclude from CSRF Protection

Add to `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'webhooks/stripe',
    'webhooks/paypal',
];
```

### 4. Configure Webhook in Stripe Dashboard

1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://yourdomain.com/webhooks/stripe`
3. Select events:
    - `payment_intent.succeeded`
    - `payment_intent.payment_failed`
    - `charge.refunded`
4. Copy webhook signing secret to `.env`:

```env
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxx
```

## PayPal Webhook Integration

### 1. Create PayPal Webhook Controller

```php
<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Events\Payment\PaymentCompleted;
use App\Events\Payment\PaymentFailed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify PayPal webhook signature
        // ... PayPal verification logic ...

        $event = $request->input('event_type');

        switch ($event) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentSuccess($request->all());
                break;

            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentFailure($request->all());
                break;
        }

        return response()->json(['success' => true]);
    }
}
```

## Direct Usage Examples

### Payment Method Management

Already integrated in `ProfileController`:

```php
// Adding payment method
public function addStripePaymentMethod(Request $request)
{
    // ... save payment method logic ...

    // Dispatch event - triggers PaymentMethodAddedNotification
    event(new PaymentMethodAdded($user, $newMethod));

    return response()->json(['success' => true]);
}

// Removing payment method
public function deletePaymentMethod(Request $request)
{
    // ... delete payment method logic ...

    // Dispatch event - triggers PaymentMethodRemovedNotification
    event(new PaymentMethodRemoved($user, $methodToDelete));

    return response()->json(['success' => true]);
}

// Setting default payment
public function setDefaultPaymentMethod(Request $request)
{
    // ... update default logic ...

    // Send notification directly (not event-based)
    $user->notify(new DefaultPaymentUpdatedNotification($defaultMethod));

    return response()->json(['success' => true]);
}
```

### Manual Refund Processing

```php
use App\Notifications\Payment\RefundInitiatedNotification;
use App\Events\Payment\RefundProcessed;

// When initiating a refund
$user = $order->User;
$user->notify(new RefundInitiatedNotification($order, $refundAmount, 'Customer request'));

// When refund is processed (after webhook confirmation)
event(new RefundProcessed($order, $refundAmount));
```

### Balance Due Notification

```php
use App\Notifications\Payment\BalanceDueNotification;

// Find orders with outstanding balance
$overdueOrders = Order::whereNull('completed_at')
    ->where('created_at', '<', now()->subDays(7))
    ->get();

foreach ($overdueOrders as $order) {
    $user = $order->User;
    $user->notify(new BalanceDueNotification(
        $order,
        $order->total_price,
        now()->addDays(3) // Due in 3 days
    ));
}
```

### Card Expiration Check (Scheduled Job)

```php
use App\Notifications\Payment\PaymentMethodExpiringNotification;

// In a scheduled command (app/Console/Commands/CheckExpiringCards.php)
public function handle()
{
    $users = User::all();
    $nextMonth = now()->addMonth();

    foreach ($users as $user) {
        $prefs = $user->UserPrefs->pluck('value', 'key')->toArray();
        $savedMethods = json_decode($prefs['saved_payment_methods'] ?? '[]', true);

        foreach ($savedMethods as $method) {
            if ($method['type'] === 'card') {
                $expDate = \Carbon\Carbon::createFromDate(
                    $method['exp_year'],
                    $method['exp_month'],
                    1
                );

                if ($expDate->isSameMonth($nextMonth)) {
                    $user->notify(new PaymentMethodExpiringNotification($method));
                }
            }
        }
    }
}
```

Register in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('cards:check-expiring')
        ->monthlyOn(1, '09:00'); // Run on 1st of each month at 9 AM
}
```

## Testing

### Manual Testing

```php
// In tinker or a test route
use App\Events\Payment\PaymentCompleted;
use App\Models\Order;
use App\Models\Payment;

$order = Order::find(1);
$payment = $order->payments()->first();

// Test payment success
event(new PaymentCompleted($order, $payment));

// Check notifications table
\DB::table('notifications')
    ->where('notifiable_id', $order->user_id)
    ->latest()
    ->get();
```

### Stripe Webhook Testing

Use Stripe CLI:

```bash
# Install Stripe CLI
stripe login

# Forward webhooks to local server
stripe listen --forward-to localhost:8000/webhooks/stripe

# Trigger test events
stripe trigger payment_intent.succeeded
stripe trigger payment_intent.payment_failed
stripe trigger charge.refunded
```

## Configuration Reference

### User Notification Preferences

All payment notifications in `config/user_notifications.php`:

```php
'payment' => [
    'payment_success' => [
        'key' => 'payment.payment_success',  // Used in DB as: notification_payment.payment_success
        'name' => 'Payment Successful',
        'priority' => 'high',
        'channels' => ['database', 'mail'],
        'user_controllable' => true,  // Can be toggled by user
    ],
    'payment_failed' => [
        'key' => 'payment.payment_failed',
        'name' => 'Payment Failed',
        'priority' => 'critical',
        'channels' => ['database', 'mail'],
        'user_controllable' => false,  // Always sent (critical)
    ],
    // ... other notifications
],
```

### How User Preferences Work

**1. Config Key → Database Key Mapping:**

- Config key: `payment.payment_success`
- Stored in DB as: `notification_payment.payment_success`
- The prefix `notification_` is automatically added by the controller

**2. User Toggles in UI:**
When a user toggles a payment notification in their account settings:

- UI sends: `notifications[payment.payment_success] = true/false`
- Controller saves to `user_prefs` table:
    - `pref_name`: `notification_payment.payment_success`
    - `pref_value`: `1` or `0`

**3. Notification Classes Check Preferences:**

```php
public function via(object $notifiable): array
{
    $channels = ['database'];

    // Check if user has enabled email for this notification
    $emailEnabled = $notifiable->UserPrefs()
        ->where('key', 'notification_payment.payment_success')
        ->first()?->value ?? true;  // Default to enabled

    if ($emailEnabled) {
        $channels[] = 'mail';
    }

    return $channels;
}
```

**4. Result:**

- If user toggles OFF → No email sent, only database notification
- If user toggles ON → Both email and database notification
- Critical notifications ignore preferences and always send

### Critical vs User-Controllable

**Critical (Always Sent)**:

- Payment failed
- Refund initiated
- Refund processed
- Balance due

**User-Controllable**:

- Payment success
- Payment pending
- Payment method changes
- Invoice/receipt notifications

## Queue Configuration

Ensure queue worker is running:

```bash
# Development
php artisan queue:work

# Production (with supervisor)
php artisan queue:work --queue=default --tries=3 --timeout=90
```

## Troubleshooting

### Notifications not sending

1. Check queue worker is running
2. Verify event is being dispatched: Add `Log::info()` in event
3. Check `jobs` and `failed_jobs` tables
4. Verify user preferences don't block notification

### Webhook not working

1. Verify webhook URL is accessible publicly
2. Check webhook signing secret is correct
3. Review Laravel logs: `storage/logs/laravel.log`
4. Test with Stripe CLI or PayPal Sandbox

### Email not received

1. Check mail configuration in `.env`
2. Verify user email channel preference
3. Check `user_prefs` table for `notification_channel_mail`
4. Review mail logs

## Related Files

### Notification Classes

- `app/Notifications/Payment/*.php` - All 12 payment notification classes

### Events & Listeners

- `app/Events/Payment/*.php` - 6 payment events
- `app/Listeners/Payment/*.php` - 6 event listeners
- `app/Providers/EventServiceProvider.php` - Event registration

### Controllers

- `app/Http/Controllers/Student/ProfileController.php` - Payment method management
- `app/Http/Controllers/Webhooks/StripeWebhookController.php` - (To create)
- `app/Http/Controllers/Webhooks/PayPalWebhookController.php` - (To create)

### Configuration

- `config/user_notifications.php` - Notification definitions
- `config/services.php` - Stripe/PayPal credentials

### Routes

- `routes/web.php` - Webhook routes

### Documentation

- `docs/features/notifications.md` - General notification system
- `docs/tasks/student-notifications-implementation-phases.md` - Implementation phases
