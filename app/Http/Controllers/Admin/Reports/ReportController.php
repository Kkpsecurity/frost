<?php namespace App\Http\Controllers\Admin\Reports;


use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    use PageMetaDataTrait;     

    public function dashboard() {
        
        $reports = array(
            array(
              "title" => "<i class='fas fa-file-alt'></i> Course content analysis report",
              "description" => "Provides an analysis of the course content, including an overview of the topics covered, the difficulty level, and the relevance of the content.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-check-square'></i> Assessment performance by question report",
              "description" => "Provides an analysis of the performance of users on each individual question in the assessments for each course, allowing administrators to identify areas of weakness in the course content.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-chalkboard-teacher'></i> Instructor performance report",
              "description" => "Provides an analysis of the performance of instructors, including information on their student satisfaction ratings, assessment scores, and feedback.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-robot'></i> Chatbot performance report",
              "description" => "Provides an analysis of the performance of the chatbot used on the site, including information on the number of interactions, response times, and user satisfaction ratings.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-users'></i> Peer comparison report",
              "description" => "Compares the performance of users who have completed the same course, providing insight into areas of strength and weakness.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='far fa-clock'></i> Course completion time report",
              "description" => "Provides information on the average time it takes users to complete each course, allowing administrators to identify courses that may be too difficult or time-consuming.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-map-marker-alt'></i> Geographic location report",
              "description" => "Provides information on the geographic location of users who have registered for each course, allowing administrators to identify areas where the courses are particularly popular.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-chart-line'></i> Course popularity report",
              "description" => "Provides information on the popularity of each course offered on the site, allowing administrators to identify which courses are most in-demand.",
              "type" => "report"
            ),
            array(
              "title" => "<i class='fas fa-bullhorn'></i> New course announcement notification",
              "description" => "Notifies users when a new course is added to the site, allowing them to register and begin learning immediately.",
              "type" => "notification"
            ),
            array(
              "title" => "<i class='far fa-bell'></i> Course reminder notification",
              "description" => "Sends reminders to users who have registered for a course, reminding them of upcoming deadlines and encouraging them to complete the course.",
              "type" => "notification"
            )
        );          

        $content = array_merge([ 
            'reports' => $reports,
        ], self::renderPageMeta('reports'));
        
        return view('admin.reports.dashboard', compact('content'));
    }
}
