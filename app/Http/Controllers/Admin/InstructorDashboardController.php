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
     * Display the instructor dashboard (defaults to offline mode)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return $this->offline($request);
    }

    /**
     * Display the instructor dashboard in offline mode
     */
    public function offline(Request $request): View
    {
        // Sample data for today's lessons
        $todayLessons = collect([
            [
                'id' => 1,
                'start_time' => '9:00 AM',
                'duration' => '60 min',
                'course_title' => 'Security Fundamentals',
                'course_code' => 'SEC-101',
                'lesson_title' => 'Introduction to Security',
                'module' => '1',
                'student_count' => 18,
                'status' => 'scheduled'
            ],
            [
                'id' => 2,
                'start_time' => '11:00 AM',
                'duration' => '45 min',
                'course_title' => 'Advanced Security',
                'course_code' => 'SEC-201',
                'lesson_title' => 'Threat Assessment',
                'module' => '2',
                'student_count' => 12,
                'status' => 'in_progress'
            ],
            [
                'id' => 3,
                'start_time' => '2:00 PM',
                'duration' => '50 min',
                'course_title' => 'Cyber Defense',
                'course_code' => 'CYB-101',
                'lesson_title' => 'Network Security',
                'module' => '1',
                'student_count' => 15,
                'status' => 'completed'
            ]
        ]);

        // Sample stats
        $stats = [
            'total_students' => 45,
            'active_courses' => 6,
            'completion_rate' => 87,
            'pending_grades' => 12
        ];

        // Sample recent activity
        $recentActivity = [
            [
                'title' => 'New student enrolled',
                'description' => 'John Doe joined Security Fundamentals course',
                'time' => '2 hours ago',
                'icon' => 'fa-user',
                'color' => 'info'
            ],
            [
                'title' => 'Live session completed',
                'description' => 'Advanced Security Training - Session 3',
                'time' => '4 hours ago',
                'icon' => 'fa-video',
                'color' => 'success'
            ],
            [
                'title' => 'Assignment submitted',
                'description' => 'Jane Smith submitted Security Assessment',
                'time' => '6 hours ago',
                'icon' => 'fa-file-upload',
                'color' => 'warning'
            ]
        ];

        return view('dashboards.instructor.offline', compact(
            'todayLessons',
            'stats',
            'recentActivity'
        ));
    }

    /**
     * Display the instructor dashboard in online mode
     */
    public function online(Request $request): View
    {
        // Sample current lesson
        $currentLesson = [
            'title' => 'Risk Assessment Fundamentals',
            'module' => 'Module 1',
            'duration' => '45',
            'progress' => 65
        ];

        // Sample lessons
        $lessons = [
            [
                'id' => 1,
                'title' => 'Introduction to Security',
                'duration' => '45 min',
                'status' => 'completed'
            ],
            [
                'id' => 2,
                'title' => 'Risk Assessment Fundamentals',
                'duration' => '60 min',
                'status' => 'current'
            ],
            [
                'id' => 3,
                'title' => 'Threat Analysis',
                'duration' => '50 min',
                'status' => 'pending'
            ]
        ];

        // Sample resources
        $resources = [
            [
                'title' => 'Course Handbook',
                'type' => 'PDF',
                'icon' => 'fa-file-pdf',
                'url' => '#'
            ],
            [
                'title' => 'Security Guidelines',
                'type' => 'Document',
                'icon' => 'fa-file-alt',
                'url' => '#'
            ],
            [
                'title' => 'Assessment Tools',
                'type' => 'Interactive',
                'icon' => 'fa-laptop-code',
                'url' => '#'
            ]
        ];

        // Sample chat messages
        $chatMessages = [
            [
                'name' => 'John Doe',
                'message' => 'I have a question about the risk matrix',
                'time' => '2 min ago',
                'avatar' => '/images/default-avatar.png'
            ],
            [
                'name' => 'Jane Smith',
                'message' => 'Can you explain the CVSS scoring again?',
                'time' => '5 min ago',
                'avatar' => '/images/default-avatar.png'
            ]
        ];

        // Sample students in class
        $studentsInClass = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'avatar' => '/images/default-avatar.png',
                'status' => 'online',
                'progress' => 75
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'avatar' => '/images/default-avatar.png',
                'status' => 'online',
                'progress' => 82
            ],
            [
                'id' => 3,
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'avatar' => '/images/default-avatar.png',
                'status' => 'away',
                'progress' => 60
            ]
        ];

        return view('dashboards.instructor.online', compact(
            'currentLesson',
            'lessons',
            'resources',
            'chatMessages',
            'studentsInClass'
        ));
    }

    /**
     * API endpoint to get instructor dashboard data
     */
    public function getInstructorData(Request $request)
    {
        // This would typically fetch real data from the database
        $data = [
            'mode' => $request->get('mode', 'offline'),
            'todayLessons' => [
                [
                    'id' => 1,
                    'title' => 'Introduction to Security',
                    'startTime' => '09:00',
                    'duration' => '60 min',
                    'studentCount' => 18,
                    'status' => 'scheduled'
                ],
                [
                    'id' => 2,
                    'title' => 'Risk Assessment',
                    'startTime' => '11:00',
                    'duration' => '45 min',
                    'studentCount' => 15,
                    'status' => 'in_progress'
                ]
            ],
            'stats' => [
                'totalStudents' => 45,
                'activeCourses' => 6,
                'completionRate' => 87,
                'pendingGrades' => 12
            ],
            'studentsInClass' => [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'status' => 'online',
                    'progress' => 75
                ],
                [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'status' => 'online',
                    'progress' => 82
                ]
            ]
        ];

        return response()->json($data);
    }

    /**
     * Handle lesson start/stop
     */
    public function toggleLesson(Request $request, $lessonId)
    {
        $action = $request->get('action'); // 'start' or 'stop'

        // In a real app, you would update the lesson status in the database

        return response()->json([
            'success' => true,
            'message' => "Lesson {$action}ed successfully",
            'lessonId' => $lessonId,
            'action' => $action
        ]);
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'course_date_id' => 'nullable|integer'
        ]);

        // In a real app, you would save the message to the database

        return response()->json([
            'success' => true,
            'message' => [
                'id' => rand(1000, 9999),
                'name' => 'Instructor',
                'message' => $request->message,
                'time' => 'now',
                'avatar' => '/images/instructor-avatar.png'
            ]
        ]);
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
