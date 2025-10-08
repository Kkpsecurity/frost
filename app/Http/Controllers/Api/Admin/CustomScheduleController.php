<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Frost\Scheduling\CustomScheduleGeneratorService;
use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Custom Schedule Generator Controller
 * 
 * Handles custom course date generation patterns:
 * - Every 3 days
 * - Monday/Wednesday every other week
 * - Custom scheduling patterns
 */
class CustomScheduleController extends Controller
{
    private CustomScheduleGeneratorService $customScheduleService;
    private CourseDateGeneratorService $courseDateService;

    public function __construct(
        CustomScheduleGeneratorService $customScheduleService,
        CourseDateGeneratorService $courseDateService
    ) {
        $this->customScheduleService = $customScheduleService;
        $this->courseDateService = $courseDateService;
    }

    /**
     * Generate Monday/Wednesday every other week schedule
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generateMondayWednesdayBiweekly(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52',
                'preview_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $courseId = $request->get('course_id');
            $advanceWeeks = $request->get('advance_weeks', 8);
            $previewOnly = $request->get('preview_only', false);

            if ($previewOnly) {
                $result = $this->customScheduleService->previewPattern(
                    'monday_wednesday_biweekly',
                    $courseId,
                    $advanceWeeks
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Schedule preview generated successfully',
                    'data' => $result
                ]);
            }

            $result = $this->customScheduleService->generateMondayWednesdayEveryOtherWeek(
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Monday/Wednesday biweekly generated', [
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'dates_created' => $result['dates_created'],
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Monday/Wednesday biweekly schedule generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Monday/Wednesday biweekly generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate every 3 days schedule pattern
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generateEveryThreeDays(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52',
                'preview_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $courseId = $request->get('course_id');
            $advanceWeeks = $request->get('advance_weeks', 8);
            $previewOnly = $request->get('preview_only', false);

            if ($previewOnly) {
                $result = $this->customScheduleService->previewPattern(
                    'every_three_days',
                    $courseId,
                    $advanceWeeks
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Every 3 days schedule preview generated successfully',
                    'data' => $result
                ]);
            }

            $result = $this->customScheduleService->generateEveryThreeDaysPattern(
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Every 3 days pattern generated', [
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'dates_created' => $result['dates_created'],
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Every 3 days schedule generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Every 3 days generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate multiple patterns at once
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generateMultiplePatterns(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patterns' => 'required|array',
                'patterns.*' => 'required|string|in:monday_wednesday_biweekly,every_three_days,monday_wednesday_friday,tuesday_thursday',
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52',
                'preview_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patterns = $request->get('patterns');
            $courseId = $request->get('course_id');
            $advanceWeeks = $request->get('advance_weeks', 8);
            $previewOnly = $request->get('preview_only', false);

            if ($previewOnly) {
                $previews = [];
                foreach ($patterns as $pattern) {
                    $previews[$pattern] = $this->customScheduleService->previewPattern(
                        $pattern,
                        $courseId,
                        $advanceWeeks
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Multiple patterns preview generated successfully',
                    'data' => $previews
                ]);
            }

            $result = $this->customScheduleService->generateMultiplePatterns(
                $patterns,
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Multiple patterns generated', [
                'patterns' => $patterns,
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'total_dates_created' => $result['total_dates_created'],
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Multiple patterns generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Multiple patterns generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate multiple patterns: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview a specific pattern without generating dates
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function previewPattern(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'pattern' => 'required|string|in:monday_wednesday_biweekly,every_three_days,monday_wednesday_friday,tuesday_thursday',
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pattern = $request->get('pattern');
            $courseId = $request->get('course_id');
            $advanceWeeks = $request->get('advance_weeks', 8);

            $result = $this->customScheduleService->previewPattern(
                $pattern,
                $courseId,
                $advanceWeeks
            );

            return response()->json([
                'success' => true,
                'message' => 'Pattern preview generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Pattern preview failed', [
                'pattern' => $request->get('pattern'),
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to preview pattern: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available courses for scheduling
     * 
     * @return JsonResponse
     */
    public function getAvailableCourses(): JsonResponse
    {
        try {
            $courses = \App\Models\Course::where('is_active', true)
                ->whereHas('CourseUnits')
                ->select('id', 'title', 'course_name')
                ->orderBy('title')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Available courses retrieved successfully',
                'data' => $courses
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Failed to get available courses', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve courses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scheduling statistics and current status
     * 
     * @return JsonResponse
     */
    public function getSchedulingStats(): JsonResponse
    {
        try {
            $stats = [
                'total_active_courses' => \App\Models\Course::where('is_active', true)->count(),
                'courses_with_units' => \App\Models\Course::where('is_active', true)
                    ->whereHas('CourseUnits')->count(),
                'total_course_dates' => \App\Models\CourseDate::count(),
                'active_course_dates' => \App\Models\CourseDate::where('is_active', true)->count(),
                'future_course_dates' => \App\Models\CourseDate::where('starts_at', '>', now())->count(),
                'dates_this_week' => \App\Models\CourseDate::whereBetween('starts_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'dates_next_week' => \App\Models\CourseDate::whereBetween('starts_at', [
                    now()->addWeek()->startOfWeek(),
                    now()->addWeek()->endOfWeek()
                ])->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Scheduling statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Failed to get scheduling stats', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}