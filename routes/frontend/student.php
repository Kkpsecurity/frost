<?php

use App\Http\Controllers\React\StudentPortalController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
|
| Routes for student portal, classroom, and React components
|
*/

/**
 * Student Dashboard Routes
 */
Route::prefix('student')->name('student.')->middleware('auth')->group(function () {
    // Main student dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])
        ->name('dashboard');

    // API endpoints for student dashboard
    Route::get('/api/progress/{courseId}', [StudentDashboardController::class, 'getCourseProgress'])
        ->name('api.progress');

    Route::post('/api/lesson/{lessonId}/progress', [StudentDashboardController::class, 'updateLessonProgress'])
        ->name('api.lesson-progress');

    Route::get('/api/assignments', [StudentDashboardController::class, 'getAssignments'])
        ->name('api.assignments');

    Route::post('/api/assignments/{assignmentId}/submit', [StudentDashboardController::class, 'submitAssignment'])
        ->name('api.submit-assignment');

    Route::get('/api/activity', [StudentDashboardController::class, 'getActivityFeed'])
        ->name('api.activity');

    Route::get('/api/stats', [StudentDashboardController::class, 'getStats'])
        ->name('api.stats');
});

/**
 * Student Classroom / React mount
 */
Route::redirect('dashboard', 'classroom', 302);

Route::prefix('classroom')->name('classroom.')->group(function () {
    // Student dashboard (will mount React student app)
    Route::get('/', [StudentPortalController::class, 'dashboard'])
        ->name('dashboard')
        ->middleware('auth');

    // Legacy/portal routes used by the React app
    Route::get('/portal/class/{course_auth_id}', [StudentPortalController::class, 'RunPortal'])
        ->name('portal.class')
        ->middleware('auth');
    
    // Zoom player routes
    Route::get('/zoom/{course_auth_id}/{course_date_id}', [StudentPortalController::class, 'getZoomPlayer'])
        ->name('zoom.player')
        ->middleware('auth');
});

/**
 * Student API Routes (for React components)
 */
Route::prefix('api/student')->name('api.student.')->middleware('auth')->group(function () {
    Route::get('/classroom/{CourseAuth}', [StudentPortalController::class, 'getClassRoomData'])
        ->name('classroom.data');
    
    Route::post('/challenge/{student_lesson_id}', [StudentPortalController::class, 'studentChallenge'])
        ->name('challenge');
    
    Route::post('/agreement', [StudentPortalController::class, 'updateAgreement'])
        ->name('agreement.update');
    
    Route::post('/mark-completed', [StudentPortalController::class, 'studentMarkCompleted'])
        ->name('mark.completed');
    
    Route::post('/challenge-expired', [StudentPortalController::class, 'studentChallengeExpired'])
        ->name('challenge.expired');
    
    Route::post('/save-id-data', [StudentPortalController::class, 'saveIdData'])
        ->name('save.id.data');
    
    Route::get('/zoom-data/{course_date_id}/{course_auth_id}', [StudentPortalController::class, 'getZoomData'])
        ->name('zoom.data');
    
    Route::post('/close-lesson', [StudentPortalController::class, 'closeLesson'])
        ->name('close.lesson');
    
    Route::get('/course-date/{CourseAuth}', [StudentPortalController::class, 'getCurrentCourseDateID'])
        ->name('course.date');
    
    Route::post('/set-browser', [StudentPortalController::class, 'setBrowser'])
        ->name('set.browser');
});

/**
 * Student Zoom Player
 */
Route::get('/student/player', [StudentPortalController::class, 'StudentPlayer'])
    ->name('student.player')
    ->middleware('auth');
