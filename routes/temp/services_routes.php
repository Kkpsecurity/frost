<?php


/**
 *Chat Routes
 */
Route::prefix('chat')->group(function () {
    Route::match(
        ["GET", "POST"],
        '/messages/{course_date_id}/{user_id}',
        [App\Http\Controllers\React\ChatController::class, 'manageChatMessages']
    )
        ->name('services.chat.messages');

    Route::get('/enable/{course_date_id}', [App\Http\Controllers\React\ChatController::class, 'enableChatSystem'])
        ->name('services.chat.enable');
});

/**
 * Video Chat Routes
 */
Route::prefix('frost_video')->group(function () {
    // Instructor Video request Routes
    Route::get('/queues/{course_date_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'getAllQueues'])
        ->name('services.video_chat.queues');
    Route::post('/call_student', [App\Http\Controllers\React\AgoraVideoCaller::class, 'callStudent']);
    Route::get('/check_call_status/{course_date_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'checkCallStatus']);
    Route::get('/listen_accept_call/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'listenForStudentAcceptCall']);
    Route::post('/end_call/', [App\Http\Controllers\React\AgoraVideoCaller::class, 'endCallRequest']);

    // Student Video Chat Routes
    Route::prefix('student')->group(function () {
        Route::get('/call_request/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'studentRequestCall']);
        Route::get('/validate_request/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'validateCallRequest']);
        Route::get('/cancel_request/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'studentCancelCall']);
        Route::get('/accept_request/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'studentAcceptsCall']);
        Route::get('/end_call/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'studentEndsCall']);
        Route::get('/inqueue/{course_date_id}/{user_id}', [App\Http\Controllers\React\AgoraVideoCaller::class, 'checkIfStudentInQueue']);
    });
});

/**
 * Challenege Routes
 */
Route::prefix('challenge')->group(function () {
    Route::post('/timed-out', [App\Http\Controllers\React\StudentPortalController::class, 'studentChallengeExpired'])
        ->name('services.challenge.timed_out');
    Route::post('/verify', [App\Http\Controllers\React\StudentPortalController::class, 'studentMarkCompleted'])
        ->name('services.challenge.verify');
});


/**
 * Student Tool Routes
 */
Route::prefix('student_tools')->group(function () {
    Route::post('/eject', [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'ejectStudent'])
        ->name('services.student_tools.eject');

    Route::post('/reinstate', [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'reInState'])
        ->name('services.student_tools.reinstate');
    

    Route::post('/ban', [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'banStudent'])
        ->name('services.student_tools.ban');
    Route::post('/allow_access', [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'reEnterAccess'])
        ->name('services.student_tools.allow_access');
    Route::post('/revoke_dnc', [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'revokeDNCStudent'])
        ->name('services.student_tools.revoke_dnc');
});


