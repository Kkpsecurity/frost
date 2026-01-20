<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Courses\CourseManagementController;

/**
 * Admin Course Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('courses')->name('courses.')->group(function () {

    // Main course management routes
    Route::get('/', [CourseManagementController::class, 'index'])->name('management.index');
    Route::get('/create', [CourseManagementController::class, 'create'])->name('management.create');
    Route::post('/', [CourseManagementController::class, 'store'])->name('management.store');
    Route::get('/{course}', [CourseManagementController::class, 'show'])->name('management.show');
    Route::get('/{course}/edit', [CourseManagementController::class, 'edit'])->name('management.edit');
    Route::put('/{course}', [CourseManagementController::class, 'update'])->name('management.update');
    Route::delete('/{course}', [CourseManagementController::class, 'destroy'])->name('management.destroy');

    // Course status management
    Route::patch('/{course}/toggle-active', [CourseManagementController::class, 'toggleActive'])->name('toggle-active');

});
