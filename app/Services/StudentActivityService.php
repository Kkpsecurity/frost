<?php

namespace App\Services;

use App\Models\StudentActivity;
use App\Models\StudentUnit;
use Illuminate\Support\Facades\Log;

/**
 * Service for tracking student activities and classroom actions
 */
class StudentActivityService
{
    /**
     * Log a student activity
     */
    public function logActivity(
        int $courseAuthId,
        int $studentUnitId,
        string $action,
        int $instUnitId = null,
        array $metadata = []
    ): StudentActivity {
        $activity = StudentActivity::create([
            'course_auth_id' => $courseAuthId,
            'student_unit_id' => $studentUnitId,
            'inst_unit_id' => $instUnitId,
            'action' => $action,
        ]);

        // Optional: Log to Laravel logs for debugging
        if (!empty($metadata)) {
            Log::info("Student activity: {$action}", array_merge([
                'course_auth_id' => $courseAuthId,
                'student_unit_id' => $studentUnitId,
                'inst_unit_id' => $instUnitId,
                'activity_id' => $activity->id,
            ], $metadata));
        }

        return $activity;
    }

    /**
     * Log multiple activities in batch
     */
    public function logMultipleActivities(array $activities): array
    {
        $created = [];

        foreach ($activities as $activity) {
            $created[] = $this->logActivity(
                $activity['course_auth_id'],
                $activity['student_unit_id'],
                $activity['action'],
                $activity['inst_unit_id'] ?? null,
                $activity['metadata'] ?? []
            );
        }

        return $created;
    }

    /**
     * Check if specific activity already exists
     */
    public function hasActivity(int $studentUnitId, string $action): bool
    {
        return StudentActivity::where('student_unit_id', $studentUnitId)
            ->where('action', $action)
            ->exists();
    }

    /**
     * Get all activities for a student unit
     */
    public function getActivities(int $studentUnitId, array $actions = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = StudentActivity::where('student_unit_id', $studentUnitId)
            ->orderBy('created_at', 'asc');

        if (!empty($actions)) {
            $query->whereIn('action', $actions);
        }

        return $query->get();
    }

    /**
     * Get activity timeline for a student unit
     */
    public function getActivityTimeline(int $studentUnitId): array
    {
        $activities = $this->getActivities($studentUnitId);

        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'action' => $activity->action,
                'timestamp' => $activity->created_at,
                'formatted_time' => $activity->created_at->format('M j, Y g:i A'),
                'description' => $this->getActionDescription($activity->action),
            ];
        })->toArray();
    }

    /**
     * Log onboarding start (once per student unit)
     */
    public function logOnboardingStart(int $courseAuthId, int $studentUnitId, int $instUnitId = null): ?StudentActivity
    {
        // Only log if not already tracked
        if (!$this->hasActivity($studentUnitId, 'onboarding_started')) {
            return $this->logActivity($courseAuthId, $studentUnitId, 'onboarding_started', $instUnitId);
        }

        return null;
    }

    /**
     * Get human-readable description for activity actions
     */
    private function getActionDescription(string $action): string
    {
        $descriptions = [
            'student_unit_created' => 'Student unit created automatically',
            'onboarding_started' => 'Started onboarding process',
            'agreement_accepted' => 'Accepted student agreement',
            'rules_acknowledged' => 'Acknowledged classroom rules',
            'identity_verified' => 'Completed identity verification',
            'attendance_marked' => 'Marked attendance for class',
            'onboarding_completed' => 'Completed onboarding process',
            'classroom_entered' => 'Entered classroom session',
        ];

        return $descriptions[$action] ?? ucwords(str_replace('_', ' ', $action));
    }

    /**
     * Get activity statistics for a student unit
     */
    public function getActivityStats(int $studentUnitId): array
    {
        $activities = $this->getActivities($studentUnitId);

        return [
            'total_activities' => $activities->count(),
            'first_activity' => $activities->first()?->created_at,
            'last_activity' => $activities->last()?->created_at,
            'actions_completed' => $activities->pluck('action')->unique()->toArray(),
            'duration' => $activities->count() > 1
                ? $activities->first()->created_at->diffInMinutes($activities->last()->created_at)
                : 0,
        ];
    }
}
