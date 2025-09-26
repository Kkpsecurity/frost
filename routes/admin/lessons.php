<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Lessons\LessonManagementController;

/**
 * Admin Lesson Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('lessons')->name('lessons.')->group(function () {

    // Main lesson management routes
    Route::get('/', [LessonManagementController::class, 'index'])->name('index');
    Route::get('/create', [LessonManagementController::class, 'create'])->name('create');
    Route::post('/', [LessonManagementController::class, 'store'])->name('store');
    Route::get('/{lesson}', [LessonManagementController::class, 'show'])->name('show');
    Route::get('/{lesson}/edit', [LessonManagementController::class, 'edit'])->name('edit');
    Route::put('/{lesson}', [LessonManagementController::class, 'update'])->name('update');
    Route::delete('/{lesson}', [LessonManagementController::class, 'destroy'])->name('destroy');

    // Course Unit Management within lessons
    Route::get('/{lesson}/units', [LessonManagementController::class, 'manageUnits'])->name('units');
    Route::post('/{lesson}/units', [LessonManagementController::class, 'updateUnits'])->name('units.update');

    // Course Unit specific management (for the new course unit-focused view)
    Route::get('/units/{courseUnit}/manage', [LessonManagementController::class, 'manageCourseUnitLessons'])->name('units.manage');
    Route::post('/units/{courseUnit}/manage', [LessonManagementController::class, 'updateCourseUnitLessons'])->name('units.manage.update');

    // AJAX endpoints for dynamic course unit loading
    Route::get('/api/course/{course}/units', [LessonManagementController::class, 'getCourseUnits'])->name('api.course.units');

    // Bulk operations
    Route::post('/bulk/delete', [LessonManagementController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('/bulk/assign', [LessonManagementController::class, 'bulkAssign'])->name('bulk.assign');

    // Import/Export functionality
    Route::get('/import', [LessonManagementController::class, 'import'])->name('import');
    Route::post('/import', [LessonManagementController::class, 'processImport'])->name('import.process');
    Route::get('/export', [LessonManagementController::class, 'export'])->name('export');
});
