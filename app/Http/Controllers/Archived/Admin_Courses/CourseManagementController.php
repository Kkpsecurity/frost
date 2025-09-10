<?php

namespace App\Http\Controllers\Admin\Courses;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Traits\CoursePermissionsTrait;
use App\Support\RoleManager;

class CourseManagementController extends Controller
{
    use PageMetaDataTrait, CoursePermissionsTrait;

    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Filter by course type
        if ($request->filled('course_type')) {
            $courseType = strtoupper($request->course_type);
            if (in_array($courseType, ['D', 'G'])) {
                $query->where(function($q) use ($courseType) {
                    $q->where('title', 'like', "%{$courseType} Course%")
                      ->orWhere('title', 'like', "%{$courseType}%");
                });
            }
        }

        // Filter by active status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'archived') {
                $query->where('is_active', false);
            }
        }

        // Default to active courses only
        if (!$request->filled('status')) {
            $query->where('is_active', true);
        }

        // Sort
        $sortColumn = $request->get('sort_column', 'title');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);

        $courses = $query->paginate(15);

        // Add course type statistics
        $stats = [
            'total' => Course::count(),
            'active' => Course::where('is_active', true)->count(),
            'archived' => Course::where('is_active', false)->count(),
            'd_courses' => Course::where('is_active', true)
                ->where(function($q) {
                    $q->where('title', 'like', '%D Course%')
                      ->orWhere('title', 'like', '%D%');
                })->count(),
            'g_courses' => Course::where('is_active', true)
                ->where(function($q) {
                    $q->where('title', 'like', '%G Course%')
                      ->orWhere('title', 'like', '%G%');
                })->count(),
        ];

        $content = array_merge([
            'courses' => $courses,
            'stats' => $stats,
            'filters' => [
                'course_type' => $request->course_type,
                'status' => $request->status,
                'sort_column' => $sortColumn,
                'sort_direction' => $sortDirection,
            ],
            'permissions' => $this->getCoursePermissions(),
        ], self::renderPageMeta('Course Management'));

        return view('admin.admin-center.courses.management.index', compact('content'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $content = self::renderPageMeta('Create Course');
        return view('admin.admin-center.courses.management.create', compact('content'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:64',
            'title_long' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'exam_id' => 'required|exists:exams,id',
            'eq_spec_id' => 'required|exists:exam_question_spec,id',
            'zoom_creds_id' => 'required|exists:zoom_creds,id',
            'needs_range' => 'boolean',
        ]);

        $course = Course::create($validated);

        // Set total_minutes based on course type
        $course->update([
            'total_minutes' => $course->getCalculatedTotalMinutes()
        ]);

        return redirect()->route('admin.courses.management.index')
            ->with('success', "Course '{$course->title}' created successfully.");
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        // Get course with relationships (only load existing relationships)
        $course->load(['CourseAuths', 'CourseUnits']);

        // Get statistics
        $courseAuthsCount = $course->CourseAuths()->count();
        $activeEnrollments = $course->CourseAuths()->whereNull('disabled_at')->count();
        $courseUnits = $course->CourseUnits()->orderBy('ordering')->get();

        $content = [
            'course' => $course,
            'course_auths_count' => $courseAuthsCount,
            'active_enrollments' => $activeEnrollments,
            'course_units' => $courseUnits,
        ];

        return view('admin.admin-center.courses.management.show', [
            'content' => $content
        ], self::renderPageMeta("Course Details: {$course->title}"));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $content = array_merge([
            'course' => $course,
        ], self::renderPageMeta("Edit Course: {$course->title}"));

        return view('admin.admin-center.courses.management.edit', compact('content'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:64',
            'title_long' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'exam_id' => 'required|exists:exams,id',
            'eq_spec_id' => 'required|exists:exam_question_spec,id',
            'zoom_creds_id' => 'required|exists:zoom_creds,id',
            'needs_range' => 'boolean',
        ]);

        $course->update($validated);

        // Update total_minutes if course type changed
        if ($course->total_minutes !== $course->getCalculatedTotalMinutes()) {
            $course->update([
                'total_minutes' => $course->getCalculatedTotalMinutes()
            ]);
        }

        return redirect()->route('admin.courses.management.show', $course)
            ->with('success', "Course '{$course->title}' updated successfully.");
    }

    /**
     * Archive the specified course
     */
    public function archive(Course $course)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $course->archive();

        return redirect()->route('admin.courses.management.index')
            ->with('success', "Course '{$course->title}' has been archived.");
    }

    /**
     * Restore the specified course from archive
     */
    public function restore(Course $course)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $course->restore();

        return redirect()->route('admin.courses.management.index')
            ->with('success', "Course '{$course->title}' has been restored from archive.");
    }

    /**
     * Remove the specified course from storage (soft delete)
     */
    public function destroy(Course $course)
    {
        if (!$this->canDeleteCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if course has active enrollments
        $activeEnrollments = $course->CourseAuths()->whereNull('completed_at')->count();
        if ($activeEnrollments > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete course with {$activeEnrollments} active enrollments. Archive instead."
            ], 422);
        }

        $courseName = $course->title;
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => "Course '{$courseName}' has been deleted successfully."
        ]);
    }

    /**
     * Get course type statistics for dashboard
     */
    public function courseTypeStats()
    {
        $dCourses = Course::where('is_active', true)
            ->get()
            ->filter(function($course) {
                return $course->isDCourse();
            });

        $gCourses = Course::where('is_active', true)
            ->get()
            ->filter(function($course) {
                return $course->isGCourse();
            });

        return response()->json([
            'd_courses' => [
                'count' => $dCourses->count(),
                'total_enrollments' => $dCourses->sum(function($course) {
                    return $course->CourseAuths()->count();
                }),
            ],
            'g_courses' => [
                'count' => $gCourses->count(),
                'total_enrollments' => $gCourses->sum(function($course) {
                    return $course->CourseAuths()->count();
                }),
            ]
        ]);
    }
}
