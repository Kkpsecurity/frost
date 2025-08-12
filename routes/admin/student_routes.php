<?php

// Student Management Routes - Following AdminUser Pattern
Route::get('students', [App\Http\Controllers\Admin\Students\StudentController::class, 'index'])
    ->name('students.index');

Route::get('students/data', [App\Http\Controllers\Admin\Students\StudentController::class, 'getData'])
    ->name('students.data');

Route::get('students/create', [App\Http\Controllers\Admin\Students\StudentController::class, 'create'])
    ->name('students.create');

Route::post('students', [App\Http\Controllers\Admin\Students\StudentController::class, 'store'])
    ->name('students.store');

Route::get('students/{student}', [App\Http\Controllers\Admin\Students\StudentController::class, 'show'])
    ->name('students.show');

Route::get('students/{student}/edit', [App\Http\Controllers\Admin\Students\StudentController::class, 'edit'])
    ->name('students.edit');

Route::put('students/{student}', [App\Http\Controllers\Admin\Students\StudentController::class, 'update'])
    ->name('students.update');

Route::delete('students/{student}', [App\Http\Controllers\Admin\Students\StudentController::class, 'destroy'])
    ->name('students.destroy');

// Student details API endpoint
Route::get('students/details/{studentId}', [App\Http\Controllers\Admin\Students\StudentController::class, 'getStudentDetails'])
    ->name('students.details');
