<?php

Route::get('courses/dashboard', [App\Http\Controllers\Admin\Courses\CourseController::class, 'dashboard'])
    ->name('admin.courses.dashboard');
