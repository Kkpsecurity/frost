<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Student Classroom Controller
 * Handles the student classroom dashboard functionality
 */
class ClassroomController extends Controller
{
    /**
     * Display the student classroom dashboard
     *
     * @param Request $request
     * @return View
     */
    public function dashboard(Request $request): View
    {
        // You can add data fetching logic here
        // For now, we'll pass some basic data
        $data = [
            'pageTitle' => 'My Classroom',
            'breadcrumbs' => [
                ['title' => 'Classroom', 'url' => null]
            ]
        ];

        return view('student.classroom.dashboard', $data);
    }

    /**
     * Get student statistics (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        // Mock data - replace with actual database queries
        $stats = [
            'enrolledCourses' => 5,
            'completedLessons' => 34,
            'assignmentsDue' => 3,
            'hoursLearned' => 127
        ];

        return response()->json($stats);
    }

    /**
     * Get recent lessons (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentLessons(Request $request)
    {
        // Mock data - replace with actual database queries
        $lessons = [
            [
                'id' => 1,
                'title' => 'Introduction to Calculus',
                'course' => 'Advanced Mathematics',
                'progress' => 85,
                'duration' => '45 min',
                'lastAccessed' => '2 hours ago'
            ],
            [
                'id' => 2,
                'title' => 'Chemical Bonding',
                'course' => 'Organic Chemistry',
                'progress' => 60,
                'duration' => '38 min',
                'lastAccessed' => '1 day ago'
            ],
            [
                'id' => 3,
                'title' => 'Newton\'s Laws',
                'course' => 'Physics Fundamentals',
                'progress' => 100,
                'duration' => '52 min',
                'lastAccessed' => '3 days ago'
            ]
        ];

        return response()->json($lessons);
    }

    /**
     * Get upcoming assignments (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingAssignments(Request $request)
    {
        // Mock data - replace with actual database queries
        $assignments = [
            [
                'id' => 1,
                'title' => 'Derivative Calculations',
                'course' => 'Advanced Mathematics',
                'dueDate' => 'Tomorrow',
                'type' => 'assignment'
            ],
            [
                'id' => 2,
                'title' => 'Molecular Structure Quiz',
                'course' => 'Organic Chemistry',
                'dueDate' => 'In 3 days',
                'type' => 'quiz'
            ],
            [
                'id' => 3,
                'title' => 'Lab Report - Motion Analysis',
                'course' => 'Physics Fundamentals',
                'dueDate' => 'Next week',
                'type' => 'project'
            ]
        ];

        return response()->json($assignments);
    }
}
