<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use App\Services\ClassroomDashboardService;
use App\Traits\PageMetaDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    protected ?StudentDashboardService $dashboardService;
    protected ?ClassroomDashboardService $classroomService;

    public function __construct(StudentDashboardService $dashboardService = null, ClassroomDashboardService $classroomService = null)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
        $this->classroomService = $classroomService;
    }

    /**
     * Get empty dashboard content structure
     */
    protected function getEmptyDashboardContent(): array
    {
        return [
            'incompleteAuths' => [],
            'completedAuths' => [],
            'mergedAuths' => [],
            'stats' => [
                'total_courses' => 0,
                'active_courses' => 0,
                'completed_courses' => 0,
                'overall_progress' => 0
            ]
        ];
    }

    /**
     * Student Dashboard
     * Main dashboard showing active and completed courses
     *
     * @return View|RedirectResponse
     */
    public function dashboard()
    {
        try {
            Log::info("StudentDashboardController: Dashboard accessed", [
                'user_id' => Auth::id()
            ]);

            if (!Auth::check()) {
                Log::warning("StudentDashboardController: Unauthenticated access");
                return redirect()->route('login');
            }

            // Create services for current user if not injected
            $user = Auth::user();
            Log::info("StudentDashboardController: User debug", [
                'user_exists' => !is_null($user),
                'user_id' => $user?->id,
                'user_class' => $user ? get_class($user) : 'null'
            ]);

            $service = $this->dashboardService ?: new StudentDashboardService($user);

            try {
                // Get classroom data for dashboard
                $classroomData = $service->getClassData();
                $dashboardData = $classroomData; // For backwards compatibility
            } catch (Exception $e) {
                Log::error("StudentDashboardController: Dashboard data error", [
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage()
                ]);
                $dashboardData = $this->getEmptyDashboardContent();
            }

            // Prepare content for view with validation
            $content = array_merge([
                'incompleteAuths' => $dashboardData['incompleteAuths'] ?? [],
                'completedAuths' => $dashboardData['completedAuths'] ?? [],
                'MergedCourseAuths' => $dashboardData['mergedAuths'] ?? [], // Legacy key name for compatibility
                'stats' => $dashboardData['stats'] ?? [
                    'total_courses' => 0,
                    'active_courses' => 0,
                    'completed_courses' => 0,
                    'overall_progress' => 0
                ],
            ], $this->renderPageMeta('index'));

            Log::info("StudentDashboardController: Rendering dashboard", [
                'user_id' => Auth::id(),
                'stats' => $dashboardData['stats'] ?? []
            ]);

            return view('frontend.students.dashboard', compact('content'));

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Fatal error", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('frontend.students.dashboard', [
                'content' => array_merge(
                    $this->getEmptyDashboardContent(),
                    $this->renderPageMeta('index')
                )
            ]);
        }
    }

    /**
     * Debug endpoint to test array structure
     */
    public function debug()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'auth_check' => Auth::check(),
                    'auth_id' => Auth::id()
                ]);
            }

            // Get student and classroom services
            $studentService = new StudentDashboardService($user);
            $classroomService = new ClassroomDashboardService($user);

            $classroomData = $classroomService->getClassroomData();
            $studentCourseAuths = $studentService->getCourseAuths();

            // ARRAY 1: Classroom Data (instructors + courseDates) - Add sample school data
            $classroomDataArray = [
                'instructors' => !empty($classroomData['instructors']) ? $classroomData['instructors'] : [
                    [
                        'id' => 67,
                        'name' => 'Dr. Sarah Johnson',
                        'email' => 'sarah.johnson@security.edu',
                        'phone' => '+1-555-0123',
                        'bio' => 'Certified security expert with 15 years experience in cybersecurity training.',
                        'certifications' => ['CISSP', 'CISM', 'CEH'],
                        'profile_image' => '/images/instructors/sarah-johnson.jpg',
                        'specialties' => ['Network Security', 'Incident Response', 'Risk Management'],
                        'rating' => 4.8,
                        'total_courses' => 45,
                        'years_experience' => 15
                    ],
                    [
                        'id' => 68,
                        'name' => 'Prof. Michael Chen',
                        'email' => 'michael.chen@security.edu',
                        'phone' => '+1-555-0124',
                        'bio' => 'Former FBI cybercrime investigator, now teaching digital forensics.',
                        'certifications' => ['GCIH', 'GCFA', 'CISSP'],
                        'profile_image' => '/images/instructors/michael-chen.jpg',
                        'specialties' => ['Digital Forensics', 'Malware Analysis', 'Threat Intelligence'],
                        'rating' => 4.9,
                        'total_courses' => 32,
                        'years_experience' => 20
                    ]
                ],
                'courseDates' => !empty($classroomData['courseDates']) ? $classroomData['courseDates'] : [
                    [
                        'id' => 123,
                        'course_id' => 45,
                        'instructor_id' => 67,
                        'start_date' => '2025-09-15',
                        'end_date' => '2025-09-20',
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'timezone' => 'America/New_York',
                        'location' => 'Online Classroom A',
                        'status' => 'active',
                        'max_students' => 25,
                        'current_enrollment' => 18,
                        'meeting_link' => 'https://zoom.us/j/123456789',
                        'course_title' => 'Advanced Network Security',
                        'created_at' => '2025-08-01T10:00:00Z',
                        'updated_at' => '2025-09-10T14:30:00Z'
                    ],
                    [
                        'id' => 124,
                        'course_id' => 46,
                        'instructor_id' => 68,
                        'start_date' => '2025-10-01',
                        'end_date' => '2025-10-05',
                        'start_time' => '10:00:00',
                        'end_time' => '16:00:00',
                        'timezone' => 'America/New_York',
                        'location' => 'Digital Forensics Lab',
                        'status' => 'scheduled',
                        'max_students' => 15,
                        'current_enrollment' => 8,
                        'meeting_link' => 'https://zoom.us/j/987654321',
                        'course_title' => 'Digital Forensics Fundamentals',
                        'created_at' => '2025-08-15T14:00:00Z',
                        'updated_at' => '2025-09-10T16:00:00Z'
                    ]
                ]
            ];

            // ARRAY 2: Student Data (student + courseAuth) - Use service method for complete user data
            $studentData = [
                'student' => $studentService->getStudentData(),
                'courseAuth' => $studentCourseAuths
            ];

            // Return the correct 2-array structure with school data
            return response()->json([
                'classroomData' => $classroomDataArray,
                'studentData' => $studentData,
                'debug_info' => [
                    'data_source' => !empty($studentCourseAuths) ? 'database' : 'sample_data',
                    'student_service_structure' => array_keys($studentData),
                    'classroom_service_structure' => array_keys($classroomData),
                    'has_course_data' => !empty($studentCourseAuths),
                    'has_instructor_data' => !empty($classroomData['instructors']),
                    'has_course_dates' => !empty($classroomData['courseDates'])
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Debug endpoint for classroom data only
     */
    public function debugClass()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ]);
            }

            $studentService = new StudentDashboardService($user);
            $classroomService = new ClassroomDashboardService($user);

            $studentDataFromService = $studentService->getStudentData();
            $courseAuths = $studentService->getCourseAuths();
            $classroomData = $classroomService->getClassroomData();

            // Classroom Data (instructors + courseDates)
            $classroomDataArray = [
                'instructors' => $classroomData['instructors'] ?? [],
                'courseDates' => $classroomData['courseDates'] ?? []
            ];

            return response()->json($classroomDataArray);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint for student data only
     */
    public function debugStudent()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ]);
            }

            $service = new StudentDashboardService($user);
            $courseAuths = $service->getCourseAuths();

            // Student Data (student + courseAuth) - Use service method for complete user data
            $studentData = [
                'student' => $service->getStudentData(),
                'courseAuth' => $courseAuths
            ];

            return response()->json($studentData);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for React - Student Statistics
     * Matches React StudentStats interface
     */
    public function getStudentStats()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            $service = new StudentDashboardService($user);
            $courseAuths = $service->getCourseAuths();

            // Transform debug data into React-expected format
            $stats = [
                'enrolledCourses' => count($courseAuths),
                'completedLessons' => 0, // TODO: Calculate from actual lessons
                'assignmentsDue' => 0,   // TODO: Calculate from actual assignments
                'hoursLearned' => 0      // TODO: Calculate from actual progress
            ];

            return response()->json($stats);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch student stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for React - Recent Lessons
     * Matches React RecentLesson[] interface
     */
    public function getRecentLessons()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            // TODO: Replace with actual lesson data from services
            $recentLessons = [
                [
                    'id' => 1,
                    'title' => 'Network Security Fundamentals',
                    'course' => 'Advanced Network Security',
                    'progress' => 85,
                    'duration' => '45 min',
                    'lastAccessed' => '2 hours ago'
                ],
                [
                    'id' => 2,
                    'title' => 'Digital Evidence Collection',
                    'course' => 'Digital Forensics Fundamentals',
                    'progress' => 60,
                    'duration' => '38 min',
                    'lastAccessed' => '1 day ago'
                ]
            ];

            return response()->json($recentLessons);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch recent lessons: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for React - Upcoming Assignments
     * Matches React UpcomingAssignment[] interface
     */
    public function getUpcomingAssignments()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            // TODO: Replace with actual assignment data from services
            $upcomingAssignments = [
                [
                    'id' => 1,
                    'title' => 'Network Security Assessment',
                    'course' => 'Advanced Network Security',
                    'dueDate' => '2025-09-15',
                    'type' => 'assignment'
                ],
                [
                    'id' => 2,
                    'title' => 'Digital Forensics Quiz',
                    'course' => 'Digital Forensics Fundamentals',
                    'dueDate' => '2025-09-18',
                    'type' => 'quiz'
                ]
            ];

            return response()->json($upcomingAssignments);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch upcoming assignments: ' . $e->getMessage()
            ], 500);
        }
    }
}
