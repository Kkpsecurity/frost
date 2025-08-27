<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Instructors\InstructorDashboardController;

/**
 * Instructor classroom management routes
 * @author rclark <richievc@gmail.com>
 */

Route::prefix('instructors')->name('instructors.')->middleware(['admin'])->group(function () {
    // Instructor dashboard view
    Route::get('/', [InstructorDashboardController::class, 'dashboard'])
        ->name('dashboard');

    // Validation endpoint used by React data layer
    Route::get('/validate', [InstructorDashboardController::class, 'validateInstructorSession'])
        ->name('validate');

    // API endpoints for data used in the instructor dashboard
    Route::get('/api/bulletin-board', [InstructorDashboardController::class, 'getBulletinBoardData'])
        ->name('api.bulletin-board');

    Route::get('/data/classroom', [InstructorDashboardController::class, 'getClassroomData'])
        ->name('data.classroom');

    Route::get('/data/students', [InstructorDashboardController::class, 'getStudentsData'])
        ->name('data.students');
});
