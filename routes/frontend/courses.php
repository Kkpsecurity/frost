<?php

use App\Http\Controllers\Frontend\Courses\CoursesController;
use App\Http\Controllers\Frontend\Site\ScheduleController;
use App\Http\Controllers\Frontend\Courses\EnrollmentController;
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

Route::get('/api/courses/schedule-data', [App\Http\Controllers\Frontend\Courses\CourseController::class, 'getScheduleData'])
    ->name('courses.schedule.data');

Route::get('/courses/enroll/{course}', [CoursesController::class, 'enroll'])
    ->name('courses.enroll')
    ->middleware('auth');

Route::get('/courses/{course}', [CoursesController::class, 'show'])
    ->name('courses.show');

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
