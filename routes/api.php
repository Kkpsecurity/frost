<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Admin Dashboard API Routes
 */
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        // Instructor Dashboard APIs
        Route::prefix('instructors')->group(function () {
            Route::get('/stats', [
                App\Http\Controllers\Admin\InstructorDashboardController::class,
                'getStats'
            ]);
            Route::get('/upcoming-classes', [
                App\Http\Controllers\Admin\InstructorDashboardController::class,
                'getUpcomingClasses'
            ]);
        });

        // Support Dashboard APIs
        Route::prefix('support')->group(function () {
            Route::get('/stats', [
                App\Http\Controllers\Admin\SupportDashboardController::class,
                'getStats'
            ]);
            Route::get('/recent-tickets', [
                App\Http\Controllers\Admin\SupportDashboardController::class,
                'getRecentTickets'
            ]);
        });
    });

/**
 * Student Dashboard API Routes
 */
Route::prefix('student')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/stats', [
            App\Http\Controllers\Student\ClassroomController::class,
            'getStats'
        ]);
        Route::get('/recent-lessons', [
            App\Http\Controllers\Student\ClassroomController::class,
            'getRecentLessons'
        ]);
        Route::get('/upcoming-assignments', [
            App\Http\Controllers\Student\ClassroomController::class,
            'getUpcomingAssignments'
        ]);
    });
