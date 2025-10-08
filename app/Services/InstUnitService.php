<?php

namespace App\Services;

use App\Models\InstUnit;
use App\Models\CourseDate;
use App\Models\StudentUnit;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for managing InstUnit (Instructor Unit) operations
 * Centralizes all instructor session management
 */
class InstUnitService
{
    /**
     * Get active InstUnit for a CourseDate
     */
    public function getActiveInstUnit(int $courseDateId): ?InstUnit
    {
        return InstUnit::where('course_date_id', $courseDateId)
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Get active InstUnit ID for a CourseDate
     */
    public function getActiveInstUnitId(int $courseDateId): ?int
    {
        return InstUnit::where('course_date_id', $courseDateId)
            ->whereNull('ended_at')
            ->value('id');
    }

    /**
     * Get active InstUnit model for StudentUnit
     */
    public function getActiveInstUnitForStudentUnit(StudentUnit $studentUnit): ?InstUnit
    {
        return $this->getActiveInstUnit($studentUnit->course_date_id);
    }

    /**
     * Get active InstUnit ID for StudentUnit
     */
    public function getActiveInstUnitIdForStudentUnit(StudentUnit $studentUnit): ?int
    {
        return $this->getActiveInstUnitId($studentUnit->course_date_id);
    }

    /**
     * Check if instructor session is active for a CourseDate
     */
    public function isInstructorSessionActive(int $courseDateId): bool
    {
        return $this->getActiveInstUnit($courseDateId) !== null;
    }

    /**
     * Get or create InstUnit for a CourseDate
     */
    public function getOrCreateInstUnit(CourseDate $courseDate): ?InstUnit
    {
        try {
            // First try to find existing InstUnit
            $instUnit = $this->getActiveInstUnit($courseDate->id);

            if (!$instUnit) {
                // Create new InstUnit if none exists
                $instUnit = InstUnit::create([
                    'course_date_id' => $courseDate->id,
                    'course_unit_id' => $courseDate->course_unit_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info('InstUnit created for CourseDate', [
                    'inst_unit_id' => $instUnit->id,
                    'course_date_id' => $courseDate->id,
                    'course_unit_id' => $courseDate->course_unit_id
                ]);
            }

            return $instUnit;

        } catch (Exception $e) {
            Log::error('Failed to get or create InstUnit', [
                'course_date_id' => $courseDate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * End an instructor session
     */
    public function endInstructorSession(int $instUnitId): bool
    {
        try {
            $instUnit = InstUnit::find($instUnitId);

            if (!$instUnit) {
                Log::warning('Attempted to end non-existent InstUnit', [
                    'inst_unit_id' => $instUnitId
                ]);
                return false;
            }

            if ($instUnit->ended_at) {
                Log::warning('Attempted to end already-ended InstUnit', [
                    'inst_unit_id' => $instUnitId,
                    'ended_at' => $instUnit->ended_at
                ]);
                return false;
            }

            $instUnit->update(['ended_at' => now()]);

            Log::info('InstUnit session ended', [
                'inst_unit_id' => $instUnitId,
                'course_date_id' => $instUnit->course_date_id,
                'ended_at' => $instUnit->ended_at
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Failed to end InstUnit session', [
                'inst_unit_id' => $instUnitId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get InstUnit statistics for a CourseDate
     */
    public function getInstUnitStats(int $courseDateId): array
    {
        try {
            $instUnits = InstUnit::where('course_date_id', $courseDateId)->get();

            $activeCount = $instUnits->whereNull('ended_at')->count();
            $endedCount = $instUnits->whereNotNull('ended_at')->count();

            $currentActive = $instUnits->whereNull('ended_at')->first();

            return [
                'total_sessions' => $instUnits->count(),
                'active_sessions' => $activeCount,
                'ended_sessions' => $endedCount,
                'current_active_id' => $currentActive?->id,
                'has_active_session' => $activeCount > 0,
                'last_started' => $instUnits->max('created_at'),
                'last_ended' => $instUnits->whereNotNull('ended_at')->max('ended_at')
            ];

        } catch (Exception $e) {
            Log::error('Failed to get InstUnit stats', [
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return [
                'total_sessions' => 0,
                'active_sessions' => 0,
                'ended_sessions' => 0,
                'current_active_id' => null,
                'has_active_session' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all InstUnits for a CourseDate
     */
    public function getInstUnitsForCourseDate(int $courseDateId): \Illuminate\Database\Eloquent\Collection
    {
        return InstUnit::where('course_date_id', $courseDateId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if InstUnit can be created for CourseDate
     */
    public function canCreateInstUnit(CourseDate $courseDate): array
    {
        try {
            // Check if CourseDate has required fields
            if (!$courseDate->course_unit_id) {
                return [
                    'can_create' => false,
                    'reason' => 'CourseDate missing course_unit_id',
                    'code' => 'MISSING_COURSE_UNIT_ID'
                ];
            }

            // Check if there's already an active session
            $activeInstUnit = $this->getActiveInstUnit($courseDate->id);
            if ($activeInstUnit) {
                return [
                    'can_create' => false,
                    'reason' => 'Active InstUnit already exists',
                    'code' => 'ACTIVE_SESSION_EXISTS',
                    'existing_inst_unit_id' => $activeInstUnit->id
                ];
            }

            return [
                'can_create' => true,
                'reason' => 'Ready to create InstUnit',
                'code' => 'READY_TO_CREATE'
            ];

        } catch (Exception $e) {
            Log::error('Error checking InstUnit creation eligibility', [
                'course_date_id' => $courseDate->id,
                'error' => $e->getMessage()
            ]);

            return [
                'can_create' => false,
                'reason' => 'System error: ' . $e->getMessage(),
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }
}
