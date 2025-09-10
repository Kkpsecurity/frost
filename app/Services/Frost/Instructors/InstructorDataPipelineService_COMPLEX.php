<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use App\Models\User;
use App\Classes\ClassroomQueries;
use App\Services\InstructorServices;
use App\Services\ClassRoomServices;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Instructor Data Pipeline Service
 *
 * PURPOSE: Utilize existing Classes (ClassroomQueries, CourseAuthObj) from a single service
 * NOT to create new functionality, but to coordinate existing Classes
 *
 * Step 1: Who is the instructor (use existing InstructorServices)
 * Step 2: What courses/classes (use existing ClassroomQueries traits)
 * Step 3: Current state (use existing ClassRoomServices methods)
 * Step 4: Bulletin board data (coordinate existing data sources)
 */
class InstructorDataPipelineService
{
    private InstructorServices $instructorServices;
    private ClassRoomServices $classRoomServices;

    public function __construct(
        InstructorServices $instructorServices,
        ClassRoomServices $classRoomServices
    ) {
        $this->instructorServices = $instructorServices;
        $this->classRoomServices = $classRoomServices;
    }

    /**
     * STEP 1: Who is the instructor?
     *
     * UTILIZES: InstructorServices->getAllRegisteredInstructors()
     * UTILIZES: User model methods (fullname(), getAvatar())
     */
    public function identifyInstructor(): ?array
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Use existing InstructorServices to get all registered instructors
        $registeredInstructors = $this->instructorServices->getAllRegisteredInstructors();

        // Check if current user is in the registered instructors list
        $currentInstructor = $registeredInstructors->firstWhere('id', $user->id);

        if (!$currentInstructor) {
            Log::info('InstructorDataPipeline: User not found in registered instructors', [
                'user_id' => $user->id,
                'registered_count' => $registeredInstructors->count()
            ]);
            return null;
        }

