<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;

use stdClass;
use App\Models\User;
use App\RCache as RCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;


/*
 *
 * foreach ( $student_counts as $record )
 *     $record->count  $record->title
 *
 */


class AdminDashboardController extends Controller
{

    use PageMetaDataTrait;

    public function dashboard()
    {
        // Get class and student statistics
        $classStats = $this->getClassStatistics();
        $studentStats = $this->getStudentStatistics();

        // Scan for available widgets
        $available_widgets = [];

        // Constructing the widgets array
        $widgets = [];
        $widgets['available_widgets'] = $available_widgets;
        $widgets['class_stats'] = $classStats;
        $widgets['student_stats'] = $studentStats;

        // Check if a specific widget exists, then execute the respective query
        if (in_array('1_new-users.blade.php', $available_widgets)) {
            $widgets['users_stats'] = $this->UsersStats();
        }
        if (in_array('2_total-users.blade.php', $available_widgets)) {
            $widgets['student_counts'] = $this->ActiveStudentCounts();
        }
        if (in_array('4_latest-registrations.blade.php', $available_widgets)) {
            // Temporarily disable RCache-dependent functionality to avoid crashes
            // Use direct database query for student role instead
            try {
                $role = \App\Models\Role::where('name', 'Student')->first();
                $studentRoleId = $role ? $role->id : 5; // Fallback to role ID 5
            } catch (\Throwable $e) {
                $studentRoleId = 5; // Safe fallback
            }

            $widgets['latestRegistrations'] = User::where('is_active', true)
                ->where('role_id', $studentRoleId)
                ->limit(10)->get();
        }

        $content = array_merge(['widgets' => $widgets], self::renderPageMeta('admin_dashboard'));

        return view('admin.dashboard', compact('content'));
    }

