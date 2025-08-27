<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Student Dashboard Controller
 * Handles the student dashboard functionality
 */
class StudentDashboardController extends Controller
{
    /**
     * Display the student dashboard
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Sample courses for the dashboard
        $courses = [
            [
                'id' => 1,
                'title' => 'Security Fundamentals',
                'instructor' => 'Dr. Smith',
                'progress' => 75,
                'lessons_completed' => 12,
                'total_lessons' => 16,
                'next_lesson' => 'Network Security Basics',
                'next_lesson_date' => '2024-01-15 10:00:00',
                'status' => 'active',
                'image' => '/images/courses/security-fundamentals.jpg'
            ],
            [
                'id' => 2,
                'title' => 'Advanced Cyber Defense',
                'instructor' => 'Prof. Johnson',
                'progress' => 45,
                'lessons_completed' => 9,
                'total_lessons' => 20,
                'next_lesson' => 'Incident Response Planning',
                'next_lesson_date' => '2024-01-16 14:00:00',
                'status' => 'active',
                'image' => '/images/courses/cyber-defense.jpg'
            ],
            [
                'id' => 3,
                'title' => 'Penetration Testing',
                'instructor' => 'Mr. Davis',
                'progress' => 100,
                'lessons_completed' => 18,
                'total_lessons' => 18,
                'next_lesson' => null,
                'next_lesson_date' => null,
                'status' => 'completed',
                'image' => '/images/courses/pen-testing.jpg'
            ]
        ];

        // Sample lessons for current course
        $lessons = [
            [
                'id' => 1,
                'title' => 'Introduction to Cybersecurity',
                'type' => 'video',
                'duration' => '45 min',
                'status' => 'completed',
                'progress' => 100,
                'date' => '2024-01-08'
            ],
            [
                'id' => 2,
                'title' => 'Threat Landscape Overview',
                'type' => 'video',
                'duration' => '60 min',
                'status' => 'completed',
                'progress' => 100,
                'date' => '2024-01-09'
            ],
            [
                'id' => 3,
                'title' => 'Risk Assessment Fundamentals',
                'type' => 'interactive',
                'duration' => '30 min',
                'status' => 'completed',
                'progress' => 100,
                'date' => '2024-01-10'
            ],
            [
                'id' => 4,
                'title' => 'Network Security Basics',
                'type' => 'video',
                'duration' => '90 min',
                'status' => 'current',
                'progress' => 60,
                'date' => '2024-01-15'
            ],
            [
                'id' => 5,
                'title' => 'Firewall Configuration',
                'type' => 'lab',
                'duration' => '120 min',
                'status' => 'locked',
                'progress' => 0,
                'date' => '2024-01-17'
            ]
        ];

        // Sample assignments
        $assignments = [
            [
                'id' => 1,
                'title' => 'Security Policy Analysis',
                'course' => 'Security Fundamentals',
                'due_date' => '2024-01-20',
                'status' => 'pending',
                'type' => 'essay',
                'points' => 100
            ],
            [
                'id' => 2,
                'title' => 'Vulnerability Assessment Report',
                'course' => 'Advanced Cyber Defense',
                'due_date' => '2024-01-25',
                'status' => 'in_progress',
                'type' => 'report',
                'points' => 150
            ],
            [
                'id' => 3,
                'title' => 'Network Security Lab',
                'course' => 'Security Fundamentals',
                'due_date' => '2024-01-18',
                'status' => 'submitted',
                'type' => 'lab',
                'points' => 75,
                'grade' => 'A-'
            ]
        ];

        // Sample recent activity
        $recentActivity = [
            [
                'type' => 'lesson_completed',
                'title' => 'Completed: Risk Assessment Fundamentals',
                'course' => 'Security Fundamentals',
                'time' => '2 hours ago',
                'icon' => 'fa-check-circle',
                'color' => 'success'
            ],
            [
                'type' => 'assignment_submitted',
                'title' => 'Submitted: Network Security Lab',
                'course' => 'Security Fundamentals',
                'time' => '1 day ago',
                'icon' => 'fa-upload',
                'color' => 'info'
            ],
            [
                'type' => 'grade_received',
                'title' => 'Grade received: A- for Security Policy Quiz',
                'course' => 'Security Fundamentals',
                'time' => '2 days ago',
                'icon' => 'fa-star',
                'color' => 'warning'
            ],
            [
                'type' => 'message',
                'title' => 'New message from Dr. Smith',
                'course' => 'Security Fundamentals',
                'time' => '3 days ago',
                'icon' => 'fa-envelope',
                'color' => 'primary'
            ]
        ];

        // Dashboard stats
        $stats = [
            'courses_enrolled' => count(array_filter($courses, fn($c) => $c['status'] === 'active')),
            'courses_completed' => count(array_filter($courses, fn($c) => $c['status'] === 'completed')),
            'total_lessons' => array_sum(array_column($courses, 'lessons_completed')),
            'assignments_pending' => count(array_filter($assignments, fn($a) => $a['status'] === 'pending')),
            'current_gpa' => 3.85,
            'total_points' => 2450
        ];

        return view('dashboards.student.index', compact(
            'courses',
            'lessons',
            'assignments',
            'recentActivity',
            'stats'
        ));
    }

    /**
     * Get course progress (API endpoint)
     */
    public function getCourseProgress(Request $request, $courseId)
    {
        // Mock data - replace with actual database queries
        $progress = [
            'course_id' => $courseId,
            'overall_progress' => 75,
            'lessons_completed' => 12,
            'total_lessons' => 16,
            'time_spent' => '24 hours',
            'last_accessed' => '2 hours ago',
            'next_lesson' => [
                'id' => 13,
                'title' => 'Network Security Basics',
                'estimated_time' => '90 min'
            ]
        ];

        return response()->json($progress);
    }

