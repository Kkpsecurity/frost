<?php

/**
 * Admin Courses Routes
 * Routes for course management functionality
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Courses\CourseController;
use App\Http\Controllers\Admin\Courses\CourseManagementController;

// Course Management Routes
Route::prefix('courses')
    ->name('courses.')
    ->group(function () {
        
        // Courses Index/Dashboard
        Route::get('/', [
            CourseManagementController::class,
            'index'
        ])->name('index');

        // Legacy course dashboard route (keep existing functionality)
        Route::get('/dashboard', [
            CourseController::class,
            'dashboard'
        ])->name('dashboard');
        
        // Courses Create Form
        Route::get('/create', [
            CourseManagementController::class,
            'create'
        ])->name('create');
        
        // Courses Store
        Route::post('/', [
            CourseManagementController::class,
            'store'
        ])->name('store');
        
        // Courses Show
        Route::get('/{course}', [
            CourseManagementController::class,
            'show'
        ])->name('show');
        
        // Courses Edit Form
        Route::get('/{course}/edit', [
            CourseManagementController::class,
            'edit'
        ])->name('edit');
        
        // Courses Update
        Route::put('/{course}', [
            CourseManagementController::class,
            'update'
        ])->name('update');
        
        // Courses Archive
        Route::patch('/{course}/archive', [
            CourseManagementController::class,
            'archive'
        ])->name('archive');
        
        // Courses Restore
        Route::patch('/{course}/restore', [
            CourseManagementController::class,
            'restore'
        ])->name('restore');
        
        // Courses Delete
        Route::delete('/{course}', [
            CourseManagementController::class,
            'destroy'
        ])->name('destroy');

        // Course Statistics API
        Route::get('/stats/course-types', [
            CourseManagementController::class,
            'courseTypeStats'
        ])->name('stats.course-types');
        
        // Course Management sub-routes (for better organization)
        Route::prefix('management')
            ->name('management.')
            ->group(function () {
                
                Route::get('/', [
                    CourseManagementController::class,
                    'index'
                ])->name('index');
                
                Route::get('/create', [
                    CourseManagementController::class,
                    'create'
                ])->name('create');
                
                Route::post('/', [
                    CourseManagementController::class,
                    'store'
                ])->name('store');
                
                Route::get('/{course}', [
                    CourseManagementController::class,
                    'show'
                ])->name('show');
                
                Route::get('/{course}/edit', [
                    CourseManagementController::class,
                    'edit'
                ])->name('edit');
                
                Route::put('/{course}', [
                    CourseManagementController::class,
                    'update'
                ])->name('update');
                
                Route::patch('/{course}/archive', [
                    CourseManagementController::class,
                    'archive'
                ])->name('archive');
                
                Route::patch('/{course}/restore', [
                    CourseManagementController::class,
                    'restore'
                ])->name('restore');
                
                Route::delete('/{course}', [
                    CourseManagementController::class,
                    'destroy'
                ])->name('destroy');
                
            });
            
    });
