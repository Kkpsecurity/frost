<?php

/**
 * Lesson Management Routes
 * Routes for managing lessons and their course unit associations
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Lessons\LessonManagementController;

// Lesson Management Routes
Route::prefix('lessons')
    ->name('lessons.')
    ->group(function () {

        // Main lesson listing route - /admin/lessons
        Route::get('/', [LessonManagementController::class, 'index'])->name('index');

        // Lesson Management
        Route::prefix('management')
            ->name('management.')
            ->group(function () {
            Route::get('/', [LessonManagementController::class, 'index'])->name('index');
            Route::get('/create', [LessonManagementController::class, 'create'])->name('create');
            Route::post('/', [LessonManagementController::class, 'store'])->name('store');
            Route::get('/{lesson}', [LessonManagementController::class, 'show'])->name('show');
            Route::get('/{lesson}/edit', [LessonManagementController::class, 'edit'])->name('edit');
            Route::put('/{lesson}', [LessonManagementController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [LessonManagementController::class, 'destroy'])->name('destroy');

            // API endpoint for getting course units by course
            Route::get('/course-units/{course}', [LessonManagementController::class, 'getCourseUnits'])->name('course-units');
        });
    });