    /**
     * Get comprehensive class statistics
     */
    private function getClassStatistics(): array
    {
        try {
            $today = date('Y-m-d');
            $thisWeek = date('Y-m-d', strtotime('monday this week'));
            $thisMonth = date('Y-m-01');

            return [
                'today' => DB::table('inst_unit')->whereDate('created_at', $today)->count(),
                'this_week' => DB::table('inst_unit')->whereDate('created_at', '>=', $thisWeek)->count(),
                'this_month' => DB::table('inst_unit')->whereDate('created_at', '>=', $thisMonth)->count(),
                'total' => DB::table('inst_unit')->count(),
                'active' => DB::table('inst_unit')->whereNull('completed_at')->count(),
                'completed' => DB::table('inst_unit')->whereNotNull('completed_at')->count(),
                'scheduled' => DB::table('course_dates')->where('starts_at', '>', now())->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting class statistics: ' . $e->getMessage());
            return [
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'scheduled' => 0
            ];
        }
    }

    /**
     * Get comprehensive student statistics
     */
    private function getStudentStatistics(): array
    {
        try {
            $today = date('Y-m-d');
            $thisWeek = date('Y-m-d', strtotime('monday this week'));
            $thisMonth = date('Y-m-01');

            return [
                'attendance_today' => DB::table('student_unit')->whereDate('created_at', $today)->count(),
                'attendance_week' => DB::table('student_unit')->whereDate('created_at', '>=', $thisWeek)->count(),
                'attendance_month' => DB::table('student_unit')->whereDate('created_at', '>=', $thisMonth)->count(),
                'online_today' => DB::table('student_unit')->whereDate('created_at', $today)->where('attendance_type', 'online')->count(),
                'offline_today' => DB::table('student_unit')->whereDate('created_at', $today)->where('attendance_type', 'offline')->count(),
                'total_students' => DB::table('users')->where('role_id', '>=', 5)->count(),
                'active_students' => DB::table('users')->where('role_id', '>=', 5)->where('is_active', true)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting student statistics: ' . $e->getMessage());
            return [
                'attendance_today' => 0,
                'attendance_week' => 0,
                'attendance_month' => 0,
                'online_today' => 0,
                'offline_today' => 0,
                'total_students' => 0,
                'active_students' => 0
            ];
        }
    }




    public function UsersStats(): stdClass
    {
        $cache_key = 'users_stats';

        // Try to get from cache first
        $cached = Cache::get($cache_key);
        if ($cached) {
            try {
                return RCache::Unserialize($cached);
            } catch (\Exception $e) {
                // Fallback to standard unserialization
                return unserialize($cached);
            }
        }

        try {
            // Try to get fresh data from database stored procedure
            $records = DB::select('SELECT * FROM sp_users_stats()')[0];
        } catch (\Exception $e) {
            // Fallback if stored procedure doesn't exist
            $records = (object)[
                'new_users' => User::whereDate('created_at', today())->count(),
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
            ];
        }

        // Cache the result for 15 minutes using standard serialization
        try {
            Cache::put($cache_key, RCache::Serialize($records), 900);
        } catch (\Exception $e) {
            Cache::put($cache_key, serialize($records), 900);
        }

        return $records;
    }
    public function ActiveStudentCounts(): array
    {
        try {
            $records = DB::table('student_unit')
                ->where('created_at', '>=', date('Y-m-d 00:00:00-04'))
                ->select(DB::raw('COUNT(*) as count'), 'course_unit_id')
                ->groupBy('course_unit_id')
                ->orderBy('count')
                ->get();

            $counts = [];

            foreach ($records as $record) {
                // Use direct database query instead of RCache
                try {
                    $courseUnit = \App\Models\CourseUnit::find($record->course_unit_id);
                    $title = $courseUnit && $courseUnit->course ?
                        $courseUnit->course->short_title ?? 'Unknown Course' :
                        'Course Unit ' . $record->course_unit_id;
                } catch (\Exception $e) {
                    $title = 'Course Unit ' . $record->course_unit_id;
                }

                array_push($counts, (object) [
                    'title' => $title,
                    'count' => $record->count
                ]);
            }

            return $counts;

        } catch (\Exception $e) {
            // Fallback if table doesn't exist or query fails
            return [
                (object) ['title' => 'Sample Course', 'count' => 0],
            ];
        }
    }

    /**
     * Get dashboard statistics API endpoint
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $classStats = $this->getClassStatistics();
            $studentStats = $this->getStudentStatistics();
            $topCourses = $this->getTopCourses();
            $recentActivity = $this->getRecentActivity();

            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classStats,
                    'students' => $studentStats,
                    'top_courses' => $topCourses,
                    'recent_activity' => $recentActivity,
                ],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard statistics',
            ], 500);
        }
    }

    /**
     * Get top courses by class count
     */
    private function getTopCourses(int $limit = 10): array
    {
        try {
            return DB::table('inst_unit')
                ->join('course_dates', 'inst_unit.course_date_id', '=', 'course_dates.id')
                ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
                ->join('courses', 'course_units.course_id', '=', 'courses.id')
                ->select('courses.title', DB::raw('COUNT(inst_unit.id) as class_count'))
                ->groupBy('courses.id', 'courses.title')
                ->orderByDesc('class_count')
                ->limit($limit)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting top courses: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent class activity
     */
    private function getRecentActivity(int $limit = 10): array
    {
        try {
            return DB::table('inst_unit')
                ->join('course_dates', 'inst_unit.course_date_id', '=', 'course_dates.id')
                ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
                ->join('courses', 'course_units.course_id', '=', 'courses.id')
                ->leftJoin(
                    DB::raw('(SELECT inst_unit_id, COUNT(*) as student_count FROM student_unit GROUP BY inst_unit_id) as su'),
                    'inst_unit.id',
                    '=',
                    'su.inst_unit_id'
                )
                ->select(
                    'inst_unit.id',
                    'inst_unit.created_at',
                    'inst_unit.completed_at',
                    'courses.title as course',
                    'course_units.title as unit',
                    DB::raw('COALESCE(su.student_count, 0) as students')
                )
                ->orderByDesc('inst_unit.created_at')
                ->limit($limit)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting recent activity: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get admin configuration data for React components
     */
    public function config(Request $request)
    {
        try {
            $user = $request->user('admin') ?? $request->user();

            return response()->json([
                'success' => true,
                'config' => [
                    'aiagents' => config('aiagents', [
                        'openai' => [
                            'enable_ai' => false,
                            'api_key' => '',
                            'org_id' => '',
                            'url' => '',
                            'default_model' => '',
                            'default_system_role' => '',
                            'default_temperature' => 0,
                        ],
                        'write_progress' => [
                            'file_path' => '',
                            'default_message' => '',
                        ],
                    ]),
                    'messaging' => config('messenger', []),
                    'notifications' => [
                        'enabled' => true,
                        'realtime' => false,
                    ]
                ],
                'auth' => [
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name ?? ($user->fname . ' ' . $user->lname),
                        'email' => $user->email,
                    ] : null,
                    'guard' => $user ? ($request->user('admin') ? 'admin' : 'web') : null,
                ],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Admin config API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Configuration loading failed',
                'config' => [
                    'aiagents' => [
                        'openai' => [
                            'enable_ai' => false,
                            'api_key' => '',
                            'org_id' => '',
                            'url' => '',
                            'default_model' => '',
                            'default_system_role' => '',
                            'default_temperature' => 0,
                        ],
                        'write_progress' => [
                            'file_path' => '',
                            'default_message' => '',
                        ],
                    ],
                    'messaging' => [],
                    'notifications' => [
                        'enabled' => false,
                        'realtime' => false,
                    ]
                ],
                'auth' => [
                    'user' => null,
                    'guard' => null,
                ],
            ], 200); // Return 200 even on error to prevent auth issues
        }
    }
}
