<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Include Frontend Routes
 * All frontend routes are organized in the frontend.php file
 */
require __DIR__ . '/frontend.php';

/**
 * Account Profile Routes
 */
Route::middleware('auth')->group(function () {
    Route::get('/account', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('account.index');
    Route::post('/account/profile', [App\Http\Controllers\Student\ProfileController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/settings', [App\Http\Controllers\Student\ProfileController::class, 'updateSettings'])->name('account.settings.update');
    Route::get('/account/invoice/{order}', [App\Http\Controllers\Student\ProfileController::class, 'downloadInvoice'])->name('student.invoice');

    // Payment method management routes
    Route::prefix('account/payments')->name('account.payments.')->group(function () {
        Route::post('/add-stripe-method', [App\Http\Controllers\Student\ProfileController::class, 'addStripePaymentMethod'])->name('add-stripe');
        Route::get('/connect-paypal', [App\Http\Controllers\Student\ProfileController::class, 'connectPayPal'])->name('connect-paypal');
        Route::post('/paypal-callback', [App\Http\Controllers\Student\ProfileController::class, 'paypalCallback'])->name('paypal-callback');
        Route::post('/set-default', [App\Http\Controllers\Student\ProfileController::class, 'setDefaultPaymentMethod'])->name('set-default');
        Route::delete('/delete-method', [App\Http\Controllers\Student\ProfileController::class, 'deletePaymentMethod'])->name('delete-method');
    });
});

/**
 * Student Offline Session Tracking Routes
 */
Route::middleware('auth')->prefix('student/offline')->name('student.offline.')->group(function () {
    // Session Management
    Route::post('session/start/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'startSession'])
        ->name('session.start');
    Route::post('session/end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'endSession'])
        ->name('session.end');
    Route::get('session/status/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionStatus'])
        ->name('session.status');

    // Activity Tracking
    Route::post('track/lesson/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackLessonActivity'])
        ->name('track.lesson');
    Route::post('track/step/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackSessionStep'])
        ->name('track.step');

    // Analytics & Reporting
    Route::get('summary/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionSummary'])
        ->name('summary');
    Route::get('activities/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getRecentActivities'])
        ->name('activities');

    // Admin/Cleanup
    Route::post('session/force-end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'forceEndSessions'])
        ->name('session.force-end');
});

/**
 * Admin Payment Configuration Routes
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Payment management routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'index'])->name('index');

        // PayPal configuration
        Route::get('/paypal', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'paypal'])->name('paypal');
        Route::put('/paypal', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'updatePayPal'])->name('update-paypal');

        // Stripe configuration
        Route::get('/stripe', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'stripe'])->name('stripe');
        Route::put('/stripe', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'updateStripe'])->name('update-stripe');

        // Connection testing
        Route::post('/test-connection', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'testConnection'])->name('test-connection');
    });
});

/**
 * Clean web routes - test/debug routes moved to separate files
 */
