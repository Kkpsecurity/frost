<?php

/**
 * Frost Routes
 *
 * This file defines the web routes for the Frost application.
 * It includes routes for the admin settings, services, and other application features.
 */

use Illuminate\Support\Facades\Route;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use App\Support\Settings;




/**
 * Admin Authentication Routes
 * These routes handle admin login/logout and are accessible without admin middleware
 */
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin/auth.php';
    });

/**
 * Protected Admin Routes
 * These routes are prefixed with 'admin' and require admin middleware.
 * They include settings management, services, and other admin functionalities.
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin'])
    ->group(function () {
        require __DIR__ . '/admin.php';
    });

/**
 * Frontend User Authentication Routes
 * These routes handle user login/logout, registration, and password reset
 */
require __DIR__ . '/auth.php';

require __DIR__ . '/admin.php';


/**
 * Frontend User Account Routes
 * These routes handle user account management functionality
 */
Route::middleware(['auth'])
    ->group(function () {
        require __DIR__ . '/frontend/account.routes.php';
    });


/**
 * Student Classroom Routes
 * These routes handle student learning functionality
 */
Route::prefix('classroom')
    ->name('classroom.')
    ->middleware(['auth'])
    ->group(function () {
        // Student Dashboard
        require __DIR__ . '/frontend/classroom.routes.php';
    });

/*
 * Test AdminLTE Notifications
 */
Route::get('/test-notifications', function () {
    return view('test-notifications');
})->name('test.notifications')->middleware('auth');

/**
 * Test Instructor Dashboard
 */
Route::get('/test-instructor-dashboard', function () {
    return view('test-instructor-dashboard');
})->name('test.instructor.dashboard')->middleware('auth');

