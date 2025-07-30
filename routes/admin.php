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

// Admin Center - Settings (already set up)
require __DIR__ . '/admin/settings_routes.php';

// Admin Center - Admin Users
require __DIR__ . '/admin/admin_user_routes.php';

// Admin Services (search, tools, etc.)
require __DIR__ . '/admin/services_routes.php';
