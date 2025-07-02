<?php

/**
 * Instructor Dashboard
 */
Route::get(
    'instructors/dashboard',
    [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'dashboard'
    ]
)
    ->name('admin.instructors.dashboard');

/**
 * Instructor Validation
 */
Route::get(
    'instructors/validate',
    [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'validateInstructorSession'
    ]
)
    ->name('admin.instructors.validate');

/**
 * Assigns Instructor to a Course Date
 */
Route::post(
    'instuctors/assign/{course_date}',
    [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'assignInstructor'
    ]
);

/**
 * Pauses the Lesson
 */
Route::post(
    'instructors/pause-lesson/{status}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'PauseLesson']
);

/**
 * Polls ClassRoomData
 */
Route::get(
    'instructors/portal/course/get',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'getClassData']
)
    ->name('admin.instructors.portal.course');

/**
 * The ZoomMeeting
 */
Route::get(
    'instructors/zoom_meeting/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'zoomMeeting']
)
    ->name('admin.instructors.zoom');

/**
 * Starts The Lesson
 */
Route::post(
    'instructors/lesson/start/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'startLesson']
);

/**
 * Resets The Zoom Payload
 */
Route::get(
    'instructors/reset_payload/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'resetPayload']
);

Route::get(
    'instructors/get_students/{course_date}/{page}/{search?}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'getStudents']
);

Route::post(
    'instructors/validate/student',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'validateStudentID']
);

Route::get(
    'instructors/zoom_meeting/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'zoomMeeting']
);

Route::get(
    'instructors/attach_zoom_meeting/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'updateZoomCredentials']
);

Route::post(
    'instructors/update_zoom_data',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'updateZoomData']
);

Route::get(
    'instructors/resetPayload/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'resetPayload']
);

Route::get(
    'instructors/student/{student_id}/{course_date_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getStudentByID']
);

Route::post('instructors/student/delete-file', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'removeStudentPhoto']);

Route::post(
    'instructors/active_lesson',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'activateLesson']
);

Route::post(
    'instructors/complete_lesson',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'completeLesson']
);

Route::post(
    'instructors/complete_course',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'completeCourse']
);

Route::get(
    'instructors/assistants/{assistant_id}',
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 
    'getAssistant']
);

/**
 * Gets the Selected student base on the Course Auth ID
 */
Route::get('instructors/validate/student/{course_auth}/{course_date}', 
[App\Http\Controllers\React\LaravelSharedData::class, 'getStudent'])
    ->name('admin.instructors.validate.student');

Route::post('instructors/allow-access/{student_unit_id}', 
    [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'allowAccess']);


Route::get('instructors/completed/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'completedCourseData']);

Route::post('instructors/reassign', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'reassignInstructor']);
Route::post('instructors/assign/assistant', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'assignAssistant']);

Route::get('instructors/get_student_detail/{student_id}/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getStudentByID']);

Route::post('instructors/student_tools/revoke-dnc', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'revokeDNC']);
Route::post('instructors/student_tools/{studentUnitId}/ban', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'banStudent']);