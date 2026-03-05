<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\SelfStudyLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * StudentLessonSessionController
 *
 * Manages lesson session lifecycle for self-study students.
 *
 * Session state is stored in the Laravel cache (keyed by UUID) rather than
 * in DB columns that have not yet been added to self_study_lessons.
 * Only the columns present in the base migration are written to the DB:
 *   course_auth_id, lesson_id, agreed_at, completed_at, seconds_viewed
 */
class StudentLessonSessionController extends Controller
{
    private const SESSION_TTL_SECONDS = 4 * 60 * 60;
    private const KEY_PREFIX = 'lesson_session_';

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    private function cacheKey(string $sessionId): string
    {
        return self::KEY_PREFIX . $sessionId;
    }

    private function getSessionCache(string $sessionId): ?array
    {
        return Cache::get($this->cacheKey($sessionId));
    }

    private function putSessionCache(string $sessionId, array $data): void
    {
        Cache::put($this->cacheKey($sessionId), $data, self::SESSION_TTL_SECONDS);
    }

    private function forgetSessionCache(string $sessionId): void
    {
        Cache::forget($this->cacheKey($sessionId));
    }

    public function startSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'lesson_id'              => 'required|integer|exists:lessons,id',
                'course_auth_id'         => 'required|integer|exists:course_auths,id',
                'video_duration_seconds' => 'required|integer|min:1',
                'lesson_title'           => 'required|string|max:255',
            ]);

            $studentId            = Auth::id();
            $lessonId             = (int) $validated['lesson_id'];
            $courseAuthId         = (int) $validated['course_auth_id'];
            $videoDurationSeconds = (int) $validated['video_duration_seconds'];

            // Create the SelfStudyLesson using only columns that exist in the DB.
            $record = SelfStudyLesson::create([
                'course_auth_id' => $courseAuthId,
                'lesson_id'      => $lessonId,
                'agreed_at'      => now(),
            ]);

            $sessionId   = (string) Str::uuid();
            $expiresAt   = now()->addSeconds(self::SESSION_TTL_SECONDS);
            $sessionData = [
                'id'                        => $record->id,
                'lesson_id'                 => $lessonId,
                'course_auth_id'            => $courseAuthId,
                'student_id'                => $studentId,
                'started_at'                => now()->toISOString(),
                'expires_at'                => $expiresAt->toISOString(),
                'video_duration_seconds'    => $videoDurationSeconds,
                'playback_progress_seconds' => 0,
                'completion_percentage'     => 0.0,
                'total_pause_allowed'       => 0,
                'pause_used'                => 0,
                'is_completed'              => false,
            ];

            $this->putSessionCache($sessionId, $sessionData);

            Log::info('Lesson session started', [
                'student_id'     => $studentId,
                'lesson_id'      => $lessonId,
                'course_auth_id' => $courseAuthId,
                'record_id'      => $record->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson session started successfully',
                'session' => [
                    'isActive'                => true,
                    'sessionId'               => $sessionId,
                    'lessonId'                => $lessonId,
                    'lessonTitle'             => $validated['lesson_title'],
                    'courseAuthId'            => $courseAuthId,
                    'startedAt'               => $sessionData['started_at'],
                    'expiresAt'               => $sessionData['expires_at'],
                    'videoDurationSeconds'    => $videoDurationSeconds,
                    'totalPauseAllowed'       => 0,
                    'pauseUsed'               => 0,
                    'completionPercentage'    => 0,
                    'playbackProgressSeconds' => 0,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to start lesson session', ['student_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to start session. Please try again or contact support.', 'debug' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function updateProgress(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id'            => 'required|string',
                'playback_seconds'      => 'required|integer|min:0',
                'completion_percentage' => 'required|numeric|min:0|max:100',
            ]);

            $sessionId = $validated['session_id'];
            $data      = $this->getSessionCache($sessionId);

            if (!$data) {
                return response()->json(['success' => false, 'error' => 'Session not found or expired'], 404);
            }

            $data['playback_progress_seconds'] = (int) $validated['playback_seconds'];
            $data['completion_percentage']      = (float) $validated['completion_percentage'];
            $this->putSessionCache($sessionId, $data);

            SelfStudyLesson::where('id', $data['id'])
                ->update(['seconds_viewed' => (int) $validated['playback_seconds']]);

            return response()->json([
                'success'  => true,
                'progress' => [
                    'playback_seconds'      => $data['playback_progress_seconds'],
                    'completion_percentage' => $data['completion_percentage'],
                    'meets_threshold'       => $data['completion_percentage'] >= 80,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to update progress', ['student_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to update progress', 'debug' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function trackPause(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id'    => 'required|string',
                'pause_minutes' => 'required|numeric|min:0',
            ]);

            $sessionId = $validated['session_id'];
            $data      = $this->getSessionCache($sessionId);

            if (!$data) {
                return response()->json(['success' => false, 'error' => 'Session not found'], 404);
            }

            $data['pause_used'] = (float) $data['pause_used'] + (float) $validated['pause_minutes'];
            $this->putSessionCache($sessionId, $data);

            return response()->json([
                'success' => true,
                'pause'   => [
                    'used_minutes'      => $data['pause_used'],
                    'allowed_minutes'   => $data['total_pause_allowed'],
                    'remaining_minutes' => max(0, $data['total_pause_allowed'] - $data['pause_used']),
                    'percentage_used'   => 0,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => 'Failed to track pause', 'debug' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function completeSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
            ]);

            $sessionId = $validated['session_id'];
            $data      = $this->getSessionCache($sessionId);

            if (!$data) {
                return response()->json(['success' => false, 'error' => 'Session not found'], 404);
            }

            $passed = (float) $data['completion_percentage'] >= 80;

            SelfStudyLesson::where('id', $data['id'])->update([
                'completed_at'   => $passed ? now() : null,
                'seconds_viewed' => (int) $data['playback_progress_seconds'],
            ]);

            $this->forgetSessionCache($sessionId);

            Log::info('Lesson session completed', [
                'student_id' => Auth::id(),
                'record_id'  => $data['id'],
                'passed'     => $passed,
            ]);

            return response()->json([
                'success'               => true,
                'message'               => $passed ? 'Lesson completed successfully.' : 'Session ended (threshold not met).',
                'passed'                => $passed,
                'completion_percentage' => $data['completion_percentage'],
                'quota_consumed_minutes'=> 0,
                'threshold_met'         => $passed,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Failed to complete lesson session', ['student_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to complete session. Please try again or contact support.', 'debug' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function getSessionStatus(string $sessionId)
    {
        try {
            $data = $this->getSessionCache($sessionId);

            if (!$data) {
                return response()->json(['success' => false, 'error' => 'Session not found'], 404);
            }

            $isExpired = now()->toISOString() >= $data['expires_at'];

            return response()->json([
                'success' => true,
                'session' => [
                    'isActive'                => !$isExpired && !$data['is_completed'],
                    'sessionId'               => $sessionId,
                    'lessonId'                => $data['lesson_id'],
                    'courseAuthId'            => $data['course_auth_id'],
                    'startedAt'               => $data['started_at'],
                    'expiresAt'               => $data['expires_at'],
                    'isExpired'               => $isExpired,
                    'timeRemaining'           => null,
                    'videoDurationSeconds'    => $data['video_duration_seconds'],
                    'playbackProgressSeconds' => $data['playback_progress_seconds'],
                    'completionPercentage'    => $data['completion_percentage'],
                    'totalPauseAllowed'       => $data['total_pause_allowed'],
                    'pauseUsed'               => $data['pause_used'],
                    'pauseRemaining'          => max(0, $data['total_pause_allowed'] - $data['pause_used']),
                    'quotaStatus'             => $data['is_completed'] ? 'consumed' : 'pending',
                    'quotaConsumed'           => 0,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get session status', ['student_id' => Auth::id(), 'session_id' => $sessionId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to retrieve session status', 'debug' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }
}