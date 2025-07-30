<?php

use App\Http\Controllers\Admin\AdminAuthController;
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
});

// Admin Logout Route (accessible to authenticated admins only)
Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('logout');
});
