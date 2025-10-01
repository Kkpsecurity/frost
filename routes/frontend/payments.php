<?php

use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Routes for payment processing and callbacks
|
*/

Route::middleware(['auth'])->prefix('payments')->name('payments.')->group(function () {

    // Payment processing page
    Route::get('/payflowpro/{payment}', [PaymentController::class, 'payflowpro'])
        ->name('payflowpro');

    // Payment processing endpoints
    Route::post('/stripe/{payment}', [PaymentController::class, 'processStripe'])
        ->name('stripe.process');

    Route::post('/paypal/{payment}', [PaymentController::class, 'processPaypal'])
        ->name('paypal.process');

    // Payment callbacks
    Route::get('/success/{payment}', [PaymentController::class, 'success'])
        ->name('success');

    Route::get('/cancel/{payment}', [PaymentController::class, 'cancel'])
        ->name('cancel');
});
