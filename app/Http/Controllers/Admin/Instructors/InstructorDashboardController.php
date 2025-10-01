<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin\Instructors;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Traits\StoragePathTrait;
use App\Services\Frost\Instructors\InstructorDashboardService;
use App\Services\Frost\Instructors\CourseDatesService;
use App\Services\Frost\Instructors\ClassroomService;
use App\Services\Frost\Students\BackendStudentService;
use App\Services\ClassroomSessionService;

class InstructorDashboardController extends Controller
{
    use PageMetaDataTrait;
    use StoragePathTrait;

    public $classData = [];
    public $instructorData = [];
    public $students = [];

    protected InstructorDashboardService $dashboardService;
    protected CourseDatesService $courseDatesService;
    protected ClassroomService $classroomService;
    protected BackendStudentService $studentService;
    protected ClassroomSessionService $sessionService;

    public function __construct(
        InstructorDashboardService $dashboardService,
        CourseDatesService $courseDatesService,
        ClassroomService $classroomService,
        BackendStudentService $studentService,
        ClassroomSessionService $sessionService
    ) {
        $this->dashboardService = $dashboardService;
        $this->courseDatesService = $courseDatesService;
        $this->classroomService = $classroomService;
        $this->studentService = $studentService;
        $this->sessionService = $sessionService;

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
        $sessionData = $this->dashboardService->validateSession();

        if (!$sessionData['authenticated']) {
            return response()->json(['message' => $sessionData['message']], 401);
        }

        return response()->json($sessionData);
    }

    /**
     * Data Stream 2: Get classroom data for instructor dashboard
     */
    public function getClassroomData()
    {
        $classroomData = $this->classroomService->getClassroomData();

        if (isset($classroomData['error'])) {
            return response()->json(['message' => $classroomData['error']], 401);
        }

        return response()->json($classroomData);
    }

    /**
     * Data Stream 3: Get students data for instructor dashboard
     */
    public function getStudentsData()
    {
        $studentsData = $this->studentService->getStudentsForInstructor();

        if (isset($studentsData['error'])) {
            return response()->json(['message' => $studentsData['error']], 401);
        }

        return response()->json($studentsData);
    }

    /**
     * Get bulletin board data for when no active course dates
     */
    public function getBulletinBoardData()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $bulletinData = $this->courseDatesService->getBulletinBoardData();

        return response()->json($bulletinData);
    }

    /**
     * Get statistics for instructor dashboard
     */
    public function getStats()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $stats = $this->dashboardService->getInstructorStats();

        return response()->json($stats);
    }

    /**
     * Get today's lessons for instructor dashboard
     */
    public function getTodayLessons()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $lessons = $this->courseDatesService->getTodaysLessons();

        return response()->json($lessons);
    }

    /**
     * Get chat messages for instructor dashboard
     */
    public function getChatMessages()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // For now, return empty array - implement when chat system is ready
        return response()->json([
            'messages' => [],
            'total' => 0,
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'view_type' => 'chat_messages'
            ]
        ]);
    }

    /**
     * Send a message (placeholder implementation)
     */
    public function sendMessage()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Placeholder implementation
        return response()->json([
            'success' => true,
            'message' => 'Message sending functionality not yet implemented',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get online students for instructor dashboard
     */
    public function getOnlineStudents()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $onlineStudents = $this->studentService->getOnlineStudentsForInstructor();

        return response()->json($onlineStudents);
    }

    /**
     * DEBUG: Get today's lessons with detailed info
     */
    public function debugTodayLessons()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $lessons = $this->courseDatesService->getTodaysLessons();

        return response()->json([
            'debug_info' => 'Today\'s lessons with full structure',
            'data' => $lessons,
            'lessons_count' => count($lessons['lessons'] ?? []),
            'sample_lesson' => $lessons['lessons'][0] ?? null
        ]);
    }

    /**
     * Get completed courses data (InstUnits that have been completed)
     */
    public function getCompletedCourses()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $completedInstUnits = $this->dashboardService->getCompletedInstUnits();

        return response()->json($completedInstUnits);
    }

    /**
     * Get upcoming courses panel data for instructor dashboard
     * Shows overview of courses scheduled in next 2 weeks
     */
    public function getUpcomingCoursesPanel()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $upcomingCoursesPanel = $this->dashboardService->getUpcomingCoursesPanel();

        return response()->json($upcomingCoursesPanel);
    }

    /**
     * Start a class session - Create InstUnit and redirect to classroom
     */
    public function startClass($courseDateId)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Create InstUnit using ClassroomSessionService
            $instUnit = $this->sessionService->startClassroomSession((int) $courseDateId);

            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start class session. Please check the logs.'
                ], 500);
            }

            // Return success with redirect URL to classroom
            return response()->json([
                'success' => true,
                'inst_unit_id' => $instUnit->id,
                'course_date_id' => $courseDateId,
                'redirect_url' => "/instructor/classroom/{$courseDateId}",
                'message' => 'Class session started successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('InstructorDashboardController: Error starting class', [
                'course_date_id' => $courseDateId,
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error starting class: ' . $e->getMessage()
            ], 500);
        }
    }
}
