<?php

use Illuminate\Support\Facades\Route;

/**
 * Instructor classroom management routes
 * @author rclark <richievc@gmail.com>
 */

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
