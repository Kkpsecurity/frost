<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use App\Services\ClassroomDashboardService;
use App\Services\StudentAttendanceService;
use App\Services\AttendanceService;
use App\Traits\PageMetaDataTrait;
use App\Models\CourseAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    protected ?StudentDashboardService $dashboardService;
    protected ?ClassroomDashboardService $classroomService;
    protected ?StudentAttendanceService $attendanceService;
    protected ?AttendanceService $coreAttendanceService;

    public function __construct(
        StudentDashboardService $dashboardService = null,
        ClassroomDashboardService $classroomService = null,
        StudentAttendanceService $attendanceService = null,
        AttendanceService $coreAttendanceService = null
    ) {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
        $this->classroomService = $classroomService;
        $this->attendanceService = $attendanceService;
        $this->coreAttendanceService = $coreAttendanceService;
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
     * Student Dashboard - Shows purchased courses in table format
     * Similar to screenshot: Date, Course Name, Last Access, View Course button
     *
     * @param int|null $id Course ID parameter (optional)
     * @return View|RedirectResponse
     */
    public function dashboard($id = null)
    {
        // Debug logging for route parameters
        Log::info("StudentDashboardController: Dashboard method called", [
            'id_parameter' => $id,
            'id_type' => gettype($id),
            'request_url' => request()->fullUrl(),
            'route_name' => request()->route()?->getName()
        ]);

        try {
            if (!Auth::check()) {
                Log::warning("StudentDashboardController: Unauthenticated access");
                return redirect()->route('login');
            }

            $user = Auth::user();
            $studentService = new StudentDashboardService($user);

            // Get user's course authorizations (purchased courses)
            $courseAuths = $studentService->getCourseAuths();

            // If specific course ID is provided, filter to that course only
            if ($id) {
                Log::info("StudentDashboardController: Filtering for specific course", [
                    'user_id' => $user->id,
                    'course_auth_id' => $id
                ]);

                $courseAuths = $courseAuths->where('id', $id);

                if ($courseAuths->isEmpty()) {
                    Log::warning("StudentDashboardController: Course not found or not authorized", [
                        'user_id' => $user->id,
                        'course_auth_id' => $id
                    ]);

                    // Redirect back to main dashboard if course not found
                    return redirect()->route('classroom.dashboard');
                }
            }

            // Convert Collection to Array for JSON serialization
            $courseAuthsArray = $courseAuths->toArray();

            Log::info("StudentDashboardController: Course auths data", [
                'user_id' => $user->id,
                'course_auths_count' => $courseAuths->count(),
                'course_auths_array_count' => count($courseAuthsArray),
                'first_auth_id' => $courseAuths->first()?->id ?? 'none',
                'course_auths_type' => get_class($courseAuths)
            ]);

            // Get lesson data for ALL courses - both instructor-led and self-paced
            $lessonsData = [];
            $classroomService = new ClassroomDashboardService($user);
            $classroomData = $classroomService->getClassroomData();

            // Always fetch lessons for student's courses
            if (!empty($courseAuths)) {
                Log::info('StudentDashboardController: Getting lessons for dashboard display', [
                    'user_id' => $user->id,
                    'course_auths_count' => $courseAuths->count(),
                    'has_course_dates' => !empty($classroomData['courseDates']),
                    'course_dates_count' => count($classroomData['courseDates'] ?? [])
                ]);

                foreach ($courseAuths as $courseAuth) {
                    $lessons = $studentService->getLessonsForCourse($courseAuth);
                    if (!empty($lessons['lessons']) && $lessons['lessons']->count() > 0) {
                        // Determine if this course has scheduled dates (instructor-led)
                        $hasScheduledDates = !empty($classroomData['courseDates']) &&
                            collect($classroomData['courseDates'])->contains('course_id', $courseAuth->course_id);

                        $lessonsData[$courseAuth->id] = [
                            'lessons' => $lessons['lessons']->toArray(),
                            'modality' => $hasScheduledDates ? 'instructor_led' : 'self_paced',
                            'current_day_only' => $hasScheduledDates, // Show only current day lessons for instructor-led
                            'course_title' => $courseAuth->Course->title ?? 'Unknown Course',
                        ];

                        Log::info('StudentDashboardController: Added lessons for course', [
                            'course_auth_id' => $courseAuth->id,
                            'course_id' => $courseAuth->course_id,
                            'course_title' => $courseAuth->Course->title ?? 'Unknown',
                            'lessons_count' => $lessons['lessons']->count(),
                            'modality' => $lessonsData[$courseAuth->id]['modality']
                        ]);
                    }
                }
            }

            // Prepare data for React props - matching the dashboard screenshot format
            $content = [
                'student' => $user,
                'course_auths' => $courseAuthsArray,
                'lessons' => $lessonsData,
                'has_lessons' => !empty($lessonsData),
                'selected_course_auth_id' => $id, // Pass the selected course ID
            ];

            // Also pass course_auth_id for backward compatibility (use selected ID if available)
            $course_auth_id = $id ?: (!empty($courseAuthsArray) ? $courseAuthsArray[0]['id'] ?? null : null);

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

            // LESSON LOGIC: When courseDates is empty, get lessons for self-paced learning
            $lessonsData = [];
            if (empty($classroomData['courseDates']) && !empty($studentCourseAuths)) {
                Log::info('StudentDashboardController: courseDates empty, retrieving lessons for self-paced mode', [
                    'user_id' => $user->id,
                    'course_auths_count' => count($studentCourseAuths)
                ]);

                // Get lessons for each course auth
                foreach ($studentCourseAuths as $courseAuth) {
                    $lessons = $studentService->getLessonsForCourse($courseAuth);
                    if (!empty($lessons['lessons']) && $lessons['lessons']->count() > 0) {
                        $lessonsData[$courseAuth->id] = $lessons;

                        Log::info('StudentDashboardController: Lessons found for course auth', [
                            'course_auth_id' => $courseAuth->id,
                            'course_title' => $courseAuth->Course->title ?? 'Unknown',
                            'lessons_count' => $lessons['lessons']->count(),
                            'modality' => $lessons['modality']
                        ]);
                    }
                }
            }

            // Add lessons data to classroom array
            $classroomDataArray['lessons'] = $lessonsData;
            $classroomDataArray['has_lessons'] = !empty($lessonsData);

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
            $relationshipCourseAuths = CourseAuth::where('user_id', $user->id)->get();

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
     * Route: GET /api/student/data
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
     * Route: GET /api/classroom/data
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
     * Enter class and create StudentUnit attendance record
     * 
     * @param Request $request
     * @param int $courseDateId
     * @return JsonResponse
     */
    public function enterClass(Request $request, int $courseDateId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$this->attendanceService) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required or service unavailable'
            ], 401);
        }

        try {
            $result = $this->attendanceService->enterClass($user, $courseDateId);

            Log::info('Student class entry API called', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId,
                'result' => $result['code']
            ]);

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (Exception $e) {
            Log::error('Student class entry API error', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to enter class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student attendance data for dashboard
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttendanceData(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$this->attendanceService) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required or service unavailable'
            ], 401);
        }

        try {
            $dashboardData = $this->attendanceService->getDashboardData($user);

            Log::info('Student attendance data API called', [
                'user_id' => $user->id,
                'current_session' => $dashboardData['current_session'] !== null,
                'present_in_classes' => $dashboardData['present_in_classes'] ?? 0
            ]);

            return response()->json($dashboardData);

        } catch (Exception $e) {
            Log::error('Student attendance data API error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get attendance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance details for a specific course date
     * 
     * @param Request $request
     * @param int $courseDateId
     * @return JsonResponse
     */
    public function getClassAttendance(Request $request, int $courseDateId): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$this->attendanceService) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required or service unavailable'
            ], 401);
        }

        try {
            $courseDate = \App\Models\CourseDate::find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            $attendanceDetails = $this->attendanceService->getStudentAttendanceDetails($user, $courseDate);

            return response()->json([
                'success' => true,
                'attendance' => $attendanceDetails
            ]);

        } catch (Exception $e) {
            Log::error('Student class attendance API error', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get class attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record offline attendance (for instructor use)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recordOfflineAttendance(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        // TODO: Add instructor role validation here
        // For now, allow any authenticated user to record offline attendance

        try {
            $validated = $request->validate([
                'student_id' => 'required|integer|exists:users,id',
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'recorded_by' => 'nullable|string|max:255',
                'verification_method' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:255'
            ]);

            $student = \App\Models\User::find($validated['student_id']);

            $metadata = [
                'recorded_by' => $validated['recorded_by'] ?? $user->name,
                'verification_method' => $validated['verification_method'] ?? 'instructor_marked',
                'location' => $validated['location'] ?? 'classroom',
                'recorded_by_user_id' => $user->id
            ];

            $result = $this->coreAttendanceService->recordOfflineAttendance(
                $student,
                $validated['course_date_id'],
                $metadata
            );

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Offline attendance recording error', [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record offline attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance summary for a course date (online vs offline counts)
     * 
     * @param Request $request
     * @param int $courseDateId
     * @return JsonResponse
     */
    public function getAttendanceSummary(Request $request, int $courseDateId): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $courseDate = \App\Models\CourseDate::find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            // Get attendance counts by type
            $onlineCount = \App\Models\StudentUnit::where('course_date_id', $courseDateId)
                ->onlineAttendance()
                ->count();

            $offlineCount = \App\Models\StudentUnit::where('course_date_id', $courseDateId)
                ->offlineAttendance()
                ->count();

            $totalCount = $onlineCount + $offlineCount;

            return response()->json([
                'success' => true,
                'course_date_id' => $courseDateId,
                'attendance_summary' => [
                    'total_present' => $totalCount,
                    'online_attendance' => $onlineCount,
                    'offline_attendance' => $offlineCount,
                    'online_percentage' => $totalCount > 0 ? round(($onlineCount / $totalCount) * 100, 1) : 0,
                    'offline_percentage' => $totalCount > 0 ? round(($offlineCount / $totalCount) * 100, 1) : 0
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Attendance summary error', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get attendance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a lesson - This triggers the attendance session
     * 
     * Business Rule: Offline sessions only start when student begins a lesson,
     * not just because they're physically present in class.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function startLesson(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $validated = $request->validate([
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'lesson_id' => 'nullable|integer',
                'attendance_type' => 'nullable|string|in:online,offline',
                'location' => 'nullable|string|max:255'
            ]);

            // Determine attendance type based on request or default to online
            $attendanceType = $validated['attendance_type'] ?? 'online';

            $metadata = [
                'lesson_id' => $validated['lesson_id'] ?? null,
                'location' => $validated['location'] ?? ($attendanceType === 'offline' ? 'classroom' : 'online'),
                'started_by' => $user->name,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ];

            // Use the new handleLessonStart method which implements the business rule
            $result = $this->coreAttendanceService->handleLessonStart(
                $user,
                $validated['course_date_id'],
                $attendanceType,
                $metadata
            );

            // Log lesson start for tracking
            Log::info('Student lesson start request', [
                'student_id' => $user->id,
                'student_name' => $user->name,
                'course_date_id' => $validated['course_date_id'],
                'lesson_id' => $validated['lesson_id'] ?? null,
                'attendance_type' => $attendanceType,
                'result_code' => $result['code'] ?? 'unknown',
                'session_started' => $result['success']
            ]);

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Lesson start error', [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start lesson',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current attendance session status for student
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttendanceStatus(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $courseDateId = $request->query('course_date_id');

            if (!$courseDateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date ID required'
                ], 400);
            }

            // Get current attendance session
            $studentUnit = \App\Models\StudentUnit::whereHas('CourseAuth', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('course_date_id', $courseDateId)
                ->latest()
                ->first();

            if ($studentUnit) {
                $sessionDuration = $studentUnit->created_at->diffInSeconds(now());

                return response()->json([
                    'success' => true,
                    'data' => [
                        'session_active' => true,
                        'attendance_type' => $studentUnit->attendance_type,
                        'session_start' => $studentUnit->created_at,
                        'session_duration' => $sessionDuration,
                        'session_duration_formatted' => gmdate('H:i:s', $sessionDuration),
                        'student_unit_id' => $studentUnit->id
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'session_active' => false,
                        'attendance_type' => null,
                        'session_start' => null,
                        'session_duration' => 0,
                        'session_duration_formatted' => '00:00:00'
                    ]
                ]);
            }

        } catch (Exception $e) {
            Log::error('Attendance status check error', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get attendance status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
