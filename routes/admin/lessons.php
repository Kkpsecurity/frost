<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Lessons\LessonManagementController;

/**
 * Admin Lesson Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('lessons')->name('lessons.')->group(function () {

    // Main lesson management routes
    Route::get('/', [LessonManagementController::class, 'index'])->name('management.index');
    Route::get('/create', [LessonManagementController::class, 'create'])->name('management.create');
    Route::post('/', [LessonManagementController::class, 'store'])->name('management.store');
    Route::get('/{lesson}', [LessonManagementController::class, 'show'])->name('management.show');
    Route::get('/{lesson}/edit', [LessonManagementController::class, 'edit'])->name('management.edit');
    Route::put('/{lesson}', [LessonManagementController::class, 'update'])->name('management.update');
    Route::delete('/{lesson}', [LessonManagementController::class, 'destroy'])->name('management.destroy');

    // Get course units for AJAX
    Route::get('/api/courses/{course}/units', [LessonManagementController::class, 'getCourseUnits'])->name('api.course-units');

});
