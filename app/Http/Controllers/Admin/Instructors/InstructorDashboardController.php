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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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
     * Get current user info for React components
     */
    public function getCurrentUser()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'id' => $admin->id,
            'name' => trim(($admin->fname ?? '') . ' ' . ($admin->lname ?? '')) ?: $admin->email,
            'email' => $admin->email,
            'role' => $admin->role ?? 'admin'
        ]);
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
                'generated_at' => now()->format('c'),
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
            'timestamp' => now()->format('c')
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
     * Get upcoming courses data split by D40/G28 types for React components
     * Shows next 14 days of courses with instructor assignment status and enrollment
     */
    public function getUpcomingCoursesData()
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Get upcoming course dates for next 14 days
            $startDate = now()->startOfDay();
            $endDate = now()->addDays(14)->endOfDay();

            $upcomingCourseDates = \App\Models\CourseDate::whereBetween('starts_at', [$startDate, $endDate])
                ->where('is_active', true)
                ->with(['CourseUnit', 'InstUnit.User', 'InstUnit.Assistant'])
                ->orderBy('starts_at', 'asc')
                ->get();

            // Separate D40 and G28 courses
            $d40Courses = [];
            $g28Courses = [];

            foreach ($upcomingCourseDates as $courseDate) {
                $course = $courseDate->GetCourse();
                $courseUnit = $courseDate->CourseUnit;
                $instUnit = $courseDate->InstUnit;

                // Get student enrollment count (StudentUnit records for this course date)
                $enrollmentCount = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->count();

                // Format course data
                $courseData = [
                    'id' => $courseDate->id,
                    'course_name' => $course->title ?? $course->course_name ?? 'Unknown Course',
                    'unit_name' => $courseUnit->title ?? 'Unit ' . ($courseUnit->unit_number ?? '?'),
                    'unit_code' => $courseUnit->admin_title ?? $course->course_code ?? 'N/A',
                    'starts_at' => $courseDate->starts_at->format('M j, Y g:i A'),
                    'starts_at_iso' => $courseDate->starts_at->toAtomString(),
                    'date' => $courseDate->starts_at->format('M j'),
                    'time' => $courseDate->starts_at->format('g:i A'),
                    'day_name' => $courseDate->starts_at->format('l'),
                    'instructor_assigned' => $instUnit ? true : false,
                    'instructor_name' => $instUnit && $instUnit->User ?
                        trim(($instUnit->User->fname ?? '') . ' ' . ($instUnit->User->lname ?? '')) ?: $instUnit->User->email :
                        null,
                    'assistant_name' => $instUnit && $instUnit->Assistant ?
                        trim(($instUnit->Assistant->fname ?? '') . ' ' . ($instUnit->Assistant->lname ?? '')) ?: $instUnit->Assistant->email :
                        null,
                    'enrollment_count' => $enrollmentCount,
                    'is_today' => $courseDate->starts_at->isToday(),
                    'is_tomorrow' => $courseDate->starts_at->isTomorrow(),
                    'days_until' => now()->startOfDay()->diffInDays($courseDate->starts_at->startOfDay(), false)
                ];

                // Categorize by course type (D40 vs G28)
                $courseCode = $course->course_code ?? '';
                if (str_contains($courseCode, 'D40') || str_contains($courseCode, 'D-40')) {
                    $d40Courses[] = $courseData;
                } elseif (str_contains($courseCode, 'G28') || str_contains($courseCode, 'G-28')) {
                    $g28Courses[] = $courseData;
                } else {
                    // Default to D40 if unclear
                    $d40Courses[] = $courseData;
                }
            }

            // Calculate summary statistics
            $totalCourses = count($d40Courses) + count($g28Courses);
            $assignedCourses = count(array_filter(array_merge($d40Courses, $g28Courses), fn($c) => $c['instructor_assigned']));
            $totalEnrollment = array_sum(array_column(array_merge($d40Courses, $g28Courses), 'enrollment_count'));

            return response()->json([
                'success' => true,
                'data' => [
                    'd40_courses' => $d40Courses,
                    'g28_courses' => $g28Courses,
                    'summary' => [
                        'total_courses' => $totalCourses,
                        'd40_count' => count($d40Courses),
                        'g28_count' => count($g28Courses),
                        'assigned_count' => $assignedCourses,
                        'unassigned_count' => $totalCourses - $assignedCourses,
                        'total_enrollment' => $totalEnrollment,
                        'date_range_start' => $startDate->format('M j, Y'),
                        'date_range_end' => $endDate->format('M j, Y'),
                        'generated_at' => now()->toAtomString()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('InstructorDashboardController: Error fetching upcoming courses data', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching upcoming courses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign an instructor to a CourseDate (creates InstUnit without starting class)
     * NOTE: This endpoint is deprecated - instructors should be assigned by clicking "Start Class"
     * Keeping for backward compatibility but requiring explicit instructor_id
     */
    public function assignInstructor($courseDateId, Request $request)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // FIXED: Require explicit instructor_id - no auto-assignment
            $instructorId = $request->input('instructor_id');

            if (!$instructorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'instructor_id is required. Auto-assignment has been disabled to prevent accidental assignments.'
                ], 400);
            }

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
                    'created_at' => $instUnit->created_at->format('c'),
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
                    'created_at' => $this->formatDateSafely($instUnit->created_at),
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

            // Use CourseUnit to get lessons directly (not CourseUnitLessons pivot)
            $lessons = $courseUnit->getLessons();

            // Get course information
            $course = $courseDate->GetCourse();

            // DEBUG: Log what we found
            Log::info('InstructorDashboardController: Course lessons data', [
                'course_date_id' => $courseDateId,
                'course_unit_id' => $courseUnit->id,
                'course_unit_title' => $courseUnit->title,
                'lessons_count' => $lessons->count(),
                'lessons_sample' => $lessons->take(2)->toArray()
            ]);

            // Format lessons for the sidebar
            $formattedLessons = $lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title ?? 'Untitled Lesson',
                    'sort_order' => $lesson->pivot->ordering ?? 0,
                    'lesson_type' => 'lesson',
                    'is_completed' => false, // TODO: Track lesson completion status
                    'duration_minutes' => $lesson->credit_minutes ?? 45, // Use actual credit minutes
                    'description' => 'Course lesson content',
                    'content_url' => null,
                    'objectives' => null
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

    /**
     * Safely format a date value that could be Carbon, timestamp, or string
     */
    private function formatDateSafely($date): string
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format('c');
        }

        if (is_numeric($date)) {
            return \Carbon\Carbon::createFromTimestamp((int) $date)->format('c');
        }

        if (is_string($date)) {
            return \Carbon\Carbon::parse($date)->format('c');
        }

        // Fallback to current time if all else fails
        return now()->format('c');
    }

    /*
    |--------------------------------------------------------------------------
    | Classroom Data Array Endpoints (Configuration-Driven)
    |--------------------------------------------------------------------------
    */

    /**
     * Get complete classroom data array using configuration system
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassroomDataArray(Request $request)
    {
        try {
            $courseDateId = $request->input('course_date_id');
            $template = $request->input('template', 'full_classroom_data');

            // Find CourseDate
            $courseDate = null;
            if ($courseDateId) {
                $courseDate = \App\Models\CourseDate::find($courseDateId);
                if (!$courseDate) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Course date not found',
                        'data' => []
                    ], 404);
                }
            } else {
                // Try to find active course date for current instructor
                $courseDate = $this->findActiveCourseDateForInstructor();
            }

            // Build classroom data using service
            $service = new \App\Services\ClassroomDataArrayService($courseDate, Auth::user());
            $classroomData = $service->buildClassroomDataArray($template);

            return response()->json([
                'success' => true,
                'message' => 'Classroom data retrieved successfully',
                'data' => $classroomData,
                'metadata' => [
                    'course_date_id' => $courseDate?->id,
                    'template_used' => $template,
                    'instructor_id' => Auth::id(),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get classroom data array', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve classroom data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get classroom polling data for real-time updates
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassroomPollData(Request $request)
    {
        try {
            $courseDateId = $request->input('course_date_id');

            // Find CourseDate
            $courseDate = null;
            if ($courseDateId) {
                $courseDate = \App\Models\CourseDate::find($courseDateId);
            } else {
                $courseDate = $this->findActiveCourseDateForInstructor();
            }

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active course date found',
                    'data' => []
                ]);
            }

            // Build polling data using service
            $service = new \App\Services\ClassroomDataArrayService($courseDate, Auth::user());
            $pollData = $service->buildClassroomPollData();

            return response()->json([
                'success' => true,
                'message' => 'Classroom poll data retrieved successfully',
                'data' => $pollData,
                'metadata' => [
                    'course_date_id' => $courseDate->id,
                    'instructor_id' => Auth::id(),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get classroom poll data', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve poll data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get instructor-specific classroom data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstructorClassroomData(Request $request)
    {
        try {
            $courseDateId = $request->input('course_date_id');

            // Find CourseDate
            $courseDate = null;
            if ($courseDateId) {
                $courseDate = \App\Models\CourseDate::find($courseDateId);
            } else {
                $courseDate = $this->findActiveCourseDateForInstructor();
            }

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active course date found',
                    'data' => []
                ]);
            }

            // Check if instructor has access to this classroom
            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: You are not assigned to this classroom',
                    'data' => []
                ], 403);
            }

            // Build instructor classroom data using service
            $service = new \App\Services\ClassroomDataArrayService($courseDate, Auth::user());
            $instructorData = $service->buildInstructorClassroomData();

            return response()->json([
                'success' => true,
                'message' => 'Instructor classroom data retrieved successfully',
                'data' => $instructorData,
                'metadata' => [
                    'course_date_id' => $courseDate->id,
                    'instructor_id' => Auth::id(),
                    'access_level' => 'instructor',
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get instructor classroom data', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve instructor data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get lesson management data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonManagementData(Request $request)
    {
        try {
            $courseDateId = $request->input('course_date_id');

            // Find CourseDate
            $courseDate = null;
            if ($courseDateId) {
                $courseDate = \App\Models\CourseDate::find($courseDateId);
            } else {
                $courseDate = $this->findActiveCourseDateForInstructor();
            }

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active course date found',
                    'data' => []
                ]);
            }

            // Check instructor access
            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: You are not assigned to this classroom',
                    'data' => []
                ], 403);
            }

            // Build lesson management data using service
            $service = new \App\Services\ClassroomDataArrayService($courseDate, Auth::user());
            $lessonData = $service->buildLessonManagementData();

            return response()->json([
                'success' => true,
                'message' => 'Lesson management data retrieved successfully',
                'data' => $lessonData,
                'metadata' => [
                    'course_date_id' => $courseDate->id,
                    'instructor_id' => Auth::id(),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get lesson management data', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve lesson data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Find active course date for current instructor
     *
     * @return \App\Models\CourseDate|null
     */
    protected function findActiveCourseDateForInstructor(): ?\App\Models\CourseDate
    {
        $instructor = Auth::user();

        return \App\Models\CourseDate::whereDate('starts_at', today())
            ->where('is_active', true)
            ->whereHas('TodaysInstUnit', function ($query) use ($instructor) {
                $query->where('created_by', $instructor->id)
                    ->orWhere('assistant_id', $instructor->id);
            })
            ->with(['TodaysInstUnit', 'CourseUnit'])
            ->first();
    }

    /**
     * Check if current instructor has access to the classroom
     *
     * @param \App\Models\CourseDate $courseDate
     * @return bool
     */
    protected function hasInstructorAccess(\App\Models\CourseDate $courseDate): bool
    {
        $instructor = Auth::user();

        // Use TodaysInstUnit instead of InstUnit to get the actual active session
        $instUnit = $courseDate->TodaysInstUnit;

        if (!$instUnit) {
            return false;
        }

        return $instUnit->created_by === $instructor->id ||
            $instUnit->assistant_id === $instructor->id;
    }

    // =====================================================
    // LESSON MANAGEMENT - Start/Complete/Track Lessons
    // =====================================================

    /**
     * Get current instructor lesson state for a course date
     *
     * @param int $courseDateId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInstructorLessonState(int $courseDateId)
    {
        try {
            $courseDate = \App\Models\CourseDate::with(['InstUnit', 'CourseUnit'])->findOrFail($courseDateId);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            $instUnit = $courseDate->InstUnit;
            if (!$instUnit) {
                return response()->json([
                    'success' => true,
                    'completedInstLessons' => [],
                    'instUnitLesson' => null,
                ]);
            }

            // Get completed instructor lessons
            $completedInstLessons = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->whereNotNull('completed_at')
                ->select('lesson_id', 'completed_at')
                ->get()
                ->toArray();

            // Get current instructor lesson (in progress)
            $instUnitLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->whereNull('completed_at')
                ->select('lesson_id', 'created_at as started_at')
                ->first();

            return response()->json([
                'success' => true,
                'completedInstLessons' => $completedInstLessons,
                'instUnitLesson' => $instUnitLesson,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get instructor lesson state', [
                'error' => $e->getMessage(),
                'course_date_id' => $courseDateId,
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve lesson state',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Start a lesson (create InstLesson record)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startLesson(Request $request)
    {
        $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        try {
            $courseDate = \App\Models\CourseDate::with(['InstUnit'])->findOrFail($request->course_date_id);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            $instUnit = $courseDate->InstUnit;
            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No instructor unit found. Please start the class first.',
                ], 400);
            }

            // Check if lesson is already started
            $existingInstLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->where('lesson_id', $request->lesson_id)
                ->first();

            if ($existingInstLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson already started',
                ], 400);
            }

            // Check if another lesson is currently active
            $activeInstLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->whereNull('completed_at')
                ->first();

            if ($activeInstLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Another lesson is currently active. Please complete it first.',
                ], 400);
            }

            // Create new InstLesson record
            $instLesson = \App\Models\InstLesson::create([
                'inst_unit_id' => $instUnit->id,
                'lesson_id' => $request->lesson_id,
                'created_by' => Auth::id(),
            ]);

            // Refresh to get the created_at timestamp
            $instLesson->refresh();

            Log::info('Instructor started lesson', [
                'instructor_id' => Auth::id(),
                'course_date_id' => $request->course_date_id,
                'lesson_id' => $request->lesson_id,
                'inst_lesson_id' => $instLesson->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson started successfully',
                'data' => [
                    'inst_lesson_id' => $instLesson->id,
                    'lesson_id' => $request->lesson_id,
                    'started_at' => $instLesson->created_at->toIso8601String(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to start lesson', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start lesson',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Complete a lesson (mark InstLesson as completed)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeLesson(Request $request)
    {
        $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        try {
            $courseDate = \App\Models\CourseDate::with(['InstUnit'])->findOrFail($request->course_date_id);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            $instUnit = $courseDate->InstUnit;
            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No instructor unit found',
                ], 400);
            }

            // Find the active InstLesson
            $instLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->where('lesson_id', $request->lesson_id)
                ->whereNull('completed_at')
                ->first();

            if (!$instLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active lesson found to complete',
                ], 400);
            }

            // Mark lesson as completed
            $instLesson->update([
                'completed_at' => now(),
                'completed_by' => Auth::id(),
            ]);

            Log::info('Instructor completed lesson', [
                'instructor_id' => Auth::id(),
                'course_date_id' => $request->course_date_id,
                'lesson_id' => $request->lesson_id,
                'inst_lesson_id' => $instLesson->id,
                'duration_minutes' => $instLesson->created_at->diffInMinutes(now()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson completed successfully',
                'data' => [
                    'inst_lesson_id' => $instLesson->id,
                    'lesson_id' => $request->lesson_id,
                    'started_at' => $instLesson->created_at->toISOString(),
                    'ended_at' => $instLesson->completed_at->toISOString(),
                    'duration_minutes' => $instLesson->created_at->diffInMinutes($instLesson->completed_at),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to complete lesson', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete lesson',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Pause a lesson (instructor break)
     * Session-based tracking - no database records, only validates break limits
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pauseLesson(Request $request)
    {
        $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
            'breaks_taken' => 'required|integer|min:0', // Frontend tracks this
        ]);

        try {
            $courseDate = \App\Models\CourseDate::with(['InstUnit'])->findOrFail($request->course_date_id);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            $instUnit = $courseDate->InstUnit;
            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No instructor unit found',
                ], 400);
            }

            // Find active InstLesson
            $instLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->where('lesson_id', $request->lesson_id)
                ->whereNull('completed_at')
                ->first();

            if (!$instLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active lesson found to pause',
                ], 400);
            }

            // Get lesson duration to determine break limits
            $lesson = \App\Models\Lesson::findOrFail($request->lesson_id);
            $lessonDurationMinutes = $lesson->credit_minutes ?? 0;

            // Calculate allowed breaks based on lesson duration
            $breakLimits = $this->calculateBreakLimits($lessonDurationMinutes);
            $breaksAllowed = $breakLimits['breaks_allowed'];
            $breaksTaken = $request->breaks_taken;

            // Check if instructor exceeded break limit
            if ($breaksTaken >= $breaksAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum breaks reached. This lesson allows {$breaksAllowed} breaks.",
                    'breaks_allowed' => $breaksAllowed,
                    'breaks_taken' => $breaksTaken,
                ], 400);
            }

            // Set lesson as paused
            $instLesson->update(['is_paused' => true]);

            $nextBreakNumber = $breaksTaken + 1;
            $nextBreakDuration = $breakLimits['break_durations'][$nextBreakNumber] ?? 15;

            Log::info('Instructor paused lesson', [
                'instructor_id' => Auth::id(),
                'course_date_id' => $request->course_date_id,
                'lesson_id' => $request->lesson_id,
                'inst_lesson_id' => $instLesson->id,
                'break_number' => $nextBreakNumber,
                'breaks_allowed' => $breaksAllowed,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson paused for break',
                'data' => [
                    'inst_lesson_id' => $instLesson->id,
                    'is_paused' => true,
                    'break_number' => $nextBreakNumber,
                    'break_duration_minutes' => $nextBreakDuration,
                    'breaks_taken' => $nextBreakNumber,
                    'breaks_allowed' => $breaksAllowed,
                    'breaks_remaining' => $breaksAllowed - $nextBreakNumber,
                    'is_last_break' => $nextBreakNumber === $breaksAllowed,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to pause lesson', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to pause lesson',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Resume a lesson (end break)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumeLesson(Request $request)
    {
        $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        try {
            $courseDate = \App\Models\CourseDate::with(['InstUnit'])->findOrFail($request->course_date_id);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            $instUnit = $courseDate->InstUnit;
            if (!$instUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'No instructor unit found',
                ], 400);
            }

            // Find paused InstLesson
            $instLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->where('lesson_id', $request->lesson_id)
                ->where('is_paused', true)
                ->whereNull('completed_at')
                ->first();

            if (!$instLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'No paused lesson found to resume',
                ], 400);
            }

            // Resume lesson
            $instLesson->update(['is_paused' => false]);

            Log::info('Instructor resumed lesson', [
                'instructor_id' => Auth::id(),
                'course_date_id' => $request->course_date_id,
                'lesson_id' => $request->lesson_id,
                'inst_lesson_id' => $instLesson->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lesson resumed',
                'data' => [
                    'inst_lesson_id' => $instLesson->id,
                    'is_paused' => false,
                    'resumed_at' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to resume lesson', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resume lesson',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Calculate break limits based on lesson duration
     * Uses config/classroom_breaks.php rules
     *
     * @param int $durationMinutes
     * @return array
     */
    private function calculateBreakLimits(int $durationMinutes): array
    {
        $thresholds = config('classroom_breaks.duration_thresholds');

        // Very long lessons (6+ hours)
        if ($durationMinutes >= ($thresholds['very_long']['min_duration'] ?? 360)) {
            return [
                'breaks_allowed' => $thresholds['very_long']['breaks_allowed'],
                'break_durations' => $thresholds['very_long']['break_durations'],
            ];
        }

        // Long lessons (4-6 hours)
        if ($durationMinutes >= ($thresholds['long']['min_duration'] ?? 240)) {
            return [
                'breaks_allowed' => $thresholds['long']['breaks_allowed'],
                'break_durations' => $thresholds['long']['break_durations'],
            ];
        }

        // Medium lessons (2-4 hours)
        if ($durationMinutes >= ($thresholds['medium']['min_duration'] ?? 120)) {
            return [
                'breaks_allowed' => $thresholds['medium']['breaks_allowed'],
                'break_durations' => $thresholds['medium']['break_durations'],
            ];
        }

        // Short lessons (< 2 hours)
        return [
            'breaks_allowed' => $thresholds['short']['breaks_allowed'],
            'break_durations' => $thresholds['short']['break_durations'],
        ];
    }

    /**
     * Get screen sharing status for classroom preparation
     *
     * @param int $courseDateId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScreenSharingStatus(int $courseDateId)
    {
        try {
            $courseDate = \App\Models\CourseDate::findOrFail($courseDateId);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            // TODO: Implement actual screen sharing service integration
            // For now, we'll simulate based on class start time and InstUnit existence
            $instUnit = $courseDate->InstUnit;
            $screenSharingActive = false;

            if ($instUnit) {
                // If instructor has started the class, consider screen sharing active after 1 minute
                $classStarted = $instUnit->created_at;
                $screenSharingDelay = 1; // 1 minute delay
                $screenSharingTime = $classStarted->addMinutes($screenSharingDelay);
                $screenSharingActive = now() >= $screenSharingTime;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'active' => $screenSharingActive,
                    'course_date_id' => $courseDateId,
                    'class_started' => $instUnit ? $instUnit->created_at->toISOString() : null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get screen sharing status', [
                'error' => $e->getMessage(),
                'course_date_id' => $courseDateId,
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve screen sharing status',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    // =====================================================
    // STUDENT TRACKING - Real-time Student Panel Data
    // =====================================================

    /**
     * Get students for a specific course date (real-time tracking via StudentUnit)
     *
     * @param int $courseDateId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentsForCourseDate(int $courseDateId)
    {
        try {
            $courseDate = \App\Models\CourseDate::findOrFail($courseDateId);

            if (!$this->hasInstructorAccess($courseDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }

            // Get StudentUnit records for this course date filtered by today's date
            $studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDateId)
                ->whereDate('created_at', now()->format('Y-m-d'))
                ->with(['CourseAuth.User'])
                ->orderBy('created_at', 'desc')
                ->get();

            $students = [];
            foreach ($studentUnits as $studentUnit) {
                $user = $studentUnit->GetUser(); // Use the existing method
                if ($user) {
                    // Determine student status based on last activity
                    $lastActivity = $studentUnit->updated_at;
                    $now = now();

                    // Ensure lastActivity is a Carbon instance
                    if (!$lastActivity instanceof \Carbon\Carbon) {
                        $lastActivity = \Carbon\Carbon::parse($lastActivity);
                    }

                    $minutesSinceActivity = $lastActivity->diffInMinutes($now);
                    $status = 'online';
                    if ($minutesSinceActivity > 5) {
                        $status = 'away';
                    }
                    if ($minutesSinceActivity > 15) {
                        $status = 'offline';
                    }

                    // Calculate progress based on completed lessons
                    $completedLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                        ->whereNotNull('dnc_at')
                        ->count();

                    $totalLessons = $courseDate->CourseUnit ? $courseDate->CourseUnit->getLessons()->count() : 1;
                    $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                    $students[] = [
                        'id' => $user->id,
                        'student_unit_id' => $studentUnit->id,
                        'name' => $user->fullname(),
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'status' => $status,
                        'progress' => $progress,
                        'avatar' => method_exists($user, 'hasCustomAvatar') && $user->hasCustomAvatar() ? $user->getAvatar() : null,
                        'joined_at' => formatClassTime(\Carbon\Carbon::parse($studentUnit->created_at), 'datetime_medium'),
                        'joined_at_iso' => \Carbon\Carbon::parse($studentUnit->created_at)->toISOString(),
                        'last_activity' => formatClassTime(\Carbon\Carbon::parse($lastActivity), 'datetime_medium'),
                        'last_activity_iso' => $lastActivity->toISOString(),
                        'verified' => $studentUnit->verified ? true : false,
                        'attendance_type' => $studentUnit->attendance_type ?? 'online',
                        'completed_lessons' => $completedLessons,
                        'total_lessons' => $totalLessons,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'course_date_id' => $courseDateId,
                    'course_name' => $courseDate->CourseUnit?->Course?->course_name ?? 'Unknown Course',
                    'students' => $students,
                    'summary' => [
                        'total' => count($students),
                        'online' => count(array_filter($students, fn($s) => $s['status'] === 'online')),
                        'away' => count(array_filter($students, fn($s) => $s['status'] === 'away')),
                        'offline' => count(array_filter($students, fn($s) => $s['status'] === 'offline')),
                        'verified' => count(array_filter($students, fn($s) => $s['verified'] === true)),
                        'last_updated' => formatClassTime(\Carbon\Carbon::now(), 'datetime_medium'),
                        'last_updated_iso' => now()->toISOString(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get students for course date', [
                'error' => $e->getMessage(),
                'course_date_id' => $courseDateId,
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get current zoom status for the authenticated instructor
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getZoomStatus()
    {
        try {
            $instructor = Auth::user();

            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instructor not authenticated',
                ], 401);
            }

            // Get instructor's active InstUnit to determine the course
            $activeInstUnit = \App\Models\InstUnit::where('created_by', $instructor->id)
                ->whereNull('completed_at')
                ->with(['CourseDate.CourseUnit.Course'])
                ->first();

            if (!$activeInstUnit || !$activeInstUnit->CourseDate) {
                return response()->json([
                    'success' => true,
                    'status' => 'disabled',
                    'message' => 'No active classroom session',
                ], 200);
            }

            $course = $activeInstUnit->CourseDate->CourseUnit->Course ?? null;
            $courseTitle = strtoupper($course->title ?? '');

            // Determine which Zoom account to use (same logic as toggleZoomStatus)
            // Priority 1: Admin/Support roles (role_id 1 or 2) always use admin account for testing
            $zoomEmail = null;
            if (in_array($instructor->role_id, [1, 2])) {
                // System Admin (1) or Administrator/Support (2) use admin account for testing
                $zoomEmail = 'instructor_admin@stgroupusa.com';
            }
            // Priority 2: Course type determines account (D or G)
            elseif (strpos($courseTitle, ' D') !== false || strpos($courseTitle, 'D40') !== false || strpos($courseTitle, 'D20') !== false) {
                $zoomEmail = 'instructor_d@stgroupusa.com';
            } elseif (strpos($courseTitle, ' G') !== false || strpos($courseTitle, 'G40') !== false || strpos($courseTitle, 'G20') !== false) {
                $zoomEmail = 'instructor_g@stgroupusa.com';
            } else {
                $zoomEmail = 'instructor_admin@stgroupusa.com';
            }

            // Get the SPECIFIC Zoom credential for this instructor/course
            $zoomCreds = \App\Models\ZoomCreds::where('zoom_email', $zoomEmail)->first();

            if (!$zoomCreds) {
                return response()->json([
                    'success' => true,
                    'status' => 'disabled',
                    'message' => 'No zoom credentials found for ' . $zoomEmail,
                    'course_name' => $course ? $course->name : 'Unknown',
                    'email' => $zoomEmail,
                ], 200);
            }

            // Decrypt Zoom credentials
            try {
                $decryptedPasscode = Crypt::decrypt($zoomCreds->zoom_passcode);
                $decryptedPassword = Crypt::decrypt($zoomCreds->zoom_password);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                Log::error('Failed to decrypt Zoom credentials', [
                    'zoom_creds_id' => $zoomCreds->id,
                    'error' => $e->getMessage(),
                ]);
                $decryptedPasscode = 'Error decrypting';
                $decryptedPassword = 'Error decrypting';
            }

            return response()->json([
                'success' => true,
                'status' => $zoomCreds->zoom_status ?? 'disabled',
                'meeting_id' => $zoomCreds->pmi,
                'passcode' => $decryptedPasscode,
                'password' => $decryptedPassword,
                'email' => $zoomCreds->zoom_email,
                'instructor_name' => $instructor->name,
                'course_name' => $course ? $course->name : 'Unknown',
                'inst_unit_id' => $activeInstUnit->id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to get zoom status', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve zoom status',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }    /**
         * Toggle zoom status (enable/disable) for the authenticated instructor
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\JsonResponse
         */
    public function toggleZoomStatus(Request $request)
    {
        try {
            $instructor = Auth::user();

            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instructor not authenticated',
                ], 401);
            }

            // Get instructor's active InstUnit to determine the course
            $activeInstUnit = \App\Models\InstUnit::where('created_by', $instructor->id)
                ->whereNull('completed_at')
                ->with(['CourseDate.CourseUnit.Course'])
                ->first();

            if (!$activeInstUnit || !$activeInstUnit->CourseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active classroom session',
                ], 400);
            }

            $course = $activeInstUnit->CourseDate->CourseUnit->Course ?? null;
            $courseTitle = strtoupper($course->title ?? '');

            // Determine which Zoom account to use
            // Priority 1: Admin/Support roles (role_id 1 or 2) always use admin account for testing
            $zoomEmail = null;
            if (in_array($instructor->role_id, [1, 2])) {
                // System Admin (1) or Administrator/Support (2) use admin account for testing
                $zoomEmail = 'instructor_admin@stgroupusa.com';
            }
            // Priority 2: Course type determines account (D or G)
            elseif (strpos($courseTitle, ' D') !== false || strpos($courseTitle, 'D40') !== false || strpos($courseTitle, 'D20') !== false) {
                $zoomEmail = 'instructor_d@stgroupusa.com';
            } elseif (strpos($courseTitle, ' G') !== false || strpos($courseTitle, 'G40') !== false || strpos($courseTitle, 'G20') !== false) {
                $zoomEmail = 'instructor_g@stgroupusa.com';
            } else {
                $zoomEmail = 'instructor_admin@stgroupusa.com';
            }

            // Get requested status
            $requestedStatus = $request->input('status');

            if ($requestedStatus === 'enabled') {
                // Find the SPECIFIC Zoom credential for this course type
                $zoomCreds = \App\Models\ZoomCreds::where('zoom_email', $zoomEmail)
                    ->first();

                if (!$zoomCreds) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Zoom credentials found for ' . $zoomEmail,
                        'instructor_email' => $instructor->email,
                        'course_name' => $course ? $course->name : 'Unknown',
                        'zoom_email' => $zoomEmail,
                    ], 400);
                }

                // Activate this credential
                $zoomCreds->zoom_status = 'enabled';
                $zoomCreds->save();
                $newStatus = 'enabled';
            } else {
                // Find the SPECIFIC Zoom credential for this course type and disable it
                $zoomCreds = \App\Models\ZoomCreds::where('zoom_email', $zoomEmail)
                    ->first();

                if (!$zoomCreds) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Zoom credentials found for ' . $zoomEmail,
                        'instructor_email' => $instructor->email,
                        'course_name' => $course ? $course->name : 'Unknown',
                        'zoom_email' => $zoomEmail,
                    ], 400);
                }

                // Disable this credential
                $zoomCreds->zoom_status = 'disabled';
                $zoomCreds->save();
                $newStatus = 'disabled';
            }

            // Decrypt Zoom credentials for response
            try {
                $decryptedPasscode = Crypt::decrypt($zoomCreds->zoom_passcode);
                $decryptedPassword = Crypt::decrypt($zoomCreds->zoom_password);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                Log::error('Failed to decrypt Zoom credentials during toggle', [
                    'zoom_creds_id' => $zoomCreds->id,
                    'error' => $e->getMessage(),
                ]);
                $decryptedPasscode = 'Error decrypting';
                $decryptedPassword = 'Error decrypting';
            }

            Log::info('Zoom status toggled', [
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->name,
                'zoom_creds_id' => $zoomCreds->id,
                'zoom_email' => $zoomCreds->zoom_email,
                'course_id' => $course ? $course->id : null,
                'course_name' => $course ? $course->name : 'Unknown',
                'inst_unit_id' => $activeInstUnit->id,
                'new_status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => $newStatus === 'enabled'
                    ? 'Screen sharing activated'
                    : 'Screen sharing deactivated',
                'meeting_id' => $zoomCreds->pmi,
                'passcode' => $decryptedPasscode,
                'password' => $decryptedPassword,
                'email' => $zoomCreds->zoom_email,
                'course_name' => $course->name,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to toggle zoom status', [
                'error' => $e->getMessage(),
                'instructor_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle zoom status',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
