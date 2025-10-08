<?php

/**
 * Admin Account Profile Routes
 * 1. Dashboard 
 * 2. Update Account
 * 3. Update Password
 * 4. Upload Avatar
 * 5. Delete Avatar
 * 6. Manage Settings
 */

Route::get('account/dashboard', [App\Http\Controllers\Admin\Account\AccountDashboardController::class, 'dashboard'])
    ->name('admin.account.dashboard');

Route::post('account/profile/update', [App\Http\Controllers\Admin\Account\AccountDashboardController::class, 'accountUpdate'])
    ->name('admin.account.profile.update');

Route::post('account/password/update', [App\Http\Controllers\Admin\Account\AccountDashboardController::class, 'accountPasswordUpdate'])
    ->name('admin.account.password.update');

Route::post('account/avatar/upload', [App\Http\Controllers\Admin\Account\AccountDashboardController::class, 'accountAvatarUpdate'])
    ->name('admin.account.avatar.upload');

Route::post('account/avatar/delete', [App\Http\Controllers\Admin\Account\AccountDashboardController::class, 'accountAvatarDelete'])
    ->name('admin.account.avatar.delete');

