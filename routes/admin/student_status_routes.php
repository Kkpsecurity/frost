<?php

/**
 * DNC Route
 * Allows access to class if late 
 */
Route::post('students/statuses/allow-access/{student_unit_id}', 
    [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'allowAccess']);

/**
 * Eject From Class
 * this route will remove the student from the class and or reenter
 */
Route::post('students/statuses/eject/{student_unit_id}', 
    [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'ejectStudent']);

/**
 * Student Ban Route
 */
Route::post('students/statuses/ban/{student_unit_id}', 
    [App\Http\Controllers\Admin\Services\StudentToolActionsController::class, 'banStudent']);