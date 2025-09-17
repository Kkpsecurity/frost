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

    // Enter classroom for specific course
    Route::get('/classroom/enter/{courseAuth}', [StudentDashboardController::class, 'enterClassroom'])
        ->name('classroom.enter');

    /**
     * Student Polling Route
     */
    Route::get('/classroom/student/data', [StudentDashboardController::class, 'getStudentData'])
        ->name('classroom.student.data');






    /**
     * Classroom Polling Route
     */
    Route::get('/classroom/class/data', [StudentDashboardController::class, 'getClassData'])
        ->name('classroom.class.data');



    /**
     * DEBUG ROUTES - to be removed in production
     */
    Route::get('/classroom/debug', [StudentDashboardController::class, 'debug'])
        ->name('classroom.debug');

    // Debug route for classroom data only
    Route::get('/classroom/debug/class', [StudentDashboardController::class, 'debugClass'])
        ->name('classroom.debug.class');

    // Debug route for student data only
    Route::get('/classroom/debug/student', [StudentDashboardController::class, 'debugStudent'])
        ->name('classroom.debug.student');

    // Test route for lesson data
    Route::get('/classroom/test/lessons/{courseAuth}', [StudentDashboardController::class, 'testLessonData'])
        ->name('classroom.test.lessons');

});
