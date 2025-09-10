<?php

namespace App\Services;

use App\Models\User;
use App\Models\CourseAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Student Purchase Dashboard Service
 *
 * Handles data preparation and organization for the student purchase dashboard.
 * This service manages course authorizations, progress tracking, and dashboard analytics.
 *
 * @author FROST Development Team
 * @version 2.0
 */
class StudentPurchaseDashboardService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get comprehensive dashboard data for student
     *
     * Returns organized data structure for the main student dashboard
     * including courses, progress, activity, and quick actions.
     */
    public function getDashboardData()
    {
        try {
            // Use cache for performance
            $cacheKey = "student_dashboard_data_{$this->user->id}";

            return Cache::remember($cacheKey, 300, function() {
                return [
                    'user' => $this->getUserData(),
                    'summary' => $this->getDashboardSummary(),
                    'courses' => $this->getCoursesData(),
                    'recent_activity' => $this->getRecentActivity(),
                    'quick_actions' => $this->getQuickActions()
                ];
            });
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage(), [
                'user_id' => $this->user->id
            ]);
            return $this->getEmptyDashboardData();
        }
    }

    /**
     * Get user data for dashboard
     */
    protected function getUserData()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar ?? null,
            'timezone' => $this->user->timezone ?? config('app.timezone'),
            'preferences' => $this->getUserPreferences()
        ];
    }

    /**
     * Get dashboard summary statistics
     */
    protected function getDashboardSummary()
    {
        $courseAuths = $this->getUserCourseAuths();

        return [
            'total_courses' => $courseAuths->count(),
            'active_courses' => $courseAuths->where('completed_at', null)->count(),
            'completed_courses' => $courseAuths->where('completed_at', '!=', null)->count(),
            'total_progress' => $this->calculateOverallProgress($courseAuths),
            'certificates_earned' => 0, // TODO: Implement certificate tracking
            'study_streak' => $this->calculateStudyStreak(),
            'total_study_time' => $this->calculateTotalStudyTime()
        ];
    }

    /**
     * Get organized courses data
     */
    protected function getCoursesData()
    {
        $courseAuths = $this->getUserCourseAuths();

        return [
            'active' => $this->filterActiveCourses($courseAuths),
            'upcoming' => $this->filterUpcomingCourses($courseAuths),
            'completed' => $this->filterCompletedCourses($courseAuths),
            'expired' => $this->filterExpiredCourses($courseAuths)
        ];
    }

    /**
     * Get recent learning activity
     */
    protected function getRecentActivity($limit = 10)
    {
        // TODO: Implement activity tracking
        // For now, return placeholder data
        return [
            'activities' => [],
            'last_updated' => now()
        ];
    }

    /**
     * Get quick action buttons for dashboard
     */
    protected function getQuickActions()
    {
        return [
            [
                'title' => 'Continue Learning',
                'description' => 'Resume your last course',
                'url' => $this->getLastCourseUrl(),
                'icon' => 'play-circle',
                'color' => 'primary'
            ],
            [
                'title' => 'Browse Courses',
                'description' => 'Find new courses to enroll',
                'url' => route('courses.index'),
                'icon' => 'book-open',
                'color' => 'success'
            ],
            [
                'title' => 'View Progress',
                'description' => 'Check your learning progress',
                'url' => route('student.progress'),
                'icon' => 'chart-line',
                'color' => 'info'
            ]
        ];
    }

    // --- PROTECTED HELPER METHODS ---

    /**
     * Get user's course authorizations
     */
    protected function getUserCourseAuths()
    {
        return CourseAuth::where('user_id', $this->user->id)
            ->whereNull('disabled_at')
            ->with(['course', 'course.instructor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Filter active courses
     */
    protected function filterActiveCourses($courseAuths)
    {
        return $courseAuths->filter(function($courseAuth) {
            return is_null($courseAuth->completed_at) &&
                   (is_null($courseAuth->expire_date) || now()->lt($courseAuth->expire_date));
        })->take(6)->map(function($courseAuth) {
            return [
                'course_auth_id' => $courseAuth->id,
                'title' => $courseAuth->course->title,
                'description' => $courseAuth->course->description,
                'instructor' => $courseAuth->course->instructor->name ?? 'Unknown',
                'progress' => $this->calculateCourseProgress($courseAuth),
                'last_accessed' => $courseAuth->last_accessed_at,
                'url' => route('classroom.portal.class', $courseAuth->id)
            ];
        })->values();
    }

    /**
     * Filter upcoming courses
     */
    protected function filterUpcomingCourses($courseAuths)
    {
        return $courseAuths->filter(function($courseAuth) {
            return $courseAuth->start_date && now()->lt($courseAuth->start_date);
        })->take(3)->map(function($courseAuth) {
            return [
                'course_auth_id' => $courseAuth->id,
                'title' => $courseAuth->course->title,
                'start_date' => $courseAuth->start_date,
                'instructor' => $courseAuth->course->instructor->name ?? 'Unknown'
            ];
        })->values();
    }

    /**
     * Filter completed courses
     */
    protected function filterCompletedCourses($courseAuths)
    {
        return $courseAuths->filter(function($courseAuth) {
            return !is_null($courseAuth->completed_at);
        })->take(3)->map(function($courseAuth) {
            return [
                'course_auth_id' => $courseAuth->id,
                'title' => $courseAuth->course->title,
                'completed_at' => $courseAuth->completed_at,
                'final_score' => $courseAuth->final_score
            ];
        })->values();
    }

    /**
     * Filter expired courses
     */
    protected function filterExpiredCourses($courseAuths)
    {
        return $courseAuths->filter(function($courseAuth) {
            return $courseAuth->expire_date && now()->gt($courseAuth->expire_date);
        })->take(3)->map(function($courseAuth) {
            return [
                'course_auth_id' => $courseAuth->id,
                'title' => $courseAuth->course->title,
                'expired_at' => $courseAuth->expire_date
            ];
        })->values();
    }

    /**
     * Calculate progress for a specific course
     */
    protected function calculateCourseProgress($courseAuth)
    {
        // TODO: Implement actual progress calculation
        // For now, return placeholder
        return [
            'percentage' => 0,
            'completed_lessons' => 0,
            'total_lessons' => 0
        ];
    }

    /**
     * Calculate overall progress across all courses
     */
    protected function calculateOverallProgress($courseAuths)
    {
        if ($courseAuths->isEmpty()) {
            return 0;
        }

        // Simple calculation based on completion ratio
        $totalCourses = $courseAuths->count();
        $completedCourses = $courseAuths->where('completed_at', '!=', null)->count();

        return $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100) : 0;
    }

    /**
     * Calculate study streak (days)
     */
    protected function calculateStudyStreak()
    {
        // TODO: Implement study streak calculation
        return 0;
    }

    /**
     * Calculate total study time
     */
    protected function calculateTotalStudyTime()
    {
        // TODO: Implement study time tracking
        return '0h 0m';
    }

    /**
     * Get user preferences
     */
    protected function getUserPreferences()
    {
        return [
            'dashboard_view' => 'cards',
            'show_progress_bars' => true,
            'show_recent_activity' => true,
            'notifications_enabled' => true
        ];
    }

    /**
     * Get URL for last accessed course
     */
    protected function getLastCourseUrl()
    {
        $lastCourse = CourseAuth::where('user_id', $this->user->id)
            ->whereNull('disabled_at')
            ->whereNull('completed_at')
            ->orderBy('last_accessed_at', 'desc')
            ->first();

        if ($lastCourse) {
            return route('classroom.portal.class', $lastCourse->id);
        }

        return route('courses.index');
    }

    /**
     * Get empty dashboard data structure
     */
    protected function getEmptyDashboardData()
    {
        return [
            'user' => $this->getUserData(),
            'summary' => [
                'total_courses' => 0,
                'active_courses' => 0,
                'completed_courses' => 0,
                'total_progress' => 0,
                'certificates_earned' => 0,
                'study_streak' => 0,
                'total_study_time' => '0h 0m'
            ],
            'courses' => [
                'active' => [],
                'upcoming' => [],
                'completed' => [],
                'expired' => []
            ],
            'recent_activity' => [
                'activities' => [],
                'last_updated' => now()
            ],
            'quick_actions' => $this->getQuickActions()
        ];
    }
}
