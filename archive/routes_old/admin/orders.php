<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Orders\OrderController;

/**
 * Admin Orders Routes
 * These routes handle order management in the admin panel
 */

// Orders DataTables Data (AJAX endpoint) - Must come before resource routes
Route::get('orders/data', [OrderController::class, 'getData'])->name('orders.data');

// Export Orders - Must come before resource routes
Route::get('orders/export/{format}', [
    OrderController::class, 
    'export'
])->name('orders.export');

// Orders Resource Routes
Route::resource('orders', OrderController::class);

// Mark Order as Paid
Route::post('orders/{order}/mark-as-paid', [
    OrderController::class, 
    'markAsPaid'
])->name('orders.mark-as-paid');

// Process Order Refund
Route::post('orders/{order}/process-refund', [
    OrderController::class, 
    'processRefund'
])->name('orders.process-refund');

// Grant Manual CourseAuth (separate from orders)
Route::post('orders/grant-manual-course-auth', [
    OrderController::class, 
    'grantManualCourseAuth'
])->name('orders.grant-manual-course-auth');
