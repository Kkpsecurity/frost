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
        Route::post('/assign-instructor/{courseDateId}', [InstructorDashboardController::class, 'assignInstructor'])
            ->name('assign-instructor');

        Route::post('/start-class/{courseDateId}', [InstructorDashboardController::class, 'startClass'])
            ->name('start-class');

        Route::post('/end-class', [InstructorDashboardController::class, 'endClass'])
            ->name('end-class');

        Route::post('/take-over', [InstructorDashboardController::class, 'takeOverClass'])
            ->name('take-over');

        Route::post('/assist/{courseDateId?}', [InstructorDashboardController::class, 'assistClass'])
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

        // Current user info for React components
        Route::get('/user/current', [InstructorDashboardController::class, 'getCurrentUser'])
            ->name('user.current');

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

        // Upcoming courses data split by D40/G28 types for React components
        Route::get('/upcoming-courses', [InstructorDashboardController::class, 'getUpcomingCoursesData'])
            ->name('upcoming-courses');

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

    // =====================================================
    // CLASSROOM DATA ARRAY ENDPOINTS (Configuration-Driven)
    // =====================================================

    Route::prefix('classroom-data')->name('classroom-data.')->group(function () {
        // Complete classroom data array (replaces legacy data endpoints)
        Route::get('/array', [InstructorDashboardController::class, 'getClassroomDataArray'])
            ->name('array');

        // Lightweight polling data for real-time updates
        Route::get('/poll', [InstructorDashboardController::class, 'getClassroomPollData'])
            ->name('poll');

        // Instructor-specific classroom data
        Route::get('/instructor', [InstructorDashboardController::class, 'getInstructorClassroomData'])
            ->name('instructor');

        // Lesson management data
        Route::get('/lessons', [InstructorDashboardController::class, 'getLessonManagementData'])
            ->name('lessons');
    });

    // =====================================================
    // LESSON MANAGEMENT - Start/Complete/Track Lessons
    // =====================================================

    Route::prefix('lessons')->name('lessons.')->group(function () {
        // Get current instructor lesson state for a course date
        Route::get('/state/{courseDateId}', [InstructorDashboardController::class, 'getInstructorLessonState'])
            ->name('state');

        // Start a lesson (create InstLesson record)
        Route::post('/start', [InstructorDashboardController::class, 'startLesson'])
            ->name('start');

        // Complete a lesson (mark InstLesson as completed)
        Route::post('/complete', [InstructorDashboardController::class, 'completeLesson'])
            ->name('complete');

        // Pause a lesson (instructor break)
        Route::post('/pause', [InstructorDashboardController::class, 'pauseLesson'])
            ->name('pause');

        // Resume a lesson (end break)
        Route::post('/resume', [InstructorDashboardController::class, 'resumeLesson'])
            ->name('resume');

        // Get screen sharing status for classroom preparation
        Route::get('/screen-sharing/status/{courseDateId}', [InstructorDashboardController::class, 'getScreenSharingStatus'])
            ->name('screen-sharing.status');
    });

    // =====================================================
    // STUDENT TRACKING - Real-time Student Panel Data
    // =====================================================

    Route::prefix('students')->name('students.')->group(function () {
        // Get students for a specific course date (real-time tracking)
        Route::get('/course-date/{courseDateId}', [InstructorDashboardController::class, 'getStudentsForCourseDate'])
            ->name('course-date');
    });

    // =====================================================
    // ZOOM MANAGEMENT - Screen Sharing Control
    // =====================================================

    Route::prefix('zoom')->name('zoom.')->group(function () {
        // Get current zoom status for instructor
        Route::get('/status', [InstructorDashboardController::class, 'getZoomStatus'])
            ->name('status');

        // Toggle zoom status (enable/disable)
        Route::post('/toggle', [InstructorDashboardController::class, 'toggleZoomStatus'])
            ->name('toggle');
    });

    // =====================================================
    // POLLING ROUTES - Instructor Dashboard Real-time Data
    // =====================================================
    Route::get('/instructor/data', [InstructorDashboardController::class, 'getInstructorData'])
        ->name('instructor.data');
    Route::get('/classroom/data', [InstructorDashboardController::class, 'getClassroomData'])
        ->name('classroom.data');
    Route::get('/classroom/chat', [InstructorDashboardController::class, 'getChatMessages'])
        ->name('classroom.chat');
});
