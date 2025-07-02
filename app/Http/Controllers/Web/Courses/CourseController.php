<?php

namespace App\Http\Controllers\Web\Courses;

use App\Classes\MiscQueries;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseDate;
use App\RCache; 
use Carbon\Carbon;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    use PageMetaDataTrait;

    public function list()
    {
        $courses = Course::where('is_active', 1)->paginate(10);

        // Merge the page meta data into the content array
        $content = array_merge([], self::renderPageMeta('Online Courses'));

        return view('frontend.shop.list', compact('content', 'courses'));
    }

    public function details($course_id)
    {
        $course = Course::where('is_active', 1)->findOrFail($course_id);

        $course->image = 'https://via.placeholder.com/500';
        if ($course_id == 1) {
            $course->description = view('frontend.shop.partials.dcourse')->render();
        } else if ($course_id == 2) {
            $course->description = view('frontend.shop.partials.dcourse_night')->render();
        } else if ($course_id == 3) {
            $course->description = view('frontend.shop.partials.gcourse')->render();
        }


        // Merge the page meta data into the content array
        $content = array_merge([], self::renderPageMeta($course->title_long));

        // Return the view, passing in the content and courses variables
        return view('frontend.shop.detail', compact('content', 'course'));
    }

    public function schedules(Course $Course)
    {
        // Retrieve all active courses and their associated events
        $activeCourses = RCache::Courses()->where('is_active', true);
        
        $allEvents = []; // To store formatted events for all courses    
       
        foreach ($activeCourses as $course) {
            $eventsForThisCourse = MiscQueries::CalenderDates($course);
        
            foreach ($eventsForThisCourse as $event) {
               
                $allEvents[] = [
                    'title' => $event->CalendarTitle(),
                    'start' => $event->StartsAt('YYYY-MM-DD HH:mm'),
                    'end'   => $event->EndsAt('YYYY-MM-DD HH:mm'),
                    #'start' => $start->format('Y-m-d H:i'), // format to date-only
                    #'end' => $end->format('Y-m-d H:i'),     // format to date-only
                    'url'  => route('courses.detail', $course->id)  
                ];
            }
        }
        
    
        $content = self::renderPageMeta('schedules');
    
        return view('frontend.shop.schedules', compact('content', 'allEvents'));
    }
    


}