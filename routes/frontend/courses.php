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

// Simple GET test route for enrollment debugging
Route::get('/test-enroll/{course}', function (App\Models\Course $course) {
    if (!auth()->check()) {
        return 'Please log in first';
    }

    try {
        $controller = new App\Http\Controllers\Web\EnrollmentController();
        return $controller->AutoPayFlowPro($course);
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage() . '<br>Stack: ' . nl2br($e->getTraceAsString());
    }
})->middleware('auth')->name('test.enroll');
