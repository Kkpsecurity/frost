<?php

Route::match(['POST', 'GET', 'DELETE'], 'center/adminusers/{view?}/{id?}',    
    [App\Http\Controllers\Admin\AdminCenter\AdminUserController::class, 'dashboard'])
    ->name('admin.center.adminusers');

