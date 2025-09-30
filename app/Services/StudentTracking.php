<?php

namespace App\Services;

use App\Models\StudentActivity;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * StudentTracking Service
 *
 * Handles comprehensive student activity and session tracking
 * with focus on offline session management and activity logging
 */
class StudentTracking
{
    protected int $userId;
    protected int $courseAuthId;
    protected ?StudentSession $currentSession = null;

    public function __construct(int $userId, int $courseAuthId)
    {
        $this->userId = $userId;
        $this->courseAuthId = $courseAuthId;
    }

    // =============================================================================
    // SCHOOL/CLASSROOM ENTRY TRACKING
    // =============================================================================

    /**
     * Track initial school/classroom entry - first event when student arrives
     * Works for both online and offline scenarios, before joining any specific course
     */
    public function trackSchoolEntry(array $entryData = []): StudentActivity
    {
        try {
            // Log the school entry activity
            $activity = $this->logActivity(
                'school_entry',
                'entry',
                array_merge([
                    'entry_type' => $entryData['entry_type'] ?? 'online', // online or offline
                    'entry_method' => $entryData['entry_method'] ?? 'web', // web, mobile, etc
                    'location' => $entryData['location'] ?? null, // physical location if offline
                    'device_info' => [
                        'user_agent' => request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'screen_resolution' => $entryData['screen_resolution'] ?? null,
                        'timezone' => $entryData['timezone'] ?? null,
                        'platform' => $entryData['platform'] ?? null,
                    ],
                    'session_intent' => $entryData['session_intent'] ?? 'study', // study, assignment, exam, etc
                    'previous_session_id' => $entryData['previous_session_id'] ?? null,
                ], $entryData)
            );

            Log::info("School entry tracked", [
                'user_id' => $this->userId,
                'activity_id' => $activity->id,
                'entry_type' => $entryData['entry_type'] ?? 'online',
                'entry_method' => $entryData['entry_method'] ?? 'web'
            ]);

            return $activity;

        } catch (\Exception $e) {
            Log::error("Failed to track school entry", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Track classroom/course entry - when student joins a specific class
     * This happens after school entry, when they select a course
     */
    public function trackClassroomEntry(int $courseDateId = null, array $classData = []): StudentActivity
    {
        try {
            $activity = $this->logActivity(
                'classroom_entry',
                'entry',
                array_merge([
                    'course_date_id' => $courseDateId,
                    'entry_method' => $classData['entry_method'] ?? 'direct', // direct, from_dashboard, etc
                    'previous_activity' => $classData['previous_activity'] ?? null,
                    'preparation_time' => $classData['preparation_time'] ?? null, // time between school entry and class entry
                ], $classData),
                null,
                $courseDateId
            );

            Log::info("Classroom entry tracked", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'course_date_id' => $courseDateId,
                'activity_id' => $activity->id
            ]);

            return $activity;

        } catch (\Exception $e) {
            Log::error("Failed to track classroom entry", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // =============================================================================
    // OFFLINE SESSION TRACKING
    // =============================================================================

    /**
     * Start an offline study session
     * Records session start and initial activity
     */
    public function startOfflineSession(array $sessionData = []): StudentSession
    {
        try {
            DB::beginTransaction();

            // End any existing active session first
            $this->endActiveOfflineSessions();

            // Create new offline session
            $this->currentSession = StudentSession::create([
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'course_date_id' => null, // NULL for offline sessions
                'session_type' => 'offline',
                'started_at' => now(),
                'ip_address' => request()->ip(),
            ]);

            // Log session start activity
            $this->logActivity(
                'offline_session_start',
                'offline',
                array_merge([
                    'session_id' => $this->currentSession->id,
                    'session_type' => 'offline',
                    'user_agent' => request()->userAgent(),
                    'screen_resolution' => $sessionData['screen_resolution'] ?? null,
                    'timezone' => $sessionData['timezone'] ?? null,
                ], $sessionData)
            );

            DB::commit();

            Log::info("Offline session started", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'session_id' => $this->currentSession->id
            ]);

            return $this->currentSession;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to start offline session", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * End current offline session and calculate metrics
     */
    public function endOfflineSession(?StudentSession $session = null): ?StudentSession
    {
        $session = $session ?? $this->getCurrentOfflineSession();

        if (!$session) {
            Log::warning("No active offline session to end", [
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId
            ]);
            return null;
        }

        try {
            DB::beginTransaction();

            $endTime = now();
            $duration = $endTime->diffInMinutes($session->started_at);

            // Get all activities for this session
            $activities = $this->getSessionActivities($session);

            // Calculate session metrics
            $lessonsAccessed = $activities->whereNotNull('lesson_id')
                                        ->pluck('lesson_id')
                                        ->unique()
                                        ->values()
                                        ->toArray();

            $completionRate = $this->calculateOfflineSessionCompletionRate($activities);

            // Update session with final metrics
            $session->update([
                'ended_at' => $endTime,
                'duration_minutes' => $duration,
                'activities_count' => $activities->count(),
                'lessons_accessed' => $lessonsAccessed,
                'completion_rate' => $completionRate,
            ]);

            // Log session end activity
            $this->logActivity(
                'offline_session_end',
                'offline',
                [
                    'session_id' => $session->id,
                    'duration_minutes' => $duration,
                    'activities_count' => $activities->count(),
                    'lessons_accessed_count' => count($lessonsAccessed),
                    'completion_rate' => $completionRate,
                ],
                null,
                null,
                null
            );

            DB::commit();

            Log::info("Offline session ended", [
                'user_id' => $this->userId,
                'session_id' => $session->id,
                'duration_minutes' => $duration,
                'activities_count' => $activities->count()
            ]);

            $this->currentSession = null;
            return $session;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to end offline session", [
                'session_id' => $session->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Track offline lesson activity
     */
    public function trackOfflineLessonActivity(
        string $activityType,
        int $lessonId,
        array $lessonData = []
    ): StudentActivity {
        // Ensure we have an active offline session
        if (!$this->currentSession) {
            $this->currentSession = $this->getCurrentOfflineSession() ?? $this->startOfflineSession();
        }

        $trackingData = array_merge([
            'session_id' => $this->currentSession->id,
            'lesson_id' => $lessonId,
            'lesson_title' => $lessonData['title'] ?? null,
            'lesson_duration' => $lessonData['duration'] ?? null,
            'progress_percentage' => $lessonData['progress'] ?? null,
        ], $lessonData);

        return $this->logActivity(
            $activityType,
            'offline',
            $trackingData,
            $lessonId
        );
    }

    /**
     * Track offline session step progression
     */
    public function trackOfflineSessionStep(
        string $stepKey,
        string $stepLabel,
        array $stepData = []
    ): StudentActivity {
        // Map step to activity type
        $activityTypeMapping = [
            'view_lessons' => 'offline_lessons_accessed',
            'begin_session' => 'offline_session_start',
            'student_agreement' => 'offline_agreement_accepted',
            'student_rules' => 'offline_rules_acknowledged',
            'student_verification' => 'offline_identity_verified',
            'active_session' => 'offline_study_active',
            'completed_session' => 'offline_session_end',
        ];

        $activityType = $activityTypeMapping[$stepKey] ?? 'offline_step_unknown';

        // Handle session start step
        if ($stepKey === 'begin_session' && !$this->currentSession) {
            $this->startOfflineSession($stepData);
        }

        // Handle session end step
        if ($stepKey === 'completed_session' && $this->currentSession) {
            $this->endOfflineSession();
        }

        $trackingData = array_merge([
            'step_key' => $stepKey,
            'step_label' => $stepLabel,
            'session_id' => $this->currentSession?->id,
            'session_type' => 'offline',
        ], $stepData);

        return $this->logActivity(
            $activityType,
            'offline',
            $trackingData
        );
    }

    // =============================================================================
    // ACTIVITY LOGGING
    // =============================================================================

    /**
     * Log student activity with full context
     */
    public function logActivity(
        string $activityType,
        string $category,
        array $data = [],
        ?int $lessonId = null,
        ?int $courseDateId = null,
        ?int $studentUnitId = null
    ): StudentActivity {
        try {
            $activity = StudentActivity::create([
                'user_id' => $this->userId,
                'course_auth_id' => $this->courseAuthId,
                'course_date_id' => $courseDateId,
                'student_unit_id' => $studentUnitId,
                'lesson_id' => $lessonId,
                'activity_type' => $activityType,
                'category' => $category,
                'data' => array_merge([
                    'timestamp' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ], $data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::debug("Activity logged", [
                'activity_id' => $activity->id,
                'user_id' => $this->userId,
                'activity_type' => $activityType,
                'category' => $category
            ]);

            return $activity;

        } catch (\Exception $e) {
            Log::error("Failed to log activity", [
                'user_id' => $this->userId,
                'activity_type' => $activityType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // =============================================================================
    // SESSION MANAGEMENT
    // =============================================================================

    /**
     * Get current active offline session
     */
    public function getCurrentOfflineSession(): ?StudentSession
    {
        if ($this->currentSession) {
            return $this->currentSession;
        }

        $this->currentSession = StudentSession::where('user_id', $this->userId)
            ->where('course_auth_id', $this->courseAuthId)
            ->where('session_type', 'offline')
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        return $this->currentSession;
    }

    /**
     * End any existing active offline sessions
     */
    protected function endActiveOfflineSessions(): void
    {
        $activeSessions = StudentSession::where('user_id', $this->userId)
            ->where('course_auth_id', $this->courseAuthId)
            ->where('session_type', 'offline')
            ->whereNull('ended_at')
            ->get();

        foreach ($activeSessions as $session) {
            $this->endOfflineSession($session);
        }
    }

    /**
     * Get all activities for a specific session
     */
    protected function getSessionActivities(StudentSession $session)
    {
        return StudentActivity::where('user_id', $session->user_id)
            ->where('course_auth_id', $session->course_auth_id)
            ->whereBetween('created_at', [
                $session->started_at,
                $session->ended_at ?? now()
            ])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Calculate completion rate for offline session
     */
    protected function calculateOfflineSessionCompletionRate($activities): float
    {
        $lessonStarts = $activities->where('activity_type', 'offline_lesson_start')->count();
        $lessonCompletes = $activities->where('activity_type', 'offline_lesson_complete')->count();

        if ($lessonStarts === 0) return 0;

        return min(100, ($lessonCompletes / $lessonStarts) * 100);
    }

    // =============================================================================
    // REPORTING & ANALYTICS
    // =============================================================================

    /**
     * Get offline session summary for date range
     */
    public function getOfflineSessionSummary(string $dateFrom, string $dateTo): array
    {
        $sessions = StudentSession::where('user_id', $this->userId)
            ->where('course_auth_id', $this->courseAuthId)
            ->where('session_type', 'offline')
            ->whereBetween('started_at', [$dateFrom, $dateTo])
            ->get();

        return [
            'total_sessions' => $sessions->count(),
            'total_time_minutes' => $sessions->sum('duration_minutes'),
            'average_session_duration' => $sessions->avg('duration_minutes'),
            'average_completion_rate' => $sessions->avg('completion_rate'),
            'total_lessons_accessed' => $sessions->sum(function($session) {
                return count($session->lessons_accessed ?? []);
            }),
            'sessions_by_date' => $sessions->groupBy(function($session) {
                return $session->started_at->format('Y-m-d');
            })->map(function($daySessions) {
                return [
                    'count' => $daySessions->count(),
                    'total_minutes' => $daySessions->sum('duration_minutes'),
                    'avg_completion' => $daySessions->avg('completion_rate'),
                ];
            }),
        ];
    }

    /**
     * Get recent offline activities
     */
    public function getRecentOfflineActivities(int $limit = 20): array
    {
        return StudentActivity::where('user_id', $this->userId)
            ->where('course_auth_id', $this->courseAuthId)
            ->where('category', 'offline')
            ->with(['lesson'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->activity_type,
                    'lesson' => $activity->lesson?->title,
                    'session_id' => $activity->data['session_id'] ?? null,
                    'timestamp' => $activity->created_at->diffForHumans(),
                    'data' => $activity->data,
                ];
            })
            ->toArray();
    }

    /**
     * Get current session status
     */
    public function getSessionStatus(): array
    {
        $currentSession = $this->getCurrentOfflineSession();

        if (!$currentSession) {
            return [
                'has_active_session' => false,
                'session_type' => null,
                'session_id' => null,
                'duration_minutes' => 0,
                'activities_count' => 0,
            ];
        }

        $activities = $this->getSessionActivities($currentSession);

        return [
            'has_active_session' => true,
            'session_type' => 'offline',
            'session_id' => $currentSession->id,
            'started_at' => $currentSession->started_at->toISOString(),
            'duration_minutes' => $currentSession->started_at->diffInMinutes(now()),
            'activities_count' => $activities->count(),
            'lessons_accessed' => $activities->whereNotNull('lesson_id')->pluck('lesson_id')->unique()->count(),
            'recent_activities' => $activities->take(5)->map(function($activity) {
                return [
                    'type' => $activity->activity_type,
                    'timestamp' => $activity->created_at->diffForHumans(),
                ];
            })->toArray(),
        ];
    }

    // =============================================================================
    // UTILITY METHODS
    // =============================================================================

    /**
     * Check if user has active offline session
     */
    public function hasActiveOfflineSession(): bool
    {
        return $this->getCurrentOfflineSession() !== null;
    }

    /**
     * Get user and course context
     */
    public function getContext(): array
    {
        return [
            'user_id' => $this->userId,
            'course_auth_id' => $this->courseAuthId,
            'has_active_session' => $this->hasActiveOfflineSession(),
            'current_session_id' => $this->currentSession?->id,
        ];
    }

    /**
     * Force end all sessions (cleanup utility)
     */
    public function forceEndAllSessions(): int
    {
        $count = StudentSession::where('user_id', $this->userId)
            ->where('course_auth_id', $this->courseAuthId)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'),
            ]);

        $this->currentSession = null;

        Log::info("Force ended sessions", [
            'user_id' => $this->userId,
            'course_auth_id' => $this->courseAuthId,
            'sessions_ended' => $count
        ]);

        return $count;
    }
}
