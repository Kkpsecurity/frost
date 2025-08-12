<?php

namespace App\Http\Controllers\Admin\Lessons;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseUnitLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CoursePermissionsTrait;

class LessonManagementController extends Controller
{
    use CoursePermissionsTrait;

    /**
     * Display a listing of lessons
     */
    public function index()
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Get lessons with course unit relationships
        $lessons = Lesson::with(['CourseUnits.Course'])
                        ->orderBy('title')
                        ->paginate(25);

        // Get statistics
        $stats = [
            'total' => Lesson::count(),
            'with_units' => Lesson::whereHas('CourseUnits')->count(),
            'without_units' => Lesson::whereDoesntHave('CourseUnits')->count(),
            'total_minutes' => Lesson::sum('credit_minutes'),
        ];

        $content = array_merge([
            'lessons' => $lessons,
            'stats' => $stats,
        ], self::renderPageMeta('Lesson Management'));

        return view('admin.admin-center.lessons.management.index', compact('content'));
    }

    /**
     * Show the form for creating a new lesson
     */
    public function create()
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Get courses and their units for assignment
        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $content = array_merge([
            'courses' => $courses,
        ], self::renderPageMeta('Create New Lesson'));

        return view('admin.admin-center.lessons.management.create', compact('content'));
    }

    /**
     * Store a newly created lesson
     */
    public function store(Request $request)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:64',
            'credit_minutes' => 'required|integer|min:1|max:9999',
            'video_seconds' => 'nullable|integer|min:0|max:999999',
            'course_units' => 'nullable|array',
            'course_units.*' => 'exists:course_units,id',
            'progress_minutes' => 'nullable|array',
            'progress_minutes.*' => 'nullable|integer|min:0|max:9999',
        ]);

        DB::beginTransaction();
        try {
            // Create the lesson
            $lesson = Lesson::create([
                'title' => $validated['title'],
                'credit_minutes' => $validated['credit_minutes'],
                'video_seconds' => $validated['video_seconds'] ?? 0,
            ]);

            // Attach course units if provided
            if (!empty($validated['course_units'])) {
                foreach ($validated['course_units'] as $index => $courseUnitId) {
                    $progressMinutes = $validated['progress_minutes'][$index] ?? $validated['credit_minutes'];

                    CourseUnitLesson::create([
                        'course_unit_id' => $courseUnitId,
                        'lesson_id' => $lesson->id,
                        'progress_minutes' => $progressMinutes,
                        'ordering' => $index + 1,
                        'instr_seconds' => 0, // Default value
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.lessons.management.show', $lesson)
                           ->with('success', "Lesson '{$lesson->title}' created successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to create lesson: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified lesson
     */
    public function show(Lesson $lesson)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Load relationships
        $lesson->load(['CourseUnits.Course', 'CourseUnitLessons.CourseUnit.Course', 'ExamQuestions']);

        // Get statistics
        $stats = [
            'course_units_count' => $lesson->CourseUnits->count(),
            'courses_count' => $lesson->CourseUnits->pluck('course_id')->unique()->count(),
            'exam_questions_count' => $lesson->ExamQuestions->count(),
            'total_progress_minutes' => $lesson->CourseUnitLessons->sum('progress_minutes'),
        ];

        $content = array_merge([
            'lesson' => $lesson,
            'stats' => $stats,
        ], self::renderPageMeta("Lesson Details: {$lesson->title}"));

        return view('admin.admin-center.lessons.management.show', compact('content'));
    }

    /**
     * Show the form for editing the specified lesson
     */
    public function edit(Lesson $lesson)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Load relationships
        $lesson->load(['CourseUnitLessons.CourseUnit.Course']);

        // Get all courses and their units for assignment
        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $content = array_merge([
            'lesson' => $lesson,
            'courses' => $courses,
        ], self::renderPageMeta("Edit Lesson: {$lesson->title}"));

        return view('admin.admin-center.lessons.management.edit', compact('content'));
    }

    /**
     * Update the specified lesson
     */
    public function update(Request $request, Lesson $lesson)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:64',
            'credit_minutes' => 'required|integer|min:1|max:9999',
            'video_seconds' => 'nullable|integer|min:0|max:999999',
            'course_units' => 'nullable|array',
            'course_units.*' => 'exists:course_units,id',
            'progress_minutes' => 'nullable|array',
            'progress_minutes.*' => 'nullable|integer|min:0|max:9999',
        ]);

        DB::beginTransaction();
        try {
            // Update lesson
            $lesson->update([
                'title' => $validated['title'],
                'credit_minutes' => $validated['credit_minutes'],
                'video_seconds' => $validated['video_seconds'] ?? 0,
            ]);

            // Remove existing course unit associations
            CourseUnitLesson::where('lesson_id', $lesson->id)->delete();

            // Attach new course units if provided
            if (!empty($validated['course_units'])) {
                foreach ($validated['course_units'] as $index => $courseUnitId) {
                    $progressMinutes = $validated['progress_minutes'][$index] ?? $validated['credit_minutes'];

                    CourseUnitLesson::create([
                        'course_unit_id' => $courseUnitId,
                        'lesson_id' => $lesson->id,
                        'progress_minutes' => $progressMinutes,
                        'ordering' => $index + 1,
                        'instr_seconds' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.lessons.management.show', $lesson)
                           ->with('success', "Lesson '{$lesson->title}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update lesson: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified lesson
     */
    public function destroy(Lesson $lesson)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if lesson has exam questions
        if ($lesson->ExamQuestions()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete lesson with associated exam questions.']);
        }

        DB::beginTransaction();
        try {
            // Remove course unit associations
            CourseUnitLesson::where('lesson_id', $lesson->id)->delete();

            // Delete the lesson
            $lessonTitle = $lesson->title;
            $lesson->delete();

            DB::commit();

            return redirect()->route('admin.lessons.management.index')
                           ->with('success', "Lesson '{$lessonTitle}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete lesson: ' . $e->getMessage()]);
        }
    }

    /**
     * Get course units for a specific course (API endpoint for AJAX)
     */
    public function getCourseUnits(Course $course)
    {
        if (!$this->canManageCourses()) {
            abort(403, 'Unauthorized action.');
        }

        $courseUnits = $course->CourseUnits()
                             ->orderBy('ordering')
                             ->get(['id', 'title', 'admin_title']);

        return response()->json($courseUnits);
    }

    /**
     * Render page meta data for consistent page setup
     */
    private static function renderPageMeta(string $title): array
    {
        return [
            'title' => $title,
            'breadcrumbs' => [
                ['name' => 'Admin Center', 'url' => route('admin.dashboard')],
                ['name' => 'Lessons', 'url' => route('admin.lessons.management.index')],
                ['name' => $title, 'url' => null],
            ],
        ];
    }
}
