<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use App\Services\ClassroomDashboardService;

/**
 * ClassroomController - Handles classroom polling and real-time data
 *
 * Responsibilities:
 * - /classroom/classroom/poll - Poll endpoint for classroom data ONLY
 * - Real-time status updates
 * - Lesson progress tracking
 * - Classroom session management
 *
 * NOTE: Student polling is handled by StudentDashboardController
 */
class ClassroomController extends Controller
{
    private $studentDashboardService;
    private $classroomDashboardService;

    public function __construct(
        StudentDashboardService $studentDashboardService,
        ClassroomDashboardService $classroomDashboardService
    ) {
        $this->middleware('auth');
        $this->studentDashboardService = $studentDashboardService;
        $this->classroomDashboardService = $classroomDashboardService;
    }

    /**
     * Classroom Polling Endpoint
     *
     * Returns all classroom-related data for the classroom context
     * Includes: course structure, lessons, instructor info, config, etc.
     * Called every 5 seconds by React polling
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function classroomPoll(Request $request): JsonResponse
    {
        try {
            $courseDateId = $request->query('course_date_id');

            if (!$courseDateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'course_date_id is required',
                    'error_code' => 'MISSING_COURSE_DATE_ID',
                ], 400);
            }

            // Get classroom data from service
            $classroomData = $this->classroomDashboardService->getClassroomPollData($courseDateId);

            return response()->json([
                'success' => true,
                'data' => $classroomData,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in classroomPoll:', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classroom data',
                'error_code' => 'CLASSROOM_POLL_ERROR',
            ], 500);
        }
    }

    /**
     * Get Classroom Status
     *
     * Quick endpoint to check if classroom is active
     * Used for class detection on student dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function classroomStatus(Request $request): JsonResponse
    {
        try {
            $courseAuthId = $request->query('course_auth_id');

            if (!$courseAuthId) {
                return response()->json([
                    'success' => false,
                    'message' => 'course_auth_id is required',
                ], 400);
            }

            $status = $this->classroomDashboardService->getClassroomStatus($courseAuthId);

            return response()->json([
                'success' => true,
                'data' => $status,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in classroomStatus:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classroom status',
            ], 500);
        }
    }

    /**
     * Record Student Entry into Classroom
     *
     * Called when student clicks "Enter Class"
     * Marks attendance and creates/updates StudentUnit
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function enterClassroom(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer',
                'course_date_id' => 'required|integer',
            ]);

            $result = $this->classroomDashboardService->recordStudentEntry(
                $validated['course_auth_id'],
                $validated['course_date_id']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Successfully entered classroom',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in enterClassroom:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to enter classroom',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record Student Exit from Classroom
     *
     * Called when student leaves or class ends
     * Marks final attendance/exit time
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exitClassroom(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer',
                'course_date_id' => 'required|integer',
            ]);

            $result = $this->classroomDashboardService->recordStudentExit(
                $validated['course_auth_id'],
                $validated['course_date_id']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Successfully exited classroom',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in exitClassroom:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to exit classroom',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record Lesson Start
     *
     * Called when student starts watching a lesson
     * Creates StudentLesson record
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startLesson(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_unit_id' => 'required|integer',
                'lesson_id' => 'required|integer',
            ]);

            $result = $this->classroomDashboardService->recordLessonStart(
                $validated['student_unit_id'],
                $validated['lesson_id']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Lesson started',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in startLesson:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start lesson',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record Lesson Completion
     *
     * Called when student completes a lesson
     * Updates StudentLesson with completion time
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function completeLesson(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_unit_id' => 'required|integer',
                'lesson_id' => 'required|integer',
            ]);

            $result = $this->classroomDashboardService->recordLessonCompletion(
                $validated['student_unit_id'],
                $validated['lesson_id']
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Lesson completed',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in completeLesson:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete lesson',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Lesson Progress
     *
     * Returns student's progress through lessons in current classroom session
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function lessonProgress(Request $request): JsonResponse
    {
        try {
            $studentUnitId = $request->query('student_unit_id');

            if (!$studentUnitId) {
                return response()->json([
                    'success' => false,
                    'message' => 'student_unit_id is required',
                ], 400);
            }

            $progress = $this->classroomDashboardService->getLessonProgress($studentUnitId);

            return response()->json([
                'success' => true,
                'data' => $progress,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in lessonProgress:', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lesson progress',
            ], 500);
        }
    }
}
