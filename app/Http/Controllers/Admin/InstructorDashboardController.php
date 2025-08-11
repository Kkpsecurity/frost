<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Instructor Dashboard Controller
 * Handles the instructor dashboard functionality in the admin panel
 */
class InstructorDashboardController extends Controller
{
    /**
     * Display the instructor dashboard
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // You can add data fetching logic here
        // For now, we'll pass some basic data
        $data = [
            'pageTitle' => 'Instructor Dashboard',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Instructor Dashboard', 'url' => null]
            ]
        ];

        return view('admin.instructor.dashboard', $data);
    }

    /**
     * Get instructor statistics (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        // Mock data - replace with actual database queries
        $stats = [
            'totalClasses' => 24,
            'activeStudents' => 156,
            'completedLessons' => 89,
            'upcomingClasses' => 3
        ];

        return response()->json($stats);
    }

    /**
     * Get upcoming classes (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingClasses(Request $request)
    {
        // Mock data - replace with actual database queries
        $classes = [
            [
                'id' => 1,
                'title' => 'Advanced Mathematics',
                'time' => '2:00 PM',
                'students' => 25,
                'duration' => '60 min'
            ],
            [
                'id' => 2,
                'title' => 'Physics Lab Session',
                'time' => '4:30 PM',
                'students' => 18,
                'duration' => '90 min'
            ],
            [
                'id' => 3,
                'title' => 'Chemistry Review',
                'time' => '6:00 PM',
                'students' => 32,
                'duration' => '45 min'
            ]
        ];

        return response()->json($classes);
    }
}
