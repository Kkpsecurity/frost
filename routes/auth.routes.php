<?php

use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Frontend\Auth\NewPasswordController;
use App\Http\Controllers\Frontend\Auth\PasswordController;
use App\Http\Controllers\Frontend\Auth\PasswordResetLinkController;
use App\Http\Controllers\Frontend\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication for the frontend/public user area.
| Separate from admin authentication which uses different guards.
|
*/

/**
 * Guest Routes (Login & Registration)
 */
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login Routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Password Reset Routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

/**
 * Authenticated User Routes
 */
Route::middleware('auth')->group(function () {
    // Email Verification Routes
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password Confirmation Routes
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Password Update Route
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

/**
 * Dashboard and User Area Routes
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard - Redirect to classroom (main entry point)
    Route::get('dashboard', function () {
        return redirect()->route('classroom.dashboard');
    })->name('dashboard');

    // User Profile Routes
    Route::get('profile', function () {
        return view('frontend.profile.edit');
    })->name('profile.edit');

    Route::patch('profile', function () {
        // Handle profile update logic
        return redirect()->route('profile.edit');
    })->name('profile.update');

    Route::delete('profile', function () {
        // Handle profile deletion logic
        return redirect()->route('pages');
    })->name('profile.destroy');
});
