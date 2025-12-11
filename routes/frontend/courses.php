<?php

use App\Http\Controllers\Web\CoursesController;
use App\Http\Controllers\Web\ScheduleController;
use App\Http\Controllers\Web\EnrollmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Courses Routes
|--------------------------------------------------------------------------
|
| Routes for course browsing, enrollment, and scheduling
|
*/

// Courses routes
Route::get('/courses', [CoursesController::class, 'index'])
    ->name('courses.index');

Route::get('/courses/list', [CoursesController::class, 'list'])
    ->name('courses.list');

Route::get('/courses/schedules', [ScheduleController::class, 'index'])
    ->name('courses.schedules');

Route::get('/api/courses/schedule-data', [App\Http\Controllers\Web\Courses\CourseController::class, 'getScheduleData'])
    ->name('courses.schedule.data');

Route::get('/courses/{course}', [CoursesController::class, 'show'])
    ->name('courses.show');

Route::get('/courses/enroll/{course}', [CoursesController::class, 'enroll'])
    ->name('courses.enroll')
    ->middleware('auth');

// Legacy route alias for backwards compatibility
Route::get('/enroll/{course}', [CoursesController::class, 'enroll'])
    ->name('enroll')
    ->middleware('auth');

// Course enrollment processing route
Route::post('/courses/enroll/{course}', [EnrollmentController::class, 'AutoPayFlowPro'])
    ->name('courses.enroll.process')
    ->middleware('auth');

// Legacy enrollment processing route
Route::post('/enroll/{course}', [EnrollmentController::class, 'AutoPayFlowPro'])
    ->name('enroll.process')
    ->middleware('auth');


