<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupportDashboardController;

/**
 * Support dashboard management routes
 * @author rclark <richievc@gmail.com>
 */

Route::prefix('support')->name('support.')->middleware(['admin'])->group(function () {
    // Support dashboard
    Route::get('/', function () {
        return view('dashboards.support.index');
    })->name('dashboard');

    // API endpoints
    Route::get('/api/stats', [SupportDashboardController::class, 'getStats'])
        ->name('api.stats');

    Route::get('/api/tickets', [SupportDashboardController::class, 'getRecentTickets'])
        ->name('api.tickets');

    Route::post('/api/search-students', [SupportDashboardController::class, 'searchStudents'])
        ->name('api.search-students');

    Route::patch('/api/tickets/{ticketId}', [SupportDashboardController::class, 'updateTicket'])
        ->name('api.update-ticket');

    Route::post('/api/tickets', [SupportDashboardController::class, 'createTicket'])
        ->name('api.create-ticket');

    Route::post('/api/reports', [SupportDashboardController::class, 'generateReport'])
        ->name('api.generate-report');
});

// Legacy support routes (keep for backwards compatibility)
Route::prefix('frost-support')->name('frost-support.')->middleware(['admin'])->group(function () {
    // Support center routes
    Route::get('/', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'index'])
        ->name('index');

    Route::get('/stats', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStats'])
        ->name('stats');

    Route::post('/search', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'searchStudents'])
        ->name('search');

    Route::get('/student/{studentId}', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentDetails'])
        ->name('student.details');
});
