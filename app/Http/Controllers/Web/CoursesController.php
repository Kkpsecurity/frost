<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;

class CoursesController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display the courses listing page
     */
    public function index()
    {
        $content = [
            'title' => 'Security Training Courses - ' . config('app.name'),
            'description' => 'Professional security training courses for armed and unarmed security professionals',
            'keywords' => 'security courses, training, certification, armed security, unarmed security, Class D, Class G',
        ];

        // Merge with meta data
        $content = array_merge($content, self::renderPageMeta('courses'));

        return view('frontend.courses.index', compact('content'));
    }

    /**
     * Display a specific course
     */
    public function show($slug)
    {
        // TODO: Implement course details page
        return redirect()->route('courses.index');
    }
}
