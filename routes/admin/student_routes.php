<?php

Route::match(['GET', 'POST', 'DELETE'], 'students/{view?}/{id?}', [App\Http\Controllers\Admin\Students\StudentController::class, 'dashboard'])
    ->name('admin.students');
