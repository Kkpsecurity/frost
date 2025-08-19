<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\AdminUserController;

/**
 * Admin Center - Admin Users CRUD routes
 */
Route::prefix('admin-center')->name('admin-center.')->group(function () {

    // Admin Users resource routes
    Route::resource('admin-users', AdminUserController::class);

    // Additional admin user routes
    Route::get('admin-users-data', [AdminUserController::class, 'getData'])->name('admin-users.data');
    Route::post('admin-users/{id}/password', [AdminUserController::class, 'updatePassword'])->name('admin-users.password');
    Route::post('admin-users/{id}/avatar', [AdminUserController::class, 'updateAvatar'])->name('admin-users.avatar');
    Route::post('admin-users/{id}/deactivate', [AdminUserController::class, 'deactivate'])->name('admin-users.deactivate');
    Route::post('admin-users/{id}/activate', [AdminUserController::class, 'activate'])->name('admin-users.activate');

});

