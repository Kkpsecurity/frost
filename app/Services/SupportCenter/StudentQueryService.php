<?php

namespace App\Services\SupportCenter;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * StudentQueryService - Query operations for student support
 * 
 * Handles searching, retrieving, and gathering statistics for student support operations
 */
class StudentQueryService
{
    /**
     * Search for students by name, email, or student number
     * 
     * @param string $query Search query
     * @param int $limit Maximum results to return
     * @return Collection
     */
    public function searchStudents(string $query, int $limit = 20): Collection
    {
        return User::with(['Role', 'StudentUnits' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->where(function ($q) use ($query) {
                $q->where('fname', 'ILIKE', "%{$query}%")
                    ->orWhere('lname', 'ILIKE', "%{$query}%")
                    ->orWhere('email', 'ILIKE', "%{$query}%")
                    ->orWhere('student_num', 'ILIKE', "%{$query}%")
                    ->orWhereRaw("CONCAT(fname, ' ', lname) ILIKE ?", ["%{$query}%"]);
            })
            ->where('role_id', 6) // Student role
            ->orderByRaw("
                CASE 
                    WHEN fname ILIKE ? THEN 1
                    WHEN lname ILIKE ? THEN 2
                    WHEN email ILIKE ? THEN 3
                    WHEN student_num ILIKE ? THEN 4
                    ELSE 5
                END
            ", ["{$query}%", "{$query}%", "{$query}%", "{$query}%"])
            ->orderBy('lname')
            ->orderBy('fname')
            ->limit($limit)
            ->get();
    }

    /**
     * Get detailed student information
     * 
     * @param int $studentId Student user ID
     * @return User|null
     */
    public function getStudentDetails(int $studentId): ?User
    {
        return User::with([
            'Role',
            'CourseAuths' => function ($q) {
                $q->with(['Course', 'StudentUnits' => function ($qu) {
                    $qu->with('CourseDate')->latest()->limit(10);
                }])
                ->latest();
            },
            'StudentUnits' => function ($q) {
                $q->with(['CourseDate', 'CourseAuth.Course'])
                    ->latest()
                    ->limit(20);
            },
            'Orders' => function ($q) {
                $q->latest()->limit(10);
            }
        ])->find($studentId);
    }

    /**
     * Get student statistics
     * 
     * @param int $studentId Student user ID
     * @return array
     */
    public function getStudentStatistics(int $studentId): array
    {
        // Total courses enrolled
        $totalCourses = DB::table('course_auths')
            ->where('user_id', $studentId)
            ->count();

        // Total attendance days
        $totalAttendance = DB::table('student_units')
            ->join('course_auths', 'student_units.course_auth_id', '=', 'course_auths.id')
            ->where('course_auths.user_id', $studentId)
            ->whereNull('student_units.ejected_at')
            ->count();

        // Total orders
        $totalOrders = DB::table('orders')
            ->where('user_id', $studentId)
            ->count();

        // Total spent
        $totalSpent = DB::table('orders')
            ->where('user_id', $studentId)
            ->where('payment_status', 'approved')
            ->sum('amount');

        // Last login
        $lastLogin = DB::table('users')
            ->where('id', $studentId)
            ->value('last_login_at');

        // Account created
        $accountCreated = DB::table('users')
            ->where('id', $studentId)
            ->value('created_at');

        return [
            'total_courses' => $totalCourses,
            'total_attendance' => $totalAttendance,
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent ? (float) $totalSpent : 0,
            'last_login' => $lastLogin,
            'account_created' => $accountCreated,
        ];
    }
}