    /**
     * Update lesson progress
     */
    public function updateLessonProgress(Request $request, $lessonId)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
            'time_spent' => 'sometimes|numeric|min:0'
        ]);

        // In a real app, update the lesson progress in the database
        
        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'progress' => $request->progress
        ]);
    }

    /**
     * Get assignments for student
     */
    public function getAssignments(Request $request)
    {
        $status = $request->get('status', 'all');
        $course = $request->get('course');

        // Mock assignments - replace with actual database query
        $assignments = [
            [
                'id' => 1,
                'title' => 'Security Policy Analysis',
                'course' => 'Security Fundamentals',
                'due_date' => '2024-01-20',
                'status' => 'pending',
                'description' => 'Analyze the provided security policy document and identify strengths and weaknesses.'
            ],
            [
                'id' => 2,
                'title' => 'Vulnerability Assessment',
                'course' => 'Advanced Cyber Defense',
                'due_date' => '2024-01-25',
                'status' => 'in_progress',
                'description' => 'Conduct a comprehensive vulnerability assessment on the provided network.'
            ]
        ];

        return response()->json([
            'success' => true,
            'assignments' => $assignments
        ]);
    }

    /**
     * Submit assignment
     */
    public function submitAssignment(Request $request, $assignmentId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'comments' => 'sometimes|string|max:1000'
        ]);

        // In a real app, handle file upload and save submission
        
        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully',
            'submission_id' => rand(1000, 9999)
        ]);
    }

    /**
     * Get student activity feed
     */
    public function getActivityFeed(Request $request)
    {
        $limit = $request->get('limit', 10);

        // Mock activity data - replace with actual database query
        $activities = [
            [
                'id' => 1,
                'type' => 'lesson_completed',
                'title' => 'Risk Assessment Fundamentals',
                'description' => 'Completed lesson with 95% score',
                'course' => 'Security Fundamentals',
                'timestamp' => '2024-01-14 14:30:00',
                'points' => 50
            ],
            [
                'id' => 2,
                'type' => 'assignment_graded',
                'title' => 'Network Security Lab',
                'description' => 'Received grade: A-',
                'course' => 'Security Fundamentals',
                'timestamp' => '2024-01-13 09:15:00',
                'points' => 75
            ]
        ];

        return response()->json([
            'success' => true,
            'activities' => array_slice($activities, 0, $limit)
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        // Mock stats - replace with actual database queries
        $stats = [
            'courses_active' => 2,
            'courses_completed' => 1,
            'lessons_completed' => 21,
            'assignments_pending' => 2,
            'assignments_submitted' => 8,
            'total_points' => 2450,
            'current_gpa' => 3.85,
            'study_streak' => 12,
            'total_study_time' => '48 hours'
        ];

        return response()->json($stats);
    }
}
