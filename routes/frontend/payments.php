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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Routes for payment processing (PayFlowPro, Stripe, PayPal)
|
*/

// Payment gateway page - shows payment form
Route::get('/payments/{payment}', [PaymentController::class, 'show'])
    ->name('payments.payflowpro')
    ->middleware('auth');

// Payment return/callback route
Route::post('/payments/{payment}/return', [PaymentController::class, 'handleReturn'])
    ->name('payments.payflowpro.payment_return');

// Get payment token (AJAX)
Route::get('/payments/{payment}/token', [PaymentController::class, 'getToken'])
    ->name('payments.payflowpro.get_token')
    ->middleware('auth');

// Order completed success page
Route::get('/orders/{order}/completed', [PaymentController::class, 'orderCompleted'])
    ->name('order.completed')
    ->middleware('auth');
