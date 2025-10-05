<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Instructors\InstructorDashboardController;

/**
 * Instructor classroom management routes
 * @author rclark <richievc@gmail.com>
 */

Route::prefix('instructors')->name('instructors.')->middleware(['admin'])->group(function () {

    // =====================================================
    // INSTRUCTOR ROOT - Dashboard & Main Interface
    // =====================================================

    // Main instructor dashboard (building board style)
    Route::get('/', [InstructorDashboardController::class, 'dashboard'])
        ->name('dashboard');

    // Session validation for React components
    Route::get('/validate', [InstructorDashboardController::class, 'validateInstructorSession'])
        ->name('validate');

    // =====================================================
    // CLASSROOM INTERFACE - Live Class Management
    // =====================================================

    Route::prefix('classroom')->name('classroom.')->group(function () {
        // Offline Mode Dashboard (Bulletin Board)
        Route::get('/offline', function () {
            return view('dashboards.instructor.offline');
        })->name('offline');

        // Online Class Mode Dashboard (Live Class Interface)
        Route::get('/online', function () {
            return view('dashboards.instructor.online');
        })->name('online');

        // Classroom management actions
        Route::post('/start-class/{courseDateId}', [InstructorDashboardController::class, 'startClass'])
            ->name('start-class');

        Route::post('/take-over', [InstructorDashboardController::class, 'takeOverClass'])
            ->name('take-over');

        Route::post('/assist', [InstructorDashboardController::class, 'assistClass'])
            ->name('assist');

        // Chat functionality for live classes
        Route::get('/chat-messages', [InstructorDashboardController::class, 'getChatMessages'])
            ->name('chat.messages');

        Route::post('/send-message', [InstructorDashboardController::class, 'sendMessage'])
            ->name('chat.send');
    });

    // =====================================================
    // DATA ENDPOINTS - Active Data Fetching for Dashboard
    // =====================================================

    Route::prefix('data')->name('data.')->group(function () {
        // Today's lessons data
        Route::get('/lessons/today', [InstructorDashboardController::class, 'getTodayLessons'])
            ->name('lessons.today');

        // Upcoming lessons data
        Route::get('/lessons/upcoming', [InstructorDashboardController::class, 'getUpcomingLessons'])
            ->name('lessons.upcoming');

        // Previous lessons data
        Route::get('/lessons/previous', [InstructorDashboardController::class, 'getPreviousLessons'])
            ->name('lessons.previous');

        // Class overview statistics
        Route::get('/stats/overview', [InstructorDashboardController::class, 'getStats'])
            ->name('stats.overview');

        // Student data for active classes
        Route::get('/students/active', [InstructorDashboardController::class, 'getOnlineStudents'])
            ->name('students.active');

        // Classroom data and status
        Route::get('/classroom/status', [InstructorDashboardController::class, 'getClassroomData'])
            ->name('classroom.status');

        // Students enrolled in courses
        Route::get('/students/enrolled', [InstructorDashboardController::class, 'getStudentsData'])
            ->name('students.enrolled');

        // Bulletin board data (when no active classes)
        Route::get('/bulletin-board', [InstructorDashboardController::class, 'getBulletinBoardData'])
            ->name('bulletin-board');

        // Completed courses data (InstUnits that have been completed)
        Route::get('/completed-courses', [InstructorDashboardController::class, 'getCompletedCourses'])
            ->name('completed-courses');

        // Upcoming courses panel data (next 2 weeks overview)
        Route::get('/upcoming-courses-panel', [InstructorDashboardController::class, 'getUpcomingCoursesPanel'])
            ->name('upcoming-courses-panel');

        // DEBUG: Today's lessons with full structure
        Route::get('/debug/lessons/today', [InstructorDashboardController::class, 'debugTodayLessons'])
            ->name('debug.lessons.today');

        // Course lessons for sidebar
        Route::get('/lessons/{courseDateId}', [InstructorDashboardController::class, 'getCourseLessons'])
            ->name('lessons.course');

        // Recent activity data
        Route::get('/activity/recent', [InstructorDashboardController::class, 'getRecentActivity'])
            ->name('activity.recent');

        // Notifications data
        Route::get('/notifications/unread', [InstructorDashboardController::class, 'getUnreadNotifications'])
            ->name('notifications.unread');
    });
});
