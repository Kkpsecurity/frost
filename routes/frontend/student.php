<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
|
| Web routes for student React application using Inertia.js
|
*/

/**
 * Student Dashboard Routes (Web only - no API routes needed for Inertia/React)
 */
Route::middleware(['auth'])->group(function () {

    // Main student classroom dashboard
    Route::get('/classroom', [StudentDashboardController::class, 'dashboard'])
        ->name('classroom.dashboard');

    // Debug route for testing array structure
    Route::get('/classroom/debug', [StudentDashboardController::class, 'debug'])
        ->name('classroom.debug');

    // Debug route for classroom data only
    Route::get('/classroom/debug/class', [StudentDashboardController::class, 'debugClass'])
        ->name('classroom.debug.class');

    // Debug route for student data only
    Route::get('/classroom/debug/student', [StudentDashboardController::class, 'debugStudent'])
        ->name('classroom.debug.student');

    // React API endpoints matching the React query structure
    Route::get('/classroom/api/stats', [StudentDashboardController::class, 'getStudentStats'])
        ->name('classroom.api.stats');

    Route::get('/classroom/api/recent-lessons', [StudentDashboardController::class, 'getRecentLessons'])
        ->name('classroom.api.recent-lessons');

    Route::get('/classroom/api/upcoming-assignments', [StudentDashboardController::class, 'getUpcomingAssignments'])
        ->name('classroom.api.upcoming-assignments');

    // Redirect dashboard to classroom for consistency
    Route::redirect('/dashboard', '/classroom', 302);

});
