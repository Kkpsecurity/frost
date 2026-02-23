<?php

declare(strict_types=1);

namespace App\Services\Frost\Students;

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
            // ------------------------------------------------------------------
            // Resolve course_date_id if not supplied directly.
            // Fall back to the instructor's active InstUnit.
            // ------------------------------------------------------------------
            if ($courseDateId === null) {
                $instUnit = \App\Models\InstUnit::whereNull('completed_at')
                    ->where(function ($q) use ($admin) {
                        $q->where('created_by', $admin->id)
                            ->orWhere('assistant_id', $admin->id);
                    })
                    ->orderByDesc('created_at')
                    ->first();

                $courseDateId = $instUnit?->course_date_id ? (int) $instUnit->course_date_id : null;
            }

            if ($courseDateId === null) {
                return [
                    'students' => [],
                    'summary'  => ['total' => 0, 'course_date_id' => null],
                ];
            }

            // ------------------------------------------------------------------
            // Fetch StudentUnit records for this class session.
            // This mirrors the proven approach in getStudentsForCourseDate() and
            // CourseDatesService — StudentUnit is the session-level record that
            // exists for every student who has joined (or been pre-enrolled into)
            // this specific course_date_id.
            // ------------------------------------------------------------------
            $studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDateId)
                ->with(['CourseAuth.User'])
                ->orderBy('created_at', 'asc')
                ->get();

            $now = Carbon::now();

            $students = $studentUnits->map(function ($su) use ($now) {
                $user = $su->CourseAuth?->User ?? null;
                if (!$user) {
                    return null;
                }

                // Status from last activity (updated_at) — same logic as getStudentsForCourseDate()
                $lastActivity = $su->updated_at
                    ? (($su->updated_at instanceof Carbon) ? $su->updated_at : Carbon::parse($su->updated_at))
                    : $now;

                $minutesSince = $lastActivity->diffInMinutes($now);
                $status = match (true) {
                    $minutesSince <= 5  => 'online',
                    $minutesSince <= 15 => 'away',
                    default             => 'offline',
                };

                // Verification flags stored as JSON in `verified` column
                $verified     = false;
                $verifiedData = $su->verified;
                if (is_string($verifiedData)) {
                    $verifiedData = json_decode($verifiedData, true) ?? null;
                }
                if (is_array($verifiedData)) {
                    $verified = (bool) (($verifiedData['id_card_uploaded'] ?? false) && ($verifiedData['headshot_uploaded'] ?? false));
                }

                $fullName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? ''));

                return [
                    'id'               => (int) $su->id,
                    'student_id'       => (int) $user->id,
                    'student_name'     => $fullName !== '' ? $fullName : ($user->email ?? 'Student'),
                    'student_email'    => (string) ($user->email ?? ''),
                    'avatar'           => (string) ($user->avatar ?? ''),
                    'course_auth_id'   => (int) ($su->course_auth_id ?? 0),
                    'student_unit_id'  => (int) $su->id,
                    'status'           => $status,
                    'joined_at'        => $su->created_at ? Carbon::parse($su->created_at)->toAtomString() : null,
                    'verified'         => $verified,
                    'progress_percent' => 0,
                ];
            })->filter()->values();

            return [
                'students' => $students->toArray(),
                'summary'  => [
                    'total'          => $students->count(),
                    'course_date_id' => $courseDateId,
                    'lesson_date'    => null,
                ],
                'metadata' => [
                    'view_type'   => 'instructor_course_students',
                    'last_updated' => now()->format('c'),
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get online students for instructor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error'    => 'Failed to retrieve online students',
                'students' => [],
                'summary'  => [
                    'total'          => 0,
                    'course_date_id' => $courseDateId,
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
            'name',
            'email',
            'course_id',
            'status',
            'enrollment_date',
            'completion_status',
            'attendance_rate'
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
