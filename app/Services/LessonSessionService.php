<?php

namespace App\Services;

use App\Models\SelfStudyLesson;
use App\Models\StudentLesson;
use App\Models\StudentVideoQuota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * LessonSessionService
 * 
 * Orchestrates the complete lesson session lifecycle including:
 * - Session initialization with quota validation
 * - Pause time calculation and tracking
 * - Progress monitoring and completion
 * - Quota consumption with rounding
 * - Failed lesson recovery with refunds
 * 
 * This is the main service that coordinates PauseTimeCalculator and
 * QuotaRoundingService to implement the complete session management system.
 * 
 * @package App\Services
 */
class LessonSessionService
{
    /**
     * Pause time calculator service
     *
     * @var PauseTimeCalculator
     */
    protected PauseTimeCalculator $pauseCalculator;

    /**
     * Quota rounding service
     *
     * @var QuotaRoundingService
     */
    protected QuotaRoundingService $quotaRounder;

    /**
     * Create a new LessonSessionService instance
     *
     * @param PauseTimeCalculator|null $pauseCalculator Optional custom pause calculator
     * @param QuotaRoundingService|null $quotaRounder Optional custom quota rounder
     */
    public function __construct(
        ?PauseTimeCalculator $pauseCalculator = null,
        ?QuotaRoundingService $quotaRounder = null
    ) {
        $this->pauseCalculator = $pauseCalculator ?? new PauseTimeCalculator();
        $this->quotaRounder = $quotaRounder ?? new QuotaRoundingService();
    }

    /**
     * Start a new lesson session
     * 
     * Creates a SelfStudyLesson record with:
     * - Unique session ID
     * - Calculated pause time allowance
     * - Session expiration timestamp
     * - Initial quota tracking fields
     * 
     * Validates student has sufficient quota before starting.
     *
     * @param User $student The student starting the lesson
     * @param int $courseAuthId The course authorization ID
     * @param int $lessonId The lesson ID
     * @param int $videoDurationSeconds The video duration in seconds
     * @return array{success: bool, session: SelfStudyLesson|null, message: string, error: string|null}
     * 
     * @throws Exception If quota validation fails or database error occurs
     */
    public function startSession(
        User $student,
        int $courseAuthId,
        int $lessonId,
        int $videoDurationSeconds
    ): array {
        try {
            DB::beginTransaction();

            // Check if student already has an active session for this lesson
            $existingSession = SelfStudyLesson::where('course_auth_id', $courseAuthId)
                ->where('lesson_id', $lessonId)
                ->whereNotNull('agreed_at')
                ->whereNull('completed_at')
                ->whereNull('dnc_at')
                ->where('session_expires_at', '>', now())
                ->first();

            if ($existingSession) {
                DB::rollBack();
                return [
                    'success' => false,
                    'session' => $existingSession,
                    'message' => 'You already have an active session for this lesson',
                    'error' => 'SESSION_ALREADY_ACTIVE',
                ];
            }

            // Get or create student quota record
            $quota = StudentVideoQuota::firstOrCreate(
                ['user_id' => $student->id],
                ['total_hours' => 10.00, 'used_hours' => 0.00, 'refunded_hours' => 0.00]
            );

            // Calculate pause time allowance
            $pauseData = $this->pauseCalculator->calculate($videoDurationSeconds);
            
            // Calculate expected session duration (video + buffer + pause time)
            $bufferMinutes = config('self_study.session_buffer_minutes', 15);
            $videoDurationMinutes = ceil($videoDurationSeconds / 60);
            $sessionDurationMinutes = $videoDurationMinutes + $bufferMinutes + $pauseData['total_minutes'];

            // Validate sufficient quota
            if (!$quota->hasEnoughQuota($sessionDurationMinutes)) {
                DB::rollBack();
                
                $remainingMinutes = $quota->getRemainingMinutes();
                $requiredMinutes = $sessionDurationMinutes;
                
                return [
                    'success' => false,
                    'session' => null,
                    'message' => "Insufficient quota. Required: {$requiredMinutes} minutes, Available: {$remainingMinutes} minutes",
                    'error' => 'INSUFFICIENT_QUOTA',
                ];
            }

            // Calculate session expiration
            $expiresAt = now()->addMinutes($sessionDurationMinutes)
                ->addMinutes(config('self_study.session_expiration.grace_period_minutes', 5));

            // Create session record
            $session = SelfStudyLesson::create([
                'course_auth_id' => $courseAuthId,
                'lesson_id' => $lessonId,
                'agreed_at' => now(),
                'session_id' => (string) Str::uuid(),
                'session_duration_minutes' => $sessionDurationMinutes,
                'session_expires_at' => $expiresAt,
                'total_pause_minutes_allowed' => $pauseData['total_minutes'],
                'total_pause_minutes_used' => 0,
                'pause_intervals' => $pauseData['intervals'],
                'video_duration_seconds' => $videoDurationSeconds,
                'playback_progress_seconds' => 0,
                'completion_percentage' => 0.00,
                'quota_consumed_minutes' => 0,
                'quota_status' => 'active',
                'is_redo' => false,
            ]);

            DB::commit();

            Log::info('Lesson session started', [
                'student_id' => $student->id,
                'course_auth_id' => $courseAuthId,
                'lesson_id' => $lessonId,
                'session_id' => $session->session_id,
                'session_duration' => $sessionDurationMinutes,
                'pause_allowed' => $pauseData['total_minutes'],
                'expires_at' => $expiresAt->toDateTimeString(),
            ]);

            return [
                'success' => true,
                'session' => $session,
                'message' => 'Session started successfully',
                'error' => null,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to start lesson session', [
                'student_id' => $student->id,
                'course_auth_id' => $courseAuthId,
                'lesson_id' => $lessonId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'session' => null,
                'message' => 'Failed to start session: ' . $e->getMessage(),
                'error' => 'SESSION_START_FAILED',
            ];
        }
    }

