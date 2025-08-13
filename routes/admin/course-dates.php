<?php

/**
 * Admin Course Dates Routes
 * Routes for course dates management functionality
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Frost\CourseDateController;

// Course Dates Management Routes
Route::prefix('course-dates')
    ->name('course-dates.')
    ->group(function () {
        
        // Course Dates Index/Dashboard
        Route::get('/', [
            CourseDateController::class,
            'index'
        ])->name('index');
        
        // Course Dates Create Form
        Route::get('/create', [
            CourseDateController::class,
            'create'
        ])->name('create');
        
        // Course Dates Store
        Route::post('/', [
            CourseDateController::class,
            'store'
        ])->name('store');
        
        // Course Dates Show
        Route::get('/{courseDate}', [
            CourseDateController::class,
            'show'
        ])->name('show');
        
        // Course Dates Edit Form
        Route::get('/{courseDate}/edit', [
            CourseDateController::class,
            'edit'
        ])->name('edit');
        
        // Course Dates Update
        Route::put('/{courseDate}', [
            CourseDateController::class,
            'update'
        ])->name('update');
        
        // Course Dates Delete
        Route::delete('/{courseDate}', [
            CourseDateController::class,
            'destroy'
        ])->name('destroy');

        // Toggle Active Status
        Route::post('/{courseDate}/toggle-active', [
            CourseDateController::class,
            'toggleActive'
        ])->name('toggle-active');

        // Get Course Units API endpoint
        Route::get('/course-units/{course}', [
            CourseDateController::class,
            'getCourseUnits'
        ])->name('course-units');
        
        // Course Dates Management sub-routes (for better organization)
        Route::prefix('management')
            ->name('management.')
            ->group(function () {
                
                Route::get('/', [
                    CourseDateController::class,
                    'index'
                ])->name('index');
                
                Route::get('/create', [
                    CourseDateController::class,
                    'create'
                ])->name('create');
                
                Route::post('/', [
                    CourseDateController::class,
                    'store'
                ])->name('store');
                
                Route::get('/{courseDate}', [
                    CourseDateController::class,
                    'show'
                ])->name('show');
                
                Route::get('/{courseDate}/edit', [
                    CourseDateController::class,
                    'edit'
                ])->name('edit');
                
                Route::put('/{courseDate}', [
                    CourseDateController::class,
                    'update'
                ])->name('update');
                
                Route::delete('/{courseDate}', [
                    CourseDateController::class,
                    'destroy'
                ])->name('destroy');

                // Toggle Active Status
                Route::post('/{courseDate}/toggle-active', [
                    CourseDateController::class,
                    'toggleActive'
                ])->name('toggle-active');

                // Get Course Units API endpoint
                Route::get('/course-units/{course}', [
                    CourseDateController::class,
                    'getCourseUnits'
                ])->name('course-units');
                
            });
            
    });
