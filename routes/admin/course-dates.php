<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseDates\CourseDateController;

/**
 * Admin Course Dates Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('course-dates')->name('course-dates.')->group(function () {

    // Main course date management routes
    Route::get('/', [CourseDateController::class, 'index'])->name('index');
    Route::get('/calendar', [CourseDateController::class, 'calendar'])->name('calendar');
    Route::get('/create', [CourseDateController::class, 'create'])->name('create');
    Route::post('/', [CourseDateController::class, 'store'])->name('store');
    Route::get('/{courseDate}', [CourseDateController::class, 'show'])->name('show');
    Route::get('/{courseDate}/edit', [CourseDateController::class, 'edit'])->name('edit');
    Route::put('/{courseDate}', [CourseDateController::class, 'update'])->name('update');
    Route::delete('/{courseDate}', [CourseDateController::class, 'destroy'])->name('destroy');

    // Toggle active status
    Route::patch('/{courseDate}/toggle-active', [CourseDateController::class, 'toggleActive'])->name('toggle-active');

    // AJAX endpoints
    Route::get('/api/calendar', [CourseDateController::class, 'apiCalendar'])->name('api.calendar');
    Route::get('/api/course/{course}/units', [CourseDateController::class, 'getCourseUnits'])->name('api.course.units');

    // Bulk operations
    Route::post('/bulk/delete', [CourseDateController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('/bulk/toggle-active', [CourseDateController::class, 'bulkToggleActive'])->name('bulk.toggle-active');

    // Auto-generation integration routes
    Route::get('/generator', [CourseDateController::class, 'generator'])->name('generator');
    Route::post('/generator/preview', [CourseDateController::class, 'generatorPreview'])->name('generator.preview');
    Route::post('/generator/generate', [CourseDateController::class, 'generatorGenerate'])->name('generator.generate');
    Route::post('/generator/cleanup', [CourseDateController::class, 'generatorCleanup'])->name('generator.cleanup');

    // Import/Export functionality
    Route::get('/import', [CourseDateController::class, 'import'])->name('import');
    Route::post('/import', [CourseDateController::class, 'processImport'])->name('import.process');
    Route::get('/export', [CourseDateController::class, 'export'])->name('export');
});
