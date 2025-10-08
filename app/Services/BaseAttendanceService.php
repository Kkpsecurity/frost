<?php

namespace App\Services;

use App\Models\CourseDate;
use App\Models\CourseAuth;
use App\Models\StudentUnit;
use App\Models\InstUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Base service containing shared attendance operations
 * Used by all attendance-related services to eliminate duplication
 */
abstract class BaseAttendanceService
{
    /**
     * Find active CourseDate for a specific date and time
     */
    protected function findActiveCourseDate(string $date, Carbon $time): ?CourseDate
    {
        return CourseDate::whereDate('starts_at', $date)
            ->whereTime('starts_at', '<=', $time->toTimeString())
            ->whereTime('ends_at', '>=', $time->toTimeString())
            ->first();
    }

    /**
     * Get student's course authorization for specific course
     */
    protected function getStudentCourseAuth(User $student, int $courseId): ?CourseAuth
    {
        return $student->activeCourseAuths()
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * Check if student already has a StudentUnit for this CourseDate
     */
    protected function getExistingStudentUnit(int $courseAuthId, int $courseDateId): ?StudentUnit
    {
        return StudentUnit::where('course_auth_id', $courseAuthId)
            ->where('course_date_id', $courseDateId)
            ->first();
    }

    /**
     * Get active instructor session for CourseDate
     */
    protected function getActiveInstUnit(int $courseDateId): ?InstUnit
    {
        return InstUnit::where('course_date_id', $courseDateId)
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Get active InstUnit ID for a CourseDate
     */
    protected function getActiveInstUnitId(int $courseDateId): ?int
    {
        return InstUnit::where('course_date_id', $courseDateId)
            ->whereNull('ended_at')
            ->value('id');
    }

    /**
     * Validate student access to a course date
     * Returns array with validation results
     */
    protected function validateStudentAccess(User $student, CourseDate $courseDate): array
    {
        // Check student enrollment
        $courseAuth = $this->getStudentCourseAuth($student, $courseDate->course_id);

        if (!$courseAuth) {
            return [
                'valid' => false,
                'code' => 'NOT_ENROLLED',
                'message' => 'Student is not enrolled in this course',
                'course_auth' => null
            ];
        }

        // Check if already has attendance record
        $existingStudentUnit = $this->getExistingStudentUnit($courseAuth->id, $courseDate->id);

        if ($existingStudentUnit) {
            return [
                'valid' => false,
                'code' => 'ALREADY_PRESENT',
                'message' => 'Attendance already marked for this session',
                'course_auth' => $courseAuth,
                'existing_student_unit' => $existingStudentUnit
            ];
        }

        return [
            'valid' => true,
            'code' => 'ACCESS_GRANTED',
            'message' => 'Student can access this class',
            'course_auth' => $courseAuth,
            'existing_student_unit' => null
        ];
    }

    /**
     * Check if instructor has started the session
     */
    protected function isInstructorSessionActive(int $courseDateId): bool
    {
        return $this->getActiveInstUnit($courseDateId) !== null;
    }

    /**
     * Get or create InstUnit for a CourseDate
     */
    protected function getOrCreateInstUnit(CourseDate $courseDate): ?InstUnit
    {
        // First try to find existing InstUnit
        $instUnit = $this->getActiveInstUnit($courseDate->id);

        if (!$instUnit) {
            // Create new InstUnit if none exists
            try {
                $instUnit = InstUnit::create([
                    'course_date_id' => $courseDate->id,
                    'course_unit_id' => $courseDate->course_unit_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create InstUnit', [
                    'course_date_id' => $courseDate->id,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        }

        return $instUnit;
    }

    /**
     * Create StudentUnit record for attendance
     */
    protected function createStudentUnit(
        CourseAuth $courseAuth,
        CourseDate $courseDate,
        InstUnit $instUnit,
        string $attendanceType = 'online'
    ): StudentUnit {
        return StudentUnit::create([
            'course_auth_id' => $courseAuth->id,
            'course_date_id' => $courseDate->id,
            'course_unit_id' => $courseDate->course_unit_id,
            'inst_unit_id' => $instUnit->id,
            'attendance_type' => $attendanceType,
            'created_at' => now(),
            'updated_at' => now(),
            'unit_completed' => false
        ]);
    }

    /**
     * Get class information for a CourseDate
     */
    protected function getClassInfo(CourseDate $courseDate): array
    {
        $startTime = Carbon::parse($courseDate->starts_at);
        $endTime = Carbon::parse($courseDate->ends_at);
        $now = Carbon::now();

        return [
            'start_time' => $startTime->format('g:i A'),
            'end_time' => $endTime->format('g:i A'),
            'duration_hours' => $startTime->floatDiffInHours($endTime),
            'is_current' => $startTime->isPast() && $endTime->isFuture(),
            'status' => $this->getClassStatus($startTime, $endTime, $now),
            'course_date_id' => $courseDate->id,
            'course_id' => $courseDate->course_id
        ];
    }

    /**
     * Get class status based on current time
     */
    protected function getClassStatus(Carbon $startTime, Carbon $endTime, Carbon $now): string
    {
        if ($now->isBefore($startTime)) {
            return 'upcoming';
        } elseif ($now->isBetween($startTime, $endTime)) {
            return 'active';
        } else {
            return 'completed';
        }
    }

    /**
     * Log attendance-related activity with consistent format
     */
    protected function logAttendanceActivity(string $message, array $context = []): void
    {
        Log::info($message, array_merge([
            'service' => get_class($this),
            'timestamp' => now()->toISOString()
        ], $context));
    }

    /**
     * Log attendance error with consistent format
     */
    protected function logAttendanceError(string $message, array $context = []): void
    {
        Log::error($message, array_merge([
            'service' => get_class($this),
            'timestamp' => now()->toISOString()
        ], $context));
    }
}
