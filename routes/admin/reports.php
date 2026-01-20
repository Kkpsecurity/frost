<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;

/**
 * Admin Reports Routes
 *
 * Comprehensive reporting system for FROST Online Security Training
 * Provides financial, student, course, instructor, and operational analytics
 */

Route::prefix('reports')->name('reports.')->middleware(['admin'])->group(function () {

    // Main Reports Dashboard
    Route::get('/', [ReportsController::class, 'index'])->name('index');

    // Weekly Data for Charts
    Route::get('/api/weekly-data', [ReportsController::class, 'getWeeklyData'])->name('api.weekly-data');

    // Instructor Performance Data for Charts
    Route::get('/api/instructor-data', [ReportsController::class, 'getInstructorData'])->name('api.instructor-data');

    // Financial Data for Charts
    Route::get('/api/financial-data', [ReportsController::class, 'getFinancialData'])->name('api.financial-data');

    // Financial Reports API Endpoints
    Route::prefix('api/financial')->name('api.financial.')->group(function () {
        Route::get('/', [ReportsController::class, 'getFinancialReports'])->name('index');
        Route::get('/export', [ReportsController::class, 'exportReport'])->name('export');
    });

    // Student Analytics API Endpoints
    Route::prefix('api/students')->name('api.students.')->group(function () {
        Route::get('/', [ReportsController::class, 'getStudentReports'])->name('index');
        Route::get('/export', [ReportsController::class, 'exportReport'])->name('export');
    });

    // Course Performance API Endpoints
    Route::prefix('api/courses')->name('api.courses.')->group(function () {
        Route::get('/', [ReportsController::class, 'getCourseReports'])->name('index');
        Route::get('/export', [ReportsController::class, 'exportReport'])->name('export');
    });

    // Instructor Performance API Endpoints
    Route::prefix('api/instructors')->name('api.instructors.')->group(function () {
        Route::get('/', [ReportsController::class, 'getInstructorReports'])->name('index');
        Route::get('/export', [ReportsController::class, 'exportReport'])->name('export');
    });

    // Operational Analytics API Endpoints
    Route::prefix('api/operational')->name('api.operational.')->group(function () {
        Route::get('/', [ReportsController::class, 'getOperationalReports'])->name('index');
        Route::get('/export', [ReportsController::class, 'exportReport'])->name('export');
    });

    // Generic Export Endpoint
    Route::post('/export', [ReportsController::class, 'exportReport'])->name('export');

    // Download Generated Reports
    Route::get('/download/{filename}', function ($filename) {
        $path = storage_path('app/reports/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Report file not found');
        }

        return response()->download($path);
    })->name('download');
});