    /**
     * Complete a lesson session
     * 
     * Marks session as completed, rounds and consumes quota,
     * checks completion threshold, and updates student progress.
     *
     * @param SelfStudyLesson $session The session to complete
     * @param User $student The student completing the lesson
     * @return array{success: bool, passed: bool, quota_consumed: int, message: string}
     */
    public function completeSession(SelfStudyLesson $session, User $student): array
    {
        try {
            DB::beginTransaction();

            // Calculate actual time consumed (video watched + pause time used)
            $videoDurationMinutes = ceil($session->video_duration_seconds / 60);
            $pauseUsedMinutes = $session->total_pause_minutes_used;
            $actualMinutesUsed = $videoDurationMinutes + $pauseUsedMinutes;

            // Round quota consumption to standard increments
            $roundedQuotaMinutes = $this->quotaRounder->roundUp($actualMinutesUsed);

            // Check if student met completion threshold
            $passed = $session->meetsCompletionThreshold();

            // Update session record
            $session->update([
                'completed_at' => now(),
                'quota_consumed_minutes' => $roundedQuotaMinutes,
                'quota_status' => $passed ? 'completed' : 'failed',
            ]);

            // Consume quota from student
            $quota = StudentVideoQuota::where('user_id', $student->id)->first();
            if ($quota) {
                $quota->consumeQuota($roundedQuotaMinutes);
            }

            DB::commit();

            Log::info('Lesson session completed', [
                'student_id' => $student->id,
                'session_id' => $session->session_id,
                'passed' => $passed,
                'actual_minutes' => $actualMinutesUsed,
                'rounded_minutes' => $roundedQuotaMinutes,
                'completion_percentage' => $session->completion_percentage,
            ]);

            return [
                'success' => true,
                'passed' => $passed,
                'quota_consumed' => $roundedQuotaMinutes,
                'message' => $passed 
                    ? 'Lesson completed successfully'
                    : 'Lesson incomplete - did not meet 80% threshold',
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to complete lesson session', [
                'student_id' => $student->id,
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'passed' => false,
                'quota_consumed' => 0,
                'message' => 'Failed to complete session: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle online lesson pass for failed self-study lesson
     * 
     * When a student passes a lesson in live classroom after failing
     * in self-study, this method:
     * - Marks the failed session with redo_passed = true
     * - Refunds the consumed quota back to the student
     * - Creates audit trail of the recovery
     *
     * @param SelfStudyLesson $failedSession The failed self-study session
     * @param StudentLesson $passedLesson The passed online lesson
     * @param User $student The student who passed
     * @return array{success: bool, refunded_minutes: int, message: string}
     */
    public function handleOnlineLessonPass(
        SelfStudyLesson $failedSession,
        StudentLesson $passedLesson,
        User $student
    ): array {
        try {
            DB::beginTransaction();

            // Validate the failed session is eligible for refund
            if ($failedSession->redo_passed) {
                DB::rollBack();
                return [
                    'success' => false,
                    'refunded_minutes' => 0,
                    'message' => 'This session has already been recovered',
                ];
            }

            if ($failedSession->quota_status !== 'failed') {
                DB::rollBack();
                return [
                    'success' => false,
                    'refunded_minutes' => 0,
                    'message' => 'Only failed sessions can be recovered',
                ];
            }

            // Mark the failed session as recovered
            $failedSession->update([
                'redo_passed' => true,
                'quota_status' => 'refunded',
            ]);

            // Refund the quota
            $refundMinutes = $failedSession->quota_consumed_minutes;
            $quota = StudentVideoQuota::where('user_id', $student->id)->first();
            
            if ($quota) {
                $quota->refundQuota($refundMinutes);
            }

            DB::commit();

            Log::info('Quota refunded for online lesson pass', [
                'student_id' => $student->id,
                'failed_session_id' => $failedSession->session_id,
                'passed_lesson_id' => $passedLesson->id,
                'refunded_minutes' => $refundMinutes,
            ]);

            return [
                'success' => true,
                'refunded_minutes' => $refundMinutes,
                'message' => "Quota refunded: {$refundMinutes} minutes returned to your account",
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to handle online lesson pass', [
                'student_id' => $student->id,
                'failed_session_id' => $failedSession->session_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'refunded_minutes' => 0,
                'message' => 'Failed to process refund: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get session summary for display
     *
     * @param SelfStudyLesson $session The session to summarize
     * @return array Session summary data
     */
    public function getSessionSummary(SelfStudyLesson $session): array
    {
        $videoDurationMinutes = ceil($session->video_duration_seconds / 60);
        
        return [
            'session_id' => $session->session_id,
            'status' => $session->getStatus(),
            'is_active' => $session->isSessionActive(),
            'is_expired' => $session->isSessionExpired(),
            'time_remaining_minutes' => $session->getSessionTimeRemaining(),
            'expires_at' => $session->session_expires_at?->toDateTimeString(),
            'video_duration_minutes' => $videoDurationMinutes,
            'completion_percentage' => $session->completion_percentage,
            'pause_time' => [
                'allowed_minutes' => $session->total_pause_minutes_allowed,
                'used_minutes' => $session->total_pause_minutes_used,
                'remaining_minutes' => $session->getRemainingPauseMinutes(),
                'intervals' => $session->pause_intervals,
            ],
            'quota' => [
                'consumed_minutes' => $session->quota_consumed_minutes,
                'status' => $session->quota_status,
            ],
            'recovery' => [
                'is_redo' => $session->is_redo,
                'redo_passed' => $session->redo_passed,
            ],
        ];
    }
}
