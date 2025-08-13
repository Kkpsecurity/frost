<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Frost\CourseDateController;

Route::prefix('instructors')->group(function () {

    // Dashboard & Validation


    Route::get('/', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'dashboard'])
        ->name('admin.instructors');


});




//  Route::get('validate', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'validateInstructorSession'])
//         ->name('admin.instructors.validate');

//     // Assignments & Lesson Controls
//     Route::post('assign/{course_date}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'assignInstructor']);
//     Route::post('pause-lesson/{status}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'PauseLesson']);
//     Route::post('lesson/start/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'startLesson']);
//     Route::post('active_lesson', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'activateLesson']);
//     Route::post('complete_lesson', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'completeLesson']);
//     Route::post('complete_course', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'completeCourse']);

//     // Zoom & Payload
//     Route::get('zoom_meeting/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'zoomMeeting'])
//         ->name('admin.instructors.zoom');
//     Route::get('attach_zoom_meeting/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'updateZoomCredentials']);
//     Route::post('update_zoom_data', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'updateZoomData']);
//     Route::get('reset_payload/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'resetPayload']);
//     Route::get('resetPayload/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'resetPayload']);

//     // Students
//     Route::get('get_students/{course_date}/{page}/{search?}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getStudents']);
//     Route::post('validate/student', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'validateStudentID']);
//     Route::get('student/{student_id}/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getStudentByID']);
//     Route::post('student/delete-file', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'removeStudentPhoto']);
//     Route::get('get_student_detail/{student_id}/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getStudentByID']);

//     // Assistants
//     Route::get('assistants/{assistant_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getAssistant']);
//     Route::post('assign/assistant', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'assignAssistant']);

//     // Course Data
//     Route::get('portal/course/get', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'getClassData'])
//         ->name('admin.instructors.portal.course');
//     Route::get('completed/{course_date_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'completedCourseData']);

//     // Validation & Access
//     Route::get('validate/student/{course_auth}/{course_date}', [App\Http\Controllers\React\LaravelSharedData::class, 'getStudent'])
//         ->name('admin.instructors.validate.student');
//     Route::post('allow-access/{student_unit_id}', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'allowAccess']);

//     // Reassign
//     Route::post('reassign', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'reassignInstructor']);

//     // Student Tools
//     Route::post('student_tools/revoke-dnc', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'revokeDNC']);
//     Route::post('student_tools/{studentUnitId}/ban', [App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class, 'banStudent']);
