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
});
