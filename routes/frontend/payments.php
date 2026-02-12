<?php

/**
 * ⚠️ TEMPORARY TEST FILE - TO BE REMOVED ⚠️
 *
 * Created: February 10, 2026
 * Purpose: End-to-end testing of course enrollment flow
 *
 * DO NOT USE IN PRODUCTION
 *
 * See: docs/tasks/TEMPORARY_TEST_FILES.md for removal instructions
 *
 * This file should be replaced with proper PayFlowPro/Stripe integration
 * Archived controller available at: app/Http/Controllers/Archived/Web_Payments/PayFlowProController.php
 */

use App\Http\Controllers\Frontend\Payments\PaymentController;
use App\Http\Controllers\Frontend\Payments\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Routes for payment processing (PayFlowPro, Stripe, PayPal)
|
*/

// Checkout page - select payment method
Route::get('/checkout/{order}', [CheckoutController::class, 'show'])
    ->name('checkout.show')
    ->middleware('auth');

// Process payment method selection
Route::post('/checkout/{order}/process', [CheckoutController::class, 'processPayment'])
    ->name('checkout.process')
    ->middleware('auth');

// PayFlowPro payment gateway
Route::get('/payments/payflowpro/{payment}', [PaymentController::class, 'showPayFlowPro'])
    ->name('payments.payflowpro')
    ->middleware('auth');

Route::post('/payments/payflowpro/{payment}/process', [PaymentController::class, 'processPayFlowPro'])
    ->name('payments.payflowpro.process')
    ->middleware('auth');

// Stripe payment gateway
Route::get('/payments/stripe/{payment}', [PaymentController::class, 'showStripe'])
    ->name('payments.stripe')
    ->middleware('auth');

Route::post('/payments/stripe/{payment}/intent', [PaymentController::class, 'createStripeIntent'])
    ->name('payments.stripe.intent')
    ->middleware('auth');

Route::post('/payments/stripe/{payment}/confirm', [PaymentController::class, 'confirmStripe'])
    ->name('payments.stripe.confirm')
    ->middleware('auth');

// PayPal payment gateway
Route::get('/payments/paypal/{payment}', [PaymentController::class, 'showPayPal'])
    ->name('payments.paypal')
    ->middleware('auth');

Route::post('/payments/paypal/{payment}/process', [PaymentController::class, 'processPayPal'])
    ->name('payments.paypal.process')
    ->middleware('auth');

// Legacy routes (keep for backward compatibility)
Route::post('/payments/{payment}/return', [PaymentController::class, 'handleReturn'])
    ->name('payments.payflowpro.payment_return');

Route::get('/payments/{payment}/token', [PaymentController::class, 'getToken'])
    ->name('payments.payflowpro.get_token')
    ->middleware('auth');

// Order completed success page
Route::get('/orders/{order}/completed', [PaymentController::class, 'orderCompleted'])
    ->name('order.completed')
    ->middleware('auth');
