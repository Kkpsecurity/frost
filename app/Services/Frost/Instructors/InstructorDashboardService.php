<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service for managing instructor dashboard data and operations
 * Handles authentication, validation, and dashboard content for instructors
 */
class InstructorDashboardService
{
    /**
     * Validate instructor session for React components
     *
     * @return array
     */
    public function validateSession(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [
                'authenticated' => false,
                'message' => 'Unauthenticated'
            ];
        }

        return [
            'authenticated' => true,
            'instructor' => [
                'id' => $admin->id,
                'fname' => $admin->name ?? 'Admin',
                'lname' => 'User',
                'name' => $admin->name ?? 'Admin User',
                'email' => $admin->email ?? '',
            ],
            'course_date' => null, // No active course for admin viewing
            'status' => 'admin_view'
        ];
    }

    /**
     * Get instructor profile data
     *
     * @return array
     */
    public function getInstructorProfile(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [];
        }

        return [
            'id' => $admin->id,
            'name' => $admin->name ?? 'Admin User',
            'email' => $admin->email ?? '',
            'role' => 'administrator',
            'permissions' => [
                'view_all_courses',
                'manage_students',
                'access_bulletin_board',
                'view_course_statistics'
            ],
            'last_login' => $admin->updated_at ?? now(),
            'session_start' => now(),
        ];
    }

    /**
     * Get dashboard metadata
     *
     * @return array
     */
    public function getDashboardMetadata(): array
    {
        return [
            'view_type' => 'admin_instructor_dashboard',
            'permissions' => ['view_all', 'manage_all'],
            'last_updated' => now()->toISOString(),
            'timezone' => config('app.timezone'),
            'version' => '1.0.0'
        ];
    }
}
