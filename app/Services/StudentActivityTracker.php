<?php

namespace App\Services;

use App\Models\StudentActivity;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentActivityTracker
{
    /**
     * Track a student activity
     *
     * @param int $userId
     * @param string $category
     * @param string $activityType
     * @param array $context
     * @return StudentActivity|null
     */
    public function track(
        int $userId,
        string $category,
        string $activityType,
        array $context = []
    ): ?StudentActivity {
        try {
            // Enrich with request context
            $activityData = array_merge([
                'user_id' => $userId,
                'category' => $category,
                'activity_type' => $activityType,
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'metadata' => [
                    'referer' => request()->header('referer'),
                    'method' => request()->method(),
                    'timestamp_utc' => now()->toIso8601String(),
                ],
            ], $context);

            // Create activity record
            $activity = StudentActivity::create($activityData);

            // Log for debugging
            if (config('app.debug')) {
                Log::info("Student Activity Tracked: {$category}/{$activityType}", [
                    'activity_id' => $activity->id,
                    'user_id' => $userId,
                ]);
            }

            return $activity;

        } catch (\Exception $e) {
            // Silent failure - don't break app if tracking fails
            Log::error('Failed to track student activity', [
                'user_id' => $userId,
                'category' => $category,
                'activity_type' => $activityType,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Track site entry
     */
    public function trackSiteEntry(int $userId, array $context = []): ?StudentActivity
    {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_ENTRY,
            StudentActivity::TYPE_SITE_ENTRY,
            array_merge([
                'description' => 'Student entered site',
                'started_at' => now(),
            ], $context)
        );
    }

    /**
     * Track site exit
     */
    public function trackSiteExit(int $userId, array $context = []): ?StudentActivity
    {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_ENTRY,
            StudentActivity::TYPE_SITE_EXIT,
            array_merge([
                'description' => 'Student left site',
                'ended_at' => now(),
            ], $context)
        );
    }

    /**
     * Track classroom entry
     */
    public function trackClassroomEntry(
        int $userId,
        int $courseAuthId,
        int $courseDateId,
        array $context = []
    ): ?StudentActivity {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_ENTRY,
            StudentActivity::TYPE_CLASSROOM_ENTRY,
            array_merge([
                'course_auth_id' => $courseAuthId,
                'course_date_id' => $courseDateId,
                'description' => 'Student entered classroom',
                'started_at' => now(),
            ], $context)
        );
    }

    /**
     * Track agreement acceptance
     */
    public function trackAgreementAccepted(
        int $userId,
        int $courseAuthId,
        array $context = []
    ): ?StudentActivity {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_AGREEMENT,
            StudentActivity::TYPE_AGREEMENT_ACCEPTED,
            array_merge([
                'course_auth_id' => $courseAuthId,
                'description' => 'Student accepted terms and conditions',
            ], $context)
        );
    }

    /**
     * Track rules acceptance
     */
    public function trackRulesAccepted(
        int $userId,
        int $studentUnitId,
        array $context = []
    ): ?StudentActivity {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_AGREEMENT,
            StudentActivity::TYPE_RULES_ACCEPTED,
            array_merge([
                'student_unit_id' => $studentUnitId,
                'description' => 'Student accepted classroom rules',
            ], $context)
        );
    }

    /**
     * Track tab hidden (student left site)
     */
    public function trackTabHidden(int $userId, array $context = []): ?StudentActivity
    {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_SYSTEM,
            StudentActivity::TYPE_TAB_HIDDEN,
            array_merge([
                'description' => 'Student tab hidden (left site)',
                'started_at' => now(),
            ], $context)
        );
    }

    /**
     * Track tab visible (student returned to site)
     */
    public function trackTabVisible(
        int $userId,
        ?Carbon $hiddenAt = null,
        array $context = []
    ): ?StudentActivity {
        $duration = null;
        if ($hiddenAt) {
            $duration = now()->diffInSeconds($hiddenAt);
        }

        return $this->track(
            $userId,
            StudentActivity::CATEGORY_SYSTEM,
            StudentActivity::TYPE_TAB_VISIBLE,
            array_merge([
                'description' => 'Student tab visible (returned to site)',
                'ended_at' => now(),
                'duration_seconds' => $duration,
                'data' => $duration ? [
                    'away_time' => $duration,
                    'away_formatted' => gmdate('H:i:s', $duration),
                ] : null,
            ], $context)
        );
    }

    /**
     * Track button click
     */
    public function trackButtonClick(
        int $userId,
        string $buttonName,
        array $context = []
    ): ?StudentActivity {
        return $this->track(
            $userId,
            StudentActivity::CATEGORY_INTERACTION,
            StudentActivity::TYPE_BUTTON_CLICK,
            array_merge([
                'description' => "Student clicked button: {$buttonName}",
                'data' => ['button_name' => $buttonName],
            ], $context)
        );
    }

    /**
     * Get student timeline for a date
     */
    public function getTimeline(int $userId, ?Carbon $date = null): array
    {
        $query = StudentActivity::where('user_id', $userId)
            ->orderBy('created_at', 'asc');

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $activities = $query->get();

        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'category' => $activity->category,
                'activity_type' => $activity->activity_type,
                'description' => $activity->description,
                'time' => $activity->created_at->format('H:i:s'),
                'timestamp' => $activity->created_at->toIso8601String(),
                'duration' => $activity->duration_seconds,
                'data' => $activity->data,
            ];
        })->toArray();
    }

    /**
     * Calculate time away from site (gap between tab hidden and visible)
     */
    public function calculateAwayTime(int $userId, string $sessionId): array
    {
        $hiddenEvents = StudentActivity::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('activity_type', StudentActivity::TYPE_TAB_HIDDEN)
            ->whereNotNull('started_at')
            ->get();

        $visibleEvents = StudentActivity::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('activity_type', StudentActivity::TYPE_TAB_VISIBLE)
            ->whereNotNull('ended_at')
            ->get();

        $gaps = [];
        $totalAwaySeconds = 0;

        foreach ($hiddenEvents as $hidden) {
            $nextVisible = $visibleEvents->first(function ($visible) use ($hidden) {
                return $visible->ended_at > $hidden->started_at;
            });

            if ($nextVisible) {
                $awaySeconds = $nextVisible->ended_at->diffInSeconds($hidden->started_at);
                $totalAwaySeconds += $awaySeconds;

                $gaps[] = [
                    'left_at' => $hidden->started_at->toIso8601String(),
                    'returned_at' => $nextVisible->ended_at->toIso8601String(),
                    'duration_seconds' => $awaySeconds,
                    'duration_formatted' => gmdate('H:i:s', $awaySeconds),
                ];
            }
        }

        return [
            'total_away_seconds' => $totalAwaySeconds,
            'total_away_formatted' => gmdate('H:i:s', $totalAwaySeconds),
            'gaps' => $gaps,
            'gap_count' => count($gaps),
        ];
    }
}
