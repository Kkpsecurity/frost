<?php

namespace App\Services;

use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Models\InstUnit;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling classroom attendance detection and management
 * Optimized to use shared BaseAttendanceService methods
 */
class ClassroomAttendanceService extends BaseAttendanceService
{
    protected InstUnitService $instUnitService;

    public function __construct(InstUnitService $instUnitService)
    {
        $this->instUnitService = $instUnitService;
    }

    /**
     * Check if student needs to mark attendance for an active class today
     */
    public function checkAttendanceRequired($student): array
    {
        try {
            $now = now();
            $today = $now->toDateString();

            // Find active CourseDate using shared method
            $courseDate = $this->findActiveCourseDate($today, $now);

            if (!$courseDate) {
                return [
                    'attendance_required' => false,
                    'message' => 'No active class today'
                ];
            }

            // Validate student access using shared method
            $validation = $this->validateStudentAccess($student, $courseDate);

            if (!$validation['valid']) {
                if ($validation['code'] === 'ALREADY_PRESENT') {
                    return [
                        'attendance_required' => false,
                        'message' => $validation['message'],
                        'student_unit_id' => $validation['existing_student_unit']->id
                    ];
                }

                return [
                    'attendance_required' => false,
                    'message' => $validation['message']
                ];
            }

            // Check if instructor has started the session using InstUnitService
            if (!$this->instUnitService->isInstructorSessionActive($courseDate->id)) {
                return [
                    'attendance_required' => false,
                    'message' => 'Class not started by instructor'
                ];
            }

            // All conditions met - attendance required
            return [
                'attendance_required' => true,
                'course_date_id' => $courseDate->id,
                'attendance_url' => route('classroom.attendance.mark', ['courseDate' => $courseDate->id]),
                'message' => 'Active class found — attendance required'
            ];

        } catch (Exception $e) {
            $this->logAttendanceError('Attendance check error', [
                'student_id' => $student->id ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'attendance_required' => false,
                'error' => 'Error checking attendance: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get active InstUnit ID for a StudentUnit (activity logging)
     */
    public function getActiveInstUnitIdForStudentUnit(StudentUnit $studentUnit): ?int
    {
        return $this->instUnitService->getActiveInstUnitIdForStudentUnit($studentUnit);
    }

    /**
     * Get active InstUnit model for StudentUnit
     */
    public function getActiveInstUnitModelForStudentUnit(StudentUnit $studentUnit): ?InstUnit
    {
        return $this->instUnitService->getActiveInstUnitForStudentUnit($studentUnit);
    }
}
