<?php

Route::get('center/dashboard', [App\Http\Controllers\Admin\AdminCenter\CenterController::class, 'dashboard'])
    ->name('admin.center.dashboard');

Route::get('center/settings', [App\Http\Controllers\Admin\AdminCenter\CenterController::class, 'settings'])
    ->name('admin.center.settings');

Route::get('center/server_logs', [App\Http\Controllers\Admin\AdminCenter\CenterController::class, 'server_logs'])
    ->name('admin.center.server_logs');

