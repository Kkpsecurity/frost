<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\Frost\Scheduling\CustomScheduleGeneratorService;
use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Custom Schedule Controller - Admin Web Interface
 * 
 * Handles custom course date generation patterns via web routes:
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
     * Show the main schedule generator page
     * 
     * @return View
     */
    public function index(): View
    {
        $stats = $this->getSchedulingStatsData();
        $courses = $this->getAvailableCoursesData();

        return view('admin.schedule.index', compact('stats', 'courses'));
    }

    /**
     * Show the schedule generator form
     * 
     * @return View
     */
    public function generator(): View
    {
        $courses = $this->getAvailableCoursesData();
        
        return view('admin.schedule.generator', compact('courses'));
    }

    /**
     * View generated schedules
     * 
     * @return View
     */
    public function view(): View
    {
        $recentSchedules = \App\Models\CourseDate::with('courseUnit.course')
            ->where('created_at', '>=', now()->subWeeks(2))
            ->orderBy('starts_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.schedule.view', compact('recentSchedules'));
    }

    /**
     * Generate Monday/Wednesday every other week schedule
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function generateMondayWednesdayBiweekly(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52',
                'preview_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
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

                return redirect()->back()
                    ->with('preview_result', $result)
                    ->with('success', 'Schedule preview generated successfully');
            }

            $result = $this->customScheduleService->generateMondayWednesdayEveryOtherWeek(
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Monday/Wednesday biweekly generated via web', [
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'dates_created' => $result['dates_created'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.schedule.view')
                ->with('generation_result', $result)
                ->with('success', "Monday/Wednesday biweekly schedule generated successfully! Created {$result['dates_created']} dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Monday/Wednesday biweekly generation failed via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate every 3 days schedule pattern
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function generateEveryThreeDays(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'nullable|integer|exists:courses,id',
                'advance_weeks' => 'nullable|integer|min:1|max:52',
                'preview_only' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
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

                return redirect()->back()
                    ->with('preview_result', $result)
                    ->with('success', 'Every 3 days schedule preview generated successfully');
            }

            $result = $this->customScheduleService->generateEveryThreeDaysPattern(
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Every 3 days pattern generated via web', [
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'dates_created' => $result['dates_created'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.schedule.view')
                ->with('generation_result', $result)
                ->with('success', "Every 3 days schedule generated successfully! Created {$result['dates_created']} dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Every 3 days generation failed via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate multiple patterns at once
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function generateMultiplePatterns(Request $request): RedirectResponse
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
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
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

                return redirect()->back()
                    ->with('preview_result', $previews)
                    ->with('success', 'Multiple patterns preview generated successfully');
            }

            $result = $this->customScheduleService->generateMultiplePatterns(
                $patterns,
                $courseId,
                $advanceWeeks
            );

            Log::info('Custom Schedule: Multiple patterns generated via web', [
                'patterns' => $patterns,
                'course_id' => $courseId,
                'advance_weeks' => $advanceWeeks,
                'total_dates_created' => $result['total_dates_created'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.schedule.view')
                ->with('generation_result', $result)
                ->with('success', "Multiple patterns generated successfully! Created {$result['total_dates_created']} total dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Multiple patterns generation failed via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate multiple patterns: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Preview a specific pattern without generating dates
     * 
     * @param Request $request
     * @param string $pattern
     * @return View
     */
    public function previewPattern(Request $request, string $pattern): View
    {
        $validator = Validator::make(array_merge($request->all(), ['pattern' => $pattern]), [
            'pattern' => 'required|string|in:monday-wednesday-biweekly,every-three-days,monday-wednesday-friday,tuesday-thursday',
            'course_id' => 'nullable|integer|exists:courses,id',
            'advance_weeks' => 'nullable|integer|min:1|max:52'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Convert URL pattern to internal pattern
        $internalPattern = str_replace('-', '_', $pattern);
        
        $courseId = $request->get('course_id');
        $advanceWeeks = $request->get('advance_weeks', 8);
        $courses = $this->getAvailableCoursesData();

        try {
            $result = $this->customScheduleService->previewPattern(
                $internalPattern,
                $courseId,
                $advanceWeeks
            );

            return view('admin.schedule.preview', compact('result', 'pattern', 'courses', 'courseId', 'advanceWeeks'));

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Pattern preview failed via web', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to preview pattern: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get available courses data
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableCourses()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getAvailableCoursesData()
        ]);
    }

    /**
     * Get scheduling statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedulingStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getSchedulingStatsData()
        ]);
    }

    /**
     * Activate course dates
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function activateDates(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'date_ids' => 'required|array',
            'date_ids.*' => 'integer|exists:course_dates,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $dateIds = $request->get('date_ids');
            $activated = \App\Models\CourseDate::whereIn('id', $dateIds)
                ->update(['is_active' => true]);

            Log::info('Custom Schedule: Course dates activated via web', [
                'date_ids' => $dateIds,
                'activated_count' => $activated,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', "Successfully activated {$activated} course dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Failed to activate course dates via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to activate course dates: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate course dates
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function deactivateDates(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'date_ids' => 'required|array',
            'date_ids.*' => 'integer|exists:course_dates,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $dateIds = $request->get('date_ids');
            $deactivated = \App\Models\CourseDate::whereIn('id', $dateIds)
                ->update(['is_active' => false]);

            Log::info('Custom Schedule: Course dates deactivated via web', [
                'date_ids' => $dateIds,
                'deactivated_count' => $deactivated,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', "Successfully deactivated {$deactivated} course dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Failed to deactivate course dates via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to deactivate course dates: ' . $e->getMessage());
        }
    }

    /**
     * Delete course dates
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteDates(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'date_ids' => 'required|array',
            'date_ids.*' => 'integer|exists:course_dates,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $dateIds = $request->get('date_ids');
            $deleted = \App\Models\CourseDate::whereIn('id', $dateIds)->delete();

            Log::info('Custom Schedule: Course dates deleted via web', [
                'date_ids' => $dateIds,
                'deleted_count' => $deleted,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', "Successfully deleted {$deleted} course dates.");

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Failed to delete course dates via web', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete course dates: ' . $e->getMessage());
        }
    }

    /**
     * Export schedule data
     * 
     * @param string $format
     * @return mixed
     */
    public function exportSchedule(string $format)
    {
        try {
            $dates = \App\Models\CourseDate::with('courseUnit.course')
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->get();

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($dates);
                case 'json':
                    return response()->json($dates);
                case 'pdf':
                    return $this->exportToPdf($dates);
                default:
                    return redirect()->back()
                        ->with('error', 'Invalid export format');
            }

        } catch (\Exception $e) {
            Log::error('Custom Schedule: Export failed via web', [
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to export schedule: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get available courses data
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getAvailableCoursesData()
    {
        return \App\Models\Course::where('is_active', true)
            ->whereHas('CourseUnits')
            ->select('id', 'title', 'course_name')
            ->orderBy('title')
            ->get();
    }

    /**
     * Helper method to get scheduling statistics data
     * 
     * @return array
     */
    private function getSchedulingStatsData(): array
    {
        return [
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
    }

    /**
     * Export to CSV format
     * 
     * @param \Illuminate\Support\Collection $dates
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($dates)
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=course_schedule_' . now()->format('Y_m_d') . '.csv',
        ];

        $callback = function () use ($dates) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Course', 'Date', 'Start Time', 'End Time', 'Day Number', 'Active', 'Course Unit']);

            foreach ($dates as $date) {
                fputcsv($file, [
                    $date->courseUnit->course->title ?? 'N/A',
                    $date->starts_at->format('Y-m-d'),
                    $date->starts_at->format('H:i'),
                    $date->ends_at->format('H:i'),
                    $date->day_number,
                    $date->is_active ? 'Yes' : 'No',
                    $date->courseUnit->admin_title ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF format (basic implementation)
     * 
     * @param \Illuminate\Support\Collection $dates
     * @return View
     */
    private function exportToPdf($dates)
    {
        return view('admin.schedule.pdf', compact('dates'));
    }
}