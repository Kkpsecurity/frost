<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service for managing instructor dashboard data and operations
 * Handles authentication, validation, and dashboard content for instructors
 */
class InstructorDashboardService
{
    /**
     * Validate instructor session for React components
     *
     * @return array
     */
    public function validateSession(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [
                'authenticated' => false,
                'message' => 'Unauthenticated'
            ];
        }

        return [
            'authenticated' => true,
            'instructor' => [
                'id' => $admin->id,
                'fname' => $admin->name ?? 'Admin',
                'lname' => 'User',
                'name' => $admin->name ?? 'Admin User',
                'email' => $admin->email ?? '',
                'role_id' => $admin->role_id ?? null,
                'role_name' => \App\Support\RoleManager::getRoleName($admin->role_id ?? 0),
                'is_sys_admin' => ($admin->role_id === \App\Support\RoleManager::SYS_ADMIN_ID),
            ],
            'course_date' => null, // No active course for admin viewing
            'status' => 'admin_view'
        ];
    }

    /**
     * Get instructor profile data
     *
     * @return array
     */
    public function getInstructorProfile(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [];
        }

        return [
            'id' => $admin->id,
            'name' => $admin->name ?? 'Admin User',
            'email' => $admin->email ?? '',
            'role' => 'administrator',
            'permissions' => [
                'view_all_courses',
                'manage_students',
                'access_bulletin_board',
                'view_course_statistics'
            ],
            'last_login' => $admin->updated_at ?? now(),
            'session_start' => now(),
        ];
    }

    /**
     * Get dashboard metadata
     *
     * @return array
     */
    public function getDashboardMetadata(): array
    {
        return [
            'view_type' => 'admin_instructor_dashboard',
            'permissions' => ['view_all', 'manage_all'],
            'last_updated' => now()->toISOString(),
            'timezone' => config('app.timezone'),
            'version' => '1.0.0'
        ];
    }

    /**
     * Get instructor statistics for dashboard
     *
     * @return array
     */
    public function getInstructorStats(): array
    {
        // Get basic course and student statistics
        $totalCourses = DB::table('courses')->where('is_active', true)->count();
        $totalStudents = DB::table('users')->where('role_id', '>=', 5)->count();
        $activeCourseAuths = DB::table('course_auths')->where('is_active', true)->count();
        $completedCourseAuths = DB::table('course_auths')
            ->where('is_active', false)
            ->where('completed_at', '!=', null)
            ->count();

        // Calculate completion rate
        $totalCourseAuths = $activeCourseAuths + $completedCourseAuths;
        $completionRate = $totalCourseAuths > 0 ? round(($completedCourseAuths / $totalCourseAuths) * 100) : 0;

        // Get pending assignments/grades (placeholder)
        $pendingGrades = 12; // This would come from an assignments table when implemented

        return [
            'stats' => [
                'total_students' => $totalStudents,
                'active_courses' => $totalCourses,
                'completion_rate' => $completionRate,
                'pending_grades' => $pendingGrades,
                'active_course_auths' => $activeCourseAuths,
                'completed_course_auths' => $completedCourseAuths
            ],
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'view_type' => 'instructor_stats'
            ]
        ];
    }

    /**
     * Get completed InstUnits for instructor dashboard
     *
     * @return array
     */
    public function getCompletedInstUnits(): array
    {
        try {
            // Use a simple direct DB query to avoid model issues
            $completedInstUnits = DB::table('inst_unit')
                ->join('course_dates', 'inst_unit.course_date_id', '=', 'course_dates.id')
                ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
                ->join('courses', 'course_units.course_id', '=', 'courses.id')
                ->leftJoin('users', 'inst_unit.completed_by', '=', 'users.id')
                ->whereNotNull('inst_unit.completed_at')
                ->whereNotNull('inst_unit.completed_by')
                ->select([
                    'inst_unit.id',
                    'inst_unit.course_date_id',
                    'inst_unit.completed_at',
                    'course_dates.starts_at',
                    'course_dates.ends_at',
                    'course_units.title as course_unit_title',
                    'course_units.sequence',
                    'courses.title as course_name',
                    'users.name as completed_by_name'
                ])
                ->orderBy('inst_unit.completed_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching completed InstUnits: ' . $e->getMessage());
            return [
                'completed_courses' => [],
                'message' => 'Error loading completed courses: ' . $e->getMessage(),
                'has_completed' => false,
                'metadata' => [
                    'count' => 0,
                    'generated_at' => now()->toISOString(),
                    'error' => $e->getMessage()
                ]
            ];
        }
        if ($completedInstUnits->isEmpty()) {
            return [
                'completed_courses' => [],
                'message' => 'No completed courses found',
                'has_completed' => false,
                'metadata' => [
                    'count' => 0,
                    'generated_at' => now()->toISOString()
                ]
            ];
        }

        $formattedCourses = $completedInstUnits->map(function ($instUnit) {
            try {
                // Get student count for this InstUnit
                $studentCount = DB::table('student_unit')
                    ->where('inst_unit_id', $instUnit->id)
                    ->count();

                return [
                    'id' => $instUnit->id,
                    'course_date_id' => $instUnit->course_date_id,
                    'course_name' => $instUnit->course_name ?? 'Unknown Course',
                    'course_unit_title' => $instUnit->course_unit_title ?? 'Unknown Unit',
                    'sequence' => $instUnit->sequence ?? 0,
                    'completed_at' => $instUnit->completed_at,
                    'completed_by_name' => $instUnit->completed_by_name ?? 'Unknown',
                    'student_count' => $studentCount,
                    'duration' => $instUnit->starts_at && $instUnit->ends_at ?
                        Carbon::parse($instUnit->starts_at)->diff(Carbon::parse($instUnit->ends_at))->format('%h hours %i minutes')
                        : 'Unknown',
                    'course_date' => $instUnit->starts_at ? Carbon::parse($instUnit->starts_at)->format('M j, Y') : 'Unknown',
                    'completion_date' => Carbon::parse($instUnit->completed_at)->format('M j, Y g:i A')
                ];
            } catch (\Exception $e) {
                Log::error('Error formatting InstUnit ' . $instUnit->id . ': ' . $e->getMessage());
                return null; // Skip this record
            }
        })->filter()->values()->toArray(); // Remove null values and reindex

        return [
            'completed_courses' => $formattedCourses,
            'message' => count($formattedCourses) . ' completed courses found',
            'has_completed' => true,
            'metadata' => [
                'count' => count($formattedCourses),
                'generated_at' => now()->toISOString()
            ]
        ];
    }
}
