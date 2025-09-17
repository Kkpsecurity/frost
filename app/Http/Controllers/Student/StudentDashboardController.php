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
            'student' => null,
            'courseAuths' => []
        ];
    }

    /**
     * Enter classroom for a specific course
     * Validates CourseAuth and redirects to classroom interface
     *
     * @param int $courseAuth CourseAuth ID
     * @return View|RedirectResponse
     */
    public function enterClassroom($courseAuth)
    {
        try {
            if (!Auth::check()) {
                Log::warning("StudentDashboardController: Unauthenticated classroom access attempt");
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Find and validate CourseAuth belongs to current user
            $courseAuthModel = \App\Models\CourseAuth::where('id', $courseAuth)
                ->where('user_id', $user->id)
                ->with(['Course'])
                ->first();

            if (!$courseAuthModel) {
                Log::warning("StudentDashboardController: Invalid course auth access", [
                    'user_id' => $user->id,
                    'course_auth_id' => $courseAuth
                ]);

                return redirect()->route('classroom.dashboard')
                    ->with('error', 'Course not found or access denied.');
            }

            // Validate course access (course is active, student is enrolled)
            if (!$this->validateCourseAccess($courseAuthModel)) {
                return redirect()->route('classroom.dashboard')
                    ->with('error', 'Course is not available at this time.');
            }

            // Get lesson data for classroom sidebar
            $lessonData = $this->dashboardService->getClassroomLessons($courseAuthModel);

            // Prepare classroom data
            $classroomData = [
                'student' => $user,
                'course_auth' => $courseAuthModel,
                'course' => $courseAuthModel->Course,
                'lessons' => $lessonData['lessons'],
                'modality' => $lessonData['modality'],
                'current_day_only' => $lessonData['current_day_only'],
            ];

            Log::info("StudentDashboardController: Student entering classroom", [
                'user_id' => $user->id,
                'course_auth_id' => $courseAuth,
                'course_id' => $courseAuthModel->course_id,
                'course_title' => $courseAuthModel->Course->title ?? 'Unknown Course'
            ]);

            // Render classroom interface
            return view('frontend.students.classroom', compact('classroomData'));

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Classroom entry error", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuth,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('classroom.dashboard')
                ->with('error', 'Unable to enter classroom. Please try again.');
        }
    }

    /**
     * Validate if student can access the course classroom
     *
     * @param \App\Models\CourseAuth $courseAuth
     * @return bool
     */
    protected function validateCourseAccess($courseAuth): bool
    {
        // Check if course exists and is active
        if (!$courseAuth->Course || !$courseAuth->Course->is_active) {
            Log::info("Course access denied: Course not active", [
                'course_auth_id' => $courseAuth->id,
                'course_id' => $courseAuth->course_id
            ]);
            return false;
        }

        // Check if student's enrollment is active
        if (!$courseAuth->is_active) {
            Log::info("Course access denied: Enrollment not active", [
                'course_auth_id' => $courseAuth->id,
                'user_id' => $courseAuth->user_id
            ]);
            return false;
        }

        // Check if course has started (if start_date is set)
        if ($courseAuth->start_date && now()->lt($courseAuth->start_date)) {
            Log::info("Course access denied: Course not started", [
                'course_auth_id' => $courseAuth->id,
                'start_date' => $courseAuth->start_date,
                'current_time' => now()
            ]);
            return false;
        }

        // Check if course is completed
        if ($courseAuth->completed_at) {
            Log::info("Course access: Course completed, allowing review access", [
                'course_auth_id' => $courseAuth->id,
                'completed_at' => $courseAuth->completed_at
            ]);
            // Allow access for review even if completed
            return true;
        }

        return true;
    }

    /**
     * Student Dashboard - Shows purchased courses in table format
     * Similar to screenshot: Date, Course Name, Last Access, View Course button
     *
     * @return View|RedirectResponse
     */
    public function dashboard()
    {

        try {
            if (!Auth::check()) {
                Log::warning("StudentDashboardController: Unauthenticated access");
                return redirect()->route('login');
            }

            $user = Auth::user();
            $studentService = new StudentDashboardService($user);

            // Get user's course authorizations (purchased courses)
            $courseAuths = $studentService->getCourseAuths();

            // Convert Collection to Array for JSON serialization
            $courseAuthsArray = $courseAuths->toArray();

            Log::info("StudentDashboardController: Course auths data", [
                'user_id' => $user->id,
                'course_auths_count' => $courseAuths->count(),
                'course_auths_array_count' => count($courseAuthsArray),
                'first_auth_id' => $courseAuths->first()?->id ?? 'none',
                'course_auths_type' => get_class($courseAuths)
            ]);

            // Prepare data for React props - matching the dashboard screenshot format
            $content = [
                'student' => $user,
                'course_auths' => $courseAuthsArray,
            ];

            // Also pass course_auth_id for backward compatibility
            $course_auth_id = !empty($courseAuthsArray) ? $courseAuthsArray[0]['id'] ?? null : null;

            return view('frontend.students.dashboard', compact('content', 'course_auth_id'));

        } catch (Exception $e) {
                        Log::error("StudentDashboardController: Dashboard error", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('frontend.students.dashboard', [
                'content' => [
                    'student' => null,
                    'course_auths' => [],
                ]
            ]);
        }
    }

    // ===================================================================
    // DEBUGS - For React Component Data Sync
    // ===================================================================


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
     * Debug endpoint for student data only - SIMPLIFIED VERSION FOR TESTING
     */
    public function debugStudent()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'user_id' => Auth::id(),
                    'auth_check' => Auth::check()
                ]);
            }

            // Test 1: Direct CourseAuth query
            $directCourseAuths = \App\Models\CourseAuth::where('user_id', $user->id)->get();

            // Test 2: Via User relationship
            $relationshipCourseAuths = $user->courseAuths()->get();

            // Test 3: Via Service
            $service = new StudentDashboardService($user);
            $serviceCourseAuths = $service->getCourseAuths();

            // Convert service collection to array for JSON
            $serviceArray = $serviceCourseAuths->toArray();

            return response()->json([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'test_1_direct_query' => [
                    'count' => $directCourseAuths->count(),
                    'ids' => $directCourseAuths->pluck('id')->toArray(),
                    'course_ids' => $directCourseAuths->pluck('course_id')->toArray(),
                    'first_record' => $directCourseAuths->first() ? [
                        'id' => $directCourseAuths->first()->id,
                        'course_id' => $directCourseAuths->first()->course_id,
                        'user_id' => $directCourseAuths->first()->user_id,
                        'created_at' => $directCourseAuths->first()->created_at,
                    ] : null
                ],
                'test_2_relationship' => [
                    'count' => $relationshipCourseAuths->count(),
                    'ids' => $relationshipCourseAuths->pluck('id')->toArray(),
                    'course_ids' => $relationshipCourseAuths->pluck('course_id')->toArray(),
                ],
                'test_3_service' => [
                    'count' => $serviceCourseAuths->count(),
                    'ids' => $serviceCourseAuths->pluck('id')->toArray(),
                    'array_count' => count($serviceArray),
                    'is_collection' => $serviceCourseAuths instanceof \Illuminate\Support\Collection,
                    'service_array' => $serviceArray
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }



    // ===================================================================
    // POLLING API ENDPOINTS - For React Component Data Sync
    // ===================================================================

    /**
     * API Endpoint: Student Data
     * Matches student-dashboard-data structure in blade template
     * student,
     * course_auths,
     * student_units,
     * students_unit_lessons
     * Route: GET /classroom/student/data
     */
    public function getStudentData()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'student' => null,
                    'course_auths' => []
                ], 401);
            }

            // Get student service
            $studentService = new StudentDashboardService($user);

            // Get student data (matches Student interface in TypeScript)
            $studentData = $studentService->getStudentData();

            // Get course authorizations (matches CourseAuth[] interface)
            $courseAuths = $studentService->getCourseAuths();

            // Format response to match TypeScript StudentDashboardData interface
            $response = [
                'student' => $studentData ? [
                    'id' => $studentData['id'] ?? $user->id,
                    'fname' => $studentData['fname'] ?? $user->fname ?? 'Unknown',
                    'lname' => $studentData['lname'] ?? $user->lname ?? 'User',
                    'email' => $studentData['email'] ?? $user->email,
                ] : null,
                'course_auths' => array_map(function ($auth) {
                    return [
                        'id' => $auth['id'] ?? 0,
                        'course_id' => $auth['course_id'] ?? 0,
                        'user_id' => $auth['user_id'] ?? 0,
                        'status' => $auth['status'] ?? 'enrolled',
                        'progress' => $auth['progress'] ?? 0,
                        'created_at' => $auth['created_at'] ?? now()->toISOString(),
                        'updated_at' => $auth['updated_at'] ?? now()->toISOString(),
                        'course' => isset($auth['course']) ? [
                            'id' => $auth['course']['id'] ?? 0,
                            'title' => $auth['course']['title'] ?? 'Unknown Course',
                            'description' => $auth['course']['description'] ?? null,
                            'slug' => $auth['course']['slug'] ?? 'unknown',
                        ] : null
                    ];
                }, $courseAuths ?? [])
            ];

            Log::info("StudentDashboardController: Student data API called", [
                'user_id' => $user->id,
                'student_data_exists' => !is_null($studentData),
                'course_auths_count' => count($courseAuths ?? [])
            ]);

            return response()->json($response);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Student data API error", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch student data: ' . $e->getMessage(),
                'student' => null,
                'course_auths' => []
            ], 500);
        }
    }

    /**
     * API Endpoint: Class Data
     * Matches class-dashboard-data structure in blade template
     * instructor,
     * courses
     * lessons
     * course_dates
     * course_units
     * course_unit_lessons
     *
     * Route: GET /classroom/data
     */
    public function getClassData()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated',
                    'instructor' => null,
                    'course_dates' => []
                ], 401);
            }

            // Get classroom service
            $classroomService = new ClassroomDashboardService($user);

            // Get classroom data
            $classroomData = $classroomService->getClassroomData();

            // Extract instructor from instructors array (take first instructor)
            $instructors = $classroomData['instructors'] ?? [];
            $instructor = !empty($instructors) ? $instructors[0] : null;

            // Format response to match TypeScript ClassDashboardData interface
            $response = [
                'instructor' => $instructor ? [
                    'id' => $instructor['id'] ?? 0,
                    'fname' => $instructor['fname'] ?? $instructor['name'] ?? 'Unknown',
                    'lname' => $instructor['lname'] ?? 'Instructor',
                    'email' => $instructor['email'] ?? 'unknown@example.com',
                ] : null,
                'course_dates' => array_map(function ($date) {
                    return [
                        'id' => $date['id'] ?? 0,
                        'course_id' => $date['course_id'] ?? 0,
                        'start_date' => $date['start_date'] ?? now()->toDateString(),
                        'end_date' => $date['end_date'] ?? now()->toDateString(),
                        'session_date' => $date['session_date'] ?? null,
                    ];
                }, $classroomData['courseDates'] ?? [])
            ];

            Log::info("StudentDashboardController: Class data API called", [
                'user_id' => $user->id,
                'instructor_exists' => !is_null($instructor),
                'course_dates_count' => count($classroomData['courseDates'] ?? [])
            ]);

            return response()->json($response);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Class data API error", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch class data: ' . $e->getMessage(),
                'instructor' => null,
                'course_dates' => []
            ], 500);
        }
    }

    /**
     * Test endpoint to check lesson data for a specific course auth
     */
    public function testLessonData($courseAuth)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        try {
            $user = Auth::user();
            $courseAuthModel = \App\Models\CourseAuth::where('id', $courseAuth)
                ->where('user_id', $user->id)
                ->with(['Course'])
                ->first();

            if (!$courseAuthModel) {
                return response()->json(['error' => 'Course auth not found'], 404);
            }

            // Test lesson data
            $lessonData = $this->dashboardService->getClassroomLessons($courseAuthModel);

            return response()->json([
                'course_auth_id' => $courseAuth,
                'course_title' => $courseAuthModel->Course->title,
                'lesson_data' => $lessonData,
            ]);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Test lesson data error", [
                'course_auth_id' => $courseAuth,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to get lesson data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