        // Use existing User model methods to build profile
        return [
            'instructor_id' => $currentInstructor->id,
            'name' => $currentInstructor->name,
            'email' => $currentInstructor->email,
            'full_name' => $currentInstructor->fullname() ?? $currentInstructor->name,
            'avatar' => $currentInstructor->getAvatar('thumb') ?? null,
            'role_id' => $currentInstructor->role_id,
            'status' => 'offline',
            'profile_loaded_at' => now()->toISOString(),
            'data_source' => 'InstructorServices->getAllRegisteredInstructors()'
        ];
    }

    /**
     * STEP 2: What courses/classes does the instructor have access to?
     *
     * UTILIZES: ClassroomQueries->InstructorDashboardCourseDates() static method
     * UTILIZES: ClassroomQueries->RecentInstructorUnits() static method
     */
    public function loadInstructorCourseAssignments(int $instructorId): array
    {
        // Use existing ClassroomQueries static methods
        $courseDates = ClassroomQueries::InstructorDashboardCourseDates();
        $recentUnits = ClassroomQueries::RecentInstructorUnits($instructorId, 10);

        return [
            'course_dates' => $courseDates,
            'recent_units' => $recentUnits,
            'total_assigned' => $courseDates->count(),
            'data_source' => 'ClassroomQueries static methods: InstructorDashboardCourseDates, RecentInstructorUnits'
        ];
    }

    /**
     * STEP 3: Determine current instructor state
     *
     * UTILIZES: ClassRoomServices->getClassData() existing method
     */
    public function determineInstructorState(int $instructorId): array
    {
        $courseDates = ClassroomQueries::InstructorDashboardCourseDates();

        if ($courseDates->isEmpty()) {
            return [
                'state' => 'offline',
                'reason' => 'no_assigned_courses',
                'bulletin_board_required' => true,
                'data_source' => 'ClassroomQueries->InstructorDashboardCourseDates() empty result'
            ];
        }

        // Use existing ClassRoomServices to get class data
        $classData = $this->classRoomServices->getClassData($courseDates->toArray());

        // Check if any classes are currently active
        $hasActiveClasses = collect($classData)->contains(function($classInfo) {
            return isset($classInfo['instUnit']) &&
                   is_null($classInfo['instUnit']['completed_at']);
        });

        return [
            'state' => $hasActiveClasses ? 'online' : 'offline',
            'reason' => $hasActiveClasses ? 'active_classes_found' : 'no_active_classes',
            'bulletin_board_required' => !$hasActiveClasses,
            'active_class_count' => $hasActiveClasses ? count($classData) : 0,
            'data_source' => 'ClassRoomServices->getClassData()'
        ];
    }

    /**
     * STEP 4: Prepare bulletin board data (offline state)
     *
     * SIMPLE: Show only available courses D and G as cards with "Take Control" buttons
     */
    public function prepareBulletinBoardData(int $instructorId): array
    {
        $instructor = $this->identifyInstructor();
        $currentState = $this->determineInstructorState($instructorId);

        // Get available courses D and G
        $availableCourses = $this->getAvailableCourses();

        return [
            'instructor' => $instructor,
            'bulletin_board' => [
                'header' => "Welcome back, {$instructor['name']}",
                'status' => $currentState['state'],
                'current_time' => now()->format('l, F j, Y - g:i A'),
                'message' => 'Select a course to start teaching',
                'available_courses' => $availableCourses
            ],
            'quick_stats' => [
                'available_courses' => count($availableCourses),
                'current_state' => $currentState['state']
            ]
        ];
    }

    /**
     * Get available courses D and G as cards
     * SIMPLE: Just show the 2 courses with Take Control buttons
     */
    private function getAvailableCourses(): array
    {
        // Get courses D and G from database
        $courses = \App\Models\Course::whereIn('id', [1, 3])->where('is_active', true)->get();

        $courseCards = [];

        foreach ($courses as $course) {
            $courseCards[] = [
                'course_id' => $course->id,
                'title' => $course->title, // "Florida D40 (Dy)" or "Florida G28"
                'title_long' => $course->title_long, // Full description
                'duration_hours' => round($course->total_minutes / 60), // Convert to hours
                'price' => $course->price,
                'class_type' => $this->getCourseType($course->title), // "D" or "G"
                'action_button' => [
                    'text' => 'Take Control',
                    'action' => 'start_teaching',
                    'route' => route('instructor.take-control', $course->id)
                ],
                'card_color' => $this->getCourseCardColor($course->title)
            ];
        }

        return $courseCards;
    }

    /**
     * Get course type from title (D or G)
     */
    private function getCourseType(string $title): string
    {
        if (str_contains($title, 'D')) return 'D';
        if (str_contains($title, 'G')) return 'G';
        return 'Unknown';
    }

    /**
     * Get card color based on course type
     */
    private function getCourseCardColor(string $title): string
    {
        if (str_contains($title, 'D')) return 'blue';
        if (str_contains($title, 'G')) return 'green';
        return 'gray';
    }    /**
     * Get bulletin board classroom events
     * UTILIZES: Recent instructor units and system status
     */
    private function getClassroomEvents(int $instructorId): array
    {
        $recentUnits = ClassroomQueries::RecentInstructorUnits($instructorId, 5);
        $events = [];

        foreach ($recentUnits as $instUnit) {
            $events[] = [
                'type' => 'class_completed',
                'title' => 'Class Session Completed',
                'description' => "Course Unit #{$instUnit->id}",
                'timestamp' => $instUnit->updated_at ?? $instUnit->created_at,
                'icon' => 'check-circle',
                'color' => 'success'
            ];
        }

        // Add system events for bulletin board
        $events[] = [
            'type' => 'system_status',
            'title' => 'System Status',
            'description' => 'All classroom systems operational',
            'timestamp' => now(),
            'icon' => 'server',
            'color' => 'info'
        ];

        $events[] = [
            'type' => 'instructor_status',
            'title' => 'Instructor Status',
            'description' => 'Ready to teach - No active classes scheduled',
            'timestamp' => now(),
            'icon' => 'user-check',
            'color' => 'secondary'
        ];

        return collect($events)
            ->sortByDesc('timestamp')
            ->take(8)
            ->values()
            ->all();
    }

    /**
     * Get recent classroom activity using existing Classes
     * UTILIZES: RecentInstructorUnits static method
     */
    private function getBulletinBoardActivity(int $instructorId): array
    {
        $recentUnits = ClassroomQueries::RecentInstructorUnits($instructorId, 3);
        $activity = [];

        foreach ($recentUnits as $instUnit) {
            $activity[] = [
                'type' => 'teaching_session',
                'message' => 'Completed teaching session',
                'details' => "Unit ID: {$instUnit->id}",
                'timestamp' => $instUnit->updated_at ?? $instUnit->created_at,
                'icon' => 'chalkboard-teacher'
            ];
        }

        // Add placeholder activities for bulletin board when no recent activity
        if (empty($activity)) {
            $activity[] = [
                'type' => 'ready',
                'message' => 'Ready to start teaching',
                'details' => 'All systems ready for classroom sessions',
                'timestamp' => now(),
                'icon' => 'play-circle'
            ];
        }

        return $activity;
    }

    /**
     * Get instructor metrics using existing data
     * UTILIZES: RecentInstructorUnits static method for metrics calculation
     */
    private function getInstructorMetrics(int $instructorId): array
    {
        $recentUnits = ClassroomQueries::RecentInstructorUnits($instructorId, 50);

        return [
            'total_students_taught' => $recentUnits->sum(function($unit) {
                return $unit->student_count ?? 0;
            }),
            'completed_lessons' => $recentUnits->count(),
            'teaching_hours' => $recentUnits->count() * 2, // Estimate 2 hours per unit
            'recent_activity_count' => $recentUnits->count(),
            'last_teaching_session' => $recentUnits->first()?->updated_at ?? null
        ];
    }

    /**
     * Get status message for bulletin board based on current state
     */
    private function getBulletinStatusMessage(array $currentState): string
    {
        return match($currentState['reason']) {
            'no_assigned_courses' => 'No courses assigned at the moment. Contact administration for course assignments.',
            'no_active_classes' => 'No classes scheduled for today. Check back for upcoming sessions.',
            'active_classes_found' => 'You have active classes in session!',
            default => 'Ready to teach when classes are scheduled.'
        };
    }

    /**
     * Complete Data Pipeline - coordinates all existing Classes
     */
    public function executeInstructorDataPipeline(): array
    {
        $startTime = microtime(true);

        // Step 1: Use existing InstructorServices
        $instructor = $this->identifyInstructor();
        if (!$instructor) {
            return [
                'success' => false,
                'error' => 'instructor_identification_failed',
                'message' => 'Could not identify valid instructor using InstructorServices',
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ];
        }

        // Step 2: Use existing ClassroomQueries static methods
        $courseAssignments = $this->loadInstructorCourseAssignments($instructor['instructor_id']);

        // Step 3: Use existing ClassRoomServices
        $currentState = $this->determineInstructorState($instructor['instructor_id']);

        // Step 4: Coordinate existing data sources
        $bulletinBoardData = $this->prepareBulletinBoardData($instructor['instructor_id']);

        return [
            'success' => true,
            'instructor' => $instructor,
            'course_assignments' => $courseAssignments,
            'current_state' => $currentState,
            'bulletin_board' => $bulletinBoardData,
            'data_pipeline' => [
                'step_1_completed' => true, // InstructorServices
                'step_2_completed' => true, // ClassroomQueries static methods
                'step_3_completed' => true, // ClassRoomServices
                'step_4_completed' => true, // Data coordination
                'all_existing_classes_utilized' => true
            ],
            'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
        ];
    }

    /**
     * Get instructor bulletin board data (utilizes all existing Classes)
     */
    public function getInstructorBulletinBoard(): array
    {
        $pipelineResult = $this->executeInstructorDataPipeline();

        if (!$pipelineResult['success']) {
            return $pipelineResult;
        }

        return [
            'success' => true,
            'view_type' => 'bulletin_board',
            'data' => $pipelineResult['bulletin_board'],
            'metadata' => [
                'timestamp' => now()->toISOString(),
                'classes_utilized' => [
                    'ClassroomQueries' => 'InstructorDashboardCourseDates, RecentInstructorUnits static methods',
                    'InstructorServices' => 'getAllRegisteredInstructors method',
                    'ClassRoomServices' => 'getClassData method',
                    'User Model' => 'fullname, getAvatar methods'
                ],
                'execution_time_ms' => $pipelineResult['execution_time_ms']
            ]
        ];
    }
}
