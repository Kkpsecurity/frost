<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin\Instructors;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Traits\StoragePathTrait;

class InstructorDashboardController extends Controller
{
    use PageMetaDataTrait;
    use StoragePathTrait;

    public $classData = [];

    public $instructorData = [];

    public $students = [];

    public function __construct()
    {
        // Make sure that the validation directories are created
        $idcardsPath = config('storage.paths.idcards', 'idcards');
        $headshotsPath = config('storage.paths.headshots', 'headshots');
        $this->ensureStoragePath($idcardsPath);
        $this->ensureStoragePath($headshotsPath);
    }


    /*********************** */
    /* View Outputs          */
    /*********************** */

    public function dashboard()
    {
        $content = array_merge([], self::renderPageMeta('instructor_dashboard'));
        return view('admin.instructors.dashboard', compact('content'));
    }

    /**
     * Validate instructor session for React components
     */
    public function validateInstructorSession()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'instructor' => [
                'id' => $admin->id,
                'fname' => $admin->name ?? 'Admin',
                'lname' => 'User',
                'name' => $admin->name ?? 'Admin User',
                'email' => $admin->email ?? '',
            ],
            'course_date' => null, // No active course for admin viewing
            'status' => 'admin_view'
        ]);
    }

    /**
     * Data Stream 2: Get classroom data for instructor dashboard
     */
    public function getClassroomData()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return classroom data structure for admin viewing
        return response()->json([
            'classroom' => [
                'id' => 'admin-view',
                'name' => 'Admin Classroom View',
                'course_name' => 'Course Management Overview',
                'status' => 'admin_access',
                'capacity' => null,
                'current_enrollment' => 0,
                'start_date' => null,
                'end_date' => null,
                'schedule' => null
            ],
            'metadata' => [
                'view_type' => 'admin',
                'permissions' => ['view_all', 'manage_all'],
                'last_updated' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Data Stream 3: Get students data for instructor dashboard
     */
    public function getStudentsData()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return students data structure for admin viewing
        return response()->json([
            'students' => [], // Empty for admin view - will be populated when viewing specific courses
            'summary' => [
                'total_students' => 0,
                'active_students' => 0,
                'pending_enrollments' => 0,
                'completed_courses' => 0
            ],
            'metadata' => [
                'view_type' => 'admin',
                'course_context' => null,
                'last_updated' => now()->toISOString()
            ]
        ]);
    }
}
