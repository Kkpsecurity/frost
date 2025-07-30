<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;

use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\RCache as RCache;
use App\Models\User;


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
        // Scan for available widgets
        $available_widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));

        // Constructing the widgets array
        $widgets = [];
        $widgets['available_widgets'] = $available_widgets;

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
}
