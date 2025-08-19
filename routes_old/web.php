<?php

/**
 * Frost Routes
 *
 * This file defines the web routes for the Frost application.
 * It includes routes for the admin settings, services, and other application features.
 */

use App\Http\Controllers\Web\SitePageController;
use Illuminate\Support\Facades\Route;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use App\Support\Settings;

/**
 * =============================================================================
 * ADMIN ROUTES
 * =============================================================================
 */

/**
 * Admin Authentication Routes (No Middleware)
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
 * The admin.php file contains its own route groups and prefixes.
 */
require __DIR__ . '/admin.php';

/**
 * =============================================================================
 * FRONTEND ROUTES
 * =============================================================================
 */

/**
 * Frontend User Authentication Routes
 * These routes handle user login/logout, registration, and password reset
 */
require __DIR__ . '/auth.php';

/**
 * Frontend User Account Routes
 * These routes handle user account management functionality
 */
Route::middleware(['auth'])
    ->group(function () {
        require __DIR__ . '/frontend/account.routes.php';
    });

Route::redirect('/', '/pages', 302);
Route::redirect('/home', '/pages', 302)->name('home');
Route::get('pages/{slug?}', [SitePageController::class, 'render'])->name('pages');

/**
 * Student Classroom Routes
 * These routes handle student learning functionality
 */
Route::prefix('classroom')
    ->name('classroom.')
    ->middleware(['auth'])
    ->group(function () {
        require __DIR__ . '/frontend/classroom.routes.php';
    });

/**
 * =============================================================================
 * DEVELOPMENT/TEST ROUTES
 * =============================================================================
 */

/**
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
