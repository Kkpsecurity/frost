<?php

declare(strict_types=1);

namespace App\Services\Frost\Students;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Backend Student Service for Instructor Management
 *
 * This service handles students from the instructor/admin perspective
 * Different from frontend student service which handles student-facing operations
 */
class BackendStudentService
{
    /**
     * Get students data for instructor dashboard
     *
     * @return array
     */
    public function getStudentsForInstructor(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [
                'error' => 'Unauthenticated',
                'students' => []
            ];
        }

        // Return students data structure for admin viewing
        return [
            'students' => [], // Empty for admin view - will be populated when viewing specific courses
            'summary' => [
                'total_students' => 0,
                'active_students' => 0,
                'pending_enrollments' => 0,
                'completed_courses' => 0
            ],
            'metadata' => [
                'view_type' => 'admin_instructor_students',
                'course_context' => null,
                'last_updated' => now()->format('c')
            ]
        ];
    }

    /**
     * Get online students for instructor (students in today's class)
     *
     * @return array
     */
    public function getOnlineStudentsForInstructor(?int $courseDateId = null): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [
                'error' => 'Unauthenticated',
                'students' => []
            ];
        }

        try {
            $today = Carbon::today()->format('Y-m-d');

            // Find the instructor's active InstUnit for this course date (or for today as a fallback)
            $instUnitQuery = DB::table('inst_unit as iu')
                ->whereNull('iu.completed_at')
                ->where(function ($q) use ($admin) {
                    $q->where('iu.created_by', $admin->id)
                        ->orWhere('iu.assistant_id', $admin->id);
                });

            if ($courseDateId !== null) {
                $instUnitQuery->where('iu.course_date_id', $courseDateId);
            }

            $instUnit = $instUnitQuery->orderByDesc('iu.created_at')->first();

            // Attendance list is made up of StudentUnit records for the course date ("you are here" sessions)
            $studentUnitsQuery = DB::table('student_unit as su')
                ->join('course_auths as ca', 'su.course_auth_id', '=', 'ca.id')
                ->join('users as u', 'ca.user_id', '=', 'u.id');

            if ($courseDateId !== null) {
                $studentUnitsQuery->where('su.course_date_id', $courseDateId);
            } elseif ($instUnit && isset($instUnit->course_date_id)) {
                $studentUnitsQuery->where('su.course_date_id', (int) $instUnit->course_date_id);
            } else {
                // Last-resort fallback; keeps endpoint from exploding if called without params
                $studentUnitsQuery->whereDate('su.created_at', $today);
            }

            // If we found an InstUnit, prefer matching its id, but allow NULL for legacy/incomplete records
            if ($instUnit && isset($instUnit->id)) {
                $studentUnitsQuery->where(function ($q) use ($instUnit) {
                    $q->where('su.inst_unit_id', (int) $instUnit->id)
                        ->orWhere('su.inst_unit_id', 0)
                        ->orWhereNull('su.inst_unit_id');
                });
            }

            $studentUnits = $studentUnitsQuery
                ->select([
                    'u.id as student_id',
                    'u.fname as student_fname',
                    'u.lname as student_lname',
                    'u.email as student_email',
                    'u.avatar as avatar',
                    'su.id as student_unit_id',
                    'su.created_at as joined_at',
                    'su.last_heartbeat_at as last_heartbeat_at',
                    'su.session_expires_at as session_expires_at',
                    'su.left_at as left_at',
                    'su.completed_at as completed_at',
                    'su.ejected_at as ejected_at',
                    'su.verified as verified_json'
                ])
                ->orderBy('u.lname')
                ->orderBy('u.fname')
                ->get();

            $now = Carbon::now();
            $students = $studentUnits->map(function ($row) use ($now) {
                $fullName = trim((string) ($row->student_fname ?? '') . ' ' . (string) ($row->student_lname ?? ''));
                $displayName = $fullName !== '' ? $fullName : (string) ($row->student_email ?? 'Student');

                // Determine "online/away/offline" from heartbeat + leave/eject markers
                $status = 'offline';
                $leftAt = $row->left_at ? Carbon::parse($row->left_at) : null;
                $ejectedAt = $row->ejected_at ? Carbon::parse($row->ejected_at) : null;
                $completedAt = $row->completed_at ? Carbon::parse($row->completed_at) : null;
                $heartbeatAt = $row->last_heartbeat_at ? Carbon::parse($row->last_heartbeat_at) : null;

                $isDisconnected = $leftAt !== null || $ejectedAt !== null || $completedAt !== null;
                if (!$isDisconnected && $heartbeatAt) {
                    $seconds = $heartbeatAt->diffInSeconds($now);
                    if ($seconds <= 90) {
                        $status = 'online';
                    } elseif ($seconds <= 600) {
                        $status = 'away';
                    }
                }

                // Verification: interpret JSON flags if present
                $verified = false;
                $verifiedData = $row->verified_json;
                if (is_string($verifiedData)) {
                    $decoded = json_decode($verifiedData, true);
                    $verifiedData = $decoded ?? null;
                }
                if (is_array($verifiedData)) {
                    $verified = (bool) (($verifiedData['id_card_uploaded'] ?? false) && ($verifiedData['headshot_uploaded'] ?? false));
                }

                return [
                    'id' => (int) $row->student_unit_id,
                    'student_id' => (int) $row->student_id,
                    'student_name' => $displayName,
                    'student_email' => (string) ($row->student_email ?? ''),
                    'avatar' => (string) ($row->avatar ?? ''),
                    'status' => $status,
                    'joined_at' => $row->joined_at ? Carbon::parse($row->joined_at)->toAtomString() : null,
                    'verified' => $verified,
                    'progress_percent' => 0,
                ];
            });

            return [
                'students' => $students->toArray(),
                'summary' => [
                    'total' => $students->count(),
                    'course_date_id' => $courseDateId,
                    'lesson_date' => $courseDateId === null ? $today : null,
                ],
                'metadata' => [
                    'view_type' => 'instructor_course_students',
                    'query_date' => $today,
                    'last_updated' => now()->format('c'),
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get online students for instructor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'Failed to retrieve online students',
                'students' => [],
                'summary' => [
                    'total' => 0,
                    'course_date_id' => $courseDateId,
                    'lesson_date' => $today
                ]
            ];
        }
    }

    /**
     * Get students enrolled in a specific course
     *
     * @param int $courseId
     * @return array
     */
    public function getStudentsByCourse(int $courseId): array
    {
        // This would query the enrollments/students tables
        // For now, return structure for future implementation

        return [
            'course_id' => $courseId,
            'students' => [],
            'enrollment_stats' => [
                'total_enrolled' => 0,
                'active_students' => 0,
                'completed_students' => 0,
                'dropped_students' => 0,
                'pending_students' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'course_students',
                'course_context' => $courseId
            ]
        ];
    }

    /**
     * Get student progress and performance data
     *
     * @param int $studentId
     * @param int|null $courseId
     * @return array
     */
    public function getStudentProgress(int $studentId, ?int $courseId = null): array
    {
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'progress' => [
                'completion_percentage' => 0,
                'lessons_completed' => 0,
                'total_lessons' => 0,
                'assignments_completed' => 0,
                'total_assignments' => 0,
                'attendance_rate' => 0,
                'grade_average' => 0
            ],
            'recent_activity' => [],
            'alerts' => [],
            'metadata' => [
                'last_updated' => now()->format('c'),
                'data_source' => 'student_progress_tracking'
            ]
        ];
    }

    /**
     * Get student attendance records
     *
     * @param int $studentId
     * @param int|null $courseId
     * @return array
     */
    public function getStudentAttendance(int $studentId, ?int $courseId = null): array
    {
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'attendance_records' => [],
            'summary' => [
                'total_sessions' => 0,
                'attended_sessions' => 0,
                'missed_sessions' => 0,
                'attendance_percentage' => 0,
                'tardiness_count' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'student_attendance'
            ]
        ];
    }

    /**
     * Get students requiring instructor attention
     *
     * @return array
     */
    public function getStudentsRequiringAttention(): array
    {
        return [
            'urgent_attention' => [],
            'moderate_attention' => [],
            'categories' => [
                'poor_attendance' => [],
                'failing_grades' => [],
                'overdue_assignments' => [],
                'behavioral_issues' => [],
                'technical_difficulties' => []
            ],
            'summary' => [
                'total_flagged' => 0,
                'urgent_count' => 0,
                'moderate_count' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'attention_required_students'
            ]
        ];
    }

    /**
     * Get student communication history
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentCommunicationHistory(int $studentId): array
    {
        return [
            'student_id' => $studentId,
            'communications' => [],
            'summary' => [
                'total_messages' => 0,
                'unread_messages' => 0,
                'last_contact' => null,
                'response_rate' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'student_communications'
            ]
        ];
    }

    /**
     * Get student enrollment history and status
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentEnrollmentHistory(int $studentId): array
    {
        return [
            'student_id' => $studentId,
            'current_enrollments' => [],
            'completed_courses' => [],
            'dropped_courses' => [],
            'pending_enrollments' => [],
            'summary' => [
                'total_enrollments' => 0,
                'active_enrollments' => 0,
                'completed_courses' => 0,
                'success_rate' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'enrollment_history'
            ]
        ];
    }

    /**
     * Search and filter students
     *
     * @param array $filters
     * @return array
     */
    public function searchStudents(array $filters = []): array
    {
        $allowedFilters = [
            'name', 'email', 'course_id', 'status',
            'enrollment_date', 'completion_status', 'attendance_rate'
        ];

        $validFilters = array_intersect_key($filters, array_flip($allowedFilters));

        return [
            'filters_applied' => $validFilters,
            'students' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 25,
                'total_records' => 0,
                'total_pages' => 0
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'student_search_results'
            ]
        ];
    }
}
