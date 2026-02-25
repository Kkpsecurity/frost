<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CourseAuthsController;

/**
 * Admin Course Auths Routes
 * Prefix: /admin/course-auths
 * Loaded via glob from bootstrap/app.php
 */

Route::middleware(['admin'])->prefix('course-auths')->name('course-auths.')->group(function () {

    Route::get('/', [CourseAuthsController::class, 'index'])->name('index');
});
