<?php

/**
 * Admin Authentication Routes
 * These routes handle admin login/logout and are accessible without admin middleware
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;

// Admin Login Routes
Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        
        // Show Login Form
        Route::get('/login', [
            AdminAuthController::class,
            'showLoginForm'
        ])->name('login');
        
        // Process Login
        Route::post('/login', [
            AdminAuthController::class,
            'login'
        ])->name('login.post');
        
        // Admin Logout
        Route::post('/logout', [
            AdminAuthController::class,
            'logout'
        ])->name('logout')->middleware('auth:admin');
        
        // Show Registration Form (if enabled)
        Route::get('/register', [
            AdminAuthController::class,
            'showRegistrationForm'
        ])->name('register');
        
        // Process Registration (if enabled)
        Route::post('/register', [
            AdminAuthController::class,
            'register'
        ])->name('register.post');
        
        // Password Reset Routes
        Route::prefix('password')
            ->name('password.')
            ->group(function () {
                
                // Show Reset Request Form
                Route::get('/reset', [
                    AdminAuthController::class,
                    'showLinkRequestForm'
                ])->name('request');
                
                // Send Reset Link
                Route::post('/email', [
                    AdminAuthController::class,
                    'sendResetLinkEmail'
                ])->name('email');
                
                // Show Reset Form
                Route::get('/reset/{token}', [
                    AdminAuthController::class,
                    'showResetForm'
                ])->name('reset');
                
                // Process Reset
                Route::post('/reset', [
                    AdminAuthController::class,
                    'reset'
                ])->name('update');
                
            });
            
    });
