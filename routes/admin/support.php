<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupportDashboardController;

/**
 * Support dashboard management routes
 * @author rclark <richievc@gmail.com>
 */

Route::prefix('support')->name('support.')->middleware(['admin'])->group(function () {
    // Support dashboard
    Route::get('/', function () {
        return view('dashboards.support.index');
    })->name('dashboard');

    // API endpoints
    Route::get('/api/stats', [SupportDashboardController::class, 'getStats'])
        ->name('api.stats');

    Route::get('/api/tickets', [SupportDashboardController::class, 'getRecentTickets'])
        ->name('api.tickets');

    Route::post('/api/search-students', [SupportDashboardController::class, 'searchStudents'])
        ->name('api.search-students');

    Route::patch('/api/tickets/{ticketId}', [SupportDashboardController::class, 'updateTicket'])
        ->name('api.update-ticket');

    Route::post('/api/tickets', [SupportDashboardController::class, 'createTicket'])
        ->name('api.create-ticket');

    Route::post('/api/reports', [SupportDashboardController::class, 'generateReport'])
        ->name('api.generate-report');
});

// Legacy support routes (keep for backwards compatibility)
Route::prefix('frost-support')->name('frost-support.')->middleware(['admin'])->group(function () {
    // Support center routes
    Route::get('/', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'index'])
        ->name('index');

    Route::get('/stats', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStats'])
        ->name('stats');

    Route::get('/search-students', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'searchStudents'])
        ->name('search-students');

    Route::post('/search', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'searchStudents'])
        ->name('search');

    Route::get('/student/{studentId}', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentDetails'])
        ->name('student.details');

    Route::get('/student/{studentId}/activity', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentActivity'])
        ->name('student.activity');

    Route::get('/student/{studentId}/courses', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentCourses'])
        ->name('student.courses');

    Route::get('/student/{studentId}/units', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentUnits'])
        ->name('student.units');

    Route::get('/student/{studentId}/dnc-lessons', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentDncLessons'])
        ->name('student.dnc-lessons');

    Route::get('/student/{studentId}/lessons', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentLessons'])
        ->name('student.lessons');

    Route::get('/student/{studentId}/exams', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'getStudentExams'])
        ->name('student.exams');

    Route::post('/ban-student-course', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'banStudentFromCourse'])
        ->name('ban.student.course');

    Route::post('/kick-student-unit', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'kickStudentFromUnit'])
        ->name('kick.student.unit');

    Route::post('/reinstate-student-lesson', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'reinstateStudentLesson'])
        ->name('reinstate.student.lesson');

    Route::post('/mark-student-lesson-dnc', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'markStudentLessonDnc'])
        ->name('mark.student.lesson.dnc');

    Route::post('/unban-student-course', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'unbanStudentFromCourse'])
        ->name('unban.student.course');

    Route::post('/allow-student-back-unit', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'allowStudentBackToUnit'])
        ->name('allow.student.back.unit');

    // Temporary debug route
    Route::get('/debug-db', [\App\Http\Controllers\Admin\SupportCenter\FrostSupportDashboardController::class, 'debugDatabase'])
        ->name('debug.database');
});
