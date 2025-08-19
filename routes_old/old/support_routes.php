<?php

Route::get('frost-support/dashboard', 
    [App\Http\Controllers\Admin\Frost\SupportController::class, 'dashboard'])
    ->name('admin.frost-support.dashboard');

Route::get('frost-support/dashboard/create_ticket', 
    [App\Http\Controllers\Admin\Frost\SupportController::class, 'dashboard'])
    ->name('admin.frost-support.dashboard.create_ticket');

Route::post('frost-support/search', 
    [App\Http\Controllers\Admin\Frost\SupportController::class, 'searchStudents'])
    ->name('admin.frost-support.search');

Route::get('frost-support/dashboard/get-student-data/{student_id}', 
    [App\Http\Controllers\Admin\Frost\SupportController::class, 'getStudentClassData'])
    ->name('admin.frost-support.dashboard.get-student-data');

Route::post('frost-support/student-profile/update', 
    [App\Http\Controllers\Admin\Frost\SupportController::class, 'updateStudentProfile'])
    ->name('admin.frost-support.student-profile.update');

