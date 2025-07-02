<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;

use stdClass;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use RCache;
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
            $widgets['latestRegistrations'] = User::where('is_active', true)
                ->where('role_id', RCache::RoleID('Student'))
                ->limit(10)->get();
        }
    
        $content = array_merge(['widgets' => $widgets], self::renderPageMeta('admin_dashboard'));
    
        return view('admin.dashboard', compact('content'));
    }
    



    public function UsersStats(): stdClass
    {

        $RedisConn = Cache::store('redis')->connection();
        $redis_key = 'users_stats';

        if ($serialized = $RedisConn->get($redis_key)) {
            return RCache::Unserialize($serialized);
        }

        $records = DB::select(DB::raw('SELECT * FROM sp_users_stats()'))[0];
        $RedisConn->set($redis_key, RCache::Serialize($records), 'EX', 900); // 15 min
        return $records;

    }


    public function ActiveStudentCounts(): array
    {

        $records = DB::table('student_unit')
            ->where('created_at', '>=', date('Y-m-d 00:00:00-04'))
            ->select(DB::raw('COUNT(*) as count'), 'course_unit_id')
            ->groupBy('course_unit_id')
            ->orderBy('count')
            ->get();

        $total = 0;
        $counts = [];

        foreach ($records as $record) {

            array_push($counts, (object) [
                'title' => RCache::CourseUnits($record->course_unit_id)->GetCourse()->ShortTitle(),
                'count' => $record->count
            ]);

            $total += $record->count;

        }

        array_push($counts, (object) [
            'title' => 'Total',
            'count' => $total
        ]);

        return $counts;

    }


}