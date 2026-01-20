<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\AdminCenterController;

/**
 * Admin Center Routes
 *
 * Administrative tools for system management, user administration,
 * payment gateway configuration, and system monitoring
 */

Route::prefix('admin-center')->name('admin-center.')->middleware(['admin'])->group(function () {

    // User Management
    Route::get('/admin-users', [AdminCenterController::class, 'adminUsers'])->name('admin-users');
    Route::get('/admin-users/create', [AdminCenterController::class, 'createAdminUser'])->name('admin-users.create');
    Route::post('/admin-users', [AdminCenterController::class, 'storeAdminUser'])->name('admin-users.store');
    Route::get('/admin-users/{id}', [AdminCenterController::class, 'showAdminUser'])->name('admin-users.show');
    Route::get('/admin-users/{id}/edit', [AdminCenterController::class, 'editAdminUser'])->name('admin-users.edit');
    Route::put('/admin-users/{id}', [AdminCenterController::class, 'updateAdminUser'])->name('admin-users.update');
    Route::delete('/admin-users/{id}', [AdminCenterController::class, 'deleteAdminUser'])->name('admin-users.delete');
    Route::post('/admin-users/{id}/toggle-status', [AdminCenterController::class, 'toggleAdminStatus'])->name('admin-users.toggle-status');
    Route::post('/admin-users/{id}/change-role', [AdminCenterController::class, 'changeAdminRole'])->name('admin-users.change-role');

    Route::get('/instructor-management', [AdminCenterController::class, 'instructorManagement'])->name('instructor-management');
    Route::get('/instructors/{id}', [AdminCenterController::class, 'showInstructor'])->name('instructors.show');
    Route::get('/instructors/{id}/edit', [AdminCenterController::class, 'editInstructor'])->name('instructors.edit');
    Route::put('/instructors/{id}', [AdminCenterController::class, 'updateInstructor'])->name('instructors.update');
    Route::post('/instructors/{id}/toggle-status', [AdminCenterController::class, 'toggleInstructorStatus'])->name('instructors.toggle-status');

    Route::get('/role-permissions', [AdminCenterController::class, 'rolePermissions'])->name('role-permissions');

    // Payment Gateway
    Route::get('/payment-gateway', [AdminCenterController::class, 'paymentGateway'])->name('payment-gateway');
    Route::post('/payment-gateway', [AdminCenterController::class, 'updatePaymentGateway'])->name('payment-gateway.update');
    Route::get('/transaction-logs', [AdminCenterController::class, 'transactionLogs'])->name('transaction-logs');
    Route::get('/payment-methods', [AdminCenterController::class, 'paymentMethods'])->name('payment-methods');

    // System Configuration
    Route::get('/general-settings', [AdminCenterController::class, 'generalSettings'])->name('general-settings');
    Route::post('/general-settings', [AdminCenterController::class, 'updateGeneralSettings'])->name('general-settings.update');
    Route::get('/email-templates', [AdminCenterController::class, 'emailTemplates'])->name('email-templates');
    Route::get('/notifications', [AdminCenterController::class, 'notifications'])->name('notifications');

    // Security & Access
    Route::get('/activity-logs', [AdminCenterController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/login-attempts', [AdminCenterController::class, 'loginAttempts'])->name('login-attempts');
    Route::get('/ip-whitelist', [AdminCenterController::class, 'ipWhitelist'])->name('ip-whitelist');

    // Quick Tools
    Route::get('/database-tools', [AdminCenterController::class, 'databaseTools'])->name('database-tools');
    Route::get('/cache-management', [AdminCenterController::class, 'cacheManagement'])->name('cache-management');
    Route::post('/cache-management/clear', [AdminCenterController::class, 'clearCache'])->name('cache-management.clear');
    Route::get('/system-health', [AdminCenterController::class, 'systemHealth'])->name('system-health');
});
