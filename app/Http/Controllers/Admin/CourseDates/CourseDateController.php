<?php

namespace App\Http\Controllers\Admin\CourseDates;

use App\Http\Controllers\Controller;
use App\Models\CourseDate;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\InstUnit;
use App\Models\User;
use App\Services\Frost\Instructors\CourseDatesService;
use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseDateController extends Controller
{
    protected CourseDatesService $courseDatesService;
    protected CourseDateGeneratorService $generatorService;

    public function __construct(
        CourseDatesService $courseDatesService,
        CourseDateGeneratorService $generatorService
    ) {
        $this->courseDatesService = $courseDatesService;
        $this->generatorService = $generatorService;
    }

    /**
     * Display a listing of course dates with filtering and statistics
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $filters = [
            'course_id' => $request->get('course_id'),
            'status' => $request->get('status', 'all'),
            'date_range' => $request->get('date_range', 'month'),
            'instructor_id' => $request->get('instructor_id'),
            'search' => $request->get('search'),
        ];

        // Build query with relationships
        $query = CourseDate::with([
            'CourseUnit.Course',
            'InstUnit.CreatedBy',
            'InstUnit.Assistant'
        ]);

        // Apply course filter
        if ($filters['course_id']) {
            $query->whereHas('CourseUnit', function($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        // Apply status filter
        switch ($filters['status']) {
            case 'active':
                $query->where('is_active', true);
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'upcoming':
                $query->where('starts_at', '>', now());
                break;
            case 'past':
                $query->where('ends_at', '<', now());
                break;
            case 'current':
                $query->where('starts_at', '<=', now())
                      ->where('ends_at', '>=', now());
                break;
        }

        // Apply date range filter
        switch ($filters['date_range']) {
            case 'today':
                $query->whereDate('starts_at', today());
                break;
            case 'week':
                $query->whereBetween('starts_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('starts_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereBetween('starts_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);
                break;
        }

        // Apply instructor filter
        if ($filters['instructor_id']) {
            $query->whereHas('InstUnit', function($q) use ($filters) {
                $q->where('created_by', $filters['instructor_id'])
                  ->orWhere('assistant_id', $filters['instructor_id']);
            });
        }

        // Apply search filter
        if ($filters['search']) {
            $query->whereHas('CourseUnit.Course', function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%");
            });
        }

        // Get paginated results
        $courseDates = $query->orderBy('starts_at', 'desc')
                           ->paginate(25)
                           ->appends($filters);

        // Get filter options
        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get(['id', 'title']);

        $instructors = User::whereIn('role_id', [1, 2, 3, 4, 5])
                          ->orderBy('fname')
                          ->get(['id', 'fname', 'lname']);

        // Calculate statistics
        $stats = $this->calculateStatistics();

        $content = [
            'title' => 'Course Dates Management',
            'course_dates' => $courseDates,
            'courses' => $courses,
            'instructors' => $instructors,
            'stats' => $stats,
            'filters' => $filters,
        ];

        return view('admin.course-dates.index', compact('content'));
    }

    /**
     * Show the form for creating a new course date
     */
    public function create(): View
    {
        $courses = Course::where('is_active', true)
                        ->with(['CourseUnits' => function($q) {
                            $q->where('is_active', true)
                              ->orderBy('day')
                              ->orderBy('ordering');
                        }])
                        ->orderBy('title')
                        ->get();

        $instructors = User::whereIn('role_id', [1, 2, 3, 4, 5])
                          ->orderBy('fname')
                          ->get(['id', 'fname', 'lname']);

        $content = [
            'title' => 'Create Course Date',
            'courses' => $courses,
            'instructors' => $instructors,
        ];

        return view('admin.course-dates.create', compact('content'));
    }

    /**
     * Store a newly created course date
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $courseDate = CourseDate::create([
                'course_unit_id' => $validated['course_unit_id'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'is_active' => $validated['is_active'] ?? true,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create instructor assignment if provided
            if (isset($validated['instructor_id'])) {
                InstUnit::create([
                    'course_date_id' => $courseDate->id,
                    'user_id' => $validated['instructor_id'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.course-dates.show', $courseDate)
                           ->with('success', 'Course date created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Course date creation failed: ' . $e->getMessage());

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to create course date: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified course date
     */
    public function show(CourseDate $courseDate): View
    {
        $courseDate->load([
            'CourseUnit.Course',
            'InstUnit.CreatedBy',
            'InstUnit.Assistant',
            'StudentUnits.CourseAuth.User'
        ]);

        $content = [
            'title' => "Course Date: {$courseDate->CourseUnit->Course->title} - {$courseDate->CourseUnit->title}",
            'course_date' => $courseDate,
        ];

        return view('admin.course-dates.show', compact('content'));
    }

    /**
     * Display the calendar view
     */
    public function calendar(): View
    {
        // Get today's courses
        $todayCourses = CourseDate::with(['CourseUnit.Course'])
                                ->whereDate('starts_at', today())
                                ->orderBy('starts_at')
                                ->get();

        // Calculate stats
        $stats = [
            'today_courses' => $todayCourses->count(),
            'week_courses' => CourseDate::whereBetween('starts_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'month_courses' => CourseDate::whereBetween('starts_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])->count(),
            'inactive_courses' => CourseDate::where('is_active', false)->count(),
        ];

        $content = [
            'title' => 'Course Dates Calendar',
            'today_courses' => $todayCourses,
            'stats' => $stats,
        ];

        return view('admin.course-dates.calendar', compact('content'));
    }

    /**
     * API endpoint for calendar events
     */
    public function apiCalendar(Request $request): JsonResponse
    {
        $startDate = Carbon::parse($request->get('start'));
        $endDate = Carbon::parse($request->get('end'));

        $courseDates = CourseDate::with(['CourseUnit.Course', 'InstUnit.CreatedBy', 'StudentUnits'])
                                ->whereBetween('starts_at', [$startDate, $endDate])
                                ->get();

        $events = $courseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'starts_at' => $courseDate->starts_at->toISOString(),
                'ends_at' => $courseDate->ends_at->toISOString(),
                'is_active' => $courseDate->is_active,
                'course_unit' => [
                    'id' => $courseDate->CourseUnit->id,
                    'title' => $courseDate->CourseUnit->title,
                    'day' => $courseDate->CourseUnit->day,
                    'course' => [
                        'id' => $courseDate->CourseUnit->Course->id,
                        'title' => $courseDate->CourseUnit->Course->title,
                    ]
                ],
                'student_units_count' => $courseDate->StudentUnits->count(),
                'inst_unit' => $courseDate->InstUnit ? [
                    'id' => $courseDate->InstUnit->id,
                    'created_by' => $courseDate->InstUnit->CreatedBy ? [
                        'id' => $courseDate->InstUnit->CreatedBy->id,
                        'fname' => $courseDate->InstUnit->CreatedBy->fname,
                        'lname' => $courseDate->InstUnit->CreatedBy->lname,
                    ] : null,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'course_dates' => $events
        ]);
    }

    /**
     * Show the form for editing the specified course date
     */
    public function edit(CourseDate $courseDate): View
    {
        $courseDate->load(['CourseUnit.Course', 'InstUnit.CreatedBy', 'InstUnit.Assistant']);

        $courses = Course::where('is_active', true)
                        ->with(['CourseUnits' => function($q) {
                            $q->where('is_active', true)
                              ->orderBy('day')
                              ->orderBy('ordering');
                        }])
                        ->orderBy('title')
                        ->get();

        $instructors = User::whereIn('role_id', [1, 2, 3, 4, 5])
                          ->orderBy('fname')
                          ->get(['id', 'fname', 'lname']);

        $content = [
            'title' => "Edit Course Date",
            'course_date' => $courseDate,
            'courses' => $courses,
            'instructors' => $instructors,
        ];

        return view('admin.course-dates.edit', compact('content'));
    }

    /**
     * Update the specified course date
     */
    public function update(Request $request, CourseDate $courseDate): RedirectResponse
    {
        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $courseDate->update([
                'course_unit_id' => $validated['course_unit_id'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'is_active' => $validated['is_active'] ?? $courseDate->is_active,
                'notes' => $validated['notes'] ?? $courseDate->notes,
            ]);

            // Update instructor assignment
            if (isset($validated['instructor_id'])) {
                InstUnit::updateOrCreate(
                    ['course_date_id' => $courseDate->id],
                    ['user_id' => $validated['instructor_id']]
                );
            } else {
                // Remove instructor assignment if none provided
                InstUnit::where('course_date_id', $courseDate->id)->delete();
            }

            DB::commit();

            return redirect()->route('admin.course-dates.show', $courseDate)
                           ->with('success', 'Course date updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Course date update failed: ' . $e->getMessage());

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update course date: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified course date
     */
    public function destroy(CourseDate $courseDate): RedirectResponse
    {
        // Check if there are any enrolled students
        if ($courseDate->StudentUnits()->count() > 0) {
            return back()->with('error', 'Cannot delete course date with enrolled students.');
        }

        DB::beginTransaction();
        try {
            // Delete instructor assignment
            InstUnit::where('course_date_id', $courseDate->id)->delete();

            // Delete course date
            $courseDate->delete();

            DB::commit();

            return redirect()->route('admin.course-dates.index')
                           ->with('success', 'Course date deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Course date deletion failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete course date: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle active status of course date
     */
    public function toggleActive(CourseDate $courseDate): JsonResponse
    {
        try {
            $courseDate->update([
                'is_active' => !$courseDate->is_active
            ]);

            return response()->json([
                'success' => true,
                'is_active' => $courseDate->is_active,
                'message' => $courseDate->is_active ? 'Course date activated' : 'Course date deactivated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course units for a specific course (AJAX endpoint)
     */
    public function getCourseUnits(Course $course): JsonResponse
    {
        $courseUnits = CourseUnit::where('course_id', $course->id)
                                ->where('is_active', true)
                                ->orderBy('day')
                                ->orderBy('ordering')
                                ->get(['id', 'title', 'day', 'ordering']);

        return response()->json($courseUnits);
    }

    /**
     * Show the course date generator interface
     */
    public function generator(): View
    {
        $courses = Course::where('is_active', true)
                        ->with(['CourseUnits' => function($q) {
                            $q->where('is_active', true)
                              ->orderBy('day')
                              ->orderBy('ordering');
                        }])
                        ->orderBy('title')
                        ->get();

        $instructors = User::whereIn('role_id', [1, 2, 3, 4, 5])
                          ->orderBy('fname')
                          ->get(['id', 'fname', 'lname']);

        // Get generator status - using basic statistics since getStatus doesn't exist
        $generatorStatus = [
            'total_course_dates' => CourseDate::count(),
            'upcoming_course_dates' => CourseDate::where('starts_at', '>=', now())->count(),
            'today_course_dates' => CourseDate::whereDate('starts_at', today())->count(),
            'active_courses' => Course::where('is_active', true)->whereHas('courseUnits')->count(),
        ];

        $content = [
            'title' => 'Course Date Generator',
            'courses' => $courses,
            'instructors' => $instructors,
            'generator_status' => $generatorStatus,
        ];

        return view('admin.course-dates.generator', compact('content'));
    }

    /**
     * Preview course date generation
     */
    public function generatorPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'advance_days' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $advanceDays = $validated['advance_days'] ?? null;
            $preview = $this->generatorService->previewGeneration($advanceDays);

            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate course dates using the generator service
     */
    public function generatorGenerate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            $result = $this->generatorService->generateCourseDatesForRange($startDate, $endDate);

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$result['dates_created']} course dates.",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Course date generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup course dates using the generator service
     */
    public function generatorCleanup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cleanup_type' => 'required|string|in:invalid,all',
        ]);

        try {
            // Use the cleanupInvalidCourseDates method which exists in the service
            $result = $this->generatorService->cleanupInvalidCourseDates();

            return response()->json([
                'success' => true,
                'message' => "Successfully cleaned up {$result['total_removed']} course dates.",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Course date cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete course dates
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_date_ids' => 'required|array',
            'course_date_ids.*' => 'exists:course_dates,id',
        ]);

        $deleted = 0;
        $errors = [];

        foreach ($validated['course_date_ids'] as $id) {
            $courseDate = CourseDate::find($id);

            if (!$courseDate) continue;

            // Check if there are enrolled students
            if ($courseDate->StudentUnits()->count() > 0) {
                $errors[] = "Course date '{$courseDate->CalendarTitle()}' has enrolled students and cannot be deleted.";
                continue;
            }

            try {
                DB::beginTransaction();

                // Delete instructor assignment
                InstUnit::where('course_date_id', $courseDate->id)->delete();

                // Delete course date
                $courseDate->delete();

                DB::commit();
                $deleted++;

            } catch (\Exception $e) {
                DB::rollback();
                $errors[] = "Failed to delete course date '{$courseDate->CalendarTitle()}': {$e->getMessage()}";
            }
        }

        if ($deleted > 0) {
            $message = "Successfully deleted {$deleted} course date(s).";
            if (!empty($errors)) {
                $message .= " However, some deletions failed: " . implode(' ', $errors);
            }
            return back()->with('success', $message);
        } else {
            return back()->withErrors(['error' => 'No course dates were deleted. ' . implode(' ', $errors)]);
        }
    }

    /**
     * Bulk toggle active status
     */
    public function bulkToggleActive(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_date_ids' => 'required|array',
            'course_date_ids.*' => 'exists:course_dates,id',
            'action' => 'required|string|in:activate,deactivate',
        ]);

        $updated = 0;
        $isActive = $validated['action'] === 'activate';

        try {
            $updated = CourseDate::whereIn('id', $validated['course_date_ids'])
                                ->update(['is_active' => $isActive]);

            $action = $isActive ? 'activated' : 'deactivated';
            return back()->with('success', "Successfully {$action} {$updated} course date(s).");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Bulk update failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Calculate course date statistics
     */
    private function calculateStatistics(): array
    {
        return [
            'total' => CourseDate::count(),
            'active' => CourseDate::where('is_active', true)->count(),
            'inactive' => CourseDate::where('is_active', false)->count(),
            'upcoming' => CourseDate::where('starts_at', '>', now())->count(),
            'current' => CourseDate::where('starts_at', '<=', now())
                                  ->where('ends_at', '>=', now())
                                  ->count(),
            'past' => CourseDate::where('ends_at', '<', now())->count(),
            'today' => CourseDate::whereDate('starts_at', today())->count(),
            'this_week' => CourseDate::whereBetween('starts_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'this_month' => CourseDate::whereBetween('starts_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
        ];
    }
}
