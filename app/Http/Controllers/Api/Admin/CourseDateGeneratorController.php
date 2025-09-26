<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Services\Frost\Scheduling\CourseDateGeneratorService;

class CourseDateGeneratorController extends Controller
{
    protected CourseDateGeneratorService $service;

    public function __construct(CourseDateGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * Preview CourseDate generation without creating records
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $preview = $this->service->previewGeneration($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CourseDate records
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'cleanup' => 'boolean',
            'cleanup_days' => 'integer|min:1|max:365'
        ]);

        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            // Handle cleanup if requested
            $cleanupResults = null;
            if ($request->boolean('cleanup')) {
                $cleanupDays = $request->integer('cleanup_days', 30);
                $cleanupCount = $this->service->cleanupOldCourseDates($cleanupDays);
                $cleanupResults = ['cleaned_count' => $cleanupCount];
            }

            // Generate CourseDate records
            $results = $this->service->generateCourseDatesForRange($startDate, $endDate);

            // Add cleanup results if performed
            if ($cleanupResults) {
                $results['cleanup'] = $cleanupResults;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$results['dates_created']} CourseDate records",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate CourseDate records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up old CourseDate records
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:365'
        ]);

        try {
            $days = $request->integer('days', 30);
            $cleanedCount = $this->service->cleanupOldCourseDates($days);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleaned up {$cleanedCount} old CourseDate records",
                'data' => [
                    'cleaned_count' => $cleanedCount,
                    'days_threshold' => $days
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup CourseDate records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get generation statistics and status
     */
    public function status(): JsonResponse
    {
        try {
            // Get basic stats
            $totalCourseDates = \App\Models\CourseDate::count();
            $upcomingCourseDates = \App\Models\CourseDate::where('starts_at', '>=', now())->count();
            $todayCourseDates = \App\Models\CourseDate::whereDate('starts_at', today())->count();

            // Get date range of existing CourseDate records
            $firstCourseDate = \App\Models\CourseDate::orderBy('starts_at')->first();
            $lastCourseDate = \App\Models\CourseDate::orderBy('starts_at', 'desc')->first();

            // Get active courses with units
            $activeCourses = \App\Models\Course::where('is_active', true)
                ->whereHas('courseUnits')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'total_course_dates' => $totalCourseDates,
                        'upcoming_course_dates' => $upcomingCourseDates,
                        'today_course_dates' => $todayCourseDates,
                        'active_courses_with_units' => $activeCourses
                    ],
                    'date_range' => [
                        'first_course_date' => $firstCourseDate?->starts_at?->format('Y-m-d'),
                        'last_course_date' => $lastCourseDate?->starts_at?->format('Y-m-d')
                    ],
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick generate for next 7 days (convenience endpoint)
     */
    public function quickGenerate(): JsonResponse
    {
        try {
            $startDate = now()->addDay(); // Tomorrow
            $endDate = now()->addWeek(); // 7 days from now

            $results = $this->service->generateCourseDatesForRange($startDate, $endDate);

            return response()->json([
                'success' => true,
                'message' => "Quick generation complete: {$results['dates_created']} CourseDate records created",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to quick generate CourseDate records',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
