<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Student Offline Session Controller
 *
 * Handles offline study session tracking and management
 */
class OfflineSessionController extends Controller
{
    /**
     * Start a new offline study session
     */
    public function startSession(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'timezone' => 'sometimes|string',
                'screen_resolution' => 'sometimes|string',
                'browser_info' => 'sometimes|array',
                'study_plan' => 'sometimes|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session data',
                    'errors' => $validator->errors()
                ], 400);
            }

            $tracking = new StudentTracking(auth()->id(), $courseAuthId);

            // Start offline session with provided data
            $session = $tracking->startOfflineSession($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Offline study session started successfully',
                'data' => [
                    'session_id' => $session->id,
                    'session_type' => 'offline',
                    'started_at' => $session->started_at->toISOString(),
                    'course_auth_id' => $courseAuthId,
                    'status' => 'active'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to start offline session', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start offline session',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * End current offline session
     */
    public function endSession(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $tracking = new StudentTracking(auth()->id(), $courseAuthId);

            $session = $tracking->endOfflineSession();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active offline session found to end'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Offline study session ended successfully',
                'data' => [
                    'session_id' => $session->id,
                    'duration_minutes' => $session->duration_minutes,
                    'activities_count' => $session->activities_count,
                    'lessons_accessed' => count($session->lessons_accessed ?? []),
                    'completion_rate' => $session->completion_rate,
                    'ended_at' => $session->ended_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to end offline session', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to end offline session',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Track offline lesson activity
     */
    public function trackLessonActivity(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'activity_type' => 'required|string|in:offline_lesson_start,offline_lesson_complete,offline_lesson_pause,offline_lesson_resume',
                'lesson_id' => 'required|integer',
                'lesson_data' => 'sometimes|array',
                'lesson_data.title' => 'sometimes|string',
                'lesson_data.duration' => 'sometimes|integer',
                'lesson_data.progress' => 'sometimes|numeric|min:0|max:100',
                'lesson_data.time_spent' => 'sometimes|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid lesson activity data',
                    'errors' => $validator->errors()
                ], 400);
            }

            $validated = $validator->validated();
            $tracking = new StudentTracking(auth()->id(), $courseAuthId);

            // Track the lesson activity
            $activity = $tracking->trackOfflineLessonActivity(
                $validated['activity_type'],
                $validated['lesson_id'],
                $validated['lesson_data'] ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Lesson activity tracked successfully',
                'data' => [
                    'activity_id' => $activity->id,
                    'activity_type' => $activity->activity_type,
                    'lesson_id' => $validated['lesson_id'],
                    'session_id' => $activity->data['session_id'] ?? null,
                    'timestamp' => $activity->created_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track lesson activity', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track lesson activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Track offline session step
     */
    public function trackSessionStep(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'step_key' => 'required|string|in:view_lessons,begin_session,student_agreement,student_rules,student_verification,active_session,completed_session',
                'step_label' => 'required|string',
                'step_data' => 'sometimes|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session step data',
                    'errors' => $validator->errors()
                ], 400);
            }

            $validated = $validator->validated();
            $tracking = new StudentTracking(auth()->id(), $courseAuthId);

            // Track the session step
            $activity = $tracking->trackOfflineSessionStep(
                $validated['step_key'],
                $validated['step_label'],
                $validated['step_data'] ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Session step tracked successfully',
                'data' => [
                    'activity_id' => $activity->id,
                    'activity_type' => $activity->activity_type,
                    'step_key' => $validated['step_key'],
                    'step_label' => $validated['step_label'],
                    'session_id' => $activity->data['session_id'] ?? null,
                    'timestamp' => $activity->created_at->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track session step', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'step_key' => $request->get('step_key'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track session step',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get current session status
     */
    public function getSessionStatus(int $courseAuthId): JsonResponse
    {
        try {
            $tracking = new StudentTracking(auth()->id(), $courseAuthId);
            $status = $tracking->getSessionStatus();

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get session status', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get session status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get offline session summary
     */
    public function getSessionSummary(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date|after_or_equal:date_from',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date parameters',
                    'errors' => $validator->errors()
                ], 400);
            }

            $dateFrom = $request->get('date_from', now()->subDays(7)->toDateString());
            $dateTo = $request->get('date_to', now()->toDateString());

            $tracking = new StudentTracking(auth()->id(), $courseAuthId);
            $summary = $tracking->getOfflineSessionSummary($dateFrom, $dateTo);

            return response()->json([
                'success' => true,
                'data' => array_merge($summary, [
                    'date_range' => [
                        'from' => $dateFrom,
                        'to' => $dateTo
                    ]
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get session summary', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get session summary',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get recent offline activities
     */
    public function getRecentActivities(Request $request, int $courseAuthId): JsonResponse
    {
        try {
            $limit = $request->get('limit', 20);

            if ($limit > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Limit cannot exceed 100'
                ], 400);
            }

            $tracking = new StudentTracking(auth()->id(), $courseAuthId);
            $activities = $tracking->getRecentOfflineActivities($limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'activities' => $activities,
                    'count' => count($activities)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get recent activities', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent activities',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Force end all sessions (admin/cleanup endpoint)
     */
    public function forceEndSessions(int $courseAuthId): JsonResponse
    {
        try {
            $tracking = new StudentTracking(auth()->id(), $courseAuthId);
            $count = $tracking->forceEndAllSessions();

            return response()->json([
                'success' => true,
                'message' => "Force ended {$count} sessions",
                'data' => [
                    'sessions_ended' => $count
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to force end sessions', [
                'user_id' => auth()->id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to force end sessions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
