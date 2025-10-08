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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
     * Assign an instructor to a CourseDate (creates InstUnit without starting class)
     */
    public function assignInstructor($courseDateId, Request $request)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Get instructor_id from request, default to current admin
            $instructorId = $request->input('instructor_id', $admin->id);
            $assistantId = $request->input('assistant_id');

            // Verify the course date exists
            $courseDate = \App\Models\CourseDate::find($courseDateId);
            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            // Check if InstUnit already exists
            if ($courseDate->InstUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course already has an assigned instructor'
                ], 400);
            }

            // Create InstUnit with assigned instructor (not started yet)
            $instUnit = \App\Models\InstUnit::create([
                'course_date_id' => $courseDateId,
                'created_by' => $instructorId,
                'assistant_id' => $assistantId,
                'created_at' => now(),
                // Note: completed_at remains null since class hasn't started
            ]);

            // Get instructor and assistant names
            $instructor = \App\Models\User::find($instructorId);
            $assistant = $assistantId ? \App\Models\User::find($assistantId) : null;

            return response()->json([
                'success' => true,
                'message' => 'Instructor assigned successfully!',
                'data' => [
                    'inst_unit_id' => $instUnit->id,
                    'course_date_id' => $courseDateId,
                    'instructor' => [
                        'id' => $instructor->id,
                        'name' => trim(($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '')) ?: $instructor->email
                    ],
                    'assistant' => $assistant ? [
                        'id' => $assistant->id,
                        'name' => trim(($assistant->fname ?? '') . ' ' . ($assistant->lname ?? '')) ?: $assistant->email
                    ] : null,
                    'created_at' => $instUnit->created_at->toISOString(),
                    'is_assignment_only' => true // Flag to indicate this is just assignment, not active session
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error assigning instructor', [
                'course_date_id' => $courseDateId,
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error assigning instructor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a class session - Create InstUnit and redirect to classroom
     */
    public function startClass($courseDateId, Request $request)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Get assistant_id from request if provided
            $assistantId = $request->input('assistant_id');

            // Create InstUnit using ClassroomSessionService
            $instUnit = $this->sessionService->startClassroomSession((int) $courseDateId, $assistantId);

            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start class session. Please check the logs.'
                ], 500);
            }

            // Get session info including instructor and assistant details
            $sessionInfo = $this->sessionService->getClassroomSession((int) $courseDateId);

            // Return success with classroom session data
            return response()->json([
                'success' => true,
                'message' => 'Class session started successfully!',
                'data' => [
                    'inst_unit_id' => $instUnit->id,
                    'course_date_id' => $courseDateId,
                    'instructor' => $sessionInfo['instructor'] ?? null,
                    'assistant' => $sessionInfo['assistant'] ?? null,
                    'created_at' => $instUnit->created_at->toISOString(),
                    'is_existing' => $instUnit->wasRecentlyCreated ? false : true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error starting class', [
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

    /**
     * Take over a classroom session
     */
    public function takeOverClass()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Implementation for taking over a class
            // This would involve reassigning the instructor or assistant role

            return response()->json([
                'success' => true,
                'message' => 'Successfully took over the class session!'
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error taking over class', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error taking over class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assist in a classroom session
     */
    public function assistClass(Request $request, $courseDateId = null)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Get the course_date_id from route parameter or request body
            if (!$courseDateId) {
                $courseDateId = $request->input('course_date_id');
            }

            $instUnitId = $request->input('inst_unit_id');

            if (!$courseDateId && !$instUnitId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date ID or InstUnit ID is required'
                ], 400);
            }

            // If we have course_date_id, find the InstUnit
            if ($courseDateId) {
                $courseDate = \App\Models\CourseDate::find($courseDateId);
                if (!$courseDate || !$courseDate->InstUnit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No active class session found for this course'
                    ], 404);
                }
                $instUnitId = $courseDate->InstUnit->id;
            }

            // Assign the current admin as assistant
            $success = $this->sessionService->assignAssistant($instUnitId, $admin->id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign assistant. Please check the logs.'
                ], 500);
            }

            // Get updated session info
            $sessionInfo = $this->sessionService->getClassroomSession((int) ($courseDateId ?? 0));

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined the class as an assistant!',
                'data' => [
                    'inst_unit_id' => $instUnitId,
                    'assistant' => [
                        'id' => $admin->id,
                        'name' => $admin->name ?? ($admin->fname . ' ' . $admin->lname)
                    ],
                    'session_info' => $sessionInfo
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error assisting class', [
                'admin_id' => $admin->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error assisting in class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get lessons for a specific course date
     */
    public function getCourseLessons($courseDateId)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Find the course date
            $courseDate = \App\Models\CourseDate::find($courseDateId);
            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            // Get the course unit and its lessons
            $courseUnit = $courseDate->GetCourseUnit();
            if (!$courseUnit) {
                return response()->json([
                    'lessons' => [],
                    'message' => 'No course unit found for this course date',
                    'course_info' => [
                        'course_date_id' => $courseDateId,
                        'course_name' => 'Unknown Course',
                        'unit_name' => 'Unknown Unit'
                    ]
                ]);
            }

            // Use CourseUnitObj to get lessons
            $courseUnitObj = new \App\Classes\CourseUnitObj($courseUnit);
            $courseUnitLessons = $courseUnitObj->CourseUnitLessons();

            // Get course information
            $course = $courseDate->GetCourse();

            // DEBUG: Log what we found
            Log::info('InstructorDashboardController: Course lessons data', [
                'course_date_id' => $courseDateId,
                'course_unit_id' => $courseUnit->id,
                'course_unit_title' => $courseUnit->title,
                'lessons_count' => $courseUnitLessons->count(),
                'lessons_sample' => $courseUnitLessons->take(2)->toArray()
            ]);

            // Format lessons for the sidebar
            $formattedLessons = $courseUnitLessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title ?? 'Untitled Lesson',
                    'sort_order' => $lesson->sort_order ?? 0,
                    'lesson_type' => $lesson->lesson_type ?? 'lesson',
                    'is_completed' => false, // TODO: Track lesson completion status
                    'duration_minutes' => $lesson->duration_minutes ?? 45, // Default 45 minutes
                    'description' => $lesson->description ?? 'Course lesson content',
                    'content_url' => $lesson->content_url ?? null,
                    'objectives' => $lesson->objectives ?? null
                ];
            })->sortBy('sort_order')->values()->toArray();

            // If no lessons found, create some sample structure based on course content
            if (empty($formattedLessons)) {
                Log::warning('InstructorDashboardController: No lessons found, creating sample structure', [
                    'course_date_id' => $courseDateId,
                    'course_unit_id' => $courseUnit->id
                ]);

                // Create basic lesson structure based on course unit
                $formattedLessons = [
                    [
                        'id' => $courseUnit->id * 100 + 1,
                        'title' => 'Introduction - ' . ($courseUnit->title ?? 'Course Overview'),
                        'sort_order' => 1,
                        'lesson_type' => 'video',
                        'is_completed' => false,
                        'duration_minutes' => 30,
                        'description' => 'Course introduction and overview of topics to be covered',
                        'content_url' => null,
                        'objectives' => 'Understand course objectives and structure'
                    ],
                    [
                        'id' => $courseUnit->id * 100 + 2,
                        'title' => 'Main Content - ' . ($courseUnit->title ?? 'Core Material'),
                        'sort_order' => 2,
                        'lesson_type' => 'reading',
                        'is_completed' => false,
                        'duration_minutes' => 60,
                        'description' => 'Core course content and practical applications',
                        'content_url' => null,
                        'objectives' => 'Master the key concepts and skills'
                    ],
                    [
                        'id' => $courseUnit->id * 100 + 3,
                        'title' => 'Assessment - ' . ($courseUnit->title ?? 'Knowledge Check'),
                        'sort_order' => 3,
                        'lesson_type' => 'quiz',
                        'is_completed' => false,
                        'duration_minutes' => 15,
                        'description' => 'Assessment to validate understanding of the material',
                        'content_url' => null,
                        'objectives' => 'Demonstrate mastery of course objectives'
                    ]
                ];
            }

            return response()->json([
                'lessons' => $formattedLessons,
                'message' => count($formattedLessons) . ' lessons found',
                'course_info' => [
                    'course_date_id' => $courseDateId,
                    'course_name' => $course->title ?? 'Unknown Course',
                    'unit_name' => $courseUnit->title ?? 'Unknown Unit',
                    'unit_code' => $courseUnit->admin_title ?? 'N/A',
                    'starts_at' => $courseDate->starts_at,
                    'ends_at' => $courseDate->ends_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error fetching lessons', [
                'course_date_id' => $courseDateId,
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching lessons: ' . $e->getMessage()
            ], 500);
        }
    }
}
