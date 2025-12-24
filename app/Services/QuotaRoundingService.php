<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * QuotaRoundingService
 * 
 * Rounds quota consumption values to standard increments to prevent
 * inflated durations from pause time and ensure consistent quota tracking.
 * 
 * Algorithm:
 * - Rounds UP to nearest standard increment [15, 30, 60] minutes
 * - Prevents quota inflation: 22 minutes → 30 minutes (not 45)
 * - Configurable increments via config/self_study.php
 * 
 * Examples:
 * - 1 minute → 15 minutes
 * - 14 minutes → 15 minutes
 * - 16 minutes → 30 minutes
 * - 45 minutes → 60 minutes
 * - 75 minutes → 90 minutes (60 + 30)
 * 
 * @package App\Services
 */
class QuotaRoundingService
{
    /**
     * Standard rounding increments in minutes (from config)
     *
     * @var array<int>
     */
    protected array $increments;

    /**
     * Create a new QuotaRoundingService instance
     *
     * @param array|null $increments Optional custom increments (defaults to config)
     */
    public function __construct(?array $increments = null)
    {
        $this->increments = $increments ?? config('self_study.quota_rounding_increments', [15, 30, 60]);
        
        // Sort increments ascending for proper rounding logic
        sort($this->increments);
        
        // Validate increments
        if (empty($this->increments)) {
            throw new InvalidArgumentException('Rounding increments cannot be empty');
        }
        
        foreach ($this->increments as $increment) {
            if (!is_int($increment) || $increment <= 0) {
                throw new InvalidArgumentException('All increments must be positive integers');
            }
        }
    }

    /**
     * Round minutes up to the nearest standard increment
     *
     * Algorithm:
     * 1. If minutes <= smallest increment, return smallest increment
     * 2. Otherwise, find the smallest increment that is >= minutes
     * 3. If no single increment fits, use largest increment and recurse with remainder
     *
     * @param int $minutes The number of minutes to round
     * @return int The rounded value
     * 
     * @example
     * // With increments [15, 30, 60]:
     * roundUp(1)   → 15
     * roundUp(14)  → 15
     * roundUp(16)  → 30
     * roundUp(45)  → 60
     * roundUp(75)  → 90 (60 + 30)
     * roundUp(120) → 120 (60 + 60)
     */
    public function roundUp(int $minutes): int
    {
        if ($minutes <= 0) {
            return 0;
        }

        // If minutes fits within smallest increment, return it
        if ($minutes <= $this->increments[0]) {
            return $this->increments[0];
        }

        // Find the smallest increment that is >= minutes
        foreach ($this->increments as $increment) {
            if ($minutes <= $increment) {
                return $increment;
            }
        }

        // If no single increment fits, use largest increment and recurse with remainder
        $largestIncrement = end($this->increments);
        $remainder = $minutes - $largestIncrement;
        
        return $largestIncrement + $this->roundUp($remainder);
    }

    /**
     * Round multiple values in batch
     *
     * @param array<int> $minutesArray Array of minute values to round
     * @return array<int> Array of rounded values
     */
    public function roundUpBatch(array $minutesArray): array
    {
        return array_map(fn($minutes) => $this->roundUp($minutes), $minutesArray);
    }

    /**
     * Calculate the difference between original and rounded value
     *
     * This helps track how much "padding" was added by rounding,
     * which is useful for quota accounting and reporting.
     *
     * @param int $minutes Original minutes value
     * @return array{original: int, rounded: int, difference: int, percentage_increase: float}
     */
    public function getRoundingDetails(int $minutes): array
    {
        $rounded = $this->roundUp($minutes);
        $difference = $rounded - $minutes;
        $percentageIncrease = $minutes > 0 
            ? (($difference / $minutes) * 100) 
            : 0;

        return [
            'original' => $minutes,
            'rounded' => $rounded,
            'difference' => $difference,
            'percentage_increase' => round($percentageIncrease, 2),
        ];
    }

    /**
     * Get human-readable summary of rounding operation
     *
     * @param int $minutes Minutes to round
     * @return string Human-readable summary
     * 
     * @example
     * getSummary(22) → "22 minutes rounded up to 30 minutes (+8 minutes, +36.36% padding)"
     */
    public function getSummary(int $minutes): string
    {
        $details = $this->getRoundingDetails($minutes);
        
        if ($details['difference'] === 0) {
            return "{$minutes} minutes (no rounding needed - exact increment match)";
        }

        return sprintf(
            '%d minutes rounded up to %d minutes (+%d minutes, +%.2f%% padding)',
            $details['original'],
            $details['rounded'],
            $details['difference'],
            $details['percentage_increase']
        );
    }

    /**
     * Get the configured rounding increments
     *
     * @return array<int> Array of increment values in minutes
     */
    public function getIncrements(): array
    {
        return $this->increments;
    }

    /**
     * Validate the configured increments
     *
     * Checks that increments are properly configured and within reasonable ranges
     *
     * @return array{valid: bool, errors: array<string>}
     */
    public function validateConfig(): array
    {
        $errors = [];

        if (count($this->increments) < 1) {
            $errors[] = 'At least one increment is required';
        }

        if (count($this->increments) > 10) {
            $errors[] = 'Too many increments (maximum 10 recommended)';
        }

        foreach ($this->increments as $increment) {
            if ($increment < 1) {
                $errors[] = "Increment {$increment} is too small (minimum 1 minute)";
            }
            if ($increment > 240) {
                $errors[] = "Increment {$increment} is too large (maximum 240 minutes recommended)";
            }
        }

        // Check for duplicates
        if (count($this->increments) !== count(array_unique($this->increments))) {
            $errors[] = 'Duplicate increments found';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
