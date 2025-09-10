<?php

namespace App\Services;

use App\Casts\JSONCast;
use App\Models\User;
use App\Services\ClassroomDashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

/**
 * Student Dashboard Service
 *
 * Handles data preparation and organization for the main student dashboard.
 * Focuses specifically on student course authorizations, progress tracking, and student-specific analytics.
 * Uses ClassroomDashboardService for classroom-related data.
 */
class StudentDashboardService
{
    protected ?User $user;
    protected ClassroomDashboardService $classroomService;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
        $this->classroomService = new ClassroomDashboardService($user);

     
    }

    /**
     * Get complete dashboard data for the user
     */
    public function getClassData(): array
    {
        if (!$this->user) {
            return $this->getEmptyDashboardData();
        }

        try {
            $classroomData = $this->classroomService->getClassroomData();
            $instructorData = $classroomData['instructors'];
            $courseDates = $classroomData['courseDates'];

            return [
                'instructors' => $instructorData,
                'courseDates' => $courseDates,
            ];
        } catch (\Exception $e) {
            Log::error('StudentDashboardService: Error getting dashboard data: ' . $e->getMessage());
            return $this->getEmptyDashboardData();
        }
    }

    /**
     * Get empty dashboard data structure (for missing user / errors)
     */
    protected function getEmptyDashboardData(): array
    {
        return [
            'instructors' => collect(),
            'courseDates' => collect(),
        ];
    }

    /**
     * Get student data matching the actual User model structure
     * Based on user-provided data structure: id, fname, lname, email, class
     */
    public function getStudentData(): array
    {
        if (!$this->user) {
            return [];
        }

        return [
            'id' => $this->user->id,
            'fname' => $this->user->fname,
            'lname' => $this->user->lname,
            'email' => $this->user->email,
            'email_verified_at' => 'datetime',

            'password' => 'string',
            // 100
            'remember_token' => 'string',
            // 100

            'avatar' => 'string',
            'use_gravatar' => 'boolean',

            'student_info' => [
                'fname' => $this->user->fname,
                'middle_initial' => $this->user->student_info['middle_initial'] ?? null,
                'lname' => $this->user->lname,
                'email' => $this->user->email,
                'suffix' => $this->user->student_info['suffix'] ?? null,
                'dob' => $this->user->student_info['dob'] ?? null,
                'phone' => $this->user->student_info['phone'] ?? null,
            ],

            'email_opt_in' => 'boolean',

            'created_at' => 'datetime',
            'update_at' => 'datetime',
        ];
    }

    /**
     * Get merged course authorizations (active + completed) as a simple collection
     * This is student-specific data focused on their course progress
     */
    public function getCourseAuths(): Collection
    {
        if (!$this->user) {
            return collect();
        }

        try {


            // Check if user has required methods
            if (!$this->user || !method_exists($this->user, 'ActiveCourseAuths')) {
                Log::warning('StudentDashboardService: User missing required relationships');
                return collect();
            }

            $activeCourseAuths = $this->user->ActiveCourseAuths()->get();

            $completedCourseAuths = $this->user->InActiveCourseAuths()
                ->whereNotNull('completed_at')
                ->get();

            return $activeCourseAuths->concat($completedCourseAuths)->values();
        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error getting course auths', [
                'user_id' => $this->user?->id,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Clear cache for student-specific data
     */
    public function clearCache(): void
    {
        if (!$this->user) {
            return;
        }

        try {
            $keys = [
                "student_dashboard_stats_{$this->user->id}",
                "student_course_auths_{$this->user->id}",
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Also clear classroom cache since dashboard depends on it
            $this->classroomService->clearCache();

            Log::info("StudentDashboardService: Cleared cache for user: {$this->user->id}");
        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error clearing cache', [
                'user_id' => $this->user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
