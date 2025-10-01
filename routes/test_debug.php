<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Development/Testing Routes
 * Only available in development environment
 */

if (app()->environment(['local', 'staging'])) {

    Route::prefix('test')->name('test.')->group(function () {

        // Dashboard debugging
        Route::get('/debug', [StudentDashboardController::class, 'debug'])
            ->name('debug');

        // kkpdebug function test
        Route::get('/kkpdebug', function () {
            try {
                \kkpdebug("Testing kkpdebug function", "TEST");
                return response()->json([
                    'status' => 'success',
                    'message' => 'kkpdebug function is working',
                    'environment' => app()->environment()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'kkpdebug function error: ' . $e->getMessage()
                ]);
            }
        })->name('kkpdebug');

        // Service testing routes
        Route::get('/service', function () {
            $service = new \App\Services\Frost\Scheduling\CourseDatesService();

            try {
                $result = $service->generateCourseDates(1, 5);
                return response()->json([
                    'status' => 'success',
                    'data' => $result
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        })->name('service');

        // Instructor data analysis
        Route::get('/instructor-data', function () {
            try {
                $service = new \App\Services\Frost\Instructors\CourseDatesService();
                $lessons = $service->getTodaysLessons();

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
        })->name('instructor-data');

        // Admin Users DataTables AJAX test
        Route::get('/admin-users-ajax', function () {
            try {
                // Create a mock AJAX request
                $request = request();
                $request->headers->set('X-Requested-With', 'XMLHttpRequest');

                $controller = new \App\Http\Controllers\Admin\AdminCenter\AdminUserController();
                $response = $controller->index($request);

                return [
                    'status' => 'success',
                    'response_type' => get_class($response),
                    'is_json' => $response instanceof \Illuminate\Http\JsonResponse,
                    'data' => $response instanceof \Illuminate\Http\JsonResponse ? 'JSON Response Ready' : 'View Response'
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
            }
        })->name('admin-users-ajax');

    });
}
