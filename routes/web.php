<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Include Frontend Routes
 * All frontend routes are organized in the frontend.php file
 */
require __DIR__ . '/frontend.php';

/**
 * Account Profile Routes
 */
Route::middleware('auth')->group(function () {
    Route::get('/account', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('account.index');
    Route::post('/account/profile', [App\Http\Controllers\Student\ProfileController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/settings', [App\Http\Controllers\Student\ProfileController::class, 'updateSettings'])->name('account.settings.update');
});

/**
 * Student Offline Session Tracking Routes
 */
Route::middleware('auth')->prefix('student/offline')->name('student.offline.')->group(function () {
    // Session Management
    Route::post('session/start/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'startSession'])
        ->name('session.start');
    Route::post('session/end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'endSession'])
        ->name('session.end');
    Route::get('session/status/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionStatus'])
        ->name('session.status');

    // Activity Tracking
    Route::post('track/lesson/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackLessonActivity'])
        ->name('track.lesson');
    Route::post('track/step/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackSessionStep'])
        ->name('track.step');

    // Analytics & Reporting
    Route::get('summary/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionSummary'])
        ->name('summary');
    Route::get('activities/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getRecentActivities'])
        ->name('activities');

    // Admin/Cleanup
    Route::post('session/force-end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'forceEndSessions'])
        ->name('session.force-end');
});

// Temporary test route for debugging CourseDatesService
Route::get('/test-service', function () {
    try {
        $service = new \App\Services\Frost\Instructors\CourseDatesService();
        $result = $service->getTodaysLessons();

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test kkpdebug function availability
Route::get('/test/kkpdebug', function () {
    try {
        kkpdebug("Testing kkpdebug function", "TEST");
        return response()->json([
            'status' => 'success',
            'message' => 'kkpdebug function is working'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'kkpdebug function error: ' . $e->getMessage()
        ]);
    }
});

// Test StudentDashboardController debug without auth
Route::get('/test/debug', function () {
    try {
        $controller = new \App\Http\Controllers\Student\StudentDashboardController();
        $result = $controller->debug();
        return $result;
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test instructor dashboard data for discrepancy analysis
Route::get('/test/instructor-data', function () {
    try {
        $service = new \App\Services\Frost\Instructors\CourseDatesService();
        $lessons = $service->getTodaysLessons();

        // Also get raw course dates for comparison
        $rawCourseDates = \App\Models\CourseDate::whereDate('starts_at', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->with(['CourseUnit', 'InstUnit', 'InstUnit.GetCreatedBy', 'GetCourse'])
            ->get()
            ->map(function ($cd) {
                return [
                    'id' => $cd->id,
                    'starts_at' => $cd->starts_at,
                    'course_title' => $cd->GetCourse()->title ?? 'No Course',
                    'unit_title' => $cd->CourseUnit->title ?? 'No Unit',
                    'has_inst_unit' => $cd->InstUnit !== null,
                    'inst_unit_id' => $cd->InstUnit?->id,
                    'inst_unit_created_by' => $cd->InstUnit?->created_by,
                    'instructor_from_inst_unit' => $cd->InstUnit && $cd->InstUnit->GetCreatedBy()
                        ? ($cd->InstUnit->GetCreatedBy()->fname ?? '') . ' ' . ($cd->InstUnit->GetCreatedBy()->lname ?? '')
                        : null,
                ];
            });

        return response()->json([
            'service_lessons' => $lessons,
            'raw_course_dates' => $rawCourseDates,
            'analysis' => [
                'service_count' => count($lessons['lessons'] ?? []),
                'raw_count' => $rawCourseDates->count(),
                'discrepancy_check' => 'Compare instructor_name in service vs instructor_from_inst_unit in raw data'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
