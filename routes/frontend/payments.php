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

Route::prefix('payments')->name('payments.')->group(function () {

    // Debug endpoint for course payment issues (no auth required for testing)
    Route::get('/debug/course/{course}', [PaymentController::class, 'debugCoursePayment'])
        ->name('debug.course');

    // Course payment selection page - GUEST CHECKOUT ENABLED
    Route::get('/course/{course}', [PaymentController::class, 'coursePayment'])
        ->name('course');

    // Payment success simulation - for testing checkout flows
    Route::post('/success-simulation', [PaymentController::class, 'successSimulation'])
        ->name('success-simulation');

    // Authenticated payment routes
    Route::middleware(['auth'])->group(function () {

        // Payment processing page
    Route::get('/payflowpro/{payment}', [PaymentController::class, 'payflowpro'])
        ->name('payflowpro');

    // Payment processing endpoints
    Route::post('/stripe/{payment}', [PaymentController::class, 'processStripe'])
        ->name('stripe.process');

    Route::post('/paypal/{payment}', [PaymentController::class, 'processPaypal'])
        ->name('paypal.process');

    // Course payment processing (creates order and payment on the fly)
    Route::post('/course/stripe/{course}', [PaymentController::class, 'processCourseStripe'])
        ->name('course.stripe');

    Route::post('/course/paypal/{course}', [PaymentController::class, 'processCoursePaypal'])
        ->name('course.paypal');

        // Payment callbacks
        Route::get('/success/{payment}', [PaymentController::class, 'success'])
            ->name('success');

        Route::get('/cancel/{payment}', [PaymentController::class, 'cancel'])
            ->name('cancel');
    });
});
