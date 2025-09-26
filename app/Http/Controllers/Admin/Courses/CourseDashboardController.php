<?php

namespace App\Http\Controllers\Admin\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\CourseUnit;
use App\Models\Order;
use App\Models\Exam;
use App\Models\ExamQuestionSpec;
use App\Models\ZoomCreds;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CourseDashboardController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display the course management dashboard
     */
    public function dashboard(Request $request): View
    {
        $content = array_merge([], self::renderPageMeta('courses_dashboard', 'Course Management'));

        // Get course statistics
        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'archived_courses' => Course::where('is_active', false)->count(),
            'course_authorizations' => CourseAuth::count(),
            'completed_courses' => CourseAuth::whereNotNull('completed_at')->count(),
            'revenue' => Order::whereNotNull('completed_at')->sum('total_price'),
        ];

        return view('admin.courses.dashboard', compact('content', 'stats'));
    }

    /**
     * Get courses data for DataTables
     */
    public function getCoursesData(Request $request): JsonResponse
    {
        $query = Course::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'ILIKE', "%{$searchValue}%")
                  ->orWhere('title_long', 'ILIKE', "%{$searchValue}%")
                  ->orWhere('id', 'LIKE', "%{$searchValue}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'archived') {
                $query->where('is_active', false);
            }
        }

        // Course type filter
        if ($request->has('course_type') && $request->course_type !== 'all') {
            if ($request->course_type === 'D') {
                $query->where(function ($q) {
                    $q->where('title', 'ILIKE', '%D COURSE%')
                      ->orWhere('title', 'ILIKE', '%D-COURSE%');
                });
            } elseif ($request->course_type === 'G') {
                $query->where(function ($q) {
                    $q->where('title', 'ILIKE', '%G COURSE%')
                      ->orWhere('title', 'ILIKE', '%G-COURSE%');
                });
            }
        }

        // Get total count for pagination
        $totalRecords = Course::count();
        $filteredRecords = $query->count();

        // Sorting
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnName = $request->columns[$columnIndex]['data'];
            $sortDirection = $request->order[0]['dir'];

            $query->orderBy($columnName, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;

        $courses = $query->skip($start)->take($length)->get();

        // Transform data for DataTable
        $data = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'title_long' => $course->title_long ?? '',
                'price' => number_format($course->price, 2),
                'type' => $course->getCourseType(),
                'type_badge' => '<span class="badge badge-' . $course->getCourseTypeBadgeColor() . '">' . $course->getCourseType() . '</span>',
                'status' => $course->is_active ? 'active' : 'archived',
                'status_badge' => $course->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-secondary">Archived</span>',
                'enrollments' => $course->CourseAuths()->count(),
                'total_minutes' => number_format($course->total_minutes),
                'actions' => $this->generateActionButtons($course),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Generate action buttons for each course row
     */
    private function generateActionButtons(Course $course): string
    {
        $viewUrl = route('admin.courses.manage.view', $course);
        $editUrl = route('admin.courses.manage.edit', $course);

        $buttons = '<div class="btn-group" role="group">';
        $buttons .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View Course"><i class="fas fa-eye"></i></a>';
        $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit Course"><i class="fas fa-edit"></i></a>';

        if ($course->is_active) {
            $buttons .= '<button class="btn btn-sm btn-warning archive-course" data-id="' . $course->id . '" title="Archive Course"><i class="fas fa-archive"></i></button>';
        } else {
            $buttons .= '<button class="btn btn-sm btn-success restore-course" data-id="' . $course->id . '" title="Restore Course"><i class="fas fa-undo"></i></button>';
        }

        $buttons .= '</div>';

        return $buttons;
    }

    /**
     * View a specific course
     */
    public function viewCourse(Course $course): View
    {
        $content = array_merge([], self::renderPageMeta('view_course', "Course: {$course->title}"));

        // Get course statistics
        $stats = [
            'total_enrollments' => $course->CourseAuths()->count(),
            'active_enrollments' => $course->CourseAuths()->whereNull('completed_at')->count(),
            'completed_enrollments' => $course->CourseAuths()->whereNotNull('completed_at')->count(),
            'total_revenue' => Order::whereHas('CourseAuth', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->whereNotNull('completed_at')->sum('total_price'),
            'total_units' => $course->CourseUnits()->count(),
        ];

        // Get recent enrollments
        $recentEnrollments = $course->CourseAuths()
            ->with('User')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.courses.view', compact('course', 'content', 'stats', 'recentEnrollments'));
    }

    /**
     * Show create course form
     */
    public function createCourse(): View
    {
        $content = array_merge([], self::renderPageMeta('create_course', 'Create New Course'));

        // Get necessary data for dropdowns
        $exams = Exam::orderBy('admin_title')->get();
        $examQuestionSpecs = ExamQuestionSpec::orderBy('name')->get();
        $zoomCreds = ZoomCreds::where('zoom_status', 'enabled')->orderBy('zoom_email')->get();

        return view('admin.courses.create', compact('content', 'exams', 'examQuestionSpecs', 'zoomCreds'));
    }

    /**
     * Store a new course
     */
    public function storeCourse(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:64|unique:courses,title',
            'title_long' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0|max:999.99',
            'total_minutes' => 'required|integer|min:1',
            'policy_expire_days' => 'required|integer|min:1|max:1000',
            'exam_id' => 'required|exists:exams,id',
            'eq_spec_id' => 'required|exists:exam_question_specs,id',
            'zoom_creds_id' => 'required|exists:zoom_creds,id',
            'needs_range' => 'boolean',
            'dates_template' => 'nullable|json',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'title_long' => $request->title_long,
            'price' => $request->price,
            'total_minutes' => $request->total_minutes,
            'policy_expire_days' => $request->policy_expire_days,
            'exam_id' => $request->exam_id,
            'eq_spec_id' => $request->eq_spec_id,
            'zoom_creds_id' => $request->zoom_creds_id,
            'needs_range' => $request->boolean('needs_range'),
            'dates_template' => $request->dates_template ? json_decode($request->dates_template, true) : null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.courses.manage.view', $course)
            ->with('success', 'Course created successfully');
    }

    /**
     * Show edit course form
     */
    public function editCourse(Course $course): View
    {
        $content = array_merge([], self::renderPageMeta('edit_course', "Edit Course: {$course->title}"));

        // Get necessary data for dropdowns
        $exams = Exam::orderBy('admin_title')->get();
        $examQuestionSpecs = ExamQuestionSpec::orderBy('name')->get();
        $zoomCreds = ZoomCreds::where('zoom_status', 'enabled')->orderBy('zoom_email')->get();

        return view('admin.courses.edit', compact('course', 'content', 'exams', 'examQuestionSpecs', 'zoomCreds'));
    }

    /**
     * Update a course
     */
    public function updateCourse(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:64|unique:courses,title,' . $course->id,
            'title_long' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0|max:999.99',
            'total_minutes' => 'required|integer|min:1',
            'policy_expire_days' => 'required|integer|min:1|max:1000',
            'exam_id' => 'required|exists:exams,id',
            'eq_spec_id' => 'required|exists:exam_question_specs,id',
            'zoom_creds_id' => 'required|exists:zoom_creds,id',
            'needs_range' => 'boolean',
            'is_active' => 'boolean',
            'dates_template' => 'nullable|json',
        ]);

        $course->update([
            'title' => $request->title,
            'title_long' => $request->title_long,
            'price' => $request->price,
            'total_minutes' => $request->total_minutes,
            'policy_expire_days' => $request->policy_expire_days,
            'exam_id' => $request->exam_id,
            'eq_spec_id' => $request->eq_spec_id,
            'zoom_creds_id' => $request->zoom_creds_id,
            'needs_range' => $request->boolean('needs_range'),
            'is_active' => $request->boolean('is_active'),
            'dates_template' => $request->dates_template ? json_decode($request->dates_template, true) : null,
        ]);

        return redirect()->route('admin.courses.manage.view', $course)
            ->with('success', 'Course updated successfully');
    }

    /**
     * Archive a course
     */
    public function archiveCourse(Course $course): JsonResponse
    {
        $course->update(['is_active' => false]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course archived successfully'
        ]);
    }

    /**
     * Restore a course
     */
    public function restoreCourse(Course $course): JsonResponse
    {
        $course->update(['is_active' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course restored successfully'
        ]);
    }

    /**
     * View course enrollments
     */
    public function viewEnrollments(Course $course, Request $request)
    {
        // Handle course switching
        if ($request->has('course_id') && $request->course_id != $course->id) {
            $newCourse = Course::findOrFail($request->course_id);
            return redirect()->route('admin.courses.manage.enrollments', $newCourse);
        }

        $content = array_merge([], self::renderPageMeta('course_enrollments', "Enrollments: {$course->title}"));

        $enrollments = $course->CourseAuths()
            ->with(['User', 'Order'])
            ->latest()
            ->paginate(25);

        // Get all active courses for dropdown with enrollment counts
        $allCourses = Course::where('is_active', true)
            ->withCount('CourseAuths')
            ->orderBy('title')
            ->get();

        return view('admin.courses.enrollments', compact('course', 'content', 'enrollments', 'allCourses'));
    }

    /**
     * View course units
     */
    public function viewUnits(Course $course): View
    {
        $content = array_merge([], self::renderPageMeta('course_units', "Units: {$course->title}"));

        $units = $course->CourseUnits()
            ->with('CourseUnitLessons.Lesson')
            ->orderBy('ordering')
            ->get();

        return view('admin.courses.units', compact('course', 'content', 'units'));
    }
}
