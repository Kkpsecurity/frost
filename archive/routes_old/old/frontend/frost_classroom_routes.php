<?php

use Illuminate\Http\Request;
#use Firebase\JWT\JWT;
#use Firebase\JWT\Key;

Route::prefix('classroom')->namespace('App\Http\Controllers\Web')->group(function () {

    /**
     * List All Authorized Orders for a Student
     */
    Route::get('/', [App\Http\Controllers\React\StudentPortalController::class, 'dashboard'])->name('classroom.dashboard');

    /**
     * Loads the Laravel View for the Student Portal
     */
    Route::get('/portal/class/{course_auth_id}', [App\Http\Controllers\React\StudentPortalController::class, 'RunPortal'])->name('classroom.portal.class');

    /**
     * Load the Class Data to Be Passed to React Application
     */
    Route::get('/portal/classdata/{course_auth}', [App\Http\Controllers\React\StudentPortalController::class, 'getClassRoomData'])->name('classroom.portal.classdata');

    /**
     * Load the Zoom ScreenSharing Player View
     */
    Route::get('/portal/zoom/screen_share/{course_auth_id}/{course_date_id}', [App\Http\Controllers\React\StudentPortalController::class, 'getZoomPlayer']);

    /**
     * Load Additional Zoom Data
     */
    Route::get('/portal/zoom/additional_data/{course_auth_id}/{course_date_id}', [App\Http\Controllers\React\StudentPortalController::class, 'getZoomData']);

    /**
     * Uploads a File to the Student Portal
     */
    Route::post('/portal/save_id_data', [App\Http\Controllers\React\StudentPortalController::class, 'saveIdData'])->name('classroom.portal.upload');

    /**
     * Update Student Agreement
     */
    Route::post('/portal/student/agreement', [App\Http\Controllers\React\StudentPortalController::class, 'updateAgreement']);

    /**
     * Get the Current Course Date ID
     */
    Route::get('/portal/get_current_course_date_id/{course_auth}', [App\Http\Controllers\React\StudentPortalController::class, 'getCurrentCourseDateID']);

    /**
     * Sets the Browser Type for the Student
     */
    Route::post('/portal/set_browser', [App\Http\Controllers\React\StudentPortalController::class, 'setBrowser']);

});
