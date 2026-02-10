<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SelfStudyLesson;
use App\Models\StudentLesson;
use App\Models\StudentUnit;
use App\Models\CourseAuth;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DevToolsController
 *
 * Development tools for testing - REMOVE IN PRODUCTION
 * Allows quick completion of lessons for testing exams
 */
class DevToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Complete all lessons for a course (online or offline mode)
     *
     * POST /classroom/dev/complete-all-lessons
     */
    public function completeAllLessons(Request $request)
    {
        if (!config('app.debug')) {
            return response()->json([
                'success' => false,
                'error' => 'Dev tools only available in debug mode',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id',
                'mode' => 'required|in:online,offline',
            ]);

            $courseAuthId = $validated['course_auth_id'];
            $mode = $validated['mode'];
            $userId = Auth::id();

            Log::info('Dev Tools: Complete all lessons', [
                'user_id' => $userId,
                'course_auth_id' => $courseAuthId,
                'mode' => $mode,
            ]);

            // Get course from course_auth
            $courseAuth = CourseAuth::with('Course')->findOrFail($courseAuthId);
            $course = $courseAuth->Course;

            // Get all lessons for the course through course_unit_lessons
            $lessons = DB::table('course_unit_lessons')
                ->join('lessons', 'course_unit_lessons.lesson_id', '=', 'lessons.id')
                ->join('course_units', 'course_unit_lessons.course_unit_id', '=', 'course_units.id')
                ->where('course_units.course_id', $course->id)
                ->select('lessons.*', DB::raw('MIN(course_unit_lessons.ordering) as min_ordering'))
                ->groupBy('lessons.id', 'lessons.title', 'lessons.credit_minutes', 'lessons.video_seconds')
                ->orderBy('min_ordering')
                ->get();

            if ($lessons->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No lessons found for this course',
                ], 404);
            }

            $completed = 0;
            $skipped = 0;

            DB::beginTransaction();

            try {
                if ($mode === 'offline') {
                    // OFFLINE MODE: Create SelfStudyLesson records
                    foreach ($lessons as $lesson) {
                        // Check if already exists
                        $existing = SelfStudyLesson::where('course_auth_id', $courseAuthId)
                            ->where('lesson_id', $lesson->id)
                            ->first();

                        if ($existing && $existing->completed_at) {
                            $skipped++;
                            continue;
                        }

                        if ($existing) {
                            // Update existing
                            $existing->update([
                                'completed_at' => now(),
                                'seconds_viewed' => $lesson->video_seconds,
                                'completion_percentage' => 100.00,
                                'quota_status' => 'consumed',
                                'quota_consumed_minutes' => $lesson->credit_minutes,
                            ]);
                        } else {
                            // Create new
                            SelfStudyLesson::create([
                                'course_auth_id' => $courseAuthId,
                                'lesson_id' => $lesson->id,
                                'created_at' => now(),
                                'completed_at' => now(),
                                'seconds_viewed' => $lesson->video_seconds,
                                'completion_percentage' => 100.00,
                                'quota_status' => 'consumed',
                                'quota_consumed_minutes' => $lesson->credit_minutes,
                                'video_duration_seconds' => $lesson->video_seconds,
                            ]);
                        }
                        $completed++;
                    }
                } else {
                    // ONLINE MODE: Create StudentLesson records
                    // Need StudentUnit and InstUnit first
                    $studentUnit = StudentUnit::where('course_auth_id', $courseAuthId)
                        ->latest()
                        ->first();

                    if (!$studentUnit) {
                        return response()->json([
                            'success' => false,
                            'error' => 'No StudentUnit found. Student must join a class first.',
                        ], 404);
                    }

                    if (!$studentUnit->inst_unit_id) {
                        return response()->json([
                            'success' => false,
                            'error' => 'No InstUnit found. Instructor must start the class first.',
                        ], 404);
                    }

                    foreach ($lessons as $lesson) {
                        // Find or create InstLesson (required for StudentLesson)
                        $instLesson = DB::table('inst_lesson')
                            ->where('inst_unit_id', $studentUnit->inst_unit_id)
                            ->where('lesson_id', $lesson->id)
                            ->value('id');

                        if (!$instLesson) {
                            $instLesson = DB::table('inst_lesson')->insertGetId([
                                'inst_unit_id' => $studentUnit->inst_unit_id,
                                'lesson_id' => $lesson->id,
                                'created_at' => now(),
                                'created_by' => Auth::id(),
                                'completed_at' => now(),
                                'is_paused' => false,
                            ], 'id');
                        }

                        // Check if StudentLesson already exists
                        $existing = StudentLesson::where('student_unit_id', $studentUnit->id)
                            ->where('lesson_id', $lesson->id)
                            ->first();

                        if ($existing && $existing->completed_at) {
                            $skipped++;
                            continue;
                        }

                        if ($existing) {
                            // Update existing
                            $existing->update([
                                'completed_at' => now(),
                                'inst_lesson_id' => $instLesson,
                            ]);
                        } else {
                            // Create new
                            StudentLesson::create([
                                'student_unit_id' => $studentUnit->id,
                                'lesson_id' => $lesson->id,
                                'inst_lesson_id' => $instLesson,
                                'created_at' => now(),
                                'completed_at' => now(),
                            ]);
                        }
                        $completed++;
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Completed {$completed} lessons in {$mode} mode" . ($skipped > 0 ? ", skipped {$skipped} already complete" : ""),
                    'mode' => $mode,
                    'completed' => $completed,
                    'skipped' => $skipped,
                    'total' => $lessons->count(),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Dev Tools: Failed to complete lessons', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to complete lessons',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
