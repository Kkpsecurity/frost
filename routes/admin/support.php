<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FrostSupportController;
use App\Http\Controllers\Admin\SupportController;

// All support routes require admin authentication
Route::middleware(['admin', 'admin.support'])->group(function () {
    // Support SPA Dashboard
    Route::get('/frost-support', [FrostSupportController::class, 'index'])->name('frost-support');

    // Support endpoints
    Route::get('/support/search-users', [SupportController::class, 'searchUsers'])->name('support.search-users');
    Route::get('/support/poll-data', [SupportController::class, 'pollData'])->name('support.poll-data');
    Route::post('/support/update-student/{studentId}', [SupportController::class, 'updateStudentDetails'])->name('support.update-student');

    // Exam management
    Route::post('/support/reset-exam/{examAuthId}', [SupportController::class, 'resetExam'])->name('support.reset-exam');
    Route::get('/support/exam-review/{examAuthId}', [SupportController::class, 'getExamReview'])->name('support.exam-review');

    // Student tools
    Route::post('/support/student-tools/toggle-ban/{courseAuthId}', [SupportController::class, 'toggleBan'])->name('support.student-tools.toggle-ban');
    Route::post('/support/student-tools/toggle-day-ban/{studentUnitId}', [SupportController::class, 'toggleDayBan'])->name('support.student-tools.toggle-day-ban');
    Route::get('/support/student-tools/lessons-for-day/{studentUnitId}', [SupportController::class, 'getLessonsForDay'])->name('support.student-tools.lessons-for-day');
    Route::post('/support/student-tools/grant-lesson/{studentUnitId}', [SupportController::class, 'grantLesson'])->name('support.student-tools.grant-lesson');
    Route::post('/support/student-tools/reverse-lesson-dnc/{studentLessonId}', [SupportController::class, 'reverseLessonDnc'])->name('support.student-tools.reverse-lesson-dnc');
});
