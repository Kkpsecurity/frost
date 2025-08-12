<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseDate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorDashboardController extends Controller
{
    /**
     * Get instructor dashboard data
     */
    public function dashboard(Request $request)
    {
        try {
            // Get instructor - either from request or authenticated user
            $instructorId = $request->input('instructor_id');
            $instructor = $instructorId
                ? User::findOrFail($instructorId)
                : Auth::user();

            // Allow admin users to access instructor dashboard for testing/management
            if (!$instructor || !in_array($instructor->role, ['instructor', 'admin', 'support'])) {
                // For testing purposes, create a mock instructor profile if no user is authenticated
                if (!$instructor) {
                    $instructor = (object) [
                        'id' => 1,
                        'fname' => 'Test',
                        'lname' => 'Instructor',
                        'email' => 'test@instructor.com',
                        'role' => 'instructor',
                        'instructor_id' => 1,
                    ];
                } else {
                    return response()->json([
                        'error' => 'Invalid instructor or unauthorized access'
                    ], 403);
                }
            }

            // Get today's date range
            $today = Carbon::today();
            $endOfToday = Carbon::today()->endOfDay();

            // Get active courses (today)
            $activeCourses = CourseDate::with(['course', 'course_unit', 'students', 'attendance_records'])
                ->where('instructor_id', $instructor->id)
                ->where('is_active', true)
                ->whereBetween('starts_at', [$today, $endOfToday])
                ->orderBy('starts_at')
                ->get();

            // Get upcoming courses (next 7 days)
            $nextWeek = Carbon::today()->addDays(7);
            $upcomingCourses = CourseDate::with(['course', 'course_unit', 'students'])
                ->where('instructor_id', $instructor->id)
                ->where('is_active', true)
                ->where('starts_at', '>', $endOfToday)
                ->where('starts_at', '<=', $nextWeek)
                ->orderBy('starts_at')
                ->get();

            // Get recent courses (last 7 days)
            $lastWeek = Carbon::today()->subDays(7);
            $recentCourses = CourseDate::with(['course', 'course_unit', 'students', 'attendance_records'])
                ->where('instructor_id', $instructor->id)
                ->where('ends_at', '>=', $lastWeek)
                ->where('ends_at', '<', $today)
                ->orderBy('starts_at', 'desc')
                ->get();

            // Determine if instructor has scheduled courses
            $hasScheduledCourses = $activeCourses->count() > 0 || $upcomingCourses->count() > 0;

            // Get bulletin board data (for instructors with no scheduled courses)
            $bulletinContent = $this->getBulletinBoardData();

            // Prepare instructor profile
            $instructorProfile = [
                'id' => $instructor->id,
                'fname' => $instructor->fname,
                'lname' => $instructor->lname,
                'email' => $instructor->email,
                'role' => $instructor->role,
                'instructor_id' => $instructor->instructor_id ?? $instructor->id,
            ];

            return response()->json([
                'has_scheduled_courses' => $hasScheduledCourses,
                'active_courses' => $this->formatCourseDates($activeCourses),
                'upcoming_courses' => $this->formatCourseDates($upcomingCourses),
                'recent_courses' => $this->formatCourseDates($recentCourses),
                'bulletin_content' => $bulletinContent,
                'instructor_profile' => $instructorProfile,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bulletin board data for instructors with no scheduled courses
     */
    private function getBulletinBoardData()
    {
        // Mock data for now - in a real app, these would come from database tables
        $announcements = [
            [
                'id' => 1,
                'title' => 'Welcome to the Instructor Portal',
                'content' => 'We\'re excited to have you as part of our teaching team. Check out the resources section for helpful materials.',
                'type' => 'general',
                'author' => 'Admin Team',
                'created_at' => Carbon::now()->subDays(1)->toISOString(),
                'expires_at' => null,
            ],
            [
                'id' => 2,
                'title' => 'New Training Resources Available',
                'content' => 'We\'ve added new video tutorials and teaching guides to help you get started with our platform.',
                'type' => 'training',
                'author' => 'Training Department',
                'created_at' => Carbon::now()->subDays(3)->toISOString(),
                'expires_at' => null,
            ],
            [
                'id' => 3,
                'title' => 'Schedule Your First Course',
                'content' => 'Ready to start teaching? Use the course scheduling system to set up your first class.',
                'type' => 'general',
                'author' => 'Support Team',
                'created_at' => Carbon::now()->subDays(5)->toISOString(),
                'expires_at' => null,
            ],
        ];

        // Get available courses for scheduling
        $availableCourses = Course::where('is_active', true)
            ->select('id', 'title', 'description', 'total_minutes', 'price', 'is_active')
            ->orderBy('title')
            ->limit(10)
            ->get();

        // Mock instructor resources
        $instructorResources = [
            [
                'id' => 1,
                'title' => 'Getting Started Guide',
                'description' => 'Everything you need to know to begin teaching',
                'type' => 'document',
                'url' => '/instructor/resources/getting-started.pdf',
                'category' => 'Getting Started',
                'created_at' => Carbon::now()->toISOString(),
            ],
            [
                'id' => 2,
                'title' => 'Teaching Best Practices',
                'description' => 'Tips and techniques for effective online instruction',
                'type' => 'video',
                'url' => '/instructor/resources/best-practices-video',
                'category' => 'Teaching',
                'created_at' => Carbon::now()->toISOString(),
            ],
            [
                'id' => 3,
                'title' => 'Platform Tutorial',
                'description' => 'Step-by-step walkthrough of the instructor interface',
                'type' => 'training',
                'url' => '/instructor/resources/platform-tutorial',
                'category' => 'Platform',
                'created_at' => Carbon::now()->toISOString(),
            ],
            [
                'id' => 4,
                'title' => 'Support Center',
                'description' => 'Get help with technical issues and questions',
                'type' => 'link',
                'url' => '/instructor/support',
                'category' => 'Support',
                'created_at' => Carbon::now()->toISOString(),
            ],
        ];

        // Quick stats
        $quickStats = [
            'total_instructors' => User::where('role', 'instructor')->count(),
            'active_courses_today' => CourseDate::where('is_active', true)
                ->whereBetween('starts_at', [Carbon::today(), Carbon::today()->endOfDay()])
                ->count(),
            'students_in_system' => User::where('role', 'student')->count(),
        ];

        return [
            'announcements' => $announcements,
            'available_courses' => $availableCourses,
            'instructor_resources' => $instructorResources,
            'quick_stats' => $quickStats,
        ];
    }

    /**
     * Format course dates for frontend consumption
     */
    private function formatCourseDates($courseDates)
    {
        return $courseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_id' => $courseDate->course_id,
                'course_unit_id' => $courseDate->course_unit_id,
                'instructor_id' => $courseDate->instructor_id,
                'starts_at' => $courseDate->starts_at->toISOString(),
                'ends_at' => $courseDate->ends_at->toISOString(),
                'is_active' => $courseDate->is_active,
                'student_count' => $courseDate->students->count(),
                'notes' => $courseDate->notes,
                'course' => [
                    'id' => $courseDate->course->id,
                    'title' => $courseDate->course->title,
                    'title_long' => $courseDate->course->title_long,
                    'price' => $courseDate->course->price,
                    'total_minutes' => $courseDate->course->total_minutes,
                    'is_active' => $courseDate->course->is_active,
                    'course_type' => $courseDate->course->course_type,
                    'description' => $courseDate->course->description,
                ],
                'course_unit' => [
                    'id' => $courseDate->course_unit->id,
                    'title' => $courseDate->course_unit->title,
                    'admin_title' => $courseDate->course_unit->admin_title,
                    'ordering' => $courseDate->course_unit->ordering,
                ],
                'students' => $courseDate->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'fname' => $student->fname,
                        'lname' => $student->lname,
                        'email' => $student->email,
                        'student_id' => $student->student_id,
                    ];
                }),
                'attendance_records' => $courseDate->attendance_records->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'student_id' => $record->student_id,
                        'status' => $record->status,
                        'marked_at' => $record->marked_at,
                        'notes' => $record->notes,
                    ];
                }),
            ];
        });
    }
}
