<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Models\CourseAuth;
use App\Services\RCache;

class CourseAuthsController extends Controller
{
    use PageMetaDataTrait;

    public function index(Request $request): View
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $courseFilter = $request->get('course_id');
        $tab          = $request->get('tab', 'active');

        // ─── Tab 1: Active enrollments ────────────────────────────────────────
        $activeQuery = CourseAuth::with(['User', 'Course', 'StudentUnits.StudentLessons'])
            ->whereNull('completed_at')
            ->whereNull('disabled_at');

        if ($courseFilter) {
            $activeQuery->where('course_id', $courseFilter);
        }

        $activeCourseAuths = $activeQuery
            ->orderByDesc('created_at')
            ->paginate(25, ['*'], 'active_page')
            ->withQueryString();

        // ─── Tab 2: Completed but missing DOL tracking ───────────────────────
        $missingDolQuery = CourseAuth::with(['User', 'Course'])
            ->whereNotNull('completed_at')
            ->whereNull('dol_tracking');

        if ($courseFilter) {
            $missingDolQuery->where('course_id', $courseFilter);
        }

        if ($dateFrom = $request->get('date_from')) {
            $missingDolQuery->where('completed_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $missingDolQuery->where('completed_at', '<=', $dateTo . ' 23:59:59');
        }

        $missingDol = $missingDolQuery
            ->orderByDesc('completed_at')
            ->paginate(25, ['*'], 'dol_page')
            ->withQueryString();

        // ─── Pre-build total lessons per course (RCache, no extra DB hit) ────
        $totalLessonsMap = [];
        foreach ($activeCourseAuths as $ca) {
            $courseId = $ca->course_id;
            if ($courseId && !isset($totalLessonsMap[$courseId])) {
                $course = RCache::Courses($courseId);
                $totalLessonsMap[$courseId] = $course ? $course->GetLessons()->count() : 0;
            }
        }

        // ─── Stats ────────────────────────────────────────────────────────────
        $stats = [
            'active'      => CourseAuth::whereNull('completed_at')->whereNull('disabled_at')->count(),
            'missing_dol' => CourseAuth::whereNotNull('completed_at')->whereNull('dol_tracking')->count(),
            'completed'   => CourseAuth::whereNotNull('completed_at')->count(),
        ];

        $courses = RCache::Courses()->sortBy('title')->values();

        $content = array_merge([
            'active_course_auths' => $activeCourseAuths,
            'missing_dol'         => $missingDol,
            'total_lessons_map'   => $totalLessonsMap,
            'stats'               => $stats,
            'courses'             => $courses,
            'filters'             => [
                'course_id' => $courseFilter,
                'date_from' => $request->get('date_from'),
                'date_to'   => $request->get('date_to'),
                'tab'       => $tab,
            ],
        ], self::renderPageMeta('Student Courses'));

        return view('admin.course-auths.index', compact('content'));
    }
}
