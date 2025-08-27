<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Instructors\InstructorDashboardController;

/**
 * Instructor classroom management routes
 * @author rclark <richievc@gmail.com>
 */

Route::prefix('instructors')->name('instructors.')->middleware(['admin'])->group(function () {
    // Instructor dashboard view (default to offline mode)
    Route::get('/', function () {
        return view('dashboards.instructor.offline');
    })->name('dashboard');

    // Offline Mode Dashboard (Bulletin Board)
    Route::get('/offline', function () {
        return view('dashboards.instructor.offline');
    })->name('offline');

    // Online Class Mode Dashboard
    Route::get('/online', function () {
        return view('dashboards.instructor.online');
    })->name('online');

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

    // New API endpoints for enhanced dashboard
    Route::get('/api/stats', [InstructorDashboardController::class, 'getStats'])
        ->name('api.stats');

    Route::get('/api/lessons', [InstructorDashboardController::class, 'getTodayLessons'])
        ->name('api.lessons');

    Route::get('/api/chat-messages', [InstructorDashboardController::class, 'getChatMessages'])
        ->name('api.chat');

    Route::post('/api/send-message', [InstructorDashboardController::class, 'sendMessage'])
        ->name('api.send-message');

    Route::get('/api/online-students', [InstructorDashboardController::class, 'getOnlineStudents'])
        ->name('api.online-students');
});
