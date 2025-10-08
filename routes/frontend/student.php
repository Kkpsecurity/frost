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

    /**
     * DEBUG ROUTES - must come before parameterized routes
     */
    Route::get('/classroom/debug', [StudentDashboardController::class, 'debug'])
        ->name('classroom.debug');

    // Debug route for classroom data only
    Route::get('/classroom/debug/class', [StudentDashboardController::class, 'debugClass'])
        ->name('classroom.debug.class');

    // Debug route for student data only
    Route::get('/classroom/debug/student', [StudentDashboardController::class, 'debugStudent'])
        ->name('classroom.debug.student');

    /**
     * Student Polling Routes - must come before parameterized routes
     */
    Route::get('/classroom/student/data', [StudentDashboardController::class, 'getStudentData'])
        ->name('classroom.student.data');

    Route::get('/classroom/class/data', [StudentDashboardController::class, 'getClassData'])
        ->name('classroom.class.data');

    /**
     * Student Attendance Routes
     */
    Route::post('/classroom/enter-class', [StudentDashboardController::class, 'enterClass'])
        ->name('classroom.enter');

    Route::get('/classroom/attendance/data', [StudentDashboardController::class, 'getAttendanceData'])
        ->name('classroom.attendance.data');

    Route::get('/classroom/attendance/{courseDateId}', [StudentDashboardController::class, 'getClassAttendance'])
        ->where('courseDateId', '[0-9]+')
        ->name('classroom.attendance.class');

    // Main student classroom dashboard
    Route::get('/classroom', [StudentDashboardController::class, 'dashboard'])
        ->name('classroom.dashboard');

    // Student classroom with course ID - MUST come last
    Route::get('/classroom/{id}', [StudentDashboardController::class, 'dashboard'])
        ->where('id', '[0-9]+')  // Only match numeric IDs
        ->name('classroom.course');



});
