<?php

namespace App\Services;

use App\Models\StudentUnit;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Attendance Service
 *
 * Manages student attendance by creating StudentUnit records when students
 * first arrive at a class. This service handles the logic of checking for
 * existing attendance and creating new attendance records.
 */
class AttendanceService
{
    /**
     * Mark student as present by creating a StudentUnit record
     *
     * @param User $student The student user
     * @param CourseDate $courseDate The course date/class session
     * @return array Result with success status and data
     */
    public function markStudentPresent(User $student, CourseDate $courseDate): array
    {
        try {
            // Check if student is authorized for this course
            $courseAuth = $this->getCourseAuth($student, $courseDate);
            if (!$courseAuth) {
                return [
                    'success' => false,
                    'message' => 'Student is not enrolled in this course',
                    'code' => 'NOT_ENROLLED'
                ];
            }

            // Check if student is already marked present for this class
            $existingAttendance = $this->getExistingAttendance($courseAuth, $courseDate);
            if ($existingAttendance) {
                // Check if onboarding was completed
                $onboardingCompleted = $existingAttendance->onboarding_completed ?? false;

                return [
                    'success' => true,
                    'message' => $onboardingCompleted ? 'Already in session' : 'Continue onboarding',
                    'data' => $existingAttendance,
                    'code' => 'ALREADY_PRESENT',
                    'redirect' => $onboardingCompleted ? '/classroom' : '/classroom/onboarding/' . $existingAttendance->id,
                    'student_unit_id' => $existingAttendance->id,
                    'onboarding_required' => !$onboardingCompleted
                ];
            }

            // Get or create InstUnit for this class session
            $instUnit = $this->getOrCreateInstUnit($courseDate);
            if (!$instUnit) {
                return [
                    'success' => false,
                    'message' => 'Class session not available',
                    'code' => 'NO_INST_UNIT'
                ];
            }

            // Create StudentUnit record for attendance
            $studentUnit = $this->createStudentUnit($courseAuth, $courseDate, $instUnit);

            Log::info('Student attendance marked', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'course_date_id' => $courseDate->id,
                'course_name' => $courseDate->GetCourse()->name,
                'student_unit_id' => $studentUnit->id,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => 'StudentUnit created - redirecting to onboarding',
                'data' => $studentUnit,
                'code' => 'ATTENDANCE_RECORDED',
                'redirect' => '/classroom/onboarding/' . $studentUnit->id,
                'student_unit_id' => $studentUnit->id,
                'onboarding_required' => true
            ];

        } catch (Exception $e) {
            Log::error('Failed to mark student attendance', [
                'student_id' => $student->id ?? null,
                'course_date_id' => $courseDate->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage(),
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Check if student is already present for a class
     *
     * @param User $student
     * @param CourseDate $courseDate
     * @return bool
     */
    public function isStudentPresent(User $student, CourseDate $courseDate): bool
    {
        $courseAuth = $this->getCourseAuth($student, $courseDate);
        if (!$courseAuth) {
            return false;
        }

        return $this->getExistingAttendance($courseAuth, $courseDate) !== null;
    }

    /**
     * Get attendance record for a student in a specific class
     *
     * @param User $student
     * @param CourseDate $courseDate
     * @return StudentUnit|null
     */
    public function getStudentAttendance(User $student, CourseDate $courseDate): ?StudentUnit
    {
        $courseAuth = $this->getCourseAuth($student, $courseDate);
        if (!$courseAuth) {
            return null;
        }

        return $this->getExistingAttendance($courseAuth, $courseDate);
    }

    /**
     * Handle student entry to class with attendance tracking
     *
     * @param User $student
     * @param int $courseDateId
     * @return array
     */
    public function enterClass(User $student, int $courseDateId): array
    {
        try {
            // 1. Record attendance using existing method
            $attendanceResult = $this->handleStudentArrival($student, $courseDateId);

            if (!$attendanceResult['success']) {
                return $attendanceResult;
            }

            // 2. Get CourseDate and CourseAuth for tracking
            $courseDate = CourseDate::find($courseDateId);
            $courseAuth = CourseAuth::where('user_id', $student->id)
                ->where('course_id', $courseDate->course_id ?? 0)
                ->where('active', true)
                ->first();

            // 3. Get student attendance details for dashboard
            $attendanceDetails = $this->getStudentAttendanceDetails($student, $courseDate);

            Log::info('Student class entry processed', [
                'student_id' => $student->id,
                'course_date_id' => $courseDateId,
                'attendance_code' => $attendanceResult['code'],
                'entry_time' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Successfully entered class',
                'data' => [
                    'attendance' => $attendanceResult,
                    'student_details' => $attendanceDetails,
                    'entry_timestamp' => now()->toISOString()
                ],
                'code' => 'CLASS_ENTERED'
            ];

        } catch (Exception $e) {
            Log::error('Student class entry failed', [
                'student_id' => $student->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to enter class: ' . $e->getMessage(),
                'code' => 'ENTRY_FAILED'
            ];
        }
    }

    /**
     * Get student attendance details for dashboard display
     *
     * @param User $student
     * @param CourseDate $courseDate
     * @return array
     */
    public function getStudentAttendanceDetails(User $student, CourseDate $courseDate): array
    {
        try {
            $studentUnit = $this->getStudentAttendance($student, $courseDate);

            if (!$studentUnit) {
                return [
                    'is_present' => false,
                    'entry_time' => null,
                    'attendance_status' => 'not_present',
                    'class_info' => $this->getClassInfo($courseDate)
                ];
            }

            $entryTime = Carbon::createFromTimestamp($studentUnit->created_at);

            return [
                'is_present' => !$studentUnit->ejected_at,
                'entry_time' => $entryTime->format('g:i A'),
                'entry_time_full' => $entryTime->format('Y-m-d H:i:s'),
                'entry_time_relative' => $entryTime->diffForHumans(),
                'attendance_status' => $studentUnit->ejected_at ? 'left' : 'present',
                'ejected_at' => $studentUnit->ejected_at ? Carbon::parse($studentUnit->ejected_at)->format('g:i A') : null,
                'ejected_reason' => $studentUnit->ejected_for,
                'student_unit_id' => $studentUnit->id,
                'class_info' => $this->getClassInfo($courseDate),
                'session_duration' => $this->calculateSessionDuration($studentUnit)
            ];

        } catch (Exception $e) {
            Log::error('Failed to get student attendance details', [
                'student_id' => $student->id,
                'course_date_id' => $courseDate->id,
                'error' => $e->getMessage()
            ]);

            return [
                'is_present' => false,
                'entry_time' => null,
                'attendance_status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get student dashboard attendance data
     *
     * @param User $student
     * @return array
     */
    public function getDashboardData(User $student): array
    {
        try {
            $activeAttendance = $this->getActiveClassAttendance($student);
            $recentHistory = $this->getAttendanceHistory($student, 3);

            // Get current session info if present in any class
            $currentSession = null;
            if ($activeAttendance['success'] && $activeAttendance['present_in'] > 0) {
                $presentClass = collect($activeAttendance['active_classes'])
                    ->where('attendance.is_present', true)
                    ->first();

                if ($presentClass) {
                    $currentSession = [
                        'class_name' => $presentClass['course_title'],
                        'entry_time' => $presentClass['attendance']['entry_time'],
                        'entry_time_relative' => $presentClass['attendance']['entry_time_relative'],
                        'session_duration' => $presentClass['attendance']['session_duration'],
                        'course_date_id' => $presentClass['course_date_id']
                    ];
                }
            }

            return [
                'success' => true,
                'current_session' => $currentSession,
                'today_classes' => $activeAttendance['active_classes'] ?? [],
                'present_in_classes' => $activeAttendance['present_in'] ?? 0,
                'total_today_classes' => $activeAttendance['total_classes'] ?? 0,
                'recent_history' => array_slice($recentHistory['history'] ?? [], 0, 5),
                'attendance_rate' => $recentHistory['completion_rate'] ?? 0
            ];

        } catch (Exception $e) {
            Log::error('Failed to get dashboard attendance data', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'current_session' => null,
                'today_classes' => [],
                'present_in_classes' => 0
            ];
        }
    }

    /**
     * Get student attendance status for current active classes
     *
     * @param User $student
     * @return array
     */
    public function getActiveClassAttendance(User $student): array
    {
        try {
            $today = Carbon::today();

            // Get today's course dates for the student
            $courseAuths = CourseAuth::where('user_id', $student->id)
                ->where('active', true)
                ->get();

            $activeClasses = [];

            foreach ($courseAuths as $courseAuth) {
                $todayCourseDates = CourseDate::where('course_id', $courseAuth->course_id)
                    ->whereDate('starts_at', $today)
                    ->get();

                foreach ($todayCourseDates as $courseDate) {
                    $attendanceDetails = $this->getStudentAttendanceDetails($student, $courseDate);

                    $activeClasses[] = [
                        'course_date_id' => $courseDate->id,
                        'course_auth_id' => $courseAuth->id,
                        'course_title' => $courseAuth->GetCourse()->name ?? 'Unknown Course',
                        'class_time' => Carbon::parse($courseDate->starts_at)->format('g:i A'),
                        'attendance' => $attendanceDetails
                    ];
                }
            }

            return [
                'success' => true,
                'active_classes' => $activeClasses,
                'total_classes' => count($activeClasses),
                'present_in' => collect($activeClasses)->where('attendance.is_present', true)->count()
            ];

        } catch (Exception $e) {
            Log::error('Failed to get active class attendance', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'active_classes' => []
            ];
        }
    }

    /**
     * Get student attendance history
     *
     * @param User $student
     * @param int $days
     * @return array
     */
    public function getAttendanceHistory(User $student, int $days = 7): array
    {
        try {
            $startDate = Carbon::now()->subDays($days);

            $attendanceRecords = StudentUnit::whereHas('CourseAuth', function($query) use ($student) {
                    $query->where('user_id', $student->id);
                })
                ->where('created_at', '>=', $startDate->timestamp)
                ->with(['CourseDate', 'CourseAuth'])
                ->orderBy('created_at', 'desc')
                ->get();

            $history = $attendanceRecords->map(function($studentUnit) {
                $entryTime = Carbon::createFromTimestamp($studentUnit->created_at);

                return [
                    'date' => $entryTime->format('Y-m-d'),
                    'course_name' => $studentUnit->CourseAuth->GetCourse()->name ?? 'Unknown',
                    'entry_time' => $entryTime->format('g:i A'),
                    'status' => $studentUnit->ejected_at ? 'left' : 'completed',
                    'duration' => $this->calculateSessionDuration($studentUnit),
                    'ejected_reason' => $studentUnit->ejected_for
                ];
            });

            return [
                'success' => true,
                'history' => $history->toArray(),
                'period_days' => $days,
                'total_sessions' => $history->count(),
                'completion_rate' => $this->calculateCompletionRate($history)
            ];

        } catch (Exception $e) {
            Log::error('Failed to get attendance history', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'history' => []
            ];
        }
    }

    /**
     * Mark student as left/ejected from class
     *
     * @param User $student
     * @param CourseDate $courseDate
     * @param string|null $reason
     * @return array
     */
    public function markStudentLeft(User $student, CourseDate $courseDate, string $reason = null): array
    {
        try {
            $studentUnit = $this->getStudentAttendance($student, $courseDate);

            if (!$studentUnit) {
                return [
                    'success' => false,
                    'message' => 'Student attendance record not found',
                    'code' => 'NO_ATTENDANCE'
                ];
            }

            $studentUnit->ejected_at = now();
            if ($reason) {
                $studentUnit->ejected_for = $reason;
            }
            $studentUnit->save();

            Log::info('Student marked as left class', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'course_date_id' => $courseDate->id,
                'reason' => $reason,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Student marked as left',
                'data' => $studentUnit,
                'code' => 'STUDENT_LEFT'
            ];

        } catch (Exception $e) {
            Log::error('Failed to mark student as left', [
                'student_id' => $student->id,
                'course_date_id' => $courseDate->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update attendance: ' . $e->getMessage(),
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Get attendance statistics for a course date
     *
     * @param CourseDate $courseDate
     * @return array
     */
    public function getAttendanceStats(CourseDate $courseDate): array
    {
        $totalEnrolled = CourseAuth::where('course_id', $courseDate->course_id)
            ->where('active', true)
            ->count();

        $totalPresent = StudentUnit::where('course_date_id', $courseDate->id)
            ->whereNull('ejected_at')
            ->count();

        $totalLeft = StudentUnit::where('course_date_id', $courseDate->id)
            ->whereNotNull('ejected_at')
            ->count();

        return [
            'total_enrolled' => $totalEnrolled,
            'total_present' => $totalPresent,
            'total_left' => $totalLeft,
            'attendance_rate' => $totalEnrolled > 0 ? round(($totalPresent / $totalEnrolled) * 100, 2) : 0
        ];
    }

    /**
     * Get list of students present in a class
     *
     * @param CourseDate $courseDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPresentStudents(CourseDate $courseDate)
    {
        return StudentUnit::where('course_date_id', $courseDate->id)
            ->whereNull('ejected_at')
            ->with(['CourseAuth.User', 'CourseUnit'])
            ->get();
    }

    // Private helper methods

    /**
     * Get CourseAuth for student and course
     */
    private function getCourseAuth(User $student, CourseDate $courseDate): ?CourseAuth
    {
        // Get course ID through CourseDate -> CourseUnit -> Course relationship
        $courseId = $courseDate->GetCourseUnit()->course_id;

        return $student->ActiveCourseAuths()
            ->whereHas('Course', function($query) use ($courseId) {
                $query->where('id', $courseId);
            })
            ->first();
    }

    /**
     * Check for existing attendance record
     */
    private function getExistingAttendance(CourseAuth $courseAuth, CourseDate $courseDate): ?StudentUnit
    {
        return StudentUnit::where('course_auth_id', $courseAuth->id)
            ->where('course_date_id', $courseDate->id)
            ->first();
    }

    /**
     * Get or create InstUnit for the class session
     */
    private function getOrCreateInstUnit(CourseDate $courseDate): ?InstUnit
    {
        // First try to find existing InstUnit for this CourseDate
        $instUnit = InstUnit::where('course_date_id', $courseDate->id)->first();

        if (!$instUnit) {
            // Create new InstUnit if none exists
            $instUnit = InstUnit::create([
                'course_date_id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $instUnit;
    }

    /**
     * Create StudentUnit record for attendance
     */
    private function createStudentUnit(CourseAuth $courseAuth, CourseDate $courseDate, InstUnit $instUnit, string $attendanceType = 'online'): StudentUnit
    {
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
     * Handle first-time student arrival hook
     * This is the main method to call when a student first accesses a class
     *
     * @param User $student
     * @param int $courseDateId
     * @return array
     */
    public function handleStudentArrival(User $student, int $courseDateId): array
    {
        try {
            $courseDate = CourseDate::find($courseDateId);

            if (!$courseDate) {
                return [
                    'success' => false,
                    'message' => 'Class session not found',
                    'code' => 'INVALID_COURSE_DATE'
                ];
            }

            // Check if class is today or in progress
            $classDate = Carbon::parse($courseDate->date);
            $today = Carbon::today();

            if (!$classDate->isSameDay($today)) {
                return [
                    'success' => false,
                    'message' => 'This class is not scheduled for today',
                    'code' => 'NOT_TODAY'
                ];
            }

            return $this->markStudentPresent($student, $courseDate);

        } catch (Exception $e) {
            Log::error('Student arrival handling failed', [
                'student_id' => $student->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process student arrival',
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Handle student lesson start - This triggers the attendance session
     *
     * Business Rule: Sessions only start when student begins a lesson,
     * not just because they're physically present in class.
     *
     * @param User $student The student user
     * @param int $courseDateId The course date ID
     * @param string $attendanceType 'online' or 'offline'
     * @param array $metadata Additional metadata
     * @return array Result with success status and data
     */
    public function handleLessonStart(User $student, int $courseDateId, string $attendanceType = 'online', array $metadata = []): array
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

            // Check if student is authorized for this course
            $courseAuth = $this->getCourseAuth($student, $courseDate);
            if (!$courseAuth) {
                return [
                    'success' => false,
                    'message' => 'Student is not enrolled in this course',
                    'code' => 'NOT_ENROLLED'
                ];
            }

            // Check if student already has an attendance session for this class
            $existingAttendance = $this->getExistingAttendance($courseAuth, $courseDate);
            if ($existingAttendance) {
                return [
                    'success' => true,
                    'message' => 'Attendance session already active',
                    'data' => $existingAttendance,
                    'code' => 'SESSION_ACTIVE'
                ];
            }

            // Get or create InstUnit
            $instUnit = $this->getOrCreateInstUnit($courseDate);
            if (!$instUnit) {
                return [
                    'success' => false,
                    'message' => 'Could not create or find instructor unit',
                    'code' => 'NO_INST_UNIT'
                ];
            }

            // Create StudentUnit record - THIS IS WHERE THE SESSION STARTS
            $studentUnit = $this->createStudentUnit($courseAuth, $courseDate, $instUnit, $attendanceType);

            Log::info('Student lesson started - Attendance session created', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'course_date_id' => $courseDate->id,
                'course_name' => $courseDate->GetCourse()->name,
                'student_unit_id' => $studentUnit->id,
                'attendance_type' => $attendanceType,
                'session_trigger' => 'lesson_start',
                'metadata' => $metadata,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Attendance session started successfully',
                'data' => $studentUnit,
                'code' => 'SESSION_STARTED'
            ];

        } catch (Exception $e) {
            Log::error('Failed to start lesson attendance session', [
                'student_id' => $student->id ?? null,
                'course_date_id' => $courseDateId,
                'attendance_type' => $attendanceType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to start attendance session: ' . $e->getMessage(),
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Record offline attendance (physical classroom presence)
     *
     * NOTE: This method is for instructor use to manually mark students present.
     * The actual attendance session still starts when student begins a lesson.
     *
     * @param User $student The student user
     * @param int $courseDateId The course date ID
     * @param array $metadata Additional metadata for offline attendance
     * @return array Result with success status and data
     */
    public function recordOfflineAttendance(User $student, int $courseDateId, array $metadata = []): array
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

            // Check if student is authorized for this course
            $courseAuth = $this->getCourseAuth($student, $courseDate);
            if (!$courseAuth) {
                return [
                    'success' => false,
                    'message' => 'Student is not enrolled in this course',
                    'code' => 'NOT_ENROLLED'
                ];
            }

            // Check if student is already marked present for this class
            $existingAttendance = $this->getExistingAttendance($courseAuth, $courseDate);
            if ($existingAttendance) {
                return [
                    'success' => true,
                    'message' => 'Student already marked present',
                    'data' => $existingAttendance,
                    'code' => 'ALREADY_PRESENT'
                ];
            }

            // Get or create InstUnit (instructor may not be online for offline classes)
            $instUnit = $this->getOrCreateInstUnit($courseDate);
            if (!$instUnit) {
                return [
                    'success' => false,
                    'message' => 'Could not create or find instructor unit',
                    'code' => 'NO_INST_UNIT'
                ];
            }

            // Create StudentUnit record for offline attendance
            $studentUnit = $this->createStudentUnit($courseAuth, $courseDate, $instUnit, 'offline');

            Log::info('Offline student attendance recorded', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'course_date_id' => $courseDate->id,
                'course_name' => $courseDate->GetCourse()->name,
                'student_unit_id' => $studentUnit->id,
                'attendance_type' => 'offline',
                'metadata' => $metadata,
                'timestamp' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Offline student attendance recorded successfully',
                'data' => $studentUnit,
                'code' => 'OFFLINE_ATTENDANCE_RECORDED'
            ];

        } catch (Exception $e) {
            Log::error('Failed to record offline attendance', [
                'student_id' => $student->id ?? null,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to record offline attendance: ' . $e->getMessage(),
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Get class information
     */
    private function getClassInfo(CourseDate $courseDate): array
    {
        $startTime = Carbon::parse($courseDate->starts_at);
        $endTime = Carbon::parse($courseDate->ends_at);

        return [
            'start_time' => $startTime->format('g:i A'),
            'end_time' => $endTime->format('g:i A'),
            'duration_hours' => $startTime->floatDiffInHours($endTime),
            'is_current' => $startTime->isPast() && $endTime->isFuture(),
            'status' => $this->getClassStatus($startTime, $endTime)
        ];
    }

    /**
     * Get class status based on time
     */
    private function getClassStatus(Carbon $startTime, Carbon $endTime): string
    {
        $now = Carbon::now();

        if ($now->isBefore($startTime)) {
            return 'upcoming';
        } elseif ($now->isBetween($startTime, $endTime)) {
            return 'active';
        } else {
            return 'completed';
        }
    }

    /**
     * Calculate session duration
     */
    private function calculateSessionDuration(StudentUnit $studentUnit): array
    {
        $startTime = Carbon::createFromTimestamp($studentUnit->created_at);
        $endTime = $studentUnit->ejected_at ?
            Carbon::parse($studentUnit->ejected_at) :
            Carbon::now();

        $durationMinutes = $startTime->diffInMinutes($endTime);

        return [
            'minutes' => $durationMinutes,
            'hours' => round($durationMinutes / 60, 1),
            'formatted' => $this->formatDuration($durationMinutes)
        ];
    }

    /**
     * Format duration for display
     */
    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$remainingMinutes}m";
    }

    /**
     * Calculate completion rate from history
     */
    private function calculateCompletionRate($history): float
    {
        if ($history->isEmpty()) {
            return 0;
        }

        $completed = $history->where('status', 'completed')->count();
        return round(($completed / $history->count()) * 100, 1);
    }
}
