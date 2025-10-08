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

// ===================================================================
// Student Dashboard API Routes
// ===================================================================

use App\Http\Controllers\Student\StudentDashboardController;

// Student Dashboard API Routes (protected by auth middleware)
Route::middleware(['auth:sanctum'])->prefix('student')->group(function () {
    Route::prefix('attendance')->group(function () {
        Route::post('/offline', [StudentDashboardController::class, 'recordOfflineAttendance']);
        Route::get('/summary/{courseDateId?}', [StudentDashboardController::class, 'getAttendanceSummary']);
        Route::get('/status', [StudentDashboardController::class, 'getAttendanceStatus']);
    });

    Route::prefix('lessons')->group(function () {
        Route::post('/start', [StudentDashboardController::class, 'startLesson']);
    });
});

// ===================================================================
// Admin API Routes
// ===================================================================

use App\Http\Controllers\Api\Admin\CourseDateGeneratorController;
use App\Models\Course;
use App\Models\User;

// CourseDate Generator Admin API (protected by admin middleware)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::prefix('course-date-generator')->group(function () {
        Route::post('/preview', [CourseDateGeneratorController::class, 'preview']);
        Route::post('/generate', [CourseDateGeneratorController::class, 'generate']);
        Route::post('/cleanup', [CourseDateGeneratorController::class, 'cleanup']);
        Route::get('/status', [CourseDateGeneratorController::class, 'status']);
        Route::post('/quick-generate', [CourseDateGeneratorController::class, 'quickGenerate']);
    });
});


