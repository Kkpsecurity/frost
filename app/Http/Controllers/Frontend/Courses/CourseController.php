<?php

namespace App\Http\Controllers\Frontend\Courses;

use App\Classes\MiscQueries;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseDate;
use App\Services\RCache;
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
                    'url' => route('courses.show', $course->id)
                ];
            }
        }


        $content = self::renderPageMeta('schedules');

        return view('frontend.shop.schedules', compact('content', 'allEvents'));
    }

    /**
     * Get course schedule data as JSON for AJAX requests
     */
    public function getScheduleData(Request $request)
    {
        // Get the course filter parameter if provided
        $courseFilter = $request->get('course_filter');

        // Retrieve all active courses and their associated events
        $activeCourses = RCache::Courses()->where('is_active', true);

        // If course filter is provided, filter the courses
        if ($courseFilter) {
            $activeCourses = $activeCourses->filter(function ($course) use ($courseFilter) {
                // Map frontend filter values to course criteria
                switch ($courseFilter) {
                    case 'D40':
                        return strpos(strtolower($course->title), 'd40') !== false ||
                            strpos(strtolower($course->title), 'armed') !== false;
                    case 'G28':
                        return strpos(strtolower($course->title), 'g28') !== false ||
                            strpos(strtolower($course->title), 'unarmed') !== false;
                    default:
                        return true;
                }
            });
        }

        $allEvents = []; // To store formatted events for all courses

        foreach ($activeCourses as $course) {
            $eventsForThisCourse = MiscQueries::CalenderDates($course);

            foreach ($eventsForThisCourse as $event) {
                $start = \Illuminate\Support\Carbon::parse($event->starts_at, 'UTC');
                $end = \Illuminate\Support\Carbon::parse($event->ends_at, 'UTC');

                $allEvents[] = [
                    'title' => $event->CalendarTitle(),
                    // Use ISO-8601 for reliable browser parsing (Date(...))
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'url' => route('courses.show', $course->id),
                    'course_type' => $course->getCourseType() === 'D' ? 'D40' : 'G28',
                    'course_id' => $course->id,
                    'course_title' => $course->title
                ];
            }
        }

        return response()->json([
            'success' => true,
            'events' => $allEvents
        ]);
    }
}
