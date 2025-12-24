<?php

namespace App\Services;

/**
 * Pause Time Calculator Service
 * 
 * Calculates pause time allowances for self-study lessons based on video duration.
 * Implements algorithm: base minutes per hour with max cap and interval distribution.
 * 
 * Algorithm:
 * - Base: 10 minutes per hour of video (configurable)
 * - Max: 60 minutes total (configurable)
 * - Distribution: Break into intervals [5, 10, 15] for better management
 * 
 * Examples:
 * - 1 hour video = 10 minutes pause (single interval)
 * - 3 hour video = 30 minutes pause (distributed: [15, 10, 5])
 * - 6 hour video = 60 minutes pause (max cap, distributed: [15, 15, 15, 10, 5])
 */
class PauseTimeCalculator
{
    /**
     * Calculate pause time allowance based on video duration
     * 
     * @param int $videoDurationSeconds Total video length in seconds
     * @return array ['total_minutes' => int, 'intervals' => array]
     */
    public function calculate(int $videoDurationSeconds): array
    {
        // Convert seconds to hours
        $videoDurationHours = $videoDurationSeconds / 3600;
        
        // Get config values
        $minutesPerHour = config('self_study.pause_time.minutes_per_hour', 10);
        $maxTotalMinutes = config('self_study.pause_time.max_total_minutes', 60);
        $minIntervalMinutes = config('self_study.pause_time.min_interval_minutes', 5);
        $intervalDistribution = config('self_study.pause_time.interval_distribution', [5, 10, 15]);
        
        // Calculate total pause time (apply max cap)
        $calculatedMinutes = $videoDurationHours * $minutesPerHour;
        $totalPauseMinutes = min(ceil($calculatedMinutes), $maxTotalMinutes);
        
        // Distribute into intervals
        $intervals = $this->distributeIntoIntervals(
            $totalPauseMinutes,
            $intervalDistribution,
            $minIntervalMinutes
        );
        
        return [
            'total_minutes' => $totalPauseMinutes,
            'intervals' => $intervals,
        ];
    }

    /**
     * Distribute pause time into multiple intervals for better management
     * 
     * Uses preferred intervals (e.g., [5, 10, 15]) to break down total pause time
     * into manageable chunks. This helps track pause usage more granularly.
     * 
     * Algorithm:
     * 1. Sort preferred intervals descending (largest first)
     * 2. Fill with largest intervals first
     * 3. Add remaining as final interval if >= min_interval
     * 
     * Example: 30 minutes with [5, 10, 15] = [15, 15] or [15, 10, 5]
     * 
     * @param int $totalMinutes Total pause time to distribute
     * @param array $preferredIntervals Preferred interval sizes (minutes)
     * @param int $minInterval Minimum interval size (minutes)
     * @return array Array of interval sizes
     */
    protected function distributeIntoIntervals(
        int $totalMinutes, 
        array $preferredIntervals, 
        int $minInterval
    ): array {
        $intervals = [];
        $remaining = $totalMinutes;
        
        // Sort preferred intervals descending (largest first)
        rsort($preferredIntervals);
        
        // Distribute using preferred intervals
        foreach ($preferredIntervals as $interval) {
            while ($remaining >= $interval) {
                $intervals[] = $interval;
                $remaining -= $interval;
            }
        }
        
        // Add remaining as final interval if >= minInterval
        if ($remaining >= $minInterval) {
            $intervals[] = $remaining;
        } elseif ($remaining > 0 && count($intervals) > 0) {
            // Add remaining to last interval if too small for separate interval
            $intervals[count($intervals) - 1] += $remaining;
        } elseif ($remaining > 0) {
            // Edge case: remaining is less than minInterval but no intervals yet
            $intervals[] = $remaining;
        }
        
        return $intervals;
    }

    /**
     * Calculate pause time for multiple video durations (batch processing)
     * 
     * @param array $videoDurations Array of video durations in seconds
     * @return array Array of calculation results
     */
    public function calculateBatch(array $videoDurations): array
    {
        $results = [];
        
        foreach ($videoDurations as $duration) {
            $results[] = $this->calculate($duration);
        }
        
        return $results;
    }

    /**
     * Get pause time summary for display purposes
     * 
     * @param int $videoDurationSeconds Video duration in seconds
     * @return array Human-readable summary
     */
    public function getSummary(int $videoDurationSeconds): array
    {
        $calculation = $this->calculate($videoDurationSeconds);
        $videoHours = $videoDurationSeconds / 3600;
        
        return [
            'video_duration_hours' => round($videoHours, 2),
            'video_duration_minutes' => round($videoDurationSeconds / 60),
            'total_pause_minutes' => $calculation['total_minutes'],
            'pause_intervals' => $calculation['intervals'],
            'interval_count' => count($calculation['intervals']),
            'largest_interval' => max($calculation['intervals']),
            'smallest_interval' => min($calculation['intervals']),
            'formula' => sprintf(
                '%.2f hours × %d min/hour = %d minutes pause (max %d)',
                $videoHours,
                config('self_study.pause_time.minutes_per_hour', 10),
                $calculation['total_minutes'],
                config('self_study.pause_time.max_total_minutes', 60)
            ),
        ];
    }

    /**
     * Validate pause time configuration
     * 
     * Checks if config values are valid and within reasonable ranges
     * 
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateConfig(): array
    {
        $errors = [];
        
        $minutesPerHour = config('self_study.pause_time.minutes_per_hour');
        $maxTotalMinutes = config('self_study.pause_time.max_total_minutes');
        $minIntervalMinutes = config('self_study.pause_time.min_interval_minutes');
        
        if ($minutesPerHour < 1 || $minutesPerHour > 30) {
            $errors[] = 'minutes_per_hour must be between 1 and 30';
        }
        
        if ($maxTotalMinutes < 10 || $maxTotalMinutes > 180) {
            $errors[] = 'max_total_minutes must be between 10 and 180';
        }
        
        if ($minIntervalMinutes < 1 || $minIntervalMinutes > 15) {
            $errors[] = 'min_interval_minutes must be between 1 and 15';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
