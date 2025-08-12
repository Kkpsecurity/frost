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

// Instructor Dashboard
Route::get('/instructors', [
    App\Http\Controllers\Admin\InstructorDashboardController::class,
    'index'
])->name('instructors.dashboard');

// Support Dashboard
Route::get('/support', [
    App\Http\Controllers\Admin\SupportDashboardController::class,
    'index'
])->name('support.dashboard');

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

// Student Management
require __DIR__ . '/admin/student_routes.php';

// Course Management
require __DIR__ . '/admin/course_routes.php';

// Lesson Management
require __DIR__ . '/admin/lesson_routes.php';

// Course Dates (Scheduling)
Route::prefix('course-dates')->name('course-dates.')->group(function () {
    Route::get('/', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'index'
    ])->name('index');

    Route::get('/create', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'create'
    ])->name('create');

    Route::post('/', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'store'
    ])->name('store');

    Route::get('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'show'
    ])->name('show');

    Route::get('/{courseDate}/edit', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'edit'
    ])->name('edit');

    Route::put('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'update'
    ])->name('update');

    Route::delete('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'destroy'
    ])->name('destroy');

    Route::post('/{courseDate}/toggle-active', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'toggleActive'
    ])->name('toggle-active');

    // API route for loading course units by course
    Route::get('/course-units/{course}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'getCourseUnits'
    ])->name('course-units');
});

// Admin Services (search, tools, etc.)
require __DIR__ . '/admin/services_routes.php';
