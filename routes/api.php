<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| ⚠️ IMPORTANT: These routes are for EXTERNAL/THIRD-PARTY communication only
|
| For INTERNAL application communication, use web routes in routes/web.php
| or routes/frontend.php instead. Do NOT call API routes from within the app.
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===================================================================
// Student Dashboard API Routes (EXTERNAL INTEGRATIONS ONLY)
// ===================================================================
// ⚠️ These routes are for EXTERNAL third-party systems:
//    - LMS/HR systems recording attendance
//    - External course platforms managing lessons
//    - Third-party Zoom integrations
//
// Do NOT use these from internal Frost app - use web routes instead!
//
use App\Http\Controllers\Student\StudentDashboardController;

Route::middleware(['auth:sanctum'])->prefix('student')->group(function () {
    Route::prefix('attendance')->group(function () {
        Route::post('/offline', [StudentDashboardController::class, 'recordOfflineAttendance']);
        Route::get('/summary/{courseDateId?}', [StudentDashboardController::class, 'getAttendanceSummary']);
        Route::get('/status', [StudentDashboardController::class, 'getAttendanceStatus']);
    });

    Route::prefix('lesson')->group(function () {
        Route::post('/start', [StudentDashboardController::class, 'startStudentLesson']);
        Route::post('/complete', [StudentDashboardController::class, 'completeStudentLesson']);
    });

    Route::prefix('zoom')->group(function () {
        Route::post('/generate-signature', [StudentDashboardController::class, 'generateZoomSignature']);
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


