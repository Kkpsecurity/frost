<?php

namespace App\Http\Controllers\Student;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\StudentUnit;
use App\Models\StudentActivity;
use App\Models\User;
use App\Classes\ClassroomQueries;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

use App\Services\AttendanceService;
use App\Services\IdVerificationService;
use App\Services\StudentActivityTracker;
use App\Services\StudentDashboardService;
use App\Services\StudentDataArrayService;
use App\Services\ClassroomDataArrayService;
use App\Services\ClassroomDashboardService;
use App\Services\StudentUnitService;
use App\Services\SelfStudyLessonService;

// New refactored services
use App\Services\Student\StudentAttendanceService;
use App\Services\Student\StudentVerificationService;
use App\Services\Student\StudentLessonService;
use App\Services\Student\StudentClassroomService;
use App\Traits\StudentDataHelpers;

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait, StudentDataHelpers;

    // Legacy services (being phased out)
    protected ?StudentDashboardService $dashboardService;
    protected ?ClassroomDashboardService $classroomService;
    protected ?AttendanceService $attendanceService;
    protected ?AttendanceService $coreAttendanceService;
    protected ?IdVerificationService $idVerificationService;
    protected ?StudentActivityTracker $activityTracker;
    protected ?StudentUnitService $studentUnitService;
    protected ?SelfStudyLessonService $selfStudyLessonService;

    // New refactored services
    protected StudentAttendanceService $studentAttendanceService;
    protected StudentVerificationService $studentVerificationService;
    protected StudentLessonService $studentLessonService;
    protected StudentClassroomService $studentClassroomService;

    public function __construct(
        StudentAttendanceService $studentAttendanceService,
        StudentVerificationService $studentVerificationService,
        StudentLessonService $studentLessonService,
        StudentClassroomService $studentClassroomService,
        ?StudentDashboardService $dashboardService = null,
        ?ClassroomDashboardService $classroomService = null,
        ?AttendanceService $attendanceService = null,
        ?AttendanceService $coreAttendanceService = null,
        ?IdVerificationService $idVerificationService = null,
        ?StudentActivityTracker $activityTracker = null,
        ?StudentUnitService $studentUnitService = null,
        ?SelfStudyLessonService $selfStudyLessonService = null
    ) {
        $this->middleware('auth');

        // New services (required)
        $this->studentAttendanceService = $studentAttendanceService;
        $this->studentVerificationService = $studentVerificationService;
        $this->studentLessonService = $studentLessonService;
        $this->studentClassroomService = $studentClassroomService;

        // Legacy services (optional for backward compatibility)
        $this->dashboardService = $dashboardService;
        $this->classroomService = $classroomService;
        $this->attendanceService = $attendanceService;
        $this->coreAttendanceService = $coreAttendanceService;
        $this->idVerificationService = $idVerificationService;
        $this->activityTracker = $activityTracker ?? app(StudentActivityTracker::class);
        $this->studentUnitService = $studentUnitService ?? app(StudentUnitService::class);
        $this->selfStudyLessonService = $selfStudyLessonService ?? app(SelfStudyLessonService::class);
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
        try {
            $user = Auth::user();

            // TRACK: Student entered platform (school_entry)
            $this->activityTracker->track($user->id, 'school_entry', [
                'description' => 'Student accessed dashboard',
                'data' => [
                    'page' => 'dashboard',
                    'specific_course' => $id ? true : false,
                ]
            ]);

            $studentService = new StudentDashboardService($user);

            // Auto-generate StudentUnit for any active CourseDates student should be enrolled in
            $this->autoGenerateStudentUnits($user);

            // Get user's course authorizations (purchased courses)
            $courseAuths = $studentService->getCourseAuths();

            // If specific course ID is provided, filter to that course only
            if ($id) {

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

            // Use StudentDataArrayService to get complete student data with all configured sections
            // This automatically includes verification data, course progress, and all other configured data
            $studentDataService = new StudentDataArrayService($user);
            $content = $studentDataService->buildStudentDataArray();

            // Override lessons with our detailed lesson data if we have it
            if (!empty($lessonsData)) {
                $content['lessons'] = $lessonsData;
                $content['has_lessons'] = true;
            }

            // Try to get classroom data if student has active CourseDate
            $activeClassroom = $this->findStudentActiveClassroom($user);
            if ($activeClassroom && $activeClassroom['course_date']) {
                $classroomService = new ClassroomDataArrayService($activeClassroom['course_date'], $user);
                $classroomData = $classroomService->buildStudentClassroomData();

                // CRITICAL: Add course_id to course_date so frontend can match course_auth
                $courseDateData = $classroomData['course_date'] ?? [];
                if ($courseDateData && !isset($courseDateData['course_id'])) {
                    $courseDateData['course_id'] = $activeClassroom['course_date']->GetCourseUnit()->course_id;
                }

                $content['classroom'] = [
                    'instructor' => $classroomData['instructor'] ?? null,
                    'course_dates' => [$courseDateData],
                    'inst_unit' => $classroomData['inst_unit'] ?? null,
                    'status' => $classroomService->getClassroomStatus(), // ONLINE or OFFLINE
                ];

                Log::info('StudentDashboardController: Classroom data loaded', [
                    'user_id' => $user->id,
                    'has_inst_unit' => !empty($classroomData['inst_unit']),
                    'has_instructor' => !empty($classroomData['instructor']),
                    'instructor_id' => $classroomData['instructor']['id'] ?? null,
                    'instructor_email' => $classroomData['instructor']['email'] ?? null,
                    'inst_unit' => $classroomData['inst_unit'] ?? null,
                    'course_date_id' => $activeClassroom['course_date']->id ?? null,
                ]);

                // CRITICAL FIX: If classroom is ONLINE, filter lessons to show only today's CourseUnit lessons
                // BUT ONLY for D courses (courses 1 and 2) which have CourseUnits/days
                // G course (course 3) should show ALL lessons
                if ($classroomData['inst_unit']) {
                    $courseDate = $activeClassroom['course_date'];
                    $courseUnitId = $courseDate->course_unit_id;
                    $courseId = $courseDate->GetCourseUnit()->course_id;

                    // Find the course_auth_id for this classroom
                    $matchingCourseAuth = collect($content['course_auths'] ?? [])
                        ->first(function ($ca) use ($courseId) {
                            return $ca['course_id'] === $courseId;
                        });

                    if ($matchingCourseAuth) {
                        Log::info('StudentDashboardController: DEBUG - Before filtering lessons', [
                            'user_id' => $user->id,
                            'matching_course_auth_id' => $matchingCourseAuth['id'],
                            'matching_course_id' => $matchingCourseAuth['course_id'],
                            'course_date_course_id' => $courseDate->course_id,
                            'available_course_auth_ids' => array_keys($content['lessons'] ?? []),
                            'all_course_auths' => collect($content['course_auths'] ?? [])->map(function ($ca) {
                                return ['id' => $ca['id'], 'course_id' => $ca['course_id']];
                            })->toArray(),
                        ]);

                        // Get the FULL lesson list for this course_auth from $content['lessons']
                        $allLessons = $content['lessons'][$matchingCourseAuth['id']]['lessons'] ?? [];

                        // ONLY filter D Day course (1) by CourseUnit
                        // D Night (2) is inactive, G course (3) should always show ALL lessons
                        $isDCourse = ($courseId == 1); // Florida D40 Day course with CourseUnits

                        if ($isDCourse) {
                            // Filter to show ONLY lessons for today's CourseUnit (day)
                            $todaysLessons = collect($allLessons)->filter(function ($lesson) use ($courseUnitId) {
                                return isset($lesson['unit_id']) && $lesson['unit_id'] == $courseUnitId;
                            })->values()->toArray();

                            Log::info('StudentDashboardController: Filtering D course lessons for today\'s CourseUnit', [
                                'user_id' => $user->id,
                                'course_auth_id' => $matchingCourseAuth['id'],
                                'course_id' => $courseId,
                                'course_date_id' => $courseDate->id,
                                'course_unit_id' => $courseUnitId,
                                'all_lessons_count' => count($allLessons),
                                'filtered_lessons_count' => count($todaysLessons),
                                'classroom_status' => 'ONLINE',
                                'is_d_course' => true,
                            ]);

                            // Override with filtered lessons for D course
                            if (!empty($todaysLessons)) {
                                $content['lessons'][$matchingCourseAuth['id']] = [
                                    'lessons' => $todaysLessons,
                                    'modality' => 'instructor_led',
                                    'current_day_only' => true,
                                    'course_title' => $courseDate->GetCourse()->title ?? 'Unknown Course',
                                    'course_unit_id' => $courseUnitId,
                                ];
                            }
                        } else {
                            // G course (or other) - show ALL lessons, no filtering
                            Log::info('StudentDashboardController: G course - showing ALL lessons (no filtering)', [
                                'user_id' => $user->id,
                                'course_auth_id' => $matchingCourseAuth['id'],
                                'course_id' => $courseId,
                                'course_date_id' => $courseDate->id,
                                'all_lessons_count' => count($allLessons),
                                'classroom_status' => 'ONLINE',
                                'is_d_course' => false,
                            ]);

                            // Keep all lessons for G course
                            $content['lessons'][$matchingCourseAuth['id']] = [
                                'lessons' => $allLessons,
                                'modality' => 'instructor_led',
                                'current_day_only' => false, // G course shows all lessons
                                'course_title' => $courseDate->GetCourse()->title ?? 'Unknown Course',
                            ];
                        }
                    }
                }
            } else {
                $content['classroom'] = [
                    'instructor' => null,
                    'course_dates' => [],
                    'inst_unit' => null,
                    'status' => 'OFFLINE',
                ];
            }

            // Determine course_auth_id - prioritize active classroom, then URL param, then fallbacks
            $course_auth_id = null;

            // Priority 1: Use course_auth from active classroom (instructor has started class)
            // Query the course_auths array to find the one matching the active CourseDate's course_id
            if ($activeClassroom && isset($activeClassroom['course_date'])) {
                $activeCourseDate = $activeClassroom['course_date'];

                // Query course_auths array to find match by course_id
                $matchingCourseAuth = collect($content['course_auths'] ?? [])
                    ->first(function ($ca) use ($activeCourseDate) {
                        return $ca['course_id'] === $activeCourseDate->course_id;
                    });

                $course_auth_id = $matchingCourseAuth['id'] ?? null;

                Log::info("StudentDashboardController: Using course_auth from active classroom by course_id query", [
                    'user_id' => $user->id,
                    'course_auth_id' => $course_auth_id,
                    'course_date_id' => $activeCourseDate->id,
                    'course_id' => $activeCourseDate->course_id,
                    'all_course_auth_ids' => collect($content['course_auths'] ?? [])->pluck('id', 'course_id')->toArray()
                ]);
            }
            // Priority 2: Use URL parameter if provided
            elseif ($id) {
                $course_auth_id = $id;

                Log::info("StudentDashboardController: Using course_auth from URL parameter", [
                    'user_id' => $user->id,
                    'course_auth_id' => $course_auth_id
                ]);
            }

            // Fallback: Try to find course auth from today's StudentUnit
            if (!$course_auth_id && !empty($content['student_units'])) {
                // Get today's student unit
                $todayStudentUnit = collect($content['student_units'])
                    ->first(function ($su) {
                        $entryTime = \Carbon\Carbon::parse($su['entry_time'] ?? null);
                        return $entryTime->isToday();
                    });

                if ($todayStudentUnit && isset($todayStudentUnit['course_date'])) {
                    $courseDateId = $todayStudentUnit['course_date']['id'];
                    $courseDate = CourseDate::find($courseDateId);

                    if ($courseDate) {
                        // Find course auth matching this course date's course
                        $matchingCourseAuth = collect($content['course_auths'] ?? [])
                            ->first(function ($ca) use ($courseDate) {
                                return $ca['course_id'] === $courseDate->course_id;
                            });
                        $course_auth_id = $matchingCourseAuth['id'] ?? null;

                        Log::info("StudentDashboardController: Selected course_auth from today's StudentUnit", [
                            'course_auth_id' => $course_auth_id,
                            'student_unit_id' => $todayStudentUnit['id'],
                            'course_date_id' => $courseDateId
                        ]);
                    }
                }
            }

            // Final fallback to first course auth if still not found
            if (!$course_auth_id) {
                $course_auth_id = !empty($content['course_auths']) ? $content['course_auths'][0]['id'] ?? null : null;
                Log::info("StudentDashboardController: Using fallback course_auth (first available)", [
                    'course_auth_id' => $course_auth_id
                ]);
            }

            // Set selected_course_auth_id to the determined value
            $content['selected_course_auth_id'] = $course_auth_id;

            Log::info("StudentDashboardController: Final course_auth_id determined", [
                'selected_course_auth_id' => $course_auth_id
            ]);

            Log::info("StudentDashboardController: Using StudentDataArrayService", [
                'user_id' => $user->id,
                'has_verification_data' => isset($content['validations']['id_verification']),
                'verification_status' => $content['validations']['id_verification']['verification_status'] ?? 'none',
                'course_auths_count' => count($content['course_auths'] ?? [])
            ]);

            // DEBUG: Log what's being passed to the view
            Log::info('StudentDashboardController: Passing to view', [
                'has_classroom' => isset($content['classroom']),
                'has_instructor' => isset($content['classroom']['instructor']),
                'instructor_keys' => isset($content['classroom']['instructor']) ? array_keys($content['classroom']['instructor']) : [],
                'instructor_id' => $content['classroom']['instructor']['id'] ?? null,
                'instructor_email' => $content['classroom']['instructor']['email'] ?? null,
            ]);

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
                        'created_at' => $auth['created_at'] ?? now()->format('c'),
                        'updated_at' => $auth['updated_at'] ?? now()->format('c'),
                        'course' => isset($auth['course']) ? [
                            'id' => $auth['course']['id'] ?? 0,
                            'title' => $auth['course']['title'] ?? 'Unknown Course',
                            'description' => $auth['course']['description'] ?? null,
                            'slug' => $auth['course']['slug'] ?? 'unknown',
                        ] : null
                    ];
                }, $courseAuths ?? [])
            ];

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
     * API Endpoint: Class Data (Classroom Polling)
     *
     * Used by student classroom polling to detect class status and instructor Zoom sharing
     * Route: GET /classroom/class/data
     *
     * ENHANCED FOR ZOOM SCREEN SHARE:
     * - Uses ClassroomDataArrayService to build complete instructor data
     * - Includes instructor.zoom_payload with zoom_status for auto-detection
     * - Students poll this every 5-15 seconds to detect when instructor enables screen share
     */
    public function getClassData()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated', 'instructor' => null, 'course_dates' => []], 401);
            }

            $result = $this->studentClassroomService->getClassroomData($user);
            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Class data polling API error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch class data', 'instructor' => null, 'course_dates' => [], 'classroom_active' => false], 500);
        }
    }

    /**
     * Enter class and create StudentUnit attendance record
     *
     * @param Request $request
     * @param int $courseDateId
     * @return JsonResponse
     */
    public function enterClass(Request $request, int $courseDateId = null): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        $resolvedCourseDateId = $courseDateId ?? (int) $request->input('course_date_id');

        if (!$resolvedCourseDateId) {
            return response()->json(['success' => false, 'message' => 'A valid course_date_id is required to enter class'], 422);
        }

        try {
            $result = $this->studentAttendanceService->enterClass($auth['user'], $resolvedCourseDateId);
            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (Exception $e) {
            Log::error('Student class entry API error', [
                'user_id' => $auth['user']->id,
                'course_date_id' => $resolvedCourseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to enter class'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $result = $this->studentAttendanceService->getAttendanceData($auth['user']);
            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (Exception $e) {
            Log::error('Student attendance data API error', [
                'user_id' => $auth['user']->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to get attendance data'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $courseDate = CourseDate::find($courseDateId);
            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            $result = $this->studentAttendanceService->getClassAttendance($auth['user'], $courseDate);
            return response()->json($result, $result['success'] ? 200 : 404);

        } catch (Exception $e) {
            Log::error('Student class attendance API error', [
                'user_id' => $auth['user']->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to get class attendance'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        // TODO: Add instructor role validation here

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
                'recorded_by' => $validated['recorded_by'] ?? $auth['user']->name,
                'verification_method' => $validated['verification_method'] ?? 'instructor_marked',
                'location' => $validated['location'] ?? 'classroom',
                'recorded_by_user_id' => $auth['user']->id
            ];

            $result = $this->studentAttendanceService->recordOfflineAttendance($student, $validated['course_date_id'], $metadata);
            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);

        } catch (Exception $e) {
            Log::error('Offline attendance recording error', [
                'user_id' => $auth['user']->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to record offline attendance'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $result = $this->studentAttendanceService->getAttendanceSummary($auth['user'], $courseDateId);
            return response()->json($result, $result['success'] ? 200 : 404);

        } catch (Exception $e) {
            Log::error('Attendance summary error', [
                'user_id' => $auth['user']->id,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to retrieve attendance summary'], 500);
        }
    }

    /**
     * Generate Zoom SDK signature for joining meetings
     *
     * @param Request $request
     * @return JsonResponse
     */


    /**
     * Generate Zoom SDK signature for joining meetings
     * Route: POST /api/student/zoom/signature
     */
    public function generateZoomSignature(Request $request): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $validated = $request->validate(['meeting_number' => 'required|string', 'role' => 'required|integer|in:0,1']);
            $result = $this->studentClassroomService->generateZoomSignature((int) $validated['meeting_number'], $validated['role']);
            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (\Exception $e) {
            Log::error('Zoom signature generation error', ['user_id' => $auth['user']->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to generate Zoom signature'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $validated = $request->validate([
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'lesson_id' => 'nullable|integer',
                'attendance_type' => 'nullable|string|in:online,offline',
                'location' => 'nullable|string|max:255'
            ]);

            $metadata = [
                'lesson_id' => $validated['lesson_id'] ?? null,
                'location' => $validated['location'] ?? null,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ];

            $result = $this->studentLessonService->startLesson(
                $auth['user'],
                $validated['course_date_id'],
                $validated['attendance_type'] ?? 'online',
                $metadata
            );

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);

        } catch (Exception $e) {
            Log::error('Lesson start error', [
                'user_id' => $auth['user']->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to start lesson'], 500);
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
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $courseDateId = $request->query('course_date_id');

            if (!$courseDateId) {
                return response()->json(['success' => false, 'message' => 'Course date ID required'], 400);
            }

            $result = $this->studentAttendanceService->getAttendanceStatus($auth['user'], $courseDateId);
            return response()->json($result, 200);

        } catch (Exception $e) {
            Log::error('Attendance status check error', [
                'user_id' => $auth['user']->id,
                'course_date_id' => $courseDateId ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to get attendance status'], 500);
        }
    }

    /**
     * Start ID verification process
     * Route: POST /classroom/id-verification/start
     */
    public function startIdVerification(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id_document' => 'required|image|max:10240', // 10MB max
                'course_date_id' => 'nullable|integer|exists:course_dates,id',
            ]);

            $auth = $this->getAuthenticatedStudent();
            if (isset($auth['error']))
                return response()->json($auth['data'], $auth['status']);

            $student = $auth['user'];

            Log::info('Student ID verification started', [
                'student_id' => $student->id,
                'course_date_id' => $request->course_date_id ?? null,
                'file_size' => $request->file('id_document')->getSize()
            ]);

            // Initialize service if not set
            if (!$this->idVerificationService) {
                $this->idVerificationService = app(IdVerificationService::class);
            }

            // Get course_auth_id from CourseDate if provided
            $courseAuthId = null;
            if ($request->course_date_id) {
                $courseDate = CourseDate::find($request->course_date_id);
                if ($courseDate) {
                    $courseAuthId = CourseAuth::where('user_id', $student->id)
                        ->where('course_id', $courseDate->course_id)
                        ->value('id');
                }
            }

            // TRACK: Student uploaded identity photo
            $this->activityTracker->track($student->id, 'identity_photo_uploaded', [
                'course_auth_id' => $courseAuthId,
                'description' => 'Student uploaded ID document for verification',
                'data' => [
                    'file_size' => $request->file('id_document')->getSize(),
                    'mime_type' => $request->file('id_document')->getMimeType(),
                    'course_date_id' => $request->course_date_id ?? null,
                ]
            ]);

            $result = $this->idVerificationService->startVerification(
                $student,
                $request->file('id_document'),
                $request->course_date_id ? (int) $request->course_date_id : null
            );

            return response()->json([
                'success' => true,
                'verification' => $result,
                'message' => 'ID verification started successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('ID verification start failed', [
                'error' => $e->getMessage(),
                'request' => $request->except(['id_document'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start ID verification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ID verification status for student
     * Route: GET /classroom/id-verification/status/{studentId}
     */
    public function getIdVerificationStatus(Request $request, int $studentId): JsonResponse
    {
        try {
            $result = $this->studentVerificationService->getIdVerificationStatus(
                $studentId,
                $request->query('course_date_id')
            );

            return response()->json($result, $result['success'] ? 200 : 404);

        } catch (\Exception $e) {
            Log::error('Get verification status failed', ['student_id' => $studentId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to get verification status'], 500);
        }
    }

    /**
     * Zoom Screen Share Portal - Iframe isolated Zoom SDK
     * Route: GET /classroom/portal/zoom/screen_share/{courseAuthId}/{courseDateId}
     */
    /**
     * Zoom Screen Share Portal - Iframe isolated Zoom SDK
     * Route: GET /classroom/portal/zoom/screen_share/{courseAuthId}/{courseDateId}
     */
    public function zoomScreenShare(int $courseAuthId, int $courseDateId): View
    {
        $user = Auth::user();

        if (!$user) {
            return view('student.zoomplayer2', ['error' => 'Authentication required', 'user' => null, 'courseAuth' => null, 'courseDate' => null, 'zoomCredentials' => null]);
        }

        try {
            $result = $this->studentClassroomService->getZoomScreenShareData($user, $courseAuthId, $courseDateId);

            if (!$result['success']) {
                return view('student.zoomplayer2', array_merge(['error' => $result['message']], $result['data'] ?? []));
            }

            return view('student.zoomplayer2', $result['data']);

        } catch (\Exception $e) {
            Log::error('Zoom screen share error', ['user_id' => $user->id, 'course_auth_id' => $courseAuthId, 'course_date_id' => $courseDateId, 'error' => $e->getMessage()]);
            return view('student.zoomplayer2', ['error' => 'Failed to load Zoom screen share', 'user' => $user, 'courseAuth' => null, 'courseDate' => null, 'zoomCredentials' => null]);
        }
    }

    /**
     * Get ID verification summary
     * Route: GET /classroom/id-verification/summary/{verificationId}
     */
    public function getIdVerificationSummary(int $verificationId): JsonResponse
    {
        try {
            $result = $this->studentVerificationService->getIdVerificationSummary($verificationId);
            return response()->json($result, $result['success'] ? 200 : 404);

        } catch (\Exception $e) {
            Log::error('Get verification summary failed', ['verification_id' => $verificationId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to get verification summary'], 500);
        }
    }

    /**
     * Upload daily headshot for identity verification
     * Route: POST /classroom/id-verification/upload-headshot
     */
    public function uploadHeadshot(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'headshot' => 'required|image|max:10240', // 10MB max
                'student_id' => 'required|integer|exists:users,id',
                'course_date_id' => 'required|integer|exists:course_dates,id',
            ]);

            $student = \App\Models\User::findOrFail((int) $request->student_id);
            $result = $this->studentVerificationService->uploadHeadshot(
                $student,
                $request->file('headshot'),
                $request->course_date_id
            );

            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (\Exception $e) {
            Log::error('Headshot upload failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to upload headshot'], 500);
        }
    }

    /**
     * Check if headshot uploaded for today's class
     * Route: GET /classroom/student/onboarding/check-headshot/{courseDateId}
     */
    public function checkHeadshotStatus(): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $user = $auth['user'];

            // Build the expected headshot filename using authenticated user info
            // Filename format: {studentunit_id}_{todays_date}_{firstname}_{lastname}.jpg
            // But we only have user info, so we need to get today's StudentUnit
            $todaysDate = now()->format('Y-m-d');

            // Get today's active course date for this user to find their StudentUnit
            // Also try to get ANY active ONLINE course date (not just today's)
            $courseDate = \App\Models\CourseDate::where('status', 'ONLINE')
                ->where(function ($query) use ($todaysDate) {
                    $query->where('course_date', $todaysDate)
                        ->orWhereDate('course_date', '<=', $todaysDate)
                        ->orWhereDate('course_date', '>=', $todaysDate);
                })
                ->orderBy('course_date', 'desc')
                ->first();

            if ($courseDate) {
                $studentUnit = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)
                    ->where('student_id', $user->id)
                    ->first();

                if ($studentUnit) {
                    $headshotFilename = "{$studentUnit->id}_{$todaysDate}_{$user->fname}_{$user->lname}.jpg";
                    $headshotPath = "validations/headshots/{$headshotFilename}";
                    $exists = \Storage::disk('media')->exists($headshotPath);

                    Log::info('Headshot status check', [
                        'student_id' => $user->id,
                        'student_unit_id' => $studentUnit->id,
                        'headshot_filename' => $headshotFilename,
                        'exists' => $exists
                    ]);

                    return response()->json([
                        'success' => true,
                        'exists' => $exists,
                        'filename' => $headshotFilename,
                        'message' => $exists ? 'Headshot found' : 'Headshot not yet uploaded'
                    ], 200);
                }
            }

            // No active course or student unit found - return not found instead of error
            Log::warning('Headshot status check - no active course date found', [
                'student_id' => $user->id,
                'todays_date' => $todaysDate
            ]);

            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'No active classroom session today'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Check headshot status failed', [
                'user_id' => $auth['user']->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to check headshot status'], 500);
        }
    }

    /**
     * Check if ID card exists for student
     * Route: GET /classroom/student/onboarding/check-id-card/{courseAuthId}
     *
     * Checks for physical file: {course_auth_id}_{firstname}_{lastname}.jpg
     */
    public function checkIdCardStatus(int $courseAuthId): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $user = $auth['user'];

            // Build the expected ID card filename using course_auth_id and user name
            // Filename format: {course_auth_id}_{firstname}_{lastname}.jpg
            $idCardFilename = "{$courseAuthId}_{$user->fname}_{$user->lname}.jpg";
            $idCardPath = "validations/idcards/{$idCardFilename}";

            // Check if ID card exists using the media disk
            $exists = \Storage::disk('media')->exists($idCardPath);

            Log::info('ID card status check', [
                'user_id' => $user->id,
                'course_auth_id' => $courseAuthId,
                'id_card_filename' => $idCardFilename,
                'exists' => $exists
            ]);

            return response()->json([
                'success' => true,
                'exists' => $exists,
                'id_card_exists' => $exists,
                'filename' => $idCardFilename,
                'message' => $exists ? 'ID card found' : 'ID card not yet uploaded'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Check ID card status failed', ['user_id' => $auth['user']->id, 'course_auth_id' => $courseAuthId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to check ID card status'], 500);
        }
    }

    /**
     * Get course dates with headshot status for student
     * Route: GET /classroom/student/onboarding/course-dates-headshots/{courseAuthId}
     */
    public function getCourseDatesWithHeadshots(int $courseAuthId): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        try {
            $result = $this->studentVerificationService->getCourseDatesWithHeadshots($auth['user'], $courseAuthId);
            return response()->json($result, $result['success'] ? 200 : 403);

        } catch (\Exception $e) {
            Log::error('Get course dates with headshots failed', ['user_id' => $auth['user']->id, 'course_auth_id' => $courseAuthId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to get course dates'], 500);
        }
    }



    /**
     * Get student data array using configuration-based service
     *
     * This endpoint provides a complete, structured student data response
     * based on the configuration defined in config/student_data.php
     *
     * @return JsonResponse
     */
    public function getStudentDataArray(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => []
                ], 401);
            }

            Log::info('StudentDashboardController: Building student data array', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            // Use the new configuration-based service
            $dataArrayService = new StudentDataArrayService($user);
            $studentData = $dataArrayService->buildStudentDataArray();

            Log::info('StudentDashboardController: Student data array built successfully', [
                'user_id' => $user->id,
                'course_auths_count' => count($studentData['course_auths']),
                'student_units_count' => count($studentData['student_units']),
                'student_lessons_count' => count($studentData['student_lessons']),
                'self_studying_lessons_count' => count($studentData['self_studying_lessons']),
                'has_validations' => !empty($studentData['validations'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student data loaded successfully',
                'data' => $studentData,
                'config_version' => config('student_data.version', '1.0'),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('StudentDashboardController: Failed to build student data array', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load student data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get student polling data (simplified version)
     *
     * This endpoint provides the specific data structure needed for student polling
     * as defined in the student-poll-array-structure.md documentation
     *
     * ❌ DOES NOT INCLUDE CLASSROOM DATA - Use /classroom/class/data for that
     *
     * @return JsonResponse
     */
    public function getStudentPollData(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // NEW DAY CLEANUP: Auto-complete any previous day's active lessons
            $this->studentLessonService->autoCompletePreviousDayLessons($user->id);

            $dataArrayService = new StudentDataArrayService($user);
            $fullData = $dataArrayService->buildStudentDataArray();

            // Format for polling endpoint (as per documentation)
            // ONLY STUDENT DATA - NO CLASSROOM DATA (prevents UI re-render conflicts)
            $pollData = [
                'success' => true,
                'message' => 'Student poll data loaded successfully',

                // Core student data
                'user' => $fullData['student'],

                // Course enrollment data
                'courseAuth' => $fullData['course_auths'][0] ?? null, // Current/primary course
                'courseAuths' => $fullData['course_auths'], // All enrollments

                // Student's actual attendance/progress
                'studentUnit' => $fullData['student_units'][0] ?? null, // Current attendance
                'studentLessons' => $fullData['student_lessons'], // Completed lessons
                'currentStudentLesson' => $this->getCurrentStudentLesson($fullData['student_lessons']),
                'completedStudentLessons' => $this->getCompletedStudentLessons($fullData['student_lessons']),

                // Progress metrics
                'allLessonsTotal' => $this->calculateTotalLessons($fullData),
                'allCompletedStudentLessonsTotal' => $this->calculateCompletedLessons($fullData),

                // Student validation status (full validations array for onboarding)
                'validations' => [
                    'agreement' => $fullData['validations']['summary']['authAgreement'] ?? false,
                    'rulesAccepted' => $fullData['validations']['summary']['authAgreement'] ?? false, // TODO: track separate from agreement
                    'idCard' => $fullData['validations']['summary']['idcard'] ?? null,
                    'headshots' => $this->buildHeadshotsArray($fullData['student_units'][0] ?? null),
                    'idCardStatus' => $fullData['validations']['summary']['idcard_status'] ?? null,
                    'headshotStatus' => $fullData['validations']['summary']['headshot_status'] ?? null,
                ],

                // Student session status
                'lessonInProgress' => $this->isLessonInProgress($fullData['student_lessons']),
                'lessonPaused' => false, // TODO: Implement lesson pause logic
                'gettingStarted' => empty($fullData['course_auths']),

                // Student-specific settings
                'student_unit_id' => $fullData['student_units'][0]['id'] ?? null,
            ];

            return response()->json($pollData);

        } catch (\Exception $e) {
            Log::error('StudentDashboardController: Failed to get student poll data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load student poll data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student classroom context
     *
     * This endpoint provides classroom-specific data for a student
     * including real-time lesson status and classroom features.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStudentClassroomContext(Request $request): JsonResponse
    {
        $auth = $this->getAuthenticatedStudent();
        if (isset($auth['error'])) return response()->json($auth['data'], $auth['status']);

        $courseDateId = $request->query('course_date_id');
        if (!$courseDateId) {
            return response()->json(['success' => false, 'message' => 'Course date ID required'], 400);
        }

        try {
            $result = $this->studentClassroomService->getClassroomContext($auth['user'], (int) $courseDateId);
            return response()->json($result, $result['success'] ? 200 : ($result['status'] ?? 500));

        } catch (\Exception $e) {
            Log::error('Failed to get classroom context', ['user_id' => $auth['user']->id, 'course_date_id' => $courseDateId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to load classroom context'], 500);
        }
    }

    /**
     * Helper methods for poll data processing
     */
    protected function getCurrentStudentLesson(array $studentLessons): ?array
    {
        // Find the most recent lesson without completion
        foreach (array_reverse($studentLessons) as $lesson) {
            if (!$lesson['completed_at']) {
                return $lesson;
            }
        }
        return null;
    }

    protected function getCompletedStudentLessons(array $studentLessons): array
    {
        return array_filter($studentLessons, function ($lesson) {
            return !empty($lesson['completed_at']);
        });
    }

    protected function calculateTotalLessons(array $fullData): int
    {
        // Count all available lessons from course auths
        $total = 0;
        foreach ($fullData['course_auths'] as $courseAuth) {
            // This would need to be calculated based on course structure
            // For now, return a placeholder
            $total += 10; // Placeholder - needs actual lesson counting logic
        }
        return $total;
    }

    protected function calculateCompletedLessons(array $fullData): int
    {
        return count($this->getCompletedStudentLessons($fullData['student_lessons'])) +
            count(array_filter($fullData['self_studying_lessons'], function ($lesson) {
                return !empty($lesson['completed_at']);
            }));
    }

    protected function isLessonInProgress(array $studentLessons): bool
    {
        return !empty($this->getCurrentStudentLesson($studentLessons));
    }

    /**
     * Build headshots array from StudentUnit with all daily headshot paths
     *
     * @param array|null $studentUnit
     * @return array
     */
    protected function buildHeadshotsArray(?array $studentUnit): array
    {
        if (!$studentUnit || !isset($studentUnit['id'])) {
            return [];
        }

        $studentUnitId = $studentUnit['id'];
        $headshots = [];

        // Check for headshots for multiple days
        // Format: validations/headshots/{studentUnitId}_{YYYY-MM-DD}_{firstname}_{lastname}.jpg
        $daysToCheck = 5; // Check up to 5 days of attendance

        for ($i = 0; $i < $daysToCheck; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            // Get student's first and last name from StudentUnit relationship
            if (isset($studentUnit['student']) && isset($studentUnit['student']['fname']) && isset($studentUnit['student']['lname'])) {
                $fname = $studentUnit['student']['fname'];
                $lname = $studentUnit['student']['lname'];
                $filename = "{$studentUnitId}_{$date}_{$fname}_{$lname}.jpg";
                $path = "validations/headshots/{$filename}";

                // Check if file exists
                if (\Storage::disk('media')->exists($path)) {
                    $headshots[$date] = $path;
                }
            }
        }

        return $headshots;
    }

    /**
     * Find Student's Active Classroom Session
     * Helper method to find current active classroom for student
     *
     * This mimics the logic from old StudentPortalController->getClassRoomData()
     * Uses CourseAuth->ClassroomCourseDate() pattern but adapted for new service architecture
     */
    private function findStudentActiveClassroom($user)
    {
        try {
            // PRIORITY 1: Check if student has entered classroom today (has StudentUnit for today)
            $todayStudentUnit = \App\Models\StudentUnit::whereIn('course_auth_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('course_auths')
                    ->where('user_id', $user->id)
                    ->whereNull('disabled_at');
            })
                ->whereDate('created_at', today())
                ->with(['courseDate.courseUnit.course'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($todayStudentUnit && $todayStudentUnit->courseDate) {
                $courseDate = $todayStudentUnit->courseDate;
                $courseUnit = $courseDate->courseUnit; // Get CourseUnit which has course_id

                // Find the matching CourseAuth for this course
                // CourseDate has course_unit_id, CourseUnit has course_id
                $courseAuth = CourseAuth::where('user_id', $user->id)
                    ->where('course_id', $courseUnit->course_id)
                    ->whereNull('disabled_at')
                    ->first();

                if ($courseAuth) {
                    Log::info("StudentDashboardController: Found active classroom from today's StudentUnit", [
                        'user_id' => $user->id,
                        'course_auth_id' => $courseAuth->id,
                        'course_id' => $courseAuth->course_id,
                        'course_date_id' => $courseDate->id,
                        'student_unit_id' => $todayStudentUnit->id
                    ]);

                    return [
                        'course_date' => $courseDate,
                        'course_auth' => $courseAuth,
                        'has_access' => true,
                        'is_current' => true
                    ];
                }
            }

            // PRIORITY 2: Check for any active CourseDate (fallback if no StudentUnit yet)
            // PRIORITIZE CourseDates where instructor has started class (has InstUnit)
            $courseAuths = CourseAuth::where('user_id', $user->id)
                ->whereNull('disabled_at')
                ->get();

            if ($courseAuths->isEmpty()) {
                Log::info("StudentDashboardController: No active course auths", [
                    'user_id' => $user->id
                ]);
                return null;
            }

            // FIRST PASS: Look for active CourseDate with InstUnit (instructor started)
            foreach ($courseAuths as $courseAuth) {
                // Get course unit IDs for this course
                $courseUnitIds = $courseAuth->GetCourse()
                    ->GetCourseUnits()
                    ->pluck('id')
                    ->toArray();

                if (empty($courseUnitIds)) {
                    continue;
                }

                // Find active CourseDate for today WITH InstUnit (instructor started)
                $courseDate = CourseDate::where('starts_at', '>=', \App\Helpers\DateHelpers::DayStartSQL())
                    ->where('ends_at', '<=', \App\Helpers\DateHelpers::DayEndSQL())
                    ->where('is_active', true)
                    ->whereIn('course_unit_id', $courseUnitIds)
                    ->whereHas('InstUnit', function ($query) {
                        $query->whereNull('completed_at'); // Only active InstUnits
                    })
                    ->first();

                if ($courseDate) {
                    Log::info("StudentDashboardController: Found active CourseDate with InstUnit (ONLINE)", [
                        'user_id' => $user->id,
                        'course_auth_id' => $courseAuth->id,
                        'course_date_id' => $courseDate->id,
                        'starts_at' => $courseDate->starts_at,
                        'is_active' => $courseDate->is_active,
                        'has_inst_unit' => true,
                    ]);

                    return [
                        'course_date' => $courseDate,
                        'course_auth' => $courseAuth,
                        'has_access' => true,
                        'is_current' => true
                    ];
                }
            }

            // SECOND PASS: If no InstUnit found, look for any active CourseDate (WAITING)
            foreach ($courseAuths as $courseAuth) {
                $courseUnitIds = $courseAuth->GetCourse()
                    ->GetCourseUnits()
                    ->pluck('id')
                    ->toArray();

                if (empty($courseUnitIds)) {
                    continue;
                }

                // Find active CourseDate for today (no InstUnit requirement)
                $courseDate = CourseDate::where('starts_at', '>=', \App\Helpers\DateHelpers::DayStartSQL())
                    ->where('ends_at', '<=', \App\Helpers\DateHelpers::DayEndSQL())
                    ->where('is_active', true)
                    ->whereIn('course_unit_id', $courseUnitIds)
                    ->first();

                if ($courseDate) {
                    Log::info("StudentDashboardController: Found active CourseDate (WAITING)", [
                        'user_id' => $user->id,
                        'course_auth_id' => $courseAuth->id,
                        'course_date_id' => $courseDate->id,
                        'starts_at' => $courseDate->starts_at,
                        'is_active' => $courseDate->is_active,
                        'has_inst_unit' => false,
                    ]);

                    return [
                        'course_date' => $courseDate,
                        'course_auth' => $courseAuth,
                        'has_access' => true,
                        'is_current' => true
                    ];
                }
            }

            Log::info("StudentDashboardController: No active CourseDate found for any course auth", [
                'user_id' => $user->id,
                'course_auth_count' => $courseAuths->count()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Find active classroom error", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Ensure StudentUnit exists for student accessing CourseDate
     * Creates StudentUnit automatically when student first accesses a CourseDate
     * This happens BEFORE instructor creates InstUnit (offline classroom phase)
     *
     * @param User $user The student user
     * @param CourseAuth $courseAuth The student's course authorization
     * @param CourseDate $courseDate The course date being accessed
     * @return StudentUnit The created or existing StudentUnit
     */
    private function ensureStudentUnit($user, $courseAuth, $courseDate): ?StudentUnit
    {
        try {
            // Check if StudentUnit already exists
            $existingStudentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDate->id)
                ->first();

            if ($existingStudentUnit) {
                Log::info("StudentDashboardController: StudentUnit already exists", [
                    'student_unit_id' => $existingStudentUnit->id,
                    'user_id' => $user->id,
                    'course_date_id' => $courseDate->id,
                    'created_at' => $existingStudentUnit->created_at
                ]);
                return $existingStudentUnit;
            }

            // Get CourseUnit from CourseDate
            $courseUnit = $courseDate->GetCourseUnit();
            if (!$courseUnit) {
                Log::error("StudentDashboardController: CourseUnit not found for CourseDate", [
                    'course_date_id' => $courseDate->id
                ]);
                return null;
            }

            // Create StudentUnit (OFFLINE phase - no InstUnit yet)
            $studentUnit = new StudentUnit();
            $studentUnit->course_auth_id = $courseAuth->id;
            $studentUnit->course_unit_id = $courseUnit->id;
            $studentUnit->course_date_id = $courseDate->id;
            $studentUnit->inst_unit_id = null; // Will be set when instructor starts class
            $studentUnit->attendance_type = 'offline'; // Initially offline, becomes 'online' when InstUnit created
            $studentUnit->unit_completed = false;
            $studentUnit->save();

            Log::info("StudentDashboardController: StudentUnit created automatically", [
                'student_unit_id' => $studentUnit->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $courseDate->id,
                'course_unit_id' => $courseUnit->id,
                'attendance_type' => 'offline',
                'join_time' => $studentUnit->created_at
            ]);

            return $studentUnit;

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Failed to ensure StudentUnit", [
                'user_id' => $user->id,
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $courseDate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Auto-generate StudentUnit for all student's active CourseDates
     * Called during dashboard load to ensure StudentUnit exists
     *
     * WORKFLOW:
     * - CourseDate exists → Create StudentUnit (student registered for that class session)
     * - No CourseDate → Student in offline/self-study mode
     *
     * @param User $user The student user
     * @return array Summary of created/existing StudentUnits
     */


    /**
     * Accept terms and conditions - updates CourseAuth.agreed_at timestamp
     */
    public function acceptTerms(Request $request): JsonResponse
    {
        try {
            // Debug logging
            Log::info('🎯 acceptTerms called', [
                'request_all' => $request->all(),
                'user_id' => Auth::id(),
                'has_course_auth_id' => $request->has('course_auth_id'),
                'course_auth_id_value' => $request->input('course_auth_id'),
            ]);

            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id'
            ]);

            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);

            // Verify this courseAuth belongs to authenticated user
            if ($courseAuth->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course authorization'
                ], 403);
            }

            // Update agreed_at timestamp
            $courseAuth->agreed_at = now();
            $courseAuth->save();

            // Track activity
            if ($this->activityTracker) {
                $this->activityTracker->track(
                    Auth::id(),
                    'agreement_accepted',
                    [
                        'description' => 'Student accepted terms and conditions',
                        'course_auth_id' => $courseAuth->id,
                        'course_id' => $courseAuth->course_id,
                        'agreed_at' => $courseAuth->agreed_at->toISOString()
                    ]
                );
            }

            Log::info("StudentDashboardController: Terms accepted", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuth->id,
                'agreed_at' => $courseAuth->agreed_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terms accepted successfully',
                'agreed_at' => $courseAuth->agreed_at
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Failed to accept terms", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept terms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if student has already agreed to terms
     */
    public function checkAgreementStatus($courseAuthId): JsonResponse
    {
        try {
            $courseAuth = CourseAuth::findOrFail($courseAuthId);

            // Verify this courseAuth belongs to authenticated user
            if ($courseAuth->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course authorization'
                ], 403);
            }

            $alreadyAgreed = !is_null($courseAuth->agreed_at);

            Log::info("StudentDashboardController: Checked agreement status", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuth->id,
                'already_agreed' => $alreadyAgreed,
                'agreed_at' => $courseAuth->agreed_at
            ]);

            return response()->json([
                'already_agreed' => $alreadyAgreed,
                'agreed_at' => $courseAuth->agreed_at
            ]);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Failed to check agreement status", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuthId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check agreement status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept classroom rules (tracked daily via activity tracker)
     */
    public function acceptRules(Request $request): JsonResponse
    {
        try {
            // Debug logging
            Log::info('🎯 acceptRules called', [
                'request_all' => $request->all(),
                'user_id' => Auth::id(),
                'has_course_auth_id' => $request->has('course_auth_id'),
                'has_course_date_id' => $request->has('course_date_id'),
            ]);

            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id',
                'course_date_id' => 'required|integer|exists:course_dates,id'
            ]);

            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);

            // Verify this courseAuth belongs to authenticated user
            if ($courseAuth->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course authorization'
                ], 403);
            }

            // Track activity (rules are tracked daily)
            if ($this->activityTracker) {
                $this->activityTracker->track(
                    Auth::id(),
                    'rules_accepted',
                    [
                        'description' => 'Student accepted classroom rules and procedures',
                        'course_auth_id' => $courseAuth->id,
                        'data' => [
                            'course_id' => $courseAuth->course_id,
                            'course_date_id' => (int) $validated['course_date_id'],
                            'accepted_at' => now()->toISOString()
                        ]
                    ]
                );
            }

            Log::info("StudentDashboardController: Rules accepted", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $validated['course_date_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rules accepted successfully',
                'accepted_at' => now()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Failed to accept rules", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept rules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if student has already agreed to rules today
     */
    public function checkRulesStatus($courseAuthId, $courseDateId): JsonResponse
    {
        try {
            $courseAuth = CourseAuth::findOrFail($courseAuthId);

            // Verify this courseAuth belongs to authenticated user
            if ($courseAuth->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course authorization'
                ], 403);
            }

            // Check if rules were accepted today for this course date
            $today = now()->startOfDay();
            $activity = \App\Models\StudentActivity::where('user_id', Auth::id())
                ->where('activity_type', 'rules_accepted')
                ->where('created_at', '>=', $today)
                ->whereJsonContains('data->course_date_id', (int) $courseDateId)
                ->first();

            $alreadyAgreed = !is_null($activity);

            Log::info("StudentDashboardController: Checked rules status", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $courseDateId,
                'already_agreed' => $alreadyAgreed,
                'activity_id' => $activity?->id
            ]);

            return response()->json([
                'already_agreed' => $alreadyAgreed,
                'accepted_at' => $activity?->created_at
            ]);

        } catch (Exception $e) {
            Log::error("StudentDashboardController: Failed to check rules status", [
                'user_id' => Auth::id(),
                'course_auth_id' => $courseAuthId,
                'course_date_id' => $courseDateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check rules status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete student onboarding process
     * Route: POST /classroom/student/onboarding/complete
     */
    public function completeOnboarding(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'student_unit_id' => 'nullable|integer|exists:student_unit,id',
                // Terms and rules are validated separately via StudentActivity
                // No need to require them here since they may already be accepted
            ]);

            $courseDateId = $validated['course_date_id'];
            $courseDate = CourseDate::with('CourseUnit')->findOrFail($courseDateId);

            // Get CourseAuth
            $courseAuth = CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseDate->CourseUnit->course_id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'No course authorization found'
                ], 404);
            }

            // Get or create StudentUnit
            $studentUnit = \App\Models\StudentUnit::firstOrCreate([
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $courseDateId
            ]);

            // Mark onboarding as complete
            // Terms/rules are already validated via StudentActivity checks
            Log::info('Student onboarding completed', [
                'user_id' => $user->id,
                'course_date_id' => $courseDateId,
                'student_unit_id' => $studentUnit->id,
                'course_auth_id' => $courseAuth->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding completed successfully',
                'data' => [
                    'student_unit_id' => $studentUnit->id,
                    'course_date_id' => $courseDateId,
                    'redirect_url' => route('classroom.dashboard')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Complete onboarding failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete onboarding: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a class by setting start_date and expire_date on CourseAuth
     *
     * This method:
     * 1. Sets start_date to current timestamp
     * 2. Calculates expire_date as 1 year from start_date
     * 3. Returns updated course data
     *
     * Business Logic:
     * - Student has 1 year from PURCHASE to START the course
     * - Once started, student has 1 year from START to COMPLETE
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Route: POST /api/student/course/start
     */
    public function startClass(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id',
            ]);

            $courseAuthId = $validated['course_auth_id'];

            // Get CourseAuth and verify ownership
            $courseAuth = CourseAuth::where('id', $courseAuthId)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course authorization not found or not owned by user'
                ], 404);
            }

            // Check if already started
            if ($courseAuth->start_date) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course already started',
                    'already_started' => true,
                    'data' => [
                        'course_auth_id' => $courseAuth->id,
                        'start_date' => $courseAuth->start_date,
                        'expire_date' => $courseAuth->expire_date,
                    ]
                ]);
            }

            // Start the class
            $now = now();
            $expireDate = $now->copy()->addYear(); // 1 year from start

            $courseAuth->start_date = $now;
            $courseAuth->expire_date = $expireDate;
            $courseAuth->save();

            Log::info('Student started class', [
                'user_id' => $user->id,
                'course_auth_id' => $courseAuth->id,
                'start_date' => $courseAuth->start_date,
                'expire_date' => $courseAuth->expire_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Class started successfully! You have 1 year to complete this course.',
                'data' => [
                    'course_auth_id' => $courseAuth->id,
                    'start_date' => $courseAuth->start_date->toISOString(),
                    'expire_date' => $courseAuth->expire_date->toISOString(),
                    'expires_in_days' => 365,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Start class failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete offline onboarding for FSTB (Fast Study Training Base)
     *
     * Handles:
     * 1. Student agreement to offline study rules
     * 2. Headshot upload for identity verification
     * 3. Creates or updates StudentUnit record
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function completeOfflineOnboarding(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate request
            $validated = $request->validate([
                'id_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // Max 10MB for ID card (nullable if already uploaded)
                'headshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB for headshot
                'course_auth_id' => 'required|integer|exists:course_auths,id',
                'lesson_id' => 'required|integer|exists:lessons,id',
            ]);

            $courseAuthId = $validated['course_auth_id'];
            $lessonId = $validated['lesson_id'];

            // Verify user has access to this course
            $courseAuth = CourseAuth::where('id', $courseAuthId)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course authorization not found or access denied'
                ], 403);
            }

            // DEFENSIVE CHECK: Check for any active SelfStudyLesson (not completed)
            $activeSelfStudy = \App\Models\SelfStudyLesson::where('course_auth_id', $courseAuthId)
                ->whereNull('completed_at')
                ->whereNull('dnc_at') // Not failed either
                ->first();

            if ($activeSelfStudy) {
                // NEW DAY CHECK: If the active lesson is from a previous day, auto-complete it
                $todayStart = now()->startOfDay();
                $lessonStartDate = \Carbon\Carbon::parse($activeSelfStudy->agreed_at)->startOfDay();

                if ($lessonStartDate->lt($todayStart)) {
                    // Lesson is from a previous day - auto-complete it
                    Log::info('Auto-completing previous day SelfStudyLesson', [
                        'self_study_lesson_id' => $activeSelfStudy->id,
                        'lesson_id' => $activeSelfStudy->lesson_id,
                        'started_at' => $activeSelfStudy->agreed_at,
                        'completing_at' => now(),
                    ]);

                    $activeSelfStudy->update([
                        'completed_at' => $lessonStartDate->endOfDay(),
                    ]);

                    // Also close any associated OfflineSessions
                    \App\Models\OfflineSession::where('self_study_lesson_id', $activeSelfStudy->id)
                        ->whereIn('status', ['active', 'paused'])
                        ->update([
                            'status' => 'completed',
                            'ended_at' => $lessonStartDate->endOfDay(),
                        ]);

                    // Clear the variable so the rest of the code knows there's no active session
                    $activeSelfStudy = null;
                }
            }

            // SMART CHECK: If trying to start the SAME lesson that's already active, allow resume
            if ($activeSelfStudy && $activeSelfStudy->lesson_id == $lessonId) {
                Log::info('Resuming same active lesson', [
                    'self_study_lesson_id' => $activeSelfStudy->id,
                    'lesson_id' => $lessonId,
                ]);

                // Return success with the existing lesson info for resume
                return response()->json([
                    'success' => true,
                    'message' => 'Resuming existing lesson session',
                    'self_study_lesson_id' => $activeSelfStudy->id,
                    'lesson_id' => $activeSelfStudy->lesson_id,
                    'agreed_at' => $activeSelfStudy->agreed_at,
                    'seconds_viewed' => $activeSelfStudy->seconds_viewed ?? 0,
                    'resumed' => true,
                ]);
            }

            if ($activeSelfStudy) {
                // Check if there's a corresponding active OfflineSession
                $activeSession = \App\Models\OfflineSession::where('self_study_lesson_id', $activeSelfStudy->id)
                    ->whereIn('status', ['active', 'paused'])
                    ->first();

                $sessionInfo = $activeSession ? [
                    'session_id' => $activeSession->id,
                    'status' => $activeSession->status,
                    'started_at' => $activeSession->started_at ? $activeSession->started_at->toISOString() : null,
                ] : null;

                // Format the timestamp properly for display
                $startedAt = $activeSelfStudy->agreed_at;
                if ($startedAt instanceof \Carbon\Carbon) {
                    $startedAtFormatted = $startedAt->toISOString();
                } elseif (is_numeric($startedAt)) {
                    // Handle Unix timestamp (shouldn't happen with proper casting, but be defensive)
                    $startedAtFormatted = \Carbon\Carbon::createFromTimestamp($startedAt)->toISOString();
                } else {
                    $startedAtFormatted = $startedAt;
                }

                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active lesson in progress. Please complete or exit your current lesson before starting a new one.',
                    'active_lesson_id' => $activeSelfStudy->lesson_id,
                    'active_lesson_started_at' => $startedAtFormatted,
                    'session_info' => $sessionInfo,
                ], 409); // 409 Conflict
            }

            // DEFENSIVE CHECK: Check for orphaned OfflineSessions (no SelfStudyLesson)
            // This shouldn't happen but let's clean up if it does
            $orphanedSessions = \App\Models\OfflineSession::whereDoesntHave('selfStudyLesson')
                ->whereIn('status', ['active', 'paused'])
                ->get();

            if ($orphanedSessions->count() > 0) {
                Log::warning('Found orphaned OfflineSessions, marking as failed', [
                    'count' => $orphanedSessions->count(),
                    'session_ids' => $orphanedSessions->pluck('id')->toArray(),
                ]);

                foreach ($orphanedSessions as $orphan) {
                    $orphan->update([
                        'status' => 'failed',
                        'ended_at' => now(),
                    ]);
                }
            }

            // Get the lesson to find which CourseUnit it belongs to
            $lesson = \App\Models\Lesson::findOrFail($lessonId);

            // Get the first CourseUnit that contains this lesson
            // Lessons can belong to multiple CourseUnits via course_unit_lessons pivot
            $courseUnit = $lesson->CourseUnits()->first();

            if (!$courseUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson is not associated with any course unit'
                ], 400);
            }

            // Get or create StudentUnit for offline study
            // For FSTB, we create a StudentUnit without a CourseDate
            // Note: StudentUnit doesn't have user_id, it links through course_auth_id

            // Check for existing StudentUnit from today
            $todayStart = now()->startOfDay();
            $todayEnd = now()->endOfDay();

            $existingUnit = StudentUnit::where('course_auth_id', $courseAuthId)
                ->where('course_unit_id', $courseUnit->id)
                ->whereNull('course_date_id')
                ->whereNull('completed_at') // Not completed
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->first();

            // DEFENSIVE CHECK: If StudentUnit exists but no active SelfStudyLesson, something went wrong
            // This shouldn't happen due to our earlier check, but let's be extra safe
            if ($existingUnit) {
                // Ensure created_at is a Carbon instance (handle timestamp cast)
                $createdAt = $existingUnit->created_at instanceof \Carbon\Carbon
                    ? $existingUnit->created_at
                    : \Carbon\Carbon::createFromTimestamp($existingUnit->created_at);

                // Check if there's a corresponding SelfStudyLesson for this unit
                $correspondingLesson = \App\Models\SelfStudyLesson::where('course_auth_id', $courseAuthId)
                    ->whereNull('completed_at')
                    ->whereNull('dnc_at')
                    ->whereBetween('agreed_at', [$createdAt->copy()->subMinutes(5), $createdAt->copy()->addMinutes(5)])
                    ->first();

                if ($correspondingLesson) {
                    // There's already a valid session in progress
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have an active session. Please complete or exit your current lesson before starting a new one.',
                        'active_lesson_id' => $correspondingLesson->lesson_id,
                        'student_unit_id' => $existingUnit->id,
                    ], 409);
                }

                // StudentUnit exists but no SelfStudyLesson - this is orphaned
                // Log it and we'll reuse it
                Log::warning('Found orphaned StudentUnit without SelfStudyLesson', [
                    'student_unit_id' => $existingUnit->id,
                    'course_auth_id' => $courseAuthId,
                    'created_at' => $existingUnit->created_at,
                ]);

                $studentUnit = $existingUnit; // Reuse the orphaned unit
            } else {
                // Check for existing StudentUnit from a previous day
                $yesterdayUnit = StudentUnit::where('course_auth_id', $courseAuthId)
                    ->where('course_unit_id', $courseUnit->id)
                    ->whereNull('course_date_id')
                    ->whereNull('completed_at')
                    ->where('created_at', '<', $todayStart)
                    ->first();

                // If StudentUnit exists from yesterday, mark it as completed
                if ($yesterdayUnit) {
                    // Ensure created_at is a Carbon instance (handle timestamp cast)
                    $createdAt = $yesterdayUnit->created_at instanceof \Carbon\Carbon
                        ? $yesterdayUnit->created_at
                        : \Carbon\Carbon::createFromTimestamp($yesterdayUnit->created_at);

                    $yesterdayUnit->update([
                        'completed_at' => $createdAt->endOfDay(),
                        'unit_completed' => true,
                    ]);

                    Log::info('Auto-completed previous day StudentUnit', [
                        'student_unit_id' => $yesterdayUnit->id,
                        'created_at' => $yesterdayUnit->created_at,
                        'completed_at' => $yesterdayUnit->completed_at,
                    ]);
                }

                // Create new StudentUnit for today
                $studentUnit = StudentUnit::create([
                    'course_auth_id' => $courseAuthId,
                    'course_unit_id' => $courseUnit->id,
                    'course_date_id' => null, // NULL for offline/FSTB study
                    'inst_unit_id' => 0, // No instructor unit for offline study
                    'attendance_type' => 'offline',
                    'verified' => false, // Will be verified after headshot processing
                    'unit_completed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ========================================================================
            // CRITICAL: Use database transaction to ensure atomicity
            // If headshot upload or SelfStudyLesson creation fails, rollback StudentUnit
            // ========================================================================

            try {
                \DB::beginTransaction();

                // Handle ID card upload FIRST (most likely to fail due to file size)
                $idCardPath = null;
                if ($request->hasFile('id_card')) {
                    $idCardFile = $request->file('id_card');

                    // Create filename using EXISTING format: {course_auth_id}_{firstname}_{lastname}.jpg
                    $firstName = strtolower(trim($user->fname ?? 'unknown'));
                    $lastName = strtolower(trim($user->lname ?? 'user'));

                    // Sanitize names for filename (remove spaces, special chars)
                    $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
                    $lastName = preg_replace('/[^a-z0-9]/', '', $lastName);

                    $filename = "{$courseAuthId}_{$firstName}_{$lastName}.jpg";

                    // Store in validations/idcards directory (same as existing ID cards)
                    $idCardPath = $idCardFile->storeAs('validations/idcards', $filename, 'media');

                    if (!$idCardPath) {
                        throw new \Exception('Failed to store ID card file');
                    }

                    Log::info('ID card uploaded for offline onboarding', [
                        'user_id' => $user->id,
                        'course_auth_id' => $courseAuthId,
                        'id_card_path' => $idCardPath,
                        'file_size' => $idCardFile->getSize(),
                    ]);
                }

                // Handle headshot upload SECOND
                $headshotPath = null;
                if ($request->hasFile('headshot')) {
                    $headshotFile = $request->file('headshot');

                    // Create unique filename
                    $filename = 'headshot_' . $user->id . '_' . $studentUnit->id . '_' . time() . '.' . $headshotFile->getClientOriginalExtension();

                    // Store in storage/app/public/headshots
                    $headshotPath = $headshotFile->storeAs('headshots', $filename, 'public');

                    if (!$headshotPath) {
                        throw new \Exception('Failed to store headshot file');
                    }

                    // Store BOTH ID card and headshot paths in StudentUnit verified field (JSON)
                    $studentUnit->verified = [
                        'id_card_uploaded' => true,
                        'id_card_path' => $idCardPath,
                        'headshot_uploaded' => true,
                        'headshot_path' => $headshotPath,
                        'verified_at' => now()->toISOString(),
                    ];
                    $studentUnit->save();
                }

                // Create SelfStudyLesson record ONLY AFTER successful headshot upload
                // Actual columns: course_auth_id, lesson_id, agreed_at, completed_at, credit_minutes, seconds_viewed
                $selfStudyLesson = \App\Models\SelfStudyLesson::create([
                    'course_auth_id' => $courseAuthId,
                    'lesson_id' => $lessonId,
                    'agreed_at' => now(), // Student agreed to FSTB rules
                    'completed_at' => null, // Will be set when lesson completes
                    'credit_minutes' => 0,
                    'seconds_viewed' => 0,
                ]);

                // Commit transaction - all steps succeeded
                \DB::commit();

                Log::info('Offline onboarding completed successfully', [
                    'user_id' => $user->id,
                    'student_unit_id' => $studentUnit->id,
                    'course_auth_id' => $courseAuthId,
                    'lesson_id' => $lessonId,
                    'id_card_path' => $idCardPath,
                    'headshot_path' => $headshotPath,
                    'self_study_lesson_id' => $selfStudyLesson->id,
                ]);

                // Generate unique session token for local storage locking
                $sessionToken = hash('sha256', $user->id . '_' . $studentUnit->id . '_' . $lessonId . '_' . now()->timestamp);

                return response()->json([
                    'success' => true,
                    'message' => 'Onboarding completed successfully',
                    'data' => [
                        'student_unit_id' => $studentUnit->id,
                        'verified' => is_array($studentUnit->verified) ? $studentUnit->verified : ['id_card_uploaded' => false, 'headshot_uploaded' => false],
                        'id_card_url' => $idCardPath ? asset("storage/media/{$idCardPath}") : null,
                        'headshot_url' => $headshotPath ? \Storage::url($headshotPath) : null,
                        'self_study_lesson_id' => $selfStudyLesson->id,
                        'session_token' => $sessionToken,
                        'lesson_id' => $lessonId,
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback transaction on any failure
                \DB::rollBack();

                // Delete StudentUnit if it was created
                if ($studentUnit && $studentUnit->exists) {
                    $studentUnit->delete();
                    Log::info('Rolled back StudentUnit creation due to error', [
                        'student_unit_id' => $studentUnit->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Delete uploaded headshot file if it exists
                if (isset($headshotPath) && $headshotPath && \Storage::disk('public')->exists($headshotPath)) {
                    \Storage::disk('public')->delete($headshotPath);
                    Log::info('Deleted uploaded headshot due to transaction rollback', [
                        'path' => $headshotPath,
                    ]);
                }

                Log::error('Offline onboarding failed - transaction rolled back', [
                    'user_id' => $user->id,
                    'course_auth_id' => $courseAuthId,
                    'lesson_id' => $lessonId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to complete onboarding. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Offline onboarding failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete onboarding: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-complete any active lessons from previous days
     * This runs on every poll to ensure clean state for new days
     */
    // This method has been moved to StudentLessonService
    // Keeping as wrapper for backward compatibility
    private function autoCompletePreviousDayLessons(int $userId): void
    {
        $this->studentLessonService->autoCompletePreviousDayLessons($userId);
    }

    // ===================================================================
    // OFFLINE CLASSROOM SESSION MANAGEMENT
    // StudentUnit sessions for offline self-study (no CourseDate)
    // ===================================================================

    /**
     * Check session synchronization status
     * Validates localStorage vs backend for offline StudentUnit
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkSessionSync(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auth,id',
                'student_unit_id' => 'nullable|integer|exists:student_unit,id',
            ]);

            $user = Auth::user();
            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);

            // Verify course auth belongs to user
            if ($courseAuth->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course',
                ], 403);
            }

            $result = $this->studentUnitService->validateSessionSync(
                $courseAuth,
                $validated['student_unit_id'] ?? null
            );

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Session sync check failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check session sync',
            ], 500);
        }
    }

    /**
     * Restore session from backend
     * Returns StudentUnit data when localStorage is missing
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function restoreSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auth,id',
                'student_unit_id' => 'required|integer|exists:student_unit,id',
            ]);

            $user = Auth::user();
            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);

            // Verify course auth belongs to user
            if ($courseAuth->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course',
                ], 403);
            }

            $result = $this->studentUnitService->restoreFrontendSession(
                $courseAuth,
                $validated['student_unit_id']
            );

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Session restore failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore session',
            ], 500);
        }
    }

    /**
     * Create new offline StudentUnit session
     * Creates new day session for self-study
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auth,id',
                'course_unit_id' => 'required|integer|exists:course_unit,id',
                'lesson_id' => 'nullable|integer|exists:lesson,id',
            ]);

            $user = Auth::user();
            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);
            $courseUnit = CourseUnit::findOrFail($validated['course_unit_id']);

            // Verify course auth belongs to user
            if ($courseAuth->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course',
                ], 403);
            }

            // Create StudentUnit
            $studentUnit = $this->studentUnitService->createOfflineStudentUnit(
                $courseAuth,
                $courseUnit
            );

            // Optionally start first lesson immediately
            $selfStudyLesson = null;
            if (isset($validated['lesson_id'])) {
                $lessonResult = $this->selfStudyLessonService->startLesson(
                    $courseAuth,
                    $validated['lesson_id']
                );

                if ($lessonResult['success']) {
                    $selfStudyLesson = $lessonResult['data'];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'New session created',
                'student_unit_id' => $studentUnit->id,
                'course_unit_id' => $courseUnit->id,
                'created_at' => $studentUnit->created_at,
                'lesson_started' => $selfStudyLesson !== null,
                'self_study_lesson_id' => $selfStudyLesson?->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Session creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create session',
            ], 500);
        }
    }

    /**
     * Start a new lesson (SelfStudyLesson)
     * Creates SelfStudyLesson record for offline study
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function startLessonSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auth,id',
                'lesson_id' => 'required|integer|exists:lesson,id',
            ]);

            $user = Auth::user();
            $courseAuth = CourseAuth::findOrFail($validated['course_auth_id']);

            // Verify course auth belongs to user
            if ($courseAuth->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course',
                ], 403);
            }

            $result = $this->selfStudyLessonService->startLesson(
                $courseAuth,
                $validated['lesson_id']
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            $lesson = $result['data'];

            return response()->json([
                'success' => true,
                'message' => 'Lesson started',
                'self_study_lesson_id' => $lesson->id,
                'lesson_id' => $lesson->lesson_id,
                'agreed_at' => $lesson->agreed_at,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Lesson start failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start lesson',
            ], 500);
        }
    }

    /**
     * Sync lesson progress (auto-save)
     * Updates seconds_viewed for SelfStudyLesson
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function syncLessonProgress(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'self_study_lesson_id' => 'required|integer|exists:self_study_lesson,id',
                'seconds_viewed' => 'required|integer|min:0',
            ]);

            $result = $this->selfStudyLessonService->syncProgress(
                $validated['self_study_lesson_id'],
                $validated['seconds_viewed']
            );

            return response()->json([
                'success' => true,
                'synced' => $result['synced'],
                'seconds_viewed' => $result['seconds_viewed'],
                'updated_at' => $result['updated_at'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Progress sync failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync progress',
            ], 500);
        }
    }

    /**
     * Pause lesson
     * Pauses SelfStudyLesson with activity logging
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function pauseLessonSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'self_study_lesson_id' => 'required|integer|exists:self_study_lesson,id',
                'seconds_viewed' => 'required|integer|min:0',
            ]);

            $result = $this->selfStudyLessonService->pauseLesson(
                $validated['self_study_lesson_id'],
                $validated['seconds_viewed']
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            return response()->json([
                'success' => true,
                'status' => 'paused',
                'paused_at' => $result['paused_at'],
                'seconds_viewed' => $result['seconds_viewed'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Lesson pause failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to pause lesson',
            ], 500);
        }
    }

    /**
     * Complete lesson
     * Marks SelfStudyLesson as completed and deducts credit hours
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function completeLessonSession(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'self_study_lesson_id' => 'required|integer|exists:self_study_lesson,id',
                'seconds_viewed' => 'required|integer|min:0',
            ]);

            $result = $this->selfStudyLessonService->completeLesson(
                $validated['self_study_lesson_id'],
                $validated['seconds_viewed']
            );

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            return response()->json([
                'success' => true,
                'status' => 'completed',
                'completed_at' => $result['completed_at'],
                'credit_deducted_hours' => $result['credit_deducted_hours'],
                'remaining_credit_hours' => $result['remaining_credit_hours'],
                'student_unit_still_active' => $result['student_unit_still_active'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Lesson completion failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete lesson',
            ], 500);
        }
    }

    /**
     * Start StudentLesson when instructor starts a lesson
     * Called from student frontend polling
     */
    public function startStudentLesson(Request $request): JsonResponse
    {
        try {
            Log::info('startStudentLesson: Request received', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            $validated = $request->validate([
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'lesson_id' => 'required|integer|exists:lessons,id',
                'inst_lesson_id' => 'required|integer|exists:inst_lesson,id',
            ]);

            Log::info('startStudentLesson: Validation passed', $validated);

            $user = Auth::user();

            // Get CourseDate to find the course_unit_id
            $courseDate = \App\Models\CourseDate::findOrFail($validated['course_date_id']);
            Log::info('startStudentLesson: CourseDate found', [
                'course_date_id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'course_id' => $courseDate->CourseUnit->course_id ?? 'N/A',
            ]);

            // Find student's CourseAuth for this course
            $courseAuth = \App\Models\CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseDate->CourseUnit->course_id)
                ->firstOrFail();
            Log::info('startStudentLesson: CourseAuth found', [
                'course_auth_id' => $courseAuth->id,
                'user_id' => $user->id,
                'course_id' => $courseDate->CourseUnit->course_id,
            ]);

            // Find or create StudentUnit for this course date
            $studentUnit = StudentUnit::firstOrCreate([
                'course_auth_id' => $courseAuth->id,
                'course_date_id' => $validated['course_date_id'],
            ], [
                'course_unit_id' => $courseDate->course_unit_id,
                'inst_unit_id' => $courseDate->InstUnit->id ?? null,
            ]);
            Log::info('startStudentLesson: StudentUnit found/created', [
                'student_unit_id' => $studentUnit->id,
                'was_recently_created' => $studentUnit->wasRecentlyCreated,
            ]);

            // Create StudentLesson if it doesn't exist
            $studentLesson = \App\Models\StudentLesson::firstOrCreate([
                'student_unit_id' => $studentUnit->id,
                'lesson_id' => $validated['lesson_id'],
                'inst_lesson_id' => $validated['inst_lesson_id'],
            ]);

            Log::info('StudentLesson started', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnit->id,
                'lesson_id' => $validated['lesson_id'],
                'inst_lesson_id' => $validated['inst_lesson_id'],
                'student_lesson_id' => $studentLesson->id,
                'already_existed' => !$studentLesson->wasRecentlyCreated,
            ]);

            return response()->json([
                'success' => true,
                'student_lesson_id' => $studentLesson->id,
                'message' => 'StudentLesson started successfully',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Failed to start StudentLesson', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start StudentLesson',
            ], 500);
        }
    }

    /**
     * Complete StudentLesson when instructor completes a lesson
     * Called from student frontend polling
     */
    public function completeStudentLesson(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_date_id' => 'required|integer|exists:course_dates,id',
                'lesson_id' => 'required|integer|exists:lessons,id',
                'inst_lesson_id' => 'required|integer|exists:inst_lesson,id',
            ]);

            $user = Auth::user();

            // Get CourseDate to find the course
            $courseDate = \App\Models\CourseDate::findOrFail($validated['course_date_id']);

            // Find student's CourseAuth for this course
            $courseAuth = \App\Models\CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseDate->CourseUnit->course_id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'CourseAuth not found',
                ], 404);
            }

            // Find StudentUnit
            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $validated['course_date_id'])
                ->first();

            if (!$studentUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'StudentUnit not found',
                ], 404);
            }

            // Find StudentLesson
            $studentLesson = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                ->where('lesson_id', $validated['lesson_id'])
                ->first();

            if (!$studentLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'StudentLesson not found',
                ], 404);
            }

            // Mark as completed if not already
            if (!$studentLesson->completed_at) {
                $studentLesson->completed_at = now();
                $studentLesson->save();

                Log::info('StudentLesson completed', [
                    'user_id' => $user->id,
                    'student_unit_id' => $studentUnit->id,
                    'lesson_id' => $validated['lesson_id'],
                    'inst_lesson_id' => $validated['inst_lesson_id'],
                    'student_lesson_id' => $studentLesson->id,
                    'completed_at' => $studentLesson->completed_at,
                ]);
            }

            return response()->json([
                'success' => true,
                'student_lesson_id' => $studentLesson->id,
                'completed_at' => $studentLesson->completed_at,
                'message' => 'StudentLesson completed successfully',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Failed to complete StudentLesson', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete StudentLesson',
            ], 500);
        }
    }
}




