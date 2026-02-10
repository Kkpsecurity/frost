<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\SelfStudyLesson;
use App\Models\StudentVideoQuota;
use App\Services\LessonSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * StudentLessonSessionController
 *
 * Handles lesson session management for self-study students:
 * - Start session with quota validation
 * - Update playback progress
 * - Track pause time usage
 * - Complete session with quota consumption
 * - Get session status for resuming
 */
class StudentLessonSessionController extends Controller
{
    protected LessonSessionService $sessionService;

    public function __construct(LessonSessionService $sessionService)
    {
        $this->middleware(['auth', 'verified']);
        $this->sessionService = $sessionService;
    }

    /**
     * Start a new lesson session
     *
     * POST /classroom/lesson/start-session
     *
     * Validates:
     * - Student has sufficient quota
     * - No duplicate active session
     * - Lesson exists and belongs to student's course
     *
     * Creates:
     * - Session ID and expiration time
     * - Pause time allowance
     * - Session tracking record
     */
    public function startSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id',
                'course_auth_id' => 'required|integer|exists:course_auths,id',
                'video_duration_seconds' => 'required|integer|min:1',
                'lesson_title' => 'required|string|max:255',
            ]);

            $studentId = Auth::id();
            $lessonId = $validated['lesson_id'];
            $courseAuthId = $validated['course_auth_id'];
            $videoDurationSeconds = $validated['video_duration_seconds'];

            Log::info('Starting lesson session', [
                'student_id' => $studentId,
                'lesson_id' => $lessonId,
                'course_auth_id' => $courseAuthId,
                'video_duration_seconds' => $videoDurationSeconds,
                // Avoid assuming the auth user object implements toArray()
                'auth_user_id' => $studentId,
            ]);

            // Check for duplicate active session
            $activeSession = SelfStudyLesson::where('course_auth_id', $courseAuthId)
                ->where('session_id', '!=', null)
                ->where('session_expires_at', '>', now())
                ->where('quota_status', '!=', 'consumed')
                ->first();

            if ($activeSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'You already have an active lesson session. Please complete or end the current session first.',
                    'active_session' => [
                        'lesson_id' => $activeSession->lesson_id,
                        'session_id' => $activeSession->session_id,
                        'expires_at' => $activeSession->session_expires_at->toISOString(),
                    ]
                ], 409);
            }

            // Check quota availability
            // Create quota record if missing (matches LessonSessionService behavior)
            $quota = StudentVideoQuota::firstOrCreate(
                ['user_id' => $studentId],
                ['total_hours' => 10.00, 'used_hours' => 0.00, 'refunded_hours' => 0.00]
            );

            // IMPORTANT: quota must cover the full session duration, not just video length.
            // Session duration includes:
            // - video minutes
            // - buffer minutes
            // - pause allocation minutes
            $videoDurationMinutes = (int) ceil($videoDurationSeconds / 60);
            $bufferMinutes = (int) config('self_study.session_buffer_minutes', 15);

            // Mirror pause allocation calculation used by LessonSessionService
            // (LessonSessionService uses PauseTimeCalculator)
            $pauseData = app(\App\Services\PauseTimeCalculator::class)
                ->calculate($videoDurationSeconds);

            $pauseMinutes = (int) ($pauseData['total_minutes'] ?? 0);
            $requiredMinutes = $videoDurationMinutes + $bufferMinutes + $pauseMinutes;

            if (!$quota->hasEnoughQuota($requiredMinutes)) {
                return response()->json([
                    'success' => false,
                    'error' => sprintf(
                        'Insufficient video quota. Required: %d minutes, Available: %d minutes',
                        $requiredMinutes,
                        $quota->getRemainingMinutes()
                    ),
                    'quota' => [
                        'remaining_minutes' => $quota->getRemainingMinutes(),
                        'required_minutes' => $requiredMinutes,
                        'video_minutes' => $videoDurationMinutes,
                        'buffer_minutes' => $bufferMinutes,
                        'pause_minutes' => $pauseMinutes,
                    ]
                ], 403);
            }

            // Start session using service
            $result = $this->sessionService->startSession(
                student: Auth::user(),
                courseAuthId: $courseAuthId,
                lessonId: $lessonId,
                videoDurationSeconds: $videoDurationSeconds
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['message'],
                ], 400);
            }

            $selfStudyLesson = $result['session'];

            Log::info('Lesson session started successfully', [
                'student_id' => $studentId,
                'lesson_id' => $lessonId,
                'session_id' => $selfStudyLesson->session_id,
                'expires_at' => $selfStudyLesson->session_expires_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson session started successfully',
                'session' => [
                    'isActive' => true,
                    'sessionId' => $selfStudyLesson->session_id,
                    'lessonId' => $lessonId,
                    'lessonTitle' => $validated['lesson_title'],
                    'courseAuthId' => $courseAuthId,
                    'startedAt' => $selfStudyLesson->created_at->toISOString(),
                    'expiresAt' => $selfStudyLesson->session_expires_at->toISOString(),
                    'videoDurationSeconds' => $videoDurationSeconds,
                    'totalPauseAllowed' => $selfStudyLesson->total_pause_minutes_allowed,
                    'pauseUsed' => 0,
                    'completionPercentage' => 0,
                    'playbackProgressSeconds' => 0,
                ],
                'quota' => [
                    'remaining_minutes' => $quota->getRemainingMinutes(),
                    'total_minutes' => $quota->total_hours * 60,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to start lesson session', [
                'student_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to start session. Please try again or contact support.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update playback progress
     *
     * POST /classroom/lesson/update-progress
     *
     * Updates:
     * - playback_progress_seconds
     * - completion_percentage
     */
    public function updateProgress(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string|size:36',
                'playback_seconds' => 'required|integer|min:0',
                'completion_percentage' => 'required|numeric|min:0|max:100',
            ]);

            $studentId = Auth::id();
            $sessionId = $validated['session_id'];

            // Find the session by session_id (UUID is globally unique)
            $selfStudyLesson = SelfStudyLesson::where('session_id', $sessionId)
                ->first();

            if (!$selfStudyLesson) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session not found or expired',
                ], 404);
            }

            // Check if session is expired
            if ($selfStudyLesson->isSessionExpired()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session has expired. Please start a new session.',
                    'expired' => true,
                ], 410);
            }

            // Update progress
            $selfStudyLesson->updateProgress(
                $validated['playback_seconds']
            );

            Log::debug('Progress updated', [
                'student_id' => $studentId,
                'session_id' => $sessionId,
                'playback_seconds' => $validated['playback_seconds'],
                'completion_percentage' => $validated['completion_percentage'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Progress updated',
                'progress' => [
                    'playback_seconds' => $selfStudyLesson->playback_progress_seconds,
                    'completion_percentage' => $selfStudyLesson->completion_percentage,
                    'meets_threshold' => $selfStudyLesson->meetsCompletionThreshold(),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update progress', [
                'student_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update progress',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Track pause time usage
     *
     * POST /classroom/lesson/track-pause
     *
     * Updates:
     * - total_pause_minutes_used
     * - pause_intervals (JSON array)
     */
    public function trackPause(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string|size:36',
                'pause_minutes' => 'required|numeric|min:0',
            ]);

            $studentId = Auth::id();
            $sessionId = $validated['session_id'];
            $pauseMinutes = $validated['pause_minutes'];

            // Find the session by session_id (UUID is globally unique)
            $selfStudyLesson = SelfStudyLesson::where('session_id', $sessionId)
                ->first();

            if (!$selfStudyLesson) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session not found',
                ], 404);
            }

            // Check if session is expired
            if ($selfStudyLesson->isSessionExpired()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session has expired',
                    'expired' => true,
                ], 410);
            }

            // Track pause time
            $selfStudyLesson->consumePauseTime($pauseMinutes);

            $remainingPause = $selfStudyLesson->getRemainingPauseMinutes();

            Log::debug('Pause time tracked', [
                'student_id' => $studentId,
                'session_id' => $sessionId,
                'pause_minutes' => $pauseMinutes,
                'total_used' => $selfStudyLesson->total_pause_minutes_used,
                'remaining' => $remainingPause,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pause time tracked',
                'pause' => [
                    'used_minutes' => $selfStudyLesson->total_pause_minutes_used,
                    'allowed_minutes' => $selfStudyLesson->total_pause_minutes_allowed,
                    'remaining_minutes' => $remainingPause,
                    'percentage_used' => $selfStudyLesson->total_pause_minutes_allowed > 0
                        ? round(($selfStudyLesson->total_pause_minutes_used / $selfStudyLesson->total_pause_minutes_allowed) * 100, 1)
                        : 0,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to track pause time', [
                'student_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to track pause time',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Complete lesson session
     *
     * POST /classroom/lesson/complete-session
     *
     * Finalizes:
     * - Rounds quota consumption
     * - Checks 80% completion threshold
     * - Consumes quota from StudentVideoQuota
     * - Updates quota_status to 'consumed'
     * - Clears session_id and session_expires_at
     */
    public function completeSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string|size:36',
            ]);

            $studentId = Auth::id();
            $sessionId = $validated['session_id'];

            Log::info('Completing lesson session', [
                'student_id' => $studentId,
                'session_id' => $sessionId,
            ]);

            // Find the session by session_id (UUID is globally unique)
            $selfStudyLesson = SelfStudyLesson::where('session_id', $sessionId)
                ->first();

            if (!$selfStudyLesson) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session not found',
                ], 404);
            }

            // Complete session using service - pass both session and student
            $result = $this->sessionService->completeSession($selfStudyLesson, Auth::user());

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['message'] ?? 'Failed to complete session',
                ], 500);
            }

            Log::info('Lesson session completed', [
                'student_id' => $studentId,
                'session_id' => $sessionId,
                'passed' => $result['passed'],
                'quota_consumed' => $result['quota_consumed'],
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'passed' => $result['passed'],
                'completion_percentage' => $selfStudyLesson->completion_percentage,
                'quota_consumed_minutes' => $result['quota_consumed'],
                'threshold_met' => $selfStudyLesson->meetsCompletionThreshold(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to complete lesson session', [
                'student_id' => Auth::id(),
                'session_id' => $request->input('session_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to complete session. Please try again or contact support.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get session status
     *
     * GET /classroom/lesson/session-status/{sessionId}
     *
     * Returns:
     * - Current session state
     * - Progress and pause usage
     * - Time remaining
     *
     * Used for:
     * - Resuming after browser refresh
     * - Checking session validity
     */
    public function getSessionStatus(string $sessionId)
    {
        try {
            $studentId = Auth::id();

            // Find the session by session_id (UUID is globally unique)
            $selfStudyLesson = SelfStudyLesson::where('session_id', $sessionId)
                ->first();

            if (!$selfStudyLesson) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session not found',
                ], 404);
            }

            // Check if expired
            $isExpired = $selfStudyLesson->isSessionExpired();
            $timeRemaining = $selfStudyLesson->getSessionTimeRemaining();

            return response()->json([
                'success' => true,
                'session' => [
                    'isActive' => !$isExpired && $selfStudyLesson->quota_status !== 'consumed',
                    'sessionId' => $selfStudyLesson->session_id,
                    'lessonId' => $selfStudyLesson->lesson_id,
                    'courseAuthId' => $selfStudyLesson->course_auth_id,
                    'startedAt' => $selfStudyLesson->created_at->toISOString(),
                    'expiresAt' => $selfStudyLesson->session_expires_at?->toISOString(),
                    'isExpired' => $isExpired,
                    'timeRemaining' => $timeRemaining,
                    'videoDurationSeconds' => $selfStudyLesson->video_duration_seconds,
                    'playbackProgressSeconds' => $selfStudyLesson->playback_progress_seconds,
                    'completionPercentage' => $selfStudyLesson->completion_percentage,
                    'totalPauseAllowed' => $selfStudyLesson->total_pause_minutes_allowed,
                    'pauseUsed' => $selfStudyLesson->total_pause_minutes_used,
                    'pauseRemaining' => $selfStudyLesson->getRemainingPauseMinutes(),
                    'quotaStatus' => $selfStudyLesson->quota_status,
                    'quotaConsumed' => $selfStudyLesson->quota_consumed_minutes,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get session status', [
                'student_id' => Auth::id(),
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve session status',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
