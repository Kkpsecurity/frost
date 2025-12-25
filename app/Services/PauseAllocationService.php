<?php

namespace App\Services;

/**
 * PauseAllocationService
 *
 * Calculates pause time allocation based on course duration.
 * Distributes total pause time into multiple breaks according to config rules.
 */
class PauseAllocationService
{
    /**
     * Calculate pause allocation for a given lesson duration
     *
     * @param int $durationMinutes Lesson duration in minutes
     * @return array {
     *   total_minutes: int,
     *   pauses: array of [duration_minutes, label],
     *   current_pause_index: int
     * }
     */
    public static function calculatePauseAllocation(int $durationMinutes): array
    {
        $durationHours = $durationMinutes / 60;
        $allocationRules = config('self_study.pause_time.allocation_rules', []);

        // Find matching rule (first rule where duration >= min_hours)
        $matchedRule = null;
        foreach ($allocationRules as $rule) {
            if ($durationHours >= $rule['min_hours']) {
                $matchedRule = $rule;
                break;
            }
        }

        // Fallback if no rule matches (shouldn't happen with 0 hour rule)
        if (!$matchedRule) {
            return [
                'total_minutes' => 5,
                'pauses' => [
                    ['duration_minutes' => 5, 'label' => 'Break 1']
                ],
                'current_pause_index' => 0,
            ];
        }

        // Build pause list from distribution
        $pauses = [];
        $distribution = $matchedRule['distribution'];

        foreach ($distribution as $index => $pauseMinutes) {
            $pauseNumber = $index + 1;
            $totalPauses = count($distribution);

            // Generate descriptive labels
            if ($totalPauses === 1) {
                $label = 'Break';
            } else {
                $label = "Break $pauseNumber of $totalPauses";
            }

            $pauses[] = [
                'duration_minutes' => $pauseMinutes,
                'label' => $label,
            ];
        }

        return [
            'total_minutes' => $matchedRule['total_minutes'],
            'pauses' => $pauses,
            'current_pause_index' => 0, // Will be updated as pauses are used
        ];
    }

    /**
     * Get pause warning threshold in seconds
     *
     * @return int
     */
    public static function getWarningSeconds(): int
    {
        return config('self_study.pause_time.warning_seconds', 30);
    }

    /**
     * Get alert sound path
     *
     * @return string
     */
    public static function getAlertSoundPath(): string
    {
        return config('self_study.pause_time.alert_sound', '/sounds/pause-warning.mp3');
    }
}
