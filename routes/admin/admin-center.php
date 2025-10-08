<?php

/**
 * Admin Center Routes
 *
 * Routes for admin center functionality including:
 * - Admin Users Management
 * - Site Settings
 * - Media Manager
 *
 * All routes are prefixed with 'admin/admin-center' and protected by admin middleware
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\AdminUserController;
use App\Http\Controllers\Admin\AdminCenter\SettingsController;
use App\Http\Controllers\Admin\AdminCenter\AdminPaymentsController;
use App\Http\Controllers\Admin\AdminCenter\MediaManagerController;
use App\Http\Controllers\Admin\AdminCenter\CenterController;

Route::middleware(['admin'])->group(function () {

    // Admin Center Dashboard
    Route::get('/admin-center', [CenterController::class, 'dashboard'])->name('admin-center.dashboard');

    // Admin Users Management
    Route::prefix('admin-center/admin-users')->name('admin-center.admin-users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Site Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/', [SettingsController::class, 'store'])->name('store');
        Route::get('/create', [SettingsController::class, 'create'])->name('create');

        // Specialized settings routes
        Route::get('/adminlte', [SettingsController::class, 'adminlte'])->name('adminlte');
        Route::put('/adminlte', [SettingsController::class, 'updateAdminlte'])->name('update-adminlte');
        Route::get('/adminlte/debug', [SettingsController::class, 'debugAdminlte'])->name('debug-adminlte');
        Route::get('/storage', [SettingsController::class, 'storage'])->name('storage');
        Route::put('/storage', [SettingsController::class, 'updateStorage'])->name('update-storage');
        Route::get('/auth', [SettingsController::class, 'auth'])->name('auth');
        Route::put('/auth', [SettingsController::class, 'updateAuth'])->name('update-auth');
        Route::get('/test', [SettingsController::class, 'test'])->name('test');

        // CRUD routes (must come after specialized routes)
        Route::get('/{setting}', [SettingsController::class, 'show'])->name('show');
        Route::get('/{setting}/edit', [SettingsController::class, 'edit'])->name('edit');
        Route::put('/{setting}', [SettingsController::class, 'update'])->name('update');
        Route::delete('/{setting}', [SettingsController::class, 'destroy'])->name('destroy');
    });

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentsController::class, 'index'])->name('index');

        // PayPal configuration
        Route::get('/paypal', [AdminPaymentsController::class, 'paypal'])->name('paypal');
        Route::put('/paypal', [AdminPaymentsController::class, 'updatePaypal'])->name('update-paypal');

        // Stripe configuration
        Route::get('/stripe', [AdminPaymentsController::class, 'stripe'])->name('stripe');
        Route::put('/stripe', [AdminPaymentsController::class, 'updateStripe'])->name('update-stripe');

        // Test connections
        Route::post('/test-connection', [AdminPaymentsController::class, 'testConnection'])->name('test-connection');
    });

    // Media Manager
    Route::prefix('media-manager')->name('media-manager.')->group(function () {
        Route::get('/', [MediaManagerController::class, 'index'])->name('index');
        Route::post('/upload', [MediaManagerController::class, 'upload'])->name('upload');
        Route::get('/browse', [MediaManagerController::class, 'browse'])->name('browse');
        Route::delete('/{media}', [MediaManagerController::class, 'destroy'])->name('destroy');
        Route::post('/create-folder', [MediaManagerController::class, 'createFolder'])->name('create-folder');
        Route::post('/move', [MediaManagerController::class, 'move'])->name('move');
        Route::post('/rename', [MediaManagerController::class, 'rename'])->name('rename');
    });

});
