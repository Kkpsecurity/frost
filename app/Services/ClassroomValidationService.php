<?php

namespace App\Services;

use App\Models\InstUnit;
use App\Models\StudentUnit;
use App\Models\CourseDate;
use App\Models\CourseAuth;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Classroom Validation Service
 * 
 * Handles validation and creation of InstUnit and StudentUnit records
 * when instructors and students enter classrooms. Ensures proper
 * session management and attendance tracking.
 */
class ClassroomValidationService
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Validate and prepare classroom for instructor entry
     * Ensures InstUnit exists and handles instructor as student scenario
     * 
     * @param User $instructor The instructor user
     * @param int $courseDateId
     * @return array
     */
    public function validateInstructorEntry(User $instructor, int $courseDateId): array
    {
        try {
            $courseDate = CourseDate::find($courseDateId);
            
            if (!$courseDate) {
                return [
                    'success' => false,
                    'message' => 'Course date not found',
                    'code' => 'INVALID_COURSE_DATE'
                ];
            }

            // 1. Ensure InstUnit exists
            $instUnitResult = $this->ensureInstUnitExists($courseDate, $instructor);
            if (!$instUnitResult['success']) {
                return $instUnitResult;
            }

            $instUnit = $instUnitResult['data'];

            // 2. Check if instructor is also a student in this course
            $instructorStudentResult = $this->handleInstructorAsStudent($instructor, $courseDate, $instUnit);

            // 3. Get current classroom status
            $classroomStatus = $this->getClassroomStatusInfo($courseDate, $instUnit);

            Log::info('Instructor classroom entry validated', [
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->name,
                'course_date_id' => $courseDateId,
                'inst_unit_id' => $instUnit->id,
                'instructor_as_student' => $instructorStudentResult['is_student'],
                'student_unit_created' => $instructorStudentResult['student_unit_created'],
                'total_students_present' => $classroomStatus['students_present']
            ]);

            return [
                'success' => true,
                'message' => 'Classroom ready for instructor',
                'data' => [
                    'inst_unit' => $instUnit,
                    'course_date' => $courseDate,
                    'instructor_as_student' => $instructorStudentResult,
                    'classroom_status' => $classroomStatus
                ],
                'code' => 'CLASSROOM_READY'
            ];

        } catch (Exception $e) {
            Log::error('Instructor classroom validation failed', [
                'instructor_id' => $instructor->id ?? null,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Classroom validation failed: ' . $e->getMessage(),
                'code' => 'VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Validate student entry to classroom
     * Ensures proper attendance tracking and prevents early access issues
     * 
     * @param User $student
     * @param int $courseDateId
     * @return array
     */
    public function validateStudentEntry(User $student, int $courseDateId): array
    {
        try {
            $courseDate = CourseDate::find($courseDateId);
            
            if (!$courseDate) {
                return [
                    'success' => false,
                    'message' => 'Course date not found',
                    'code' => 'INVALID_COURSE_DATE'
                ];
            }

            // 1. Check if student can enter (enrollment, timing, etc.)
            $accessResult = $this->validateStudentAccess($student, $courseDate);
            if (!$accessResult['success']) {
                return $accessResult;
            }

            // 2. Handle attendance - use AttendanceService
            $attendanceResult = $this->attendanceService->handleStudentArrival($student, $courseDateId);
            
            // 3. Get/Create InstUnit if student enters before instructor
            $instUnit = $this->getOrCreateInstUnit($courseDate);

            // 4. Get classroom info for student
            $studentClassroomInfo = $this->getStudentClassroomInfo($student, $courseDate, $instUnit);

            Log::info('Student classroom entry validated', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'course_date_id' => $courseDateId,
                'attendance_result' => $attendanceResult['code'],
                'inst_unit_id' => $instUnit->id ?? null,
                'early_arrival' => !$instUnit || !$instUnit->created_by // Student arrived before instructor
            ]);

            return [
                'success' => true,
                'message' => 'Student classroom access validated',
                'data' => [
                    'attendance' => $attendanceResult,
                    'inst_unit' => $instUnit,
                    'classroom_info' => $studentClassroomInfo,
                    'early_arrival' => !$instUnit || !$instUnit->created_by
                ],
                'code' => 'STUDENT_ACCESS_GRANTED'
            ];

        } catch (Exception $e) {
            Log::error('Student classroom validation failed', [
                'student_id' => $student->id ?? null,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Student access validation failed: ' . $e->getMessage(),
                'code' => 'VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Get comprehensive classroom status for monitoring
     * 
     * @param int $courseDateId
     * @return array
     */
    public function getComprehensiveClassroomStatus(int $courseDateId): array
    {
        try {
            $courseDate = CourseDate::find($courseDateId);
            if (!$courseDate) {
                return [
                    'success' => false,
                    'message' => 'Course date not found'
                ];
            }

            $instUnit = InstUnit::where('course_date_id', $courseDateId)->first();
            $classroomStatus = $this->getClassroomStatusInfo($courseDate, $instUnit);
            $attendanceStats = $this->attendanceService->getAttendanceStats($courseDate);

            return [
                'success' => true,
                'data' => [
                    'course_date' => $courseDate,
                    'inst_unit' => $instUnit,
                    'instructor_present' => $instUnit && $instUnit->created_by,
                    'class_started' => $instUnit && $instUnit->created_at,
                    'attendance_stats' => $attendanceStats,
                    'students_present' => $classroomStatus['students_present'],
                    'students_left' => $classroomStatus['students_left']
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get classroom status: ' . $e->getMessage()
            ];
        }
    }

    // Private helper methods

    /**
     * Ensure InstUnit exists for the classroom session with duplicate prevention
     */
    private function ensureInstUnitExists(CourseDate $courseDate, User $instructor): array
    {
        // Check for existing InstUnit - prevent duplicates
        $existingInstUnit = InstUnit::where('course_date_id', $courseDate->id)->first();

        if ($existingInstUnit) {
            // InstUnit already exists
            if (!$existingInstUnit->created_by) {
                // InstUnit exists but no instructor recorded (student arrived first)
                $existingInstUnit->update([
                    'created_by' => $instructor->id,
                    'updated_at' => now()
                ]);

                Log::info('InstUnit updated with instructor info', [
                    'inst_unit_id' => $existingInstUnit->id,
                    'instructor_id' => $instructor->id,
                    'note' => 'Student arrived before instructor'
                ]);
            } else {
                // InstUnit already has instructor - check if it's the same instructor
                if ($existingInstUnit->created_by != $instructor->id) {
                    Log::warning('Different instructor attempting to access existing class', [
                        'existing_inst_unit_id' => $existingInstUnit->id,
                        'existing_instructor_id' => $existingInstUnit->created_by,
                        'attempting_instructor_id' => $instructor->id,
                        'course_date_id' => $courseDate->id
                    ]);
                } else {
                    Log::info('Same instructor re-accessing existing class', [
                        'inst_unit_id' => $existingInstUnit->id,
                        'instructor_id' => $instructor->id
                    ]);
                }
            }

            return [
                'success' => true,
                'data' => $existingInstUnit,
                'duplicate_prevented' => true
            ];
        }

        // No existing InstUnit - create new one with duplicate protection
        try {
            $instUnit = InstUnit::create([
                'course_date_id' => $courseDate->id,
                'created_at' => now(),
                'created_by' => $instructor->id,
            ]);

            Log::info('New InstUnit created by instructor', [
                'inst_unit_id' => $instUnit->id,
                'course_date_id' => $courseDate->id,
                'instructor_id' => $instructor->id
            ]);

            return [
                'success' => true,
                'data' => $instUnit,
                'duplicate_prevented' => false
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle race condition - another process might have created InstUnit
            if (str_contains($e->getMessage(), 'duplicate') || str_contains($e->getMessage(), 'unique')) {
                Log::info('Race condition detected - fetching existing InstUnit', [
                    'course_date_id' => $courseDate->id,
                    'instructor_id' => $instructor->id
                ]);

                $instUnit = InstUnit::where('course_date_id', $courseDate->id)->first();
                return [
                    'success' => true,
                    'data' => $instUnit,
                    'duplicate_prevented' => true,
                    'race_condition' => true
                ];
            }

            throw $e;
        }
    }

    /**
     * Handle case where instructor is also a student in the course
     */
    private function handleInstructorAsStudent(User $instructor, CourseDate $courseDate, InstUnit $instUnit): array
    {
        // Check if instructor has CourseAuth (is also a student)
        $courseAuth = CourseAuth::where('user_id', $instructor->id)
            ->where('course_id', $courseDate->course_id)
            ->where('active', true)
            ->first();

        if (!$courseAuth) {
            return [
                'is_student' => false,
                'student_unit_created' => false,
                'message' => 'Instructor is not enrolled as student'
            ];
        }

        // Check if StudentUnit already exists
        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
            ->where('course_date_id', $courseDate->id)
            ->first();

        if ($studentUnit) {
            return [
                'is_student' => true,
                'student_unit_created' => true,
                'student_unit' => $studentUnit,
                'message' => 'Instructor already marked as present student'
            ];
        }

        // Create StudentUnit for instructor
        $studentUnit = StudentUnit::create([
            'course_auth_id' => $courseAuth->id,
            'course_date_id' => $courseDate->id,
            'course_unit_id' => $courseDate->course_unit_id,
            'inst_unit_id' => $instUnit->id,
            'created_at' => now(),
            'updated_at' => now(),
            'unit_completed' => false
        ]);

        return [
            'is_student' => true,
            'student_unit_created' => true,
            'student_unit' => $studentUnit,
            'message' => 'Instructor marked as present student'
        ];
    }

    /**
     * Validate if student can access the classroom
     */
    private function validateStudentAccess(User $student, CourseDate $courseDate): array
    {
        // Check enrollment
        $courseAuth = CourseAuth::where('user_id', $student->id)
            ->where('course_id', $courseDate->course_id)
            ->where('active', true)
            ->first();

        if (!$courseAuth) {
            return [
                'success' => false,
                'message' => 'Student not enrolled in this course',
                'code' => 'NOT_ENROLLED'
            ];
        }

        // Check if class is today
        $classDate = Carbon::parse($courseDate->date);
        $today = Carbon::today();

        if (!$classDate->isSameDay($today)) {
            return [
                'success' => false,
                'message' => 'Class is not scheduled for today',
                'code' => 'NOT_TODAY'
            ];
        }

        return [
            'success' => true,
            'course_auth' => $courseAuth
        ];
    }

    /**
     * Get or create InstUnit (for when student arrives before instructor)
     */
    private function getOrCreateInstUnit(CourseDate $courseDate): ?InstUnit
    {
        $instUnit = InstUnit::where('course_date_id', $courseDate->id)->first();

        if (!$instUnit) {
            // Create InstUnit without instructor info (student arrived first)
            $instUnit = InstUnit::create([
                'course_date_id' => $courseDate->id,
                'created_at' => now(),
                // created_by will be null until instructor arrives
            ]);

            Log::info('InstUnit created by student arrival', [
                'inst_unit_id' => $instUnit->id,
                'course_date_id' => $courseDate->id,
                'note' => 'Student arrived before instructor'
            ]);
        }

        return $instUnit;
    }

    /**
     * Get classroom status information
     */
    private function getClassroomStatusInfo(CourseDate $courseDate, ?InstUnit $instUnit): array
    {
        $studentsPresent = StudentUnit::where('course_date_id', $courseDate->id)
            ->whereNull('ejected_at')
            ->count();

        $studentsLeft = StudentUnit::where('course_date_id', $courseDate->id)
            ->whereNotNull('ejected_at')
            ->count();

        return [
            'inst_unit_exists' => $instUnit !== null,
            'instructor_present' => $instUnit && $instUnit->created_by,
            'students_present' => $studentsPresent,
            'students_left' => $studentsLeft,
            'class_started_at' => $instUnit ? $instUnit->created_at : null
        ];
    }

    /**
     * Get student-specific classroom information
     */
    private function getStudentClassroomInfo(User $student, CourseDate $courseDate, ?InstUnit $instUnit): array
    {
        $studentUnit = $this->attendanceService->getStudentAttendance($student, $courseDate);
        
        return [
            'student_unit' => $studentUnit,
            'is_present' => $studentUnit && !$studentUnit->ejected_at,
            'arrival_time' => $studentUnit ? $studentUnit->created_at : null,
            'instructor_present' => $instUnit && $instUnit->created_by,
            'classmates_count' => StudentUnit::where('course_date_id', $courseDate->id)
                ->whereNull('ejected_at')
                ->where('course_auth_id', '!=', $studentUnit->course_auth_id ?? 0)
                ->count()
        ];
    }
}