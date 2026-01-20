<?php

namespace App\Http\Controllers\Admin\Frost;

/**
 * CourseDate Controller
 * Handles course date related actions
 * @version 2.0.0
 * @author KKP Security
 */

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Services\RCache;
use App\Models\InstUnit;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Traits\PageMetaDataTrait;

class CourseDateController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display a listing of course dates
     */
    public function index(Request $request)
    {
        // Check permissions
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        // Get filter parameters
        $courseFilter = $request->get('course_id');
        $statusFilter = $request->get('status', 'all'); // all, active, inactive, upcoming, past
        $dateFilter = $request->get('date_range', 'month'); // month, week, year, all
        $instructorFilter = $request->get('instructor_id');

        // Build query
        $query = CourseDate::with(['CourseUnit.Course', 'InstUnit.User']);

        // Apply course filter
        if ($courseFilter) {
            $query->whereHas('CourseUnit', function($q) use ($courseFilter) {
                $q->where('course_id', $courseFilter);
            });
        }

        // Apply status filter
        switch ($statusFilter) {
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
        }

        // Apply date range filter
        switch ($dateFilter) {
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
        if ($instructorFilter) {
            $query->whereHas('InstUnit', function($q) use ($instructorFilter) {
                $q->where('user_id', $instructorFilter);
            });
        }

        // Order by start date
        $courseDates = $query->orderBy('starts_at', 'desc')
                            ->paginate(25);

        // Get filter options
        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get(['id', 'title']);

        $instructors = \App\Models\User::whereIn('role_id', [1, 2, 3]) // Admin, Instructor, etc.
                                      ->orderBy('fname')
                                      ->get(['id', 'fname', 'lname']);

        // Statistics
        $stats = [
            'total' => CourseDate::count(),
            'active' => CourseDate::where('is_active', true)->count(),
            'upcoming' => CourseDate::where('starts_at', '>', now())->count(),
            'this_week' => CourseDate::whereBetween('starts_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'this_month' => CourseDate::whereBetween('starts_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->count(),
        ];

        $content = array_merge([
            'course_dates' => $courseDates,
            'courses' => $courses,
            'instructors' => $instructors,
            'stats' => $stats,
            'filters' => [
                'course_id' => $courseFilter,
                'status' => $statusFilter,
                'date_range' => $dateFilter,
                'instructor_id' => $instructorFilter,
            ],
        ], self::renderPageMeta('Course Dates Scheduler'));

        return view('admin.course-dates.index', compact('content'));
    }

    /**
     * Show the form for creating a new course date
     */
    public function create()
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $instructors = \App\Models\User::whereIn('role_id', [1, 2, 3])
                                      ->orderBy('fname')
                                      ->get(['id', 'fname', 'lname']);

        $content = array_merge([
            'courses' => $courses,
            'instructors' => $instructors,
        ], self::renderPageMeta('Create Course Date'));

        return view('admin.course-dates.create', compact('content'));
    }

    /**
     * Store a newly created course date
     */
    public function store(Request $request)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $courseDate = CourseDate::create([
            'course_unit_id' => $validated['course_unit_id'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create instructor assignment if provided
        if (isset($validated['instructor_id'])) {
            InstUnit::create([
                'course_date_id' => $courseDate->id,
                'user_id' => $validated['instructor_id'],
            ]);
        }

        return redirect()->route('admin.course-dates.show', $courseDate)
                        ->with('success', 'Course date created successfully.');
    }

    /**
     * Display the specified course date
     */
    public function show(CourseDate $courseDate)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $courseDate->load([
            'CourseUnit.Course',
            'InstUnit.User',
            'StudentUnits.User'
        ]);

        $content = array_merge([
            'course_date' => $courseDate,
        ], self::renderPageMeta("Course Date: {$courseDate->CalendarTitle()}"));

        return view('admin.course-dates.show', compact('content'));
    }

    /**
     * Show the form for editing the specified course date
     */
    public function edit(CourseDate $courseDate)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $courseDate->load(['CourseUnit.Course', 'InstUnit.User']);

        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $instructors = \App\Models\User::whereIn('role_id', [1, 2, 3])
                                      ->orderBy('fname')
                                      ->get(['id', 'fname', 'lname']);

        $content = array_merge([
            'course_date' => $courseDate,
            'courses' => $courses,
            'instructors' => $instructors,
        ], self::renderPageMeta("Edit Course Date: {$courseDate->CalendarTitle()}"));

        return view('admin.course-dates.edit', compact('content'));
    }

    /**
     * Update the specified course date
     */
    public function update(Request $request, CourseDate $courseDate)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'course_unit_id' => 'required|exists:course_units,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'instructor_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $courseDate->update([
            'course_unit_id' => $validated['course_unit_id'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_active' => $validated['is_active'] ?? $courseDate->is_active,
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

        return redirect()->route('admin.course-dates.show', $courseDate)
                        ->with('success', 'Course date updated successfully.');
    }

    /**
     * Remove the specified course date
     */
    public function destroy(CourseDate $courseDate)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if there are any enrolled students
        if ($courseDate->StudentUnits()->count() > 0) {
            return back()->with('error', 'Cannot delete course date with enrolled students.');
        }

        // Delete instructor assignment
        InstUnit::where('course_date_id', $courseDate->id)->delete();

        // Delete course date
        $courseDate->delete();

        return redirect()->route('admin.course-dates.index')
                        ->with('success', 'Course date deleted successfully.');
    }

    /**
     * Toggle active status of course date
     */
    public function toggleActive(CourseDate $courseDate)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $courseDate->update([
            'is_active' => !$courseDate->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $courseDate->is_active,
            'message' => $courseDate->is_active ? 'Course date activated' : 'Course date deactivated'
        ]);
    }

    /**
     * Get course units for a specific course (API endpoint)
     */
    public function getCourseUnits(Course $course)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $courseUnits = CourseUnit::where('course_id', $course->id)
                                 ->where('is_active', true)
                                 ->orderBy('title')
                                 ->get(['id', 'title']);

        return response()->json($courseUnits);
    }
}
