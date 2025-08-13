<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminPasswordResetController;
use Illuminate\Support\Facades\Route;

/**
 * Admin Authentication Routes
 * These routes are for admin login/logout functionality
 * Separate from regular user authentication
 */

// Admin Login Routes (accessible to guests only)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login']);

    // Password Reset Routes
    Route::get('/forgot-password', [AdminPasswordResetController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [AdminPasswordResetController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [AdminPasswordResetController::class, 'reset'])
        ->name('password.reset');

    Route::post('/reset-password', [AdminPasswordResetController::class, 'update'])
        ->name('password.update');
});

// Admin Logout Route (accessible to authenticated admins only)
Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('logout');
});
