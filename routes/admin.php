<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

/**
 * Admin routes - loaded with 'admin' prefix and middleware
 */

// Admin Authentication Routes (Guest only)
Route::middleware(['admin.guest'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    // Password Reset Routes
    Route::get('/password/reset', [AdminAuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AdminAuthController::class, 'reset'])->name('password.update');
});

// Admin Protected Routes
Route::middleware(['admin'])->group(function () {
    // Dashboard (default admin route)
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
