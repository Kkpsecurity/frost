<?php

use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\ClassroomChatController;
use App\Http\Controllers\Student\AskInstructorController;
use App\Http\Controllers\Student\ChallengeController;
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

    // Offline classroom: course-level documents (PDFs under public/docs/<course-slug>/)
    Route::get('/classroom/course/documents', [StudentDashboardController::class, 'getCourseDocuments'])
        ->name('classroom.course.documents');

    // Offline / self-study: fetch course lessons with completion markers
    Route::get('/classroom/self-study/lessons', [StudentDashboardController::class, 'getSelfStudyLessons'])
        ->name('classroom.self-study.lessons');

    /**
     * New Configuration-Based Student Data Routes
     */
    Route::get('/classroom/student/data-array', [StudentDashboardController::class, 'getStudentDataArray'])
        ->name('classroom.student.data-array');

    Route::get('/classroom/student/poll', [StudentDashboardController::class, 'getStudentPollData'])
        ->name('classroom.student.poll');

    /**
     * Classroom Chat Routes
     */
    Route::get('/classroom/chat', [ClassroomChatController::class, 'getChat'])
        ->name('classroom.chat.get');

    Route::post('/classroom/chat-messages', [ClassroomChatController::class, 'postChatMessage'])
        ->name('classroom.chat.post-message');

    /**
     * Challenge System Routes (Step 2: Challenge Response Endpoint)
     */
    Route::post('/classroom/challenge-respond', [ChallengeController::class, 'respond'])
        ->name('classroom.challenge.respond');

    /**
     * Ask Instructor (private queue)
     */
    Route::post('/classroom/ask-instructor', [AskInstructorController::class, 'submit'])
        ->name('classroom.ask-instructor.submit');

    Route::get('/classroom/ask-instructor/my', [AskInstructorController::class, 'myQueue'])
        ->name('classroom.ask-instructor.my');

    Route::get('/classroom/session/mode', [AskInstructorController::class, 'getSessionMode'])
        ->name('classroom.session.mode');

    /**
     * Zoom Portal Routes - Iframe isolated Zoom SDK
     */
    Route::get('/classroom/portal/zoom/screen_share/{courseAuthId}/{courseDateId}', [StudentDashboardController::class, 'zoomScreenShare'])
        ->name('classroom.zoom.screen-share');

    Route::post('/classroom/portal/zoom/generate-signature', [StudentDashboardController::class, 'generateZoomSignature'])
        ->name('classroom.zoom.generate-signature');

    /**
     * Student Portal Agreement Route (legacy/original flow)
     */
    Route::post('/classroom/portal/student/agreement', [StudentDashboardController::class, 'postStudentAgreement'])
        ->name('classroom.portal.student.agreement');

    Route::post('/classroom/portal/student/rules', [StudentDashboardController::class, 'acceptRules'])
        ->name('classroom.portal.student.rules');

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

    /**
     * Class Status Routes for Waiting Room / Onboarding Flow
     */
    Route::get('/api/student/class-status/{courseDateId}', [StudentDashboardController::class, 'getClassStatus'])
        ->where('courseDateId', '[0-9]+')
        ->name('api.student.class-status');

    Route::get('/api/student/find-active-class', [StudentDashboardController::class, 'findActiveClass'])
        ->name('api.student.find-active-class');

    Route::get('/api/student/course-auth/{courseAuthId}/lessons', [StudentDashboardController::class, 'getCourseAuthLessons'])
        ->where('courseAuthId', '[0-9]+')
        ->name('api.student.course-auth.lessons');

    /**
     * Student ID Verification Routes
     */
    Route::post('/classroom/id-verification/start', [StudentDashboardController::class, 'startIdVerification'])
        ->name('classroom.id-verification.start');

    Route::post('/classroom/id-verification/upload-headshot', [StudentDashboardController::class, 'uploadHeadshot'])
        ->name('classroom.id-verification.upload-headshot');

    Route::post('/classroom/upload-student-photo', [StudentDashboardController::class, 'uploadStudentPhoto'])
        ->name('classroom.upload-student-photo');

    Route::get('/classroom/id-verification/status/{studentId}', [StudentDashboardController::class, 'getIdVerificationStatus'])
        ->where('studentId', '[0-9]+')
        ->name('classroom.id-verification.status');

    Route::get('/classroom/id-verification/summary/{verificationId}', [StudentDashboardController::class, 'getIdVerificationSummary'])
        ->where('verificationId', '[0-9]+')
        ->name('classroom.id-verification.summary');

    /**
     * Student Session & Lesson Management Routes (Phase 1)
     */
    // Session synchronization endpoints
    Route::get('/classroom/session/check-sync', [StudentDashboardController::class, 'checkSessionSync'])
        ->name('classroom.session.check-sync');

    Route::get('/classroom/session/restore', [StudentDashboardController::class, 'restoreSession'])
        ->name('classroom.session.restore');

    Route::post('/classroom/session/create', [StudentDashboardController::class, 'createSession'])
        ->name('classroom.session.create');

    // Lesson management endpoints
    Route::post('/classroom/lesson/start', [StudentDashboardController::class, 'startLessonSession'])
        ->name('classroom.lesson.start');

    Route::post('/classroom/lesson/sync-progress', [StudentDashboardController::class, 'syncLessonProgress'])
        ->name('classroom.lesson.sync-progress');

    Route::post('/classroom/lesson/pause', [StudentDashboardController::class, 'pauseLessonSession'])
        ->name('classroom.lesson.pause');

    Route::post('/classroom/lesson/complete', [StudentDashboardController::class, 'completeLessonSession'])
        ->name('classroom.lesson.complete');

    /**
     * Self-Study Lesson Session Management Routes (Phase 5)
     * Controller: StudentLessonSessionController
     */
    Route::post('/classroom/lesson/start-session', [\App\Http\Controllers\Student\StudentLessonSessionController::class, 'startSession'])
        ->name('classroom.lesson.start-session');

    Route::post('/classroom/lesson/update-progress', [\App\Http\Controllers\Student\StudentLessonSessionController::class, 'updateProgress'])
        ->name('classroom.lesson.update-progress');

    Route::post('/classroom/lesson/track-pause', [\App\Http\Controllers\Student\StudentLessonSessionController::class, 'trackPause'])
        ->name('classroom.lesson.track-pause');

    Route::post('/classroom/lesson/complete-session', [\App\Http\Controllers\Student\StudentLessonSessionController::class, 'completeSession'])
        ->name('classroom.lesson.complete-session');

    Route::get('/classroom/lesson/session-status/{sessionId}', [\App\Http\Controllers\Student\StudentLessonSessionController::class, 'getSessionStatus'])
        ->where('sessionId', '[0-9a-f\-]{36}')  // UUID format
        ->name('classroom.lesson.session-status');

    /**
     * Dev Tools Routes (only in debug mode)
     */
    Route::post('/classroom/dev/complete-all-lessons', [\App\Http\Controllers\Student\DevToolsController::class, 'completeAllLessons'])
        ->name('classroom.dev.complete-all-lessons');

    /**
     * Exam API Routes
     */
    Route::get('/classroom/exam/auth/{examAuth}', [\App\Http\Controllers\Student\ExamController::class, 'getExamAuth'])
        ->where('examAuth', '[0-9]+')
        ->name('exam.auth');

    Route::post('/classroom/exam/begin', [\App\Http\Controllers\Student\ExamController::class, 'beginExam'])
        ->name('exam.begin');

    Route::post('/classroom/exam/start/{examAuth}', [\App\Http\Controllers\Student\ExamController::class, 'startExamTimer'])
        ->where('examAuth', '[0-9]+')
        ->name('exam.start');

    Route::post('/classroom/exam/submit/{examAuth}', [\App\Http\Controllers\Student\ExamController::class, 'submitExam'])
        ->where('examAuth', '[0-9]+')
        ->name('exam.submit');

    // Get all exam attempts for review
    Route::get('/classroom/exam/attempts', [\App\Http\Controllers\Student\ExamController::class, 'getExamAttempts'])
        ->name('exam.attempts');

    // Dev-only route to reset exam attempts
    Route::post('/classroom/exam/reset', [\App\Http\Controllers\Student\ExamController::class, 'resetExam'])
        ->name('exam.reset');

    /**
     * Student Activity Tracking Routes
     */
    Route::prefix('api/student/activity')->group(function () {
        Route::post('/site-entry', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackSiteEntry'])
            ->name('api.student.activity.site-entry');

        Route::post('/site-exit', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackSiteExit'])
            ->name('api.student.activity.site-exit');

        Route::post('/classroom-entry', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackClassroomEntry'])
            ->name('api.student.activity.classroom-entry');

        Route::post('/agreement-accepted', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackAgreementAccepted'])
            ->name('api.student.activity.agreement-accepted');

        Route::post('/rules-accepted', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackRulesAccepted'])
            ->name('api.student.activity.rules-accepted');

        Route::post('/tab-visibility', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackTabVisibility'])
            ->name('api.student.activity.tab-visibility');

        Route::post('/button-click', [\App\Http\Controllers\Student\StudentActivityController::class, 'trackButtonClick'])
            ->name('api.student.activity.button-click');

        Route::get('/timeline', [\App\Http\Controllers\Student\StudentActivityController::class, 'getTimeline'])
            ->name('api.student.activity.timeline');

        Route::get('/away-time', [\App\Http\Controllers\Student\StudentActivityController::class, 'getAwayTime'])
            ->name('api.student.activity.away-time');
    });

    // Main student classroom dashboard
    Route::get('/classroom', [StudentDashboardController::class, 'dashboard'])
        ->name('classroom.dashboard');

    // Student classroom with course ID - MUST come last
    Route::get('/classroom/{id}', [StudentDashboardController::class, 'dashboard'])
        ->where('id', '[0-9]+')  // Only match numeric IDs
        ->name('classroom.course');
});
