<?php

/**
 * Course Management Routes
 * Routes for managing D and G courses
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Courses\CourseManagementController;

// Legacy course dashboard (keep existing functionality)
Route::get('courses/dashboard', [App\Http\Controllers\Admin\Courses\CourseController::class, 'dashboard'])
    ->name('admin.courses.dashboard');

// Course Management Routes
Route::prefix('courses')
    ->name('courses.')
    ->group(function () {

        // Main course listing route - /admin/courses
        Route::get('/', [CourseManagementController::class, 'index'])->name('index');

        // Course Management
        Route::prefix('management')
            ->name('management.')
            ->group(function () {
            Route::get('/', [CourseManagementController::class, 'index'])->name('index');
            Route::get('/create', [CourseManagementController::class, 'create'])->name('create');
            Route::post('/', [CourseManagementController::class, 'store'])->name('store');
            Route::get('/{course}', [CourseManagementController::class, 'show'])->name('show');
            Route::get('/{course}/edit', [CourseManagementController::class, 'edit'])->name('edit');
            Route::put('/{course}', [CourseManagementController::class, 'update'])->name('update');
            Route::delete('/{course}', [CourseManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{course}/archive', [CourseManagementController::class, 'archive'])->name('archive');
            Route::patch('/{course}/restore', [CourseManagementController::class, 'restore'])->name('restore');
        });

        // Course Statistics API
        Route::get('/stats/course-types', [CourseManagementController::class, 'courseTypeStats'])->name('stats.course-types');
    });
