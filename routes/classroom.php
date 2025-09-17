<?php

// use App\Http\Controllers\Classroom\ClassroomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Classroom Routes
|--------------------------------------------------------------------------
|
| API routes specifically for classroom-related functionality
|
| NOTE: ClassroomController doesn't exist yet, routes commented out
|
*/

/**
 * Classroom Dashboard Routes - DISABLED until ClassroomController is created
 */
Route::middleware(['auth'])->group(function () {

    // TODO: Create ClassroomController first
    // Classroom dashboard data (API endpoint)
    // Route::get('/api/classroom/dashboard', [ClassroomController::class, 'dashboard'])
    //     ->name('api.classroom.dashboard');

    // Debug route for classroom data only
    // Route::get('/api/classroom/debug', [ClassroomController::class, 'debugClass'])
    //     ->name('api.classroom.debug');

});
