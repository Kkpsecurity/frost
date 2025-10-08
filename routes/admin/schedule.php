<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomScheduleController;

/**
 * Custom Schedule Generator Routes
 * Web routes for custom course date generation patterns
 */

Route::prefix('schedule')->name('schedule.')->middleware(['admin'])->group(function () {

    // =====================================================
    // CUSTOM SCHEDULE MANAGEMENT PAGES
    // =====================================================

    // Main schedule generator page
    Route::get('/', [CustomScheduleController::class, 'index'])
        ->name('index');

    // Schedule generator form/interface
    Route::get('/generator', [CustomScheduleController::class, 'generator'])
        ->name('generator');

    // View generated schedules
    Route::get('/view', [CustomScheduleController::class, 'view'])
        ->name('view');

    // =====================================================
    // PATTERN GENERATION (POST ROUTES)
    // =====================================================

    // Generate Monday/Wednesday every other week pattern
    Route::post('/generate/monday-wednesday-biweekly', [CustomScheduleController::class, 'generateMondayWednesdayBiweekly'])
        ->name('generate.monday-wednesday-biweekly');

    // Generate every 3 days pattern
    Route::post('/generate/every-three-days', [CustomScheduleController::class, 'generateEveryThreeDays'])
        ->name('generate.every-three-days');

    // Generate multiple patterns at once
    Route::post('/generate/multiple-patterns', [CustomScheduleController::class, 'generateMultiplePatterns'])
        ->name('generate.multiple-patterns');

    // =====================================================
    // DATA ENDPOINTS (GET ROUTES)
    // =====================================================

    // Get available courses for scheduling
    Route::get('/data/courses', [CustomScheduleController::class, 'getAvailableCourses'])
        ->name('data.courses');

    // Get scheduling statistics
    Route::get('/data/stats', [CustomScheduleController::class, 'getSchedulingStats'])
        ->name('data.stats');

    // Preview a pattern (GET with query parameters)
    Route::get('/preview/{pattern}', [CustomScheduleController::class, 'previewPattern'])
        ->name('preview')
        ->where('pattern', 'monday-wednesday-biweekly|every-three-days|monday-wednesday-friday|tuesday-thursday');

    // =====================================================
    // SCHEDULE MANAGEMENT
    // =====================================================

    // Activate/deactivate course dates
    Route::post('/activate-dates', [CustomScheduleController::class, 'activateDates'])
        ->name('activate-dates');

    Route::post('/deactivate-dates', [CustomScheduleController::class, 'deactivateDates'])
        ->name('deactivate-dates');

    // Delete generated dates
    Route::delete('/delete-dates', [CustomScheduleController::class, 'deleteDates'])
        ->name('delete-dates');

    // Export schedule data
    Route::get('/export/{format}', [CustomScheduleController::class, 'exportSchedule'])
        ->name('export')
        ->where('format', 'csv|json|pdf');
});
