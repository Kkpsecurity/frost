<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Admin Reports Controller
 *
 * Comprehensive reporting system for FROST Online Security Training
 * Provides financial, student, course, instructor, and operational analytics
 */
class ReportsController extends Controller
{
    /**
     * Display the main reports dashboard
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.reports.index', [
            'pageTitle' => 'Reports & Analytics',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Reports', 'url' => null]
            ]
        ]);
    }

    /**
     * Get financial reports data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFinancialReports(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            // Revenue by month
            $monthlyRevenue = DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->whereBetween('enrollments.created_at', [$startDate, $endDate])
                ->where('enrollments.status', 'active')
                ->selectRaw('DATE_TRUNC(\'month\', enrollments.created_at) as month')
                ->selectRaw('COUNT(*) as enrollments')
                ->selectRaw('SUM(courses.price) as revenue')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Top revenue courses
            $topCourses = DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->whereBetween('enrollments.created_at', [$startDate, $endDate])
                ->where('enrollments.status', 'active')
                ->select('courses.name', 'courses.code')
                ->selectRaw('COUNT(*) as enrollments')
                ->selectRaw('SUM(courses.price) as revenue')
                ->groupBy('courses.id', 'courses.name', 'courses.code')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();

            // Summary statistics
            $totalRevenue = $monthlyRevenue->sum('revenue');
            $totalEnrollments = $monthlyRevenue->sum('enrollments');
            $avgRevenuePerEnrollment = $totalEnrollments > 0 ? $totalRevenue / $totalEnrollments : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'monthly_revenue' => $monthlyRevenue,
                    'top_courses' => $topCourses,
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_enrollments' => $totalEnrollments,
                        'avg_revenue_per_enrollment' => round($avgRevenuePerEnrollment, 2)
                    ],
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Financial Reports Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate financial reports'
            ], 500);
        }
    }

    /**
     * Get student analytics reports
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStudentReports(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            // New students by month
            $newStudents = DB::table('users')
                ->where('role_id', 5) // Student role
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE_TRUNC(\'month\', created_at) as month')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Active students
            $activeStudents = DB::table('student_lessons')
                ->join('users', 'student_lessons.student_id', '=', 'users.id')
                ->whereBetween('student_lessons.updated_at', [$startDate, $endDate])
                ->distinct('student_lessons.student_id')
                ->count();

            // Completion rates
            $completionStats = DB::table('student_lessons')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->selectRaw('COUNT(*) as total_lessons')
                ->selectRaw('SUM(CASE WHEN status = \'completed\' THEN 1 ELSE 0 END) as completed_lessons')
                ->first();

            $completionRate = $completionStats->total_lessons > 0
                ? round(($completionStats->completed_lessons / $completionStats->total_lessons) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'new_students_by_month' => $newStudents,
                    'active_students' => $activeStudents,
                    'completion_rate' => $completionRate,
                    'total_lessons' => $completionStats->total_lessons,
                    'completed_lessons' => $completionStats->completed_lessons,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Student Reports Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate student reports'
            ], 500);
        }
    }

    /**
     * Get course performance reports
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCourseReports(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            // Course enrollment trends
            $courseEnrollments = DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->whereBetween('enrollments.created_at', [$startDate, $endDate])
                ->select('courses.name', 'courses.code')
                ->selectRaw('COUNT(*) as enrollments')
                ->groupBy('courses.id', 'courses.name', 'courses.code')
                ->orderByDesc('enrollments')
                ->get();

            // Course completion rates
            $completionByUnit = DB::table('student_units')
                ->join('inst_units', 'student_units.inst_unit_id', '=', 'inst_units.id')
                ->join('courses', 'inst_units.course_id', '=', 'courses.id')
                ->whereBetween('student_units.updated_at', [$startDate, $endDate])
                ->select('courses.name', 'courses.code')
                ->selectRaw('COUNT(*) as total_students')
                ->selectRaw('SUM(CASE WHEN student_units.completed = true THEN 1 ELSE 0 END) as completed_students')
                ->groupBy('courses.id', 'courses.name', 'courses.code')
                ->get()
                ->map(function ($item) {
                    $item->completion_rate = $item->total_students > 0
                        ? round(($item->completed_students / $item->total_students) * 100, 2)
                        : 0;
                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'course_enrollments' => $courseEnrollments,
                    'completion_by_course' => $completionByUnit,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Course Reports Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate course reports'
            ], 500);
        }
    }

    /**
     * Get instructor performance reports
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInstructorReports(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            // Classes taught by instructor
            $instructorClasses = DB::table('course_dates')
                ->join('users', 'course_dates.instructor_id', '=', 'users.id')
                ->join('inst_units', 'course_dates.inst_unit_id', '=', 'inst_units.id')
                ->join('courses', 'inst_units.course_id', '=', 'courses.id')
                ->whereBetween('course_dates.class_date', [$startDate, $endDate])
                ->select(
                    'users.id as instructor_id',
                    DB::raw('CONCAT(users.fname, \' \', users.lname) as instructor_name')
                )
                ->selectRaw('COUNT(DISTINCT course_dates.id) as total_classes')
                ->selectRaw('COUNT(DISTINCT courses.id) as unique_courses')
                ->groupBy('users.id', 'users.fname', 'users.lname')
                ->orderByDesc('total_classes')
                ->get();

            // Student attendance by instructor
            $instructorAttendance = DB::table('course_dates')
                ->join('users', 'course_dates.instructor_id', '=', 'users.id')
                ->join('student_lessons', 'course_dates.id', '=', 'student_lessons.course_date_id')
                ->whereBetween('course_dates.class_date', [$startDate, $endDate])
                ->select(
                    'users.id as instructor_id',
                    DB::raw('CONCAT(users.fname, \' \', users.lname) as instructor_name')
                )
                ->selectRaw('COUNT(*) as total_students')
                ->selectRaw('SUM(CASE WHEN student_lessons.attended = true THEN 1 ELSE 0 END) as students_attended')
                ->groupBy('users.id', 'users.fname', 'users.lname')
                ->get()
                ->map(function ($item) {
                    $item->attendance_rate = $item->total_students > 0
                        ? round(($item->students_attended / $item->total_students) * 100, 2)
                        : 0;
                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'instructor_classes' => $instructorClasses,
                    'instructor_attendance' => $instructorAttendance,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Instructor Reports Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate instructor reports'
            ], 500);
        }
    }

    /**
     * Get operational analytics reports
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOperationalReports(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            // Platform usage stats
            $dailyActiveUsers = DB::table('sessions')
                ->whereBetween('last_activity', [$startDate, $endDate])
                ->selectRaw('DATE(to_timestamp(last_activity)) as date')
                ->selectRaw('COUNT(DISTINCT user_id) as active_users')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // System health
            $systemHealth = [
                'total_users' => DB::table('users')->where('is_active', true)->count(),
                'total_courses' => DB::table('courses')->where('is_active', true)->count(),
                'active_enrollments' => DB::table('enrollments')->where('status', 'active')->count(),
                'scheduled_classes' => DB::table('course_dates')
                    ->where('class_date', '>=', Carbon::now())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'daily_active_users' => $dailyActiveUsers,
                    'system_health' => $systemHealth,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Operational Reports Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate operational reports'
            ], 500);
        }
    }

    /**
     * Get weekly enrollment and sales data for charts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWeeklyData(Request $request): JsonResponse
    {
        try {
            $weeksBack = $request->input('weeks', 8);
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subWeeks($weeksBack);

            // Get course IDs for D and G classes
            $classDCourseIds = DB::table('courses')
                ->where('is_active', true)
                ->where(function($query) {
                    $query->where('title', 'LIKE', '%Class D%')
                          ->orWhere('title', 'LIKE', '%Class \'D\'%')
                          ->orWhere('title', 'LIKE', '%D40%')
                          ->orWhere('title', 'LIKE', '%Unarmed%');
                })
                ->pluck('id')
                ->toArray();

            $classGCourseIds = DB::table('courses')
                ->where('is_active', true)
                ->where(function($query) {
                    $query->where('title', 'LIKE', '%Class G%')
                          ->orWhere('title', 'LIKE', '%Class \'G\'%')
                          ->orWhere('title', 'LIKE', '%G28%')
                          ->orWhere('title', 'LIKE', '%Armed%');
                })
                ->pluck('id')
                ->toArray();

            $weeks = [];
            $sales = [];
            $totalEnrollments = [];
            $classDEnrollments = [];
            $classGEnrollments = [];

            // Generate data for each week
            for ($i = $weeksBack - 1; $i >= 0; $i--) {
                $weekEnd = $endDate->copy()->subWeeks($i);
                $weekStart = $weekEnd->copy()->subWeek();

                // Format week label
                $weeks[] = $weekEnd->format('M d');

                // Get enrollments for this week
                $weeklyData = DB::table('course_auths')
                    ->join('courses', 'course_auths.course_id', '=', 'courses.id')
                    ->whereBetween('course_auths.created_at', [$weekStart, $weekEnd])
                    ->where('courses.is_active', true)
                    ->select(
                        DB::raw('COUNT(*) as total_enrollments'),
                        DB::raw('SUM(courses.price) as total_revenue'),
                        DB::raw('SUM(CASE WHEN course_auths.course_id IN (' . implode(',', $classDCourseIds ?: [0]) . ') THEN 1 ELSE 0 END) as d_enrollments'),
                        DB::raw('SUM(CASE WHEN course_auths.course_id IN (' . implode(',', $classGCourseIds ?: [0]) . ') THEN 1 ELSE 0 END) as g_enrollments')
                    )
                    ->first();

                $sales[] = (float) ($weeklyData->total_revenue ?? 0);
                $totalEnrollments[] = (int) ($weeklyData->total_enrollments ?? 0);
                $classDEnrollments[] = (int) ($weeklyData->d_enrollments ?? 0);
                $classGEnrollments[] = (int) ($weeklyData->g_enrollments ?? 0);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'weeks' => $weeks,
                    'sales' => $sales,
                    'totalEnrollments' => $totalEnrollments,
                    'classDEnrollments' => $classDEnrollments,
                    'classGEnrollments' => $classGEnrollments,
                    'metadata' => [
                        'start_date' => $startDate->toDateString(),
                        'end_date' => $endDate->toDateString(),
                        'weeks_count' => $weeksBack,
                        'class_d_course_ids' => $classDCourseIds,
                        'class_g_course_ids' => $classGCourseIds
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Weekly Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate weekly data'
            ], 500);
        }
    }

    /**
     * Get instructor performance data for charts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInstructorData(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            Log::info('Loading instructor data', [
                'limit' => $limit,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Get instructors who have actually taught classes (through inst_unit table)
            $instructors = DB::table('users')
                ->join('inst_unit', 'users.id', '=', 'inst_unit.created_by')
                ->where('users.is_active', true)
                ->select('users.id', DB::raw('CONCAT(users.fname, \' \', users.lname) as name'), DB::raw('COUNT(DISTINCT inst_unit.id) as total_classes'))
                ->groupBy('users.id', 'users.fname', 'users.lname')
                ->orderBy('total_classes', 'desc')
                ->limit($limit)
                ->get();

            Log::info('Found instructors', ['count' => $instructors->count()]);

            if ($instructors->isEmpty()) {
                Log::warning('No instructors found in database');
                return response()->json([
                    'success' => true,
                    'data' => [
                        'instructors' => [],
                        'studentsPerInstructor' => [],
                        'classesTaught' => [],
                        'attendanceRates' => [],
                        'courseTypeDistribution' => [0, 0, 0],
                        'metadata' => [
                            'start_date' => $startDate instanceof Carbon ? $startDate->toDateString() : $startDate,
                            'end_date' => $endDate instanceof Carbon ? $endDate->toDateString() : $endDate,
                            'instructor_count' => 0
                        ]
                    ]
                ]);
            }

            $instructorNames = [];
            $studentsPerInstructor = [];
            $classesTaught = [];
            $attendanceRates = [];

            foreach ($instructors as $instructor) {
                $instructorNames[] = $instructor->name;

                try {
                    // Count total students taught (through inst_unit and student_lessons)
                    $studentCount = DB::table('inst_unit')
                        ->join('student_lessons', 'inst_unit.course_date_id', '=', 'student_lessons.course_date_id')
                        ->where('inst_unit.created_by', $instructor->id)
                        ->distinct('student_lessons.student_id')
                        ->count('student_lessons.student_id');

                    $studentsPerInstructor[] = $studentCount;
                } catch (\Exception $e) {
                    Log::warning('Error counting students for instructor', [
                        'instructor_id' => $instructor->id,
                        'error' => $e->getMessage()
                    ]);
                    $studentsPerInstructor[] = 0;
                }

                try {
                    // Count classes taught (through inst_unit)
                    $classCount = DB::table('inst_unit')
                        ->where('created_by', $instructor->id)
                        ->count();

                    $classesTaught[] = $classCount;
                } catch (\Exception $e) {
                    Log::warning('Error counting classes for instructor', [
                        'instructor_id' => $instructor->id,
                        'error' => $e->getMessage()
                    ]);
                    $classesTaught[] = 0;
                }

                try {
                    // Calculate attendance rate (through inst_unit)
                    $attendanceData = DB::table('inst_unit')
                        ->join('student_lessons', 'inst_unit.course_date_id', '=', 'student_lessons.course_date_id')
                        ->where('inst_unit.created_by', $instructor->id)
                        ->selectRaw('COUNT(*) as total_students')
                        ->selectRaw('SUM(CASE WHEN student_lessons.attended = true THEN 1 ELSE 0 END) as attended_students')
                        ->first();

                    $attendanceRate = $attendanceData && $attendanceData->total_students > 0
                        ? round(($attendanceData->attended_students / $attendanceData->total_students) * 100, 1)
                        : 0;

                    $attendanceRates[] = $attendanceRate;
                } catch (\Exception $e) {
                    Log::warning('Error calculating attendance for instructor', [
                        'instructor_id' => $instructor->id,
                        'error' => $e->getMessage()
                    ]);
                    $attendanceRates[] = 0;
                }
            }

            // Get course type distribution across all instructors
            try {
                $classDCourseIds = DB::table('courses')
                    ->where('is_active', true)
                    ->where(function($query) {
                        $query->where('title', 'LIKE', '%Class D%')
                              ->orWhere('title', 'LIKE', '%Class \'D\'%')
                              ->orWhere('title', 'LIKE', '%D40%')
                              ->orWhere('title', 'LIKE', '%Unarmed%');
                    })
                    ->pluck('id')
                    ->toArray();

                $classGCourseIds = DB::table('courses')
                    ->where('is_active', true)
                    ->where(function($query) {
                        $query->where('title', 'LIKE', '%Class G%')
                              ->orWhere('title', 'LIKE', '%Class \'G\'%')
                              ->orWhere('title', 'LIKE', '%G28%')
                              ->orWhere('title', 'LIKE', '%Armed%');
                    })
                    ->pluck('id')
                    ->toArray();

                Log::info('Course IDs found', [
                    'class_d_count' => count($classDCourseIds),
                    'class_g_count' => count($classGCourseIds)
                ]);

                $allCourseIds = array_merge($classDCourseIds, $classGCourseIds);
                if (empty($allCourseIds)) {
                    $allCourseIds = [0]; // Prevent SQL error
                }

                $courseDistribution = DB::table('course_dates')
                    ->join('inst_units', 'course_dates.inst_unit_id', '=', 'inst_units.id')
                    ->whereBetween('course_dates.class_date', [$startDate, $endDate])
                    ->selectRaw('SUM(CASE WHEN inst_units.course_id IN (' . implode(',', $classDCourseIds ?: [0]) . ') THEN 1 ELSE 0 END) as d_count')
                    ->selectRaw('SUM(CASE WHEN inst_units.course_id IN (' . implode(',', $classGCourseIds ?: [0]) . ') THEN 1 ELSE 0 END) as g_count')
                    ->selectRaw('SUM(CASE WHEN inst_units.course_id NOT IN (' . implode(',', $allCourseIds) . ') THEN 1 ELSE 0 END) as other_count')
                    ->first();
            } catch (\Exception $e) {
                Log::warning('Error getting course distribution', [
                    'error' => $e->getMessage()
                ]);
                $courseDistribution = (object) ['d_count' => 0, 'g_count' => 0, 'other_count' => 0];
                $classDCourseIds = [];
                $classGCourseIds = [];
            }

            Log::info('Instructor data compiled successfully', [
                'instructor_count' => count($instructorNames)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'instructors' => $instructorNames,
                    'studentsPerInstructor' => $studentsPerInstructor,
                    'classesTaught' => $classesTaught,
                    'attendanceRates' => $attendanceRates,
                    'courseTypeDistribution' => [
                        (int) ($courseDistribution->d_count ?? 0),
                        (int) ($courseDistribution->g_count ?? 0),
                        (int) ($courseDistribution->other_count ?? 0)
                    ],
                    'metadata' => [
                        'start_date' => $startDate instanceof Carbon ? $startDate->toDateString() : $startDate,
                        'end_date' => $endDate instanceof Carbon ? $endDate->toDateString() : $endDate,
                        'instructor_count' => count($instructorNames),
                        'class_d_course_ids' => $classDCourseIds,
                        'class_g_course_ids' => $classGCourseIds
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Instructor Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate instructor data'
            ], 500);
        }
    }

    /**
     * Export report data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exportReport(Request $request): JsonResponse
    {
        try {
            $reportType = $request->input('report_type');
            $format = $request->input('format', 'csv');

            // Validate report type
            $validTypes = ['financial', 'students', 'courses', 'instructors', 'operational'];
            if (!in_array($reportType, $validTypes)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid report type'
                ], 400);
            }

            // Generate filename
            $filename = $reportType . '_report_' . Carbon::now()->format('Y-m-d_His') . '.' . $format;

            return response()->json([
                'success' => true,
                'message' => 'Report export initiated',
                'filename' => $filename,
                'download_url' => route('admin.reports.download', ['filename' => $filename])
            ]);
        } catch (\Exception $e) {
            Log::error('Report Export Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to export report'
            ], 500);
        }
    }

    /**
     * Get Financial Data for Charts
     */
    public function getFinancialData(Request $request): JsonResponse
    {
        try {
            $months = $request->input('months', 6);

            Log::info('Loading financial data', ['months' => $months]);

            // Get last N months of data
            $startDate = Carbon::now()->subMonths($months)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            // Monthly Revenue
            $monthlyRevenue = DB::table('orders')
                ->selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, SUM(amount) as total")
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw("TO_CHAR(created_at, 'Mon YYYY')"))
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();

            $monthLabels = $monthlyRevenue->pluck('month')->toArray();
            $monthData = $monthlyRevenue->pluck('total')->map(function($val) {
                return (float)$val;
            })->toArray();

            // Payment Methods Distribution
            $paymentMethods = DB::table('orders')
                ->select('payment_method', DB::raw('COUNT(*) as count'))
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('payment_method')
                ->orderBy('count', 'desc')
                ->get();

            $paymentLabels = $paymentMethods->pluck('payment_method')->map(function($method) {
                return ucwords(str_replace('_', ' ', $method ?? 'Unknown'));
            })->toArray();
            $paymentData = $paymentMethods->pluck('count')->toArray();

            // Revenue by Course Type (using student_units to get course info)
            $revenueByCourse = DB::table('orders')
                ->join('student_units', 'orders.student_unit_id', '=', 'student_units.id')
                ->join('course_units', 'student_units.course_unit_id', '=', 'course_units.id')
                ->join('courses', 'course_units.course_id', '=', 'courses.id')
                ->selectRaw('courses.title as course, SUM(orders.amount) as total')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->groupBy('courses.title')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            $courseLabels = $revenueByCourse->pluck('course')->toArray();
            $courseData = $revenueByCourse->pluck('total')->map(function($val) {
                return (float)$val;
            })->toArray();

            // Average Order Value by Month
            $avgOrderValue = DB::table('orders')
                ->selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, AVG(amount) as avg_value")
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw("TO_CHAR(created_at, 'Mon YYYY')"))
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();

            $avgLabels = $avgOrderValue->pluck('month')->toArray();
            $avgData = $avgOrderValue->pluck('avg_value')->map(function($val) {
                return round((float)$val, 2);
            })->toArray();

            Log::info('Financial data loaded successfully', [
                'months_count' => count($monthLabels),
                'payment_methods' => count($paymentLabels),
                'courses' => count($courseLabels)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'monthlyRevenue' => [
                        'labels' => $monthLabels,
                        'data' => $monthData
                    ],
                    'paymentMethods' => [
                        'labels' => $paymentLabels,
                        'data' => $paymentData
                    ],
                    'revenueByCourse' => [
                        'labels' => $courseLabels,
                        'data' => $courseData
                    ],
                    'avgOrderValue' => [
                        'labels' => $avgLabels,
                        'data' => $avgData
                    ],
                    'metadata' => [
                        'start_date' => $startDate->toDateString(),
                        'end_date' => $endDate->toDateString(),
                        'total_orders' => DB::table('orders')
                            ->where('status', 'completed')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count(),
                        'total_revenue' => DB::table('orders')
                            ->where('status', 'completed')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->sum('amount')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Financial Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load financial data: ' . $e->getMessage()
            ], 500);
        }
    }
}
