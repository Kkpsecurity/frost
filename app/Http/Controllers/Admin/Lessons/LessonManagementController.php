<?php

namespace App\Http\Controllers\Admin\Lessons;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseUnitLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class LessonManagementController extends Controller
{
    /**
     * Display a listing of course units with their lessons for better management
     */
    public function index(Request $request): View
    {
        $query = CourseUnit::with(['Course', 'CourseUnitLessons.Lesson']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('admin_title', 'ILIKE', '%' . $request->search . '%')
                  ->orWhereHas('Course', function($subQ) use ($request) {
                      $subQ->where('title', 'ILIKE', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('has_lessons')) {
            if ($request->has_lessons === 'yes') {
                $query->whereHas('CourseUnitLessons');
            } elseif ($request->has_lessons === 'no') {
                $query->whereDoesntHave('CourseUnitLessons');
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'course_id');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortField, ['title', 'ordering', 'course_id'])) {
            if ($sortField === 'course_id') {
                $query->join('courses', 'course_units.course_id', '=', 'courses.id')
                      ->orderBy('courses.title', $sortDirection)
                      ->orderBy('course_units.ordering', 'asc')
                      ->select('course_units.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->join('courses', 'course_units.course_id', '=', 'courses.id')
                  ->orderBy('courses.title', 'asc')
                  ->orderBy('course_units.ordering', 'asc')
                  ->select('course_units.*');
        }

        $courseUnits = $query->paginate(25)->appends($request->query());

        // Get statistics
        $stats = [
            'total_course_units' => CourseUnit::count(),
            'units_with_lessons' => CourseUnit::whereHas('CourseUnitLessons')->count(),
            'empty_units' => CourseUnit::whereDoesntHave('CourseUnitLessons')->count(),
            'total_lessons' => Lesson::count(),
            'avg_hours_per_unit' => round(
                CourseUnit::join('course_unit_lessons', 'course_units.id', '=', 'course_unit_lessons.course_unit_id')
                          ->avg('progress_minutes') / 60, 1
            ) ?: 0,
        ];

        // Get courses for filter dropdown
        $courses = Course::where('is_active', true)->orderBy('title')->get();

        $content = [
            'title' => 'Course Unit & Lesson Management',
            'course_units' => $courseUnits,
            'stats' => $stats,
            'courses' => $courses,
            'filters' => $request->only(['search', 'course', 'has_lessons', 'sort', 'direction']),
        ];

        return view('admin.lessons.index', compact('content'));
    }

    /**
     * Show the form for creating a new lesson
     */
    public function create(): View
    {
        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $content = [
            'title' => 'Create New Lesson',
            'courses' => $courses,
        ];

        return view('admin.lessons.create', compact('content'));
    }

    /**
     * Store a newly created lesson
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:64|unique:lessons,title',
            'credit_minutes' => 'required|integer|min:1|max:9999',
            'video_seconds' => 'nullable|integer|min:0|max:999999',
            'course_units' => 'nullable|array',
            'course_units.*' => 'exists:course_units,id',
            'progress_minutes' => 'nullable|array',
            'progress_minutes.*' => 'nullable|integer|min:0|max:9999',
            'instr_seconds' => 'nullable|array',
            'instr_seconds.*' => 'nullable|integer|min:0|max:999999',
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
                    $instrSeconds = $validated['instr_seconds'][$index] ?? 0;

                    // Get next ordering for this course unit
                    $nextOrdering = CourseUnitLesson::where('course_unit_id', $courseUnitId)->max('ordering') + 1;

                    CourseUnitLesson::create([
                        'course_unit_id' => $courseUnitId,
                        'lesson_id' => $lesson->id,
                        'progress_minutes' => $progressMinutes,
                        'instr_seconds' => $instrSeconds,
                        'ordering' => $nextOrdering,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.lessons.show', $lesson)
                           ->with('success', "Lesson '{$lesson->title}' created successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to create lesson: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified lesson with comprehensive details
     */
    public function show(Lesson $lesson): View
    {
        // Load relationships with detailed information
        $lesson->load([
            'CourseUnits.Course',
            'CourseUnitLessons.CourseUnit.Course',
            'ExamQuestions'
        ]);

        // Get statistics
        $stats = [
            'course_units_count' => $lesson->CourseUnits->count(),
            'courses_count' => $lesson->CourseUnits->pluck('course_id')->unique()->count(),
            'exam_questions_count' => $lesson->ExamQuestions->count(),
            'total_progress_minutes' => $lesson->CourseUnitLessons->sum('progress_minutes'),
            'total_instr_seconds' => $lesson->CourseUnitLessons->sum('instr_seconds'),
            'credit_hours' => round($lesson->credit_minutes / 60, 1),
            'video_hours' => round($lesson->video_seconds / 3600, 2),
        ];

        // Group course unit lessons by course for better display
        $lessonsByCourse = $lesson->CourseUnitLessons
            ->groupBy(function ($cul) {
                return $cul->CourseUnit->Course->title;
            });

        $content = [
            'title' => "Lesson Details: {$lesson->title}",
            'lesson' => $lesson,
            'stats' => $stats,
            'lessons_by_course' => $lessonsByCourse,
        ];

        return view('admin.lessons.show', compact('content'));
    }

    /**
     * Show the form for editing the specified lesson
     */
    public function edit(Lesson $lesson): View
    {
        // Load relationships
        $lesson->load(['CourseUnitLessons.CourseUnit.Course']);

        // Get all courses and their units for assignment
        $courses = Course::where('is_active', true)
                        ->with('CourseUnits')
                        ->orderBy('title')
                        ->get();

        $content = [
            'title' => "Edit Lesson: {$lesson->title}",
            'lesson' => $lesson,
            'courses' => $courses,
        ];

        return view('admin.lessons.edit', compact('content'));
    }

    /**
     * Update the specified lesson
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:64|unique:lessons,title,' . $lesson->id,
            'credit_minutes' => 'required|integer|min:1|max:9999',
            'video_seconds' => 'nullable|integer|min:0|max:999999',
            'course_units' => 'nullable|array',
            'course_units.*' => 'exists:course_units,id',
            'progress_minutes' => 'nullable|array',
            'progress_minutes.*' => 'nullable|integer|min:0|max:9999',
            'instr_seconds' => 'nullable|array',
            'instr_seconds.*' => 'nullable|integer|min:0|max:999999',
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
                    $instrSeconds = $validated['instr_seconds'][$index] ?? 0;

                    // Get next ordering for this course unit
                    $nextOrdering = CourseUnitLesson::where('course_unit_id', $courseUnitId)->max('ordering') + 1;

                    CourseUnitLesson::create([
                        'course_unit_id' => $courseUnitId,
                        'lesson_id' => $lesson->id,
                        'progress_minutes' => $progressMinutes,
                        'instr_seconds' => $instrSeconds,
                        'ordering' => $nextOrdering,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.lessons.show', $lesson)
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
    public function destroy(Lesson $lesson): RedirectResponse
    {
        // Check for dependencies
        $dependencies = [];

        if ($lesson->ExamQuestions()->count() > 0) {
            $dependencies[] = 'exam questions';
        }

        if (!empty($dependencies)) {
            return back()->withErrors([
                'error' => 'Cannot delete lesson with associated ' . implode(', ', $dependencies) . '.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Remove course unit associations
            CourseUnitLesson::where('lesson_id', $lesson->id)->delete();

            // Delete the lesson
            $lessonTitle = $lesson->title;
            $lesson->delete();

            DB::commit();

            return redirect()->route('admin.lessons.index')
                           ->with('success', "Lesson '{$lessonTitle}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete lesson: ' . $e->getMessage()]);
        }
    }

    /**
     * Manage course units for a specific lesson
     */
    public function manageUnits(Lesson $lesson): View
    {
        $lesson->load([
            'CourseUnitLessons.CourseUnit.Course'
        ]);

        $courses = Course::where('is_active', true)
                        ->with(['CourseUnits' => function ($query) {
                            $query->orderBy('ordering');
                        }])
                        ->orderBy('title')
                        ->get();

        $content = [
            'title' => "Manage Units: {$lesson->title}",
            'lesson' => $lesson,
            'courses' => $courses,
        ];

        return view('admin.lessons.units', compact('content'));
    }

    /**
     * Update course units for a lesson via AJAX
     */
    public function updateUnits(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'units' => 'required|array',
            'units.*.course_unit_id' => 'required|exists:course_units,id',
            'units.*.progress_minutes' => 'required|integer|min:0|max:9999',
            'units.*.instr_seconds' => 'required|integer|min:0|max:999999',
            'units.*.ordering' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Remove existing associations
            CourseUnitLesson::where('lesson_id', $lesson->id)->delete();

            // Create new associations
            foreach ($validated['units'] as $unitData) {
                CourseUnitLesson::create([
                    'lesson_id' => $lesson->id,
                    'course_unit_id' => $unitData['course_unit_id'],
                    'progress_minutes' => $unitData['progress_minutes'],
                    'instr_seconds' => $unitData['instr_seconds'],
                    'ordering' => $unitData['ordering'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course units updated successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course units: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course units for a specific course (AJAX endpoint)
     */
    public function getCourseUnits(Course $course): JsonResponse
    {
        $courseUnits = $course->CourseUnits()
                             ->orderBy('ordering')
                             ->get(['id', 'title', 'admin_title', 'ordering']);

        return response()->json($courseUnits);
    }

    /**
     * Bulk delete lessons
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        $lessons = Lesson::whereIn('id', $validated['lesson_ids'])->get();
        $deletedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($lessons as $lesson) {
                // Check for dependencies
                if ($lesson->ExamQuestions()->count() > 0) {
                    $errors[] = "'{$lesson->title}' has exam questions";
                    continue;
                }

                // Remove course unit associations and delete lesson
                CourseUnitLesson::where('lesson_id', $lesson->id)->delete();
                $lesson->delete();
                $deletedCount++;
            }

            DB::commit();

            $message = "Successfully deleted {$deletedCount} lesson(s).";
            if (!empty($errors)) {
                $message .= ' Skipped: ' . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Bulk delete failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk assign lessons to course units
     */
    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
            'course_unit_id' => 'required|exists:course_units,id',
        ]);

        $lessons = Lesson::whereIn('id', $validated['lesson_ids'])->get();
        $assignedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($lessons as $lesson) {
                // Check if already assigned
                $exists = CourseUnitLesson::where('lesson_id', $lesson->id)
                                        ->where('course_unit_id', $validated['course_unit_id'])
                                        ->exists();

                if (!$exists) {
                    $nextOrdering = CourseUnitLesson::where('course_unit_id', $validated['course_unit_id'])
                                                   ->max('ordering') + 1;

                    CourseUnitLesson::create([
                        'lesson_id' => $lesson->id,
                        'course_unit_id' => $validated['course_unit_id'],
                        'progress_minutes' => $lesson->credit_minutes,
                        'instr_seconds' => 0,
                        'ordering' => $nextOrdering,
                    ]);

                    $assignedCount++;
                }
            }

            DB::commit();

            return back()->with('success', "Successfully assigned {$assignedCount} lesson(s) to course unit.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Bulk assignment failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Manage lessons for a specific course unit
     */
    public function manageCourseUnitLessons(CourseUnit $courseUnit): View
    {
        $courseUnit->load([
            'Course',
            'CourseUnitLessons.Lesson'
        ]);

        // Get all lessons that could be assigned
        $availableLessons = Lesson::orderBy('title')->get();

        // Get lessons already assigned to this unit
        $assignedLessons = $courseUnit->CourseUnitLessons()->with('Lesson')->orderBy('ordering')->get();

        $content = [
            'title' => "Manage Lessons: {$courseUnit->Course->title} - {$courseUnit->title}",
            'courseUnit' => $courseUnit,
            'availableLessons' => $availableLessons,
            'assignedLessons' => $assignedLessons,
        ];

        return view('admin.lessons.manage-unit-lessons', compact('content'));
    }

    /**
     * Update lessons for a specific course unit
     */
    public function updateCourseUnitLessons(Request $request, CourseUnit $courseUnit): RedirectResponse
    {
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.lesson_id' => 'required|exists:lessons,id',
            'lessons.*.progress_minutes' => 'required|integer|min:0|max:9999',
            'lessons.*.instr_seconds' => 'required|integer|min:0|max:999999',
            'lessons.*.ordering' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Remove existing lesson assignments for this course unit
            CourseUnitLesson::where('course_unit_id', $courseUnit->id)->delete();

            // Create new assignments
            foreach ($validated['lessons'] as $lessonData) {
                CourseUnitLesson::create([
                    'course_unit_id' => $courseUnit->id,
                    'lesson_id' => $lessonData['lesson_id'],
                    'progress_minutes' => $lessonData['progress_minutes'],
                    'instr_seconds' => $lessonData['instr_seconds'],
                    'ordering' => $lessonData['ordering'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.lessons.units.manage', $courseUnit)
                           ->with('success', 'Course unit lessons updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update lessons: ' . $e->getMessage()]);
        }
    }
}
