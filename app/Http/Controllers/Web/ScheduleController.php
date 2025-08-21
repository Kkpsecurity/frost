<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;

class ScheduleController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display the course schedules page
     */
    public function index()
    {
        $content = [
            'title' => 'Course Schedules - ' . config('app.name'),
            'description' => 'View available course schedules for armed and unarmed security training',
            'keywords' => 'course schedules, security training dates, Class D schedule, Class G schedule',
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('schedules'));

        return view('frontend.schedules.index', compact('content'));
    }
}
