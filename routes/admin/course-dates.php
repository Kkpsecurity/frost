<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Frost\CourseDateController;

/**
 * Admin Course Dates Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('course-dates')->name('course-dates.')->group(function () {

    // Main course date management routes
    Route::get('/', [CourseDateController::class, 'index'])->name('index');
    Route::get('/create', [CourseDateController::class, 'create'])->name('create');
    Route::post('/', [CourseDateController::class, 'store'])->name('store');
    Route::get('/{courseDate}', [CourseDateController::class, 'show'])->name('show');
    Route::get('/{courseDate}/edit', [CourseDateController::class, 'edit'])->name('edit');
    Route::put('/{courseDate}', [CourseDateController::class, 'update'])->name('update');
    Route::delete('/{courseDate}', [CourseDateController::class, 'destroy'])->name('destroy');

    // Course date status management
    Route::patch('/{courseDate}/toggle-active', [CourseDateController::class, 'toggleActive'])->name('toggle-active');

    // Get course units for AJAX
    Route::get('/api/courses/{course}/units', [CourseDateController::class, 'getCourseUnits'])->name('api.course-units');

});
