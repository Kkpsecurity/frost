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

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Poll: Get student-specific data
     * Called every 5 seconds from StudentDataLayer.tsx
     */
    public function getStudentPollData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'data' => [
                        'student' => null,
                        'courses' => [],
                        'progress' => null,
                        'notifications' => [],
                        'assignments' => [],
                    ],
                ]);
            }

            // Get ALL student's course authorizations with course data
            $courseAuths = $user->courseAuths()
                ->with(['course'])
                ->get();

            // Map course auths to course list for dashboard
            $courses = $courseAuths->map(function ($courseAuth) {
                // Get classroom course date using trait method
                $classroomCourseDate = $courseAuth->ClassroomCourseDate();

                // Determine status based on completion, agreement (start), and classroom date
                if ($courseAuth->completed_at) {
                    $status = 'Completed';
                } elseif ($courseAuth->agreed_at || $classroomCourseDate) {
                    $status = 'In Progress';
                } else {
                    $status = 'Not Started';
                }

                return [
                    'id' => $courseAuth->id,
                    'course_auth_id' => $courseAuth->id,
                    'course_date_id' => $classroomCourseDate?->id,
                    'course_id' => $courseAuth->course_id,
                    'course_name' => $courseAuth->course?->title ?? $courseAuth->course?->title_long ?? 'N/A',
                    'start_date' => $classroomCourseDate?->class_date ?? $courseAuth->start_date,
                    'status' => $status,
                    'completion_status' => $courseAuth->is_passed ? 'Passed' :
                        ($courseAuth->completed_at ? 'Completed' : 'In Progress'),
                ];
            })->toArray();

            // Count courses with classroom dates
            $coursesWithDates = $courseAuths->filter(function ($courseAuth) {
                return $courseAuth->ClassroomCourseDate() !== null;
            })->count();

            // Return student data with all courses
            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $user->id,
                        'name' => $user->fname . ' ' . $user->lname,
                        'email' => $user->email,
                    ],
                    'courses' => $courses,
                    'progress' => [
                        'total_courses' => $courseAuths->count(),
                        'completed' => $courseAuths->where('completed_at', '!=', null)->count(),
                        'in_progress' => $coursesWithDates,
                    ],
                    'notifications' => [],
                    'assignments' => [],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Student poll data error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get classroom-specific data for a course
     * Called when student enters a specific classroom
     */
    public function getClassData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $courseAuthId = $request->input('course_auth_id');
            $currentDayOnly = $request->input('current_day_only', false);

            if (!$user || !$courseAuthId) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                ]);
            }

            // Get the course auth
            $courseAuth = CourseAuth::with(['course'])
                ->where('id', $courseAuthId)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'error' => 'Course not found',
                ], 404);
            }

            // Get classroom course date (if scheduled)
            $courseDate = $courseAuth->ClassroomCourseDate();

            // Get instructor unit (if instructor started class)
            $instUnit = null;
            if ($courseDate) {
                $instUnit = $courseDate->InstUnit;
            }

            // Get student unit (if student entered class)
            $studentUnit = null;
            if ($courseDate) {
                $studentUnit = StudentUnit::where('user_id', $user->id)
                    ->where('course_date_id', $courseDate->id)
                    ->first();
            }

            // Determine modality: ONLINE (live class) or OFFLINE (self-study)
            $isOnline = $courseDate && $instUnit;

            // Get lessons based on modality
            $lessons = [];
            $courseUnit = null;

            if ($isOnline) {
                // ONLINE MODE: Get lessons for current CourseUnit (day's lessons)
                $courseUnit = $courseDate->GetCourseUnit();
                if ($courseUnit) {
                    $allLessons = $courseUnit->GetLessons();
                    $lessons = $allLessons ? $allLessons->toArray() : [];
                }
            } else {
                // OFFLINE MODE: Get all lessons for the entire course
                $course = $courseAuth->course;
                if ($course) {
                    $allLessons = $course->GetLessons();
                    $lessons = $allLessons ? $allLessons->toArray() : [];
                }
            }

            // Get completed lessons for this student
            $completedLessons = [];
            if ($studentUnit) {
                $completedLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                    ->where('completed_at', '!=', null)
                    ->pluck('lesson_id')
                    ->toArray();
            }

            // Get active lesson (if instructor is teaching)
            $activeLessonId = null;
            if ($instUnit) {
                $activeInstLesson = ClassroomQueries::ActiveInstLesson($instUnit);
                if ($activeInstLesson) {
                    $activeLessonId = $activeInstLesson->lesson_id;
                }
            }

            // Enhance lessons with status information
            $lessons = collect($lessons)->map(function ($lesson) use ($completedLessons, $activeLessonId, $isOnline) {
                $lessonId = $lesson['id'];

                $status = 'incomplete';
                if (in_array($lessonId, $completedLessons)) {
                    $status = 'completed';
                } elseif ($activeLessonId === $lessonId) {
                    $status = $isOnline ? 'active_live' : 'active_fstb';
                }

                return [
                    'id' => $lessonId,
                    'title' => $lesson['name'] ?? $lesson['title'] ?? 'Lesson ' . $lessonId,
                    'description' => $lesson['description'] ?? '',
                    'duration_minutes' => $lesson['credit_minutes'] ?? $lesson['duration_minutes'] ?? $lesson['progress_minutes'] ?? 0,
                    'order' => $lesson['order'] ?? $lesson['order_by'] ?? 0,
                    'status' => $status,
                    'is_completed' => in_array($lessonId, $completedLessons),
                    'is_active' => $activeLessonId === $lessonId,
                ];
            })->sortBy('order')->values()->toArray();

            // Check for active self-study session
            $activeSelfStudySession = null;
            if (!$isOnline && $courseAuth) {
                $activeSelfStudySession = \App\Models\SelfStudyLesson::where('course_auth_id', $courseAuth->id)
                    ->whereNotNull('session_id')
                    ->where('session_expires_at', '>', now())
                    ->where('quota_status', '!=', 'consumed')
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'courseAuth' => [
                        'id' => $courseAuth->id,
                        'course_id' => $courseAuth->course_id,
                        'course_name' => $courseAuth->course?->title ?? 'N/A',
                    ],
                    'courseDate' => $courseDate ? [
                        'id' => $courseDate->id,
                        'class_date' => $courseDate->class_date,
                        'start_time' => $courseDate->start_time,
                        'end_time' => $courseDate->end_time,
                    ] : null,
                    'courseUnit' => $courseUnit ? [
                        'id' => $courseUnit->id,
                        'name' => $courseUnit->name ?? 'Unit ' . $courseUnit->id,
                        'day_number' => $courseUnit->day_number ?? null,
                    ] : null,
                    'instUnit' => $instUnit ? [
                        'id' => $instUnit->id,
                        'started_at' => $instUnit->start_time,
                        'completed_at' => $instUnit->completed_at,
                    ] : null,
                    'studentUnit' => $studentUnit ? [
                        'id' => $studentUnit->id,
                        'joined_at' => $studentUnit->created_at,
                        'verified' => $studentUnit->verified,
                    ] : null,
                    'lessons' => $lessons,
                    'modality' => $isOnline ? 'online' : 'offline',
                    'active_lesson_id' => $activeLessonId,
                    'completed_lessons_count' => count($completedLessons),
                    'total_lessons_count' => count($lessons),
                    'active_self_study_session' => $activeSelfStudySession ? [
                        'session_id' => $activeSelfStudySession->session_id,
                        'lesson_id' => $activeSelfStudySession->lesson_id,
                        'started_at' => $activeSelfStudySession->created_at,
                        'expires_at' => $activeSelfStudySession->session_expires_at,
                        'time_remaining_minutes' => max(0, now()->diffInMinutes($activeSelfStudySession->session_expires_at, false)),
                        'pause_remaining_minutes' => $activeSelfStudySession->total_pause_minutes_allowed - $activeSelfStudySession->total_pause_minutes_used,
                        'completion_percentage' => $activeSelfStudySession->completion_percentage ?? 0,
                    ] : null,
                    'settings' => [
                        'completion_threshold' => config('self_study.completion_threshold', 80),
                    ],
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Class data error', [
                'error' => $e->getMessage(),
                'course_auth_id' => $request->input('course_auth_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load classroom data',
            ], 500);
        }
    }

    /**
     * Poll: Get classroom data for active course date
     * Called every 5 seconds from StudentDataLayer.tsx
     */
    public function getClassroomPollData(Request $request): JsonResponse
    {
        try {
            $courseDateId = $request->query('course_date_id');
            $user = Auth::user();

            if (!$courseDateId) {
                return response()->json([
                    'success' => false,
                    'courseDate' => null,
                    'courseUnit' => null,
                    'course' => null,
                    'lessons' => [],
                    'instUnit' => null,
                    'config' => [],
                ]);
            }

            // Load course date with relationships
            $courseDate = CourseDate::with([
                'course',
                'course.courseUnit',
                'course.courseUnit.courseUnitLessons',
                'instUnit',
                'instUnit.instLessons',
                'studentUnits',
            ])->find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'courseDate' => null,
                    'courseUnit' => null,
                    'course' => null,
                    'lessons' => [],
                    'instUnit' => null,
                    'config' => [],
                ]);
            }

            // Return classroom data
            return response()->json([
                'success' => true,
                'courseDate' => $courseDate,
                'courseUnit' => $courseDate->course?->courseUnit,
                'course' => $courseDate->course,
                'lessons' => $courseDate->course?->courseUnit?->courseUnitLessons ?? [],
                'instUnit' => $courseDate->instUnit,
                'config' => [],
            ]);
        } catch (Exception $e) {
            Log::error('Classroom poll data error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show main student dashboard
     */
    public function dashboard(Request $request): View
    {
        $user = Auth::user();

        // Get course_auth_id from request or use first available enrollment
        $courseAuthId = $request->query('course_auth_id');

        if (!$courseAuthId) {
            // Auto-select first active course enrollment
            $firstCourseAuth = $user->courseAuths()
                ->with('course')
                ->first();

            if ($firstCourseAuth) {
                $courseAuthId = $firstCourseAuth->id;
            }
        }

        // Get course enrollments
        $courseAuths = $user->courseAuths()
            ->with('course')
            ->get()
            ->map(function ($courseAuth) {
                return [
                    'id' => $courseAuth->id,
                    'course_id' => $courseAuth->course_id,
                    'course_name' => $courseAuth->course ? $courseAuth->course->name : 'N/A',
                    'status' => $courseAuth->status ?? 'active',
                ];
            })
            ->toArray();

        // Build content for React app
        $content = [
            'title' => 'Student Dashboard',
            'description' => 'Student Classroom',
            'student' => [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'email' => $user->email,
            ],
            'course_auths' => $courseAuths,
            'selected_course_auth_id' => $courseAuthId,
            'lessons' => [],
            'has_lessons' => false,
            'validations' => null,
            'student_units' => []
        ];

        return view('frontend.students.dashboard', [
            'content' => $content,
            'course_auth_id' => $courseAuthId,
        ]);
    }

    /**
     * Get student video quota
     * Returns current quota status for self-study video lessons
     *
     * @return JsonResponse
     */
    public function getVideoQuota(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // TODO: Replace with actual database table when created
            // For now, return mock data matching the expected structure
            $quotaData = [
                'total_hours' => 10.0,
                'used_hours' => 0.0,
                'remaining_hours' => 10.0,
                'refunded_hours' => 0.0,
            ];

            // Future implementation with database:
            // $quota = StudentVideoQuota::firstOrCreate(
            //     ['user_id' => $user->id],
            //     ['total_hours' => 10.0, 'used_hours' => 0.0, 'refunded_hours' => 0.0]
            // );
            // $quotaData = [
            //     'total_hours' => $quota->total_hours,
            //     'used_hours' => $quota->used_hours,
            //     'remaining_hours' => $quota->total_hours - $quota->used_hours + $quota->refunded_hours,
            //     'refunded_hours' => $quota->refunded_hours,
            // ];

            return response()->json([
                'success' => true,
                'data' => $quotaData
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching video quota', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch video quota',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
