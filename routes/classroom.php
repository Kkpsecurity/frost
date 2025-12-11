<?php

use App\Http\Controllers\Classroom\ClassroomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Classroom Routes
|--------------------------------------------------------------------------
|
| API routes specifically for classroom-related functionality
|
*/

/**
 * Classroom Dashboard Routes
 */
Route::middleware(['auth'])->group(function () {

    // Classroom dashboard data (API endpoint)
    Route::get('/api/classroom/dashboard', [ClassroomController::class, 'dashboard'])
        ->name('api.classroom.dashboard');

    // Debug route for classroom data only
    Route::get('/api/classroom/debug', [ClassroomController::class, 'debugClass'])
        ->name('api.classroom.debug');

});

/**
 * Offline Play Routes
 */
Route::middleware(['auth'])->prefix('classroom/offline')->name('classroom.offline.')->group(function () {

    // Video Room - Show offline video player for a lesson
    Route::get('/video-room/{lessonId}', [\App\Http\Controllers\Student\OfflinePlayController::class, 'showVideoRoom'])
        ->name('video-room');

    // Balance Management
    Route::get('/balance', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getBalance'])
        ->name('balance');

    Route::get('/balance/summary', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getBalanceSummary'])
        ->name('balance.summary');

    // Session Management
    Route::post('/session/start', [\App\Http\Controllers\Student\OfflinePlayController::class, 'startSession'])
        ->name('session.start');

    Route::post('/session/{sessionId}/pause', [\App\Http\Controllers\Student\OfflinePlayController::class, 'pauseSession'])
        ->name('session.pause');

    Route::post('/session/{sessionId}/resume', [\App\Http\Controllers\Student\OfflinePlayController::class, 'resumeSession'])
        ->name('session.resume');

    Route::post('/session/{sessionId}/complete', [\App\Http\Controllers\Student\OfflinePlayController::class, 'completeSession'])
        ->name('session.complete');

    Route::get('/session/active', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getActiveSession'])
        ->name('session.active');

    Route::get('/session/{sessionId}', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getSessionDetails'])
        ->name('session.details');

    Route::get('/session/{sessionId}/status', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getSessionStatus'])
        ->name('session.status');

    Route::get('/session/history', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getSessionHistory'])
        ->name('session.history');

    // Break Management
    Route::post('/session/{sessionId}/break/start', [\App\Http\Controllers\Student\OfflinePlayController::class, 'startBreak'])
        ->name('break.start');

    Route::post('/break/{breakId}/end', [\App\Http\Controllers\Student\OfflinePlayController::class, 'endBreak'])
        ->name('break.end');

    Route::get('/session/{sessionId}/break/status', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getBreakStatus'])
        ->name('break.status');

    Route::get('/session/{sessionId}/break/summary', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getBreakSummary'])
        ->name('break.summary');

    // Checkpoint Management
    Route::post('/session/{sessionId}/checkpoint', [\App\Http\Controllers\Student\OfflinePlayController::class, 'createCheckpoint'])
        ->name('checkpoint.create');

    Route::get('/session/{sessionId}/checkpoint/latest', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getLatestCheckpoint'])
        ->name('checkpoint.latest');

    Route::get('/session/{sessionId}/checkpoint/restore', [\App\Http\Controllers\Student\OfflinePlayController::class, 'restoreCheckpoint'])
        ->name('checkpoint.restore');

    // Statistics & Monitoring
    Route::get('/statistics', [\App\Http\Controllers\Student\OfflinePlayController::class, 'getStatistics'])
        ->name('statistics');
});
