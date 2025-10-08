<?php

/**
 * Admin Lessons Routes
 * Routes for lesson management functionality
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Lessons\LessonManagementController;

// Lesson Management Routes
Route::prefix('lessons')
    ->name('lessons.')
    ->group(function () {
        
        // Lessons Index/Dashboard
        Route::get('/', [
            LessonManagementController::class,
            'index'
        ])->name('index');
        
        // Lessons Create Form
        Route::get('/create', [
            LessonManagementController::class,
            'create'
        ])->name('create');
        
        // Lessons Store
        Route::post('/', [
            LessonManagementController::class,
            'store'
        ])->name('store');
        
        // Lessons Show
        Route::get('/{lesson}', [
            LessonManagementController::class,
            'show'
        ])->name('show');
        
        // Lessons Edit Form
        Route::get('/{lesson}/edit', [
            LessonManagementController::class,
            'edit'
        ])->name('edit');
        
        // Lessons Update
        Route::put('/{lesson}', [
            LessonManagementController::class,
            'update'
        ])->name('update');
        
        // Lessons Delete
        Route::delete('/{lesson}', [
            LessonManagementController::class,
            'destroy'
        ])->name('destroy');

        // Get Course Units API endpoint (for lesson assignment)
        Route::get('/course-units/{course}', [
            LessonManagementController::class,
            'getCourseUnits'
        ])->name('course-units');
        
        // Lesson Management sub-routes (for better organization)
        Route::prefix('management')
            ->name('management.')
            ->group(function () {
                
                Route::get('/', [
                    LessonManagementController::class,
                    'index'
                ])->name('index');
                
                Route::get('/create', [
                    LessonManagementController::class,
                    'create'
                ])->name('create');
                
                Route::post('/', [
                    LessonManagementController::class,
                    'store'
                ])->name('store');
                
                Route::get('/{lesson}', [
                    LessonManagementController::class,
                    'show'
                ])->name('show');
                
                Route::get('/{lesson}/edit', [
                    LessonManagementController::class,
                    'edit'
                ])->name('edit');
                
                Route::put('/{lesson}', [
                    LessonManagementController::class,
                    'update'
                ])->name('update');
                
                Route::delete('/{lesson}', [
                    LessonManagementController::class,
                    'destroy'
                ])->name('destroy');

                // API endpoint for getting course units by course
                Route::get('/course-units/{course}', [
                    LessonManagementController::class,
                    'getCourseUnits'
                ])->name('course-units');
                
            });
            
    });
