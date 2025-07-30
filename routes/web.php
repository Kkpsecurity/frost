<?php

/**
 * Frost Routes
 *
 * This file defines the web routes for the Frost application.
 * It includes routes for the admin settings, services, and other application features.
 */

use Illuminate\Support\Facades\Route;

/**
 * Admin Authentication Routes
 * These routes handle admin login/logout and are accessible without admin middleware
 */
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin/auth_routes.php';
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
 * Test Routes (for debugging)
 */
Route::get('/test/blade-directives', function () {
    return view('test.blade-directive-test');
})->middleware(['admin']);
