<?php

namespace App\Http\Controllers\Frontend\Student;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\CourseAuth;

class StudentDevToolsController extends Controller
{
    /**
     * Complete next incomplete lesson for development/testing
     */
    public function completeOneLesson(Request $request): JsonResponse
    {
        $courseAuthId = (int) $request->input('course_auth_id');

        if (!$courseAuthId) {
            return response()->json(['error' => 'Missing course_auth_id'], 400);
        }

        $courseAuth = CourseAuth::with(['StudentUnits'])->find($courseAuthId);

        if (!$courseAuth) {
            return response()->json(['error' => 'CourseAuth not found'], 404);
        }

        $studentUnitIds = $courseAuth->StudentUnits->pluck('id')->values();

        if ($studentUnitIds->isEmpty()) {
            return response()->json(['error' => 'No StudentUnits found. Student must join classroom first.'], 400);
        }

        // Find next incomplete lesson
        $target = DB::table('student_lesson')
            ->whereIn('student_unit_id', $studentUnitIds)
            ->whereNull('completed_at')
            ->whereNull('dnc_at')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first(['id', 'lesson_id', 'student_unit_id']);

        if (!$target) {
            return response()->json(['error' => 'No incomplete lessons found'], 400);
        }

        // Get lesson details
        $lesson = DB::table('lessons')->where('id', $target->lesson_id)->first(['id', 'title']);

        // Mark as completed
        DB::beginTransaction();
        try {
            DB::table('student_lesson')
                ->where('id', $target->id)
                ->update([
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            // Also mark in self_study_lessons (for offline mode) - upsert to ensure record exists
            DB::table('self_study_lessons')->updateOrInsert(
                [
                    'course_auth_id' => $courseAuthId,
                    'lesson_id' => $target->lesson_id,
                ],
                [
                    'completed_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::commit();

            // Clear cache
            Cache::forget("PCLCache:{$courseAuthId}");
            Cache::forget("StudentUnit:{$courseAuthId}");
            Cache::forget("CourseAuth:{$courseAuthId}");
            Cache::forget("StudentLesson:{$target->id}");

            // Get progress
            $completedCount = DB::table('student_lesson')
                ->whereIn('student_unit_id', $studentUnitIds)
                ->whereNotNull('completed_at')
                ->count();

            $totalCount = DB::table('student_lesson')
                ->whereIn('student_unit_id', $studentUnitIds)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Lesson completed',
                'lesson' => [
                    'id' => $target->lesson_id,
                    'title' => $lesson->title ?? 'Unknown',
                ],
                'progress' => [
                    'completed' => $completedCount,
                    'total' => $totalCount,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to complete lesson: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Restart current/last lesson for development/testing
     */
    public function restartCurrentLesson(Request $request): JsonResponse
    {
        $courseAuthId = (int) $request->input('course_auth_id');

        if (!$courseAuthId) {
            return response()->json(['error' => 'Missing course_auth_id'], 400);
        }

        $courseAuth = CourseAuth::with(['StudentUnits'])->find($courseAuthId);

        if (!$courseAuth) {
            return response()->json(['error' => 'CourseAuth not found'], 404);
        }

        $studentUnitIds = $courseAuth->StudentUnits->pluck('id')->values();

        if ($studentUnitIds->isEmpty()) {
            return response()->json(['error' => 'No StudentUnits found'], 400);
        }

        // Find current incomplete lesson OR last completed lesson
        $target = DB::table('student_lesson')
            ->whereIn('student_unit_id', $studentUnitIds)
            ->whereNull('completed_at')
            ->whereNull('dnc_at')
            ->orderBy('created_at', 'asc')
            ->first(['id', 'lesson_id']);

        if (!$target) {
            // No incomplete, try last completed
            $target = DB::table('student_lesson')
                ->whereIn('student_unit_id', $studentUnitIds)
                ->whereNotNull('completed_at')
                ->orderByDesc('completed_at')
                ->first(['id', 'lesson_id']);
        }

        if (!$target) {
            return response()->json(['error' => 'No lessons found'], 400);
        }

        // Get lesson details
        $lesson = DB::table('lessons')->where('id', $target->lesson_id)->first(['id', 'title']);

        DB::beginTransaction();
        try {
            // Delete challenges first
            DB::table('challenges')->where('student_lesson_id', $target->id)->delete();

            // Delete student_lesson
            DB::table('student_lesson')->where('id', $target->id)->delete();

            // Also delete from self_study_lessons if exists (for offline mode)
            DB::table('self_study_lessons')
                ->where('course_auth_id', $courseAuthId)
                ->where('lesson_id', $target->lesson_id)
                ->delete();

            DB::commit();

            // Clear cache
            Cache::forget("PCLCache:{$courseAuthId}");
            Cache::forget("StudentUnit:{$courseAuthId}");
            Cache::forget("CourseAuth:{$courseAuthId}");
            Cache::forget("StudentLesson:{$target->id}");

            return response()->json([
                'success' => true,
                'message' => 'Lesson restarted',
                'lesson' => [
                    'id' => $target->lesson_id,
                    'title' => $lesson->title ?? 'Unknown',
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to restart lesson: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reset all lesson progress for development/testing
     */
    public function resetAllLessons(Request $request): JsonResponse
    {
        $courseAuthId = (int) $request->input('course_auth_id');

        if (!$courseAuthId) {
            return response()->json(['error' => 'Missing course_auth_id'], 400);
        }

        $courseAuth = CourseAuth::with(['StudentUnits'])->find($courseAuthId);

        if (!$courseAuth) {
            return response()->json(['error' => 'CourseAuth not found'], 404);
        }

        $studentUnitIds = $courseAuth->StudentUnits->pluck('id')->values();

        DB::beginTransaction();
        try {
            $counts = [
                'challenges' => 0,
                'student_lessons' => 0,
                'validations' => 0,
                'student_units' => 0,
                'self_study_lessons' => 0,
            ];

            if ($studentUnitIds->isNotEmpty()) {
                // Get student_lesson IDs
                $studentLessonIds = DB::table('student_lesson')
                    ->whereIn('student_unit_id', $studentUnitIds)
                    ->pluck('id');

                // Delete challenges
                if ($studentLessonIds->isNotEmpty()) {
                    $counts['challenges'] = DB::table('challenges')
                        ->whereIn('student_lesson_id', $studentLessonIds)
                        ->delete();
                }

                // Delete student_lessons
                $counts['student_lessons'] = DB::table('student_lesson')
                    ->whereIn('student_unit_id', $studentUnitIds)
                    ->delete();

                // Delete validations
                $counts['validations'] = DB::table('validations')
                    ->whereIn('student_unit_id', $studentUnitIds)
                    ->delete();

                // Delete student_units
                $counts['student_units'] = DB::table('student_unit')
                    ->whereIn('id', $studentUnitIds)
                    ->delete();
            }

            // Delete self_study_lessons
            $counts['self_study_lessons'] = DB::table('self_study_lessons')
                ->where('course_auth_id', $courseAuthId)
                ->delete();

            // Reset video quota for this student (create if doesn't exist)
            $userId = $courseAuth->user_id;
            $quotaUpdated = DB::table('student_video_quota')
                ->updateOrInsert(
                    ['user_id' => $userId],
                    [
                        'total_hours' => 10.00,
                        'used_hours' => 0.00,
                        'refunded_hours' => 0.00,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

            DB::commit();

            // Clear cache
            Cache::forget("PCLCache:{$courseAuthId}");
            Cache::forget("StudentUnit:{$courseAuthId}");
            Cache::forget("CourseAuth:{$courseAuthId}");

            return response()->json([
                'success' => true,
                'message' => 'All lesson progress reset',
                'counts' => $counts,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to reset: ' . $e->getMessage()], 500);
        }
    }
}
