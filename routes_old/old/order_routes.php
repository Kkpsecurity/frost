<?php

Route::get('orders', [App\Http\Controllers\Admin\Orders\OrderController::class, 'dashboard'])
    ->name('admin.orders');
Route::get('orders/dashboard', [App\Http\Controllers\Admin\Orders\OrderController::class, 'dashboard'])
    ->name('admin.orders.dashboard');
