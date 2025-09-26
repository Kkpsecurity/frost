<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Courses\CourseDashboardController;

/**
 * Course Management Routes
 * Prefix: /admin/courses
 */
Route::middleware(['admin'])->prefix('courses')->name('courses.')->group(function () {

    // Dashboard
    Route::get('/', [CourseDashboardController::class, 'dashboard'])->name('dashboard');

    // Data endpoints for AJAX
    Route::get('/data', [CourseDashboardController::class, 'getCoursesData'])->name('data');

    // Course Management Routes
    Route::prefix('manage')->name('manage.')->group(function () {

        // Create
        Route::get('/create', [CourseDashboardController::class, 'createCourse'])->name('create');
        Route::post('/create', [CourseDashboardController::class, 'storeCourse'])->name('store');

        // View, Edit, Update specific course
        Route::get('/{course}', [CourseDashboardController::class, 'viewCourse'])->name('view');
        Route::get('/{course}/edit', [CourseDashboardController::class, 'editCourse'])->name('edit');
        Route::put('/{course}', [CourseDashboardController::class, 'updateCourse'])->name('update');

        // Course actions
        Route::post('/{course}/archive', [CourseDashboardController::class, 'archiveCourse'])->name('archive');
        Route::post('/{course}/restore', [CourseDashboardController::class, 'restoreCourse'])->name('restore');

        // Course sub-management
        Route::get('/{course}/enrollments', [CourseDashboardController::class, 'viewEnrollments'])->name('enrollments');
        Route::get('/{course}/units', [CourseDashboardController::class, 'viewUnits'])->name('units');

    });

});
