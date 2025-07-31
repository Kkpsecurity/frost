<?php

/**
 * Admin Master Route File
 * Parent Route load for all admin routes.
 * This file is included in the main web.php file.
 * It sets up the admin routes with the following configurations:
 * - Prefix: admin
 * - Name: admin.
 * - Middleware: admin, verified
 */

use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/dashboard', 301);

// Admin Dashboard
Route::get('/', [
    App\Http\Controllers\Admin\AdminDashboardController::class,
    'dashboard'
])->name('dashboard');

// FilePond Demo (legacy route - redirects to admin center)
Route::get('/filepond-demo', function () {
    return redirect()->route('admin.media-manager.index');
})->name('filepond.demo');

// Media Upload Routes (FilePond integration)
Route::post('/upload', [
    App\Http\Controllers\Admin\MediaController::class,
    'upload'
])->name('media.upload');

Route::delete('/upload/revert', [
    App\Http\Controllers\Admin\MediaController::class,
    'revert'
])->name('media.revert');

Route::get('/upload/{uploadId}', [
    App\Http\Controllers\Admin\MediaController::class,
    'getUploadInfo'
])->name('media.info');

Route::post('/upload/finalize', [
    App\Http\Controllers\Admin\MediaController::class,
    'finalize'
])->name('media.finalize');

// Admin Center - Settings (already set up)
require __DIR__ . '/admin/settings_routes.php';

// Admin Center - Media Manager
require __DIR__ . '/admin/media_manager_routes.php';

// Admin Center - Admin Users
require __DIR__ . '/admin/admin_user_routes.php';

// Admin Services (search, tools, etc.)
require __DIR__ . '/admin/services_routes.php';
