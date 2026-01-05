<?php

namespace App\Http\Controllers\Student;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\StudentUnit;
use App\Models\User;
use App\Classes\ClassroomQueries;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

use App\Services\AttendanceService;
use App\Services\StudentDashboardService;
use App\Services\StudentDataArrayService;
use App\Services\ClassroomDataArrayService;
use App\Services\ClassroomDashboardService;
use App\Services\StudentUnitService;
use App\Services\SelfStudyLessonService;
use App\Services\PauseAllocationService;
use App\Models\ZoomCreds;

// NOTE: Some legacy services were referenced here historically but do not exist in this repo.

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Infer Zoom credentials based on instructor role and course title pattern
     * - instructor_admin@stgroupusa.com (id: 1) if instructor is admin/sysadmin
     * - instructor_d@stgroupusa.com (id: 2) for "D" class courses
     * - instructor_g@stgroupusa.com (id: 3) for "G" class courses
     * - instructor_admin@stgroupusa.com (id: 1) for dev/admin/default
     */
    private function inferZoomCredentials($course, $instUnit = null): ?ZoomCreds
    {
        if (!$course) {
            return null;
        }

        // If instructor is admin or sysadmin, always use admin Zoom credentials
        if ($instUnit) {
            $instructor = User::find($instUnit->created_by);
            if ($instructor) {
                $role = strtolower($instructor->role ?? '');
                if (in_array($role, ['admin', 'sysadmin', 'sys admin', 'system admin'])) {
                    return ZoomCreds::find(1); // instructor_admin@stgroupusa.com
                }
            }
        }

        $courseTitle = strtoupper($course->title ?? $course->title_long ?? '');

        // Match Class D pattern
        if (preg_match('/\bCLASS\s*D\b|\bD\s*CLASS\b|\b-D\b|\bD-\b/', $courseTitle)) {
            return ZoomCreds::find(2); // instructor_d@stgroupusa.com
        }

        // Match Class G pattern
        if (preg_match('/\bCLASS\s*G\b|\bG\s*CLASS\b|\b-G\b|\bG-\b/', $courseTitle)) {
            return ZoomCreds::find(3); // instructor_g@stgroupusa.com
        }

        // Default to admin instructor for dev/testing
        return ZoomCreds::find(1); // instructor_admin@stgroupusa.com
    }

    private function getCsrfToken(Request $request): ?string
    {
        return $request->header('X-CSRF-TOKEN')
            ?? $request->header('x-csrf-token');
    }

    private function decodeVerifiedData($verified): array
    {
        if (is_array($verified)) {
            return $verified;
        }

        if (is_string($verified) && $verified !== '') {
            return json_decode($verified, true) ?? [];
        }

        if (is_object($verified)) {
            return (array) $verified;
        }

        return [];
    }

    private function findOrCreateStudentUnitForCourseDate(CourseDate $courseDate, User $user): StudentUnit
    {
        // Ensure the CourseDate belongs to this student.
        // CourseDates do not reliably carry course_auth_id; validate by CourseUnit->course_id.
        $courseId = $courseDate->CourseUnit?->course_id;
        if (!$courseId) {
            abort(500, 'Course date is missing course unit relationship');
        }

        $courseAuth = CourseAuth::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->orderByDesc('id')
            ->firstOrFail();

        // For onboarding, we require that the instructor has started the class.
        $instUnitId = $courseDate->InstUnit?->id;

        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
            ->where('course_date_id', $courseDate->id)
            ->first();

        if ($studentUnit) {
            // Keep inst_unit_id in sync when class is active.
            if ($instUnitId && (int) $studentUnit->inst_unit_id !== (int) $instUnitId) {
                $studentUnit->inst_unit_id = $instUnitId;
                $studentUnit->save();
            }
            return $studentUnit;
        }

        return StudentUnit::create([
            'course_auth_id' => $courseAuth->id,
            'course_unit_id' => $courseDate->course_unit_id,
            'course_date_id' => $courseDate->id,
            'inst_unit_id' => $instUnitId ?? 0,
            'terms_accepted' => false,
            'rules_accepted' => false,
            'onboarding_completed' => false,
        ]);
    }

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
                // Student owns the CourseAuth ("pass"), not the class schedule.
                // ClassroomCourseDate is intentionally conservative (only shows active/live class context).
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
                    'start_date' => $classroomCourseDate?->starts_at?->format('Y-m-d') ?? $courseAuth->start_date,
                    'status' => $status,
                    'completion_status' => $courseAuth->is_passed ? 'Passed' :
                        ($courseAuth->completed_at ? 'Completed' : 'In Progress'),
                ];
            })->toArray();

            // Count courses with classroom dates
            $coursesWithDates = $courseAuths->filter(function ($courseAuth) {
                return $courseAuth->ClassroomCourseDate() !== null;
            })->count();

            // -----------------------------------------------------------------
            // ACTIVE CLASSROOM LINK (student-owned view)
            // - CourseDate is a classroom entity, but the student poll is responsible for student-owned
            //   entities tied to it: StudentUnit + StudentLessons.
            // - We expose a lightweight summary for "today" so the UI can understand whether the
            //   student has a live/waiting class without treating schedule as student-owned.
            // -----------------------------------------------------------------

            $activeClassroom = null;
            try {
                $today = now()->format('Y-m-d');
                $courseIds = $courseAuths->pluck('course_id')->filter()->unique();

                if ($courseIds->isNotEmpty()) {
                    $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
                        ->whereDate('starts_at', $today)
                        ->whereHas('CourseUnit', function ($q) use ($courseIds) {
                            $q->whereIn('course_id', $courseIds);
                        })
                        ->orderBy('starts_at', 'asc')
                        ->first();

                    if ($courseDate && $courseDate->CourseUnit?->course_id) {
                        $courseId = (int) $courseDate->CourseUnit->course_id;
                        $courseAuthId = (int) (CourseAuth::where('user_id', $user->id)
                            ->where('course_id', $courseId)
                            ->orderByDesc('id')
                            ->value('id') ?? 0);

                        $studentUnit = null;
                        $completedLessonIds = [];
                        if ($courseAuthId > 0) {
                            $studentUnit = StudentUnit::where('course_auth_id', $courseAuthId)
                                ->where('course_date_id', $courseDate->id)
                                ->first();

                            if ($studentUnit) {
                                $completedLessonIds = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                                    ->whereNotNull('completed_at')
                                    ->pluck('lesson_id')
                                    ->toArray();
                            }
                        }

                        $activeClassroom = [
                            'status' => $courseDate->InstUnit ? 'active' : 'waiting',
                            'course_id' => $courseId,
                            'course_auth_id' => $courseAuthId > 0 ? $courseAuthId : null,
                            'course_date_id' => (int) $courseDate->id,
                            'inst_unit_id' => $courseDate->InstUnit?->id,
                            'student_unit' => $studentUnit ? [
                                'id' => $studentUnit->id,
                                'course_auth_id' => (int) ($studentUnit->course_auth_id ?? 0),
                                'course_date_id' => (int) ($studentUnit->course_date_id ?? 0),
                                'terms_accepted' => (bool) ($studentUnit->terms_accepted ?? false),
                                'rules_accepted' => (bool) ($studentUnit->rules_accepted ?? false),
                                'onboarding_completed' => (bool) ($studentUnit->onboarding_completed ?? false),
                                'verified' => (bool) ($studentUnit->verified ?? false),
                            ] : null,
                            'student_lessons' => [
                                'completed_lesson_ids' => $completedLessonIds,
                            ],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: keep poll responsive even if this optional summary fails.
            }

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
                    'active_classroom' => $activeClassroom,
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

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                ], 401);
            }

            // If no course_auth_id is provided, the classroom poll should still be able to answer:
            // "Is the school open today?" (i.e., is there a CourseDate for any of this student's courses)
            // This keeps CourseDate ownership in the classroom domain.
            if (!$courseAuthId) {
                $today = now()->format('Y-m-d');
                $courseIds = $user->courseAuths()->pluck('course_id')->filter()->unique();

                $courseDate = null;
                if ($courseIds->isNotEmpty()) {
                    $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
                        ->whereDate('starts_at', $today)
                        ->whereHas('CourseUnit', function ($q) use ($courseIds) {
                            $q->whereIn('course_id', $courseIds);
                        })
                        ->orderBy('starts_at', 'asc')
                        ->first();
                }

                // No class scheduled today for this student's enrollments.
                if (!$courseDate) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'course' => null,
                            'courseDate' => null,
                            'courseUnit' => null,
                            'instUnit' => null,
                            'studentUnit' => null,
                            'lessons' => [],
                            'config' => null,
                        ],
                    ]);
                }

                $courseId = $courseDate->CourseUnit?->course_id;
                $courseUnit = $courseDate->GetCourseUnit();
                $course = $courseDate->GetCourse();

                // Resolve the student's "pass" (CourseAuth) for this class schedule.
                $resolvedCourseAuthId = null;
                if ($courseId) {
                    $resolvedCourseAuthId = (int) (CourseAuth::where('user_id', $user->id)
                        ->where('course_id', (int) $courseId)
                        ->orderByDesc('id')
                        ->value('id') ?? 0);
                }

                // Create/ensure StudentUnit for this CourseDate as soon as the student hits the classroom poll.
                $studentUnit = null;
                try {
                    $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);
                } catch (\Throwable $e) {
                    // Non-fatal: keep poll responsive.
                    $studentUnit = null;
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        // Minimal course identity so the client can map to the student's course_auth_id.
                        'course' => [
                            'course_id' => (int) ($courseId ?? 0),
                            'course_name' => $course?->title ?? $course?->name ?? 'N/A',
                        ],
                        'course_auth_id' => $resolvedCourseAuthId && $resolvedCourseAuthId > 0 ? $resolvedCourseAuthId : null,
                        'courseDate' => [
                            'id' => $courseDate->id,
                            // Frontend expects these keys; derive from starts_at/ends_at.
                            'class_date' => optional($courseDate->starts_at)->format('Y-m-d'),
                            'class_time' => optional($courseDate->starts_at)->format('H:i:s'),
                            'duration_minutes' => ($courseDate->starts_at && $courseDate->ends_at)
                                ? (int) $courseDate->starts_at->diffInMinutes($courseDate->ends_at)
                                : null,
                        ],
                        'courseUnit' => $courseUnit ? [
                            'id' => $courseUnit->id,
                            'name' => $courseUnit->name ?? 'Unit ' . $courseUnit->id,
                            'day_number' => $courseUnit->day_number ?? null,
                            // Optionally keep a course_id here as well.
                            'course_id' => (int) ($courseId ?? 0),
                        ] : null,
                        'instUnit' => $courseDate->InstUnit ? [
                            'id' => $courseDate->InstUnit->id,
                            'started_at' => $courseDate->InstUnit->start_time,
                            'completed_at' => $courseDate->InstUnit->completed_at,
                        ] : null,
                        'studentUnit' => $studentUnit ? [
                            'id' => $studentUnit->id,
                            'course_auth_id' => (int) ($studentUnit->course_auth_id ?? 0),
                            'course_date_id' => (int) ($studentUnit->course_date_id ?? 0),
                            'joined_at' => $studentUnit->created_at,
                            'terms_accepted' => (bool) ($studentUnit->terms_accepted ?? false),
                            'rules_accepted' => (bool) ($studentUnit->rules_accepted ?? false),
                            'onboarding_completed' => (bool) ($studentUnit->onboarding_completed ?? false),
                            // Note: full identity verification details are returned on the per-course_auth poll.
                            'verified' => (bool) ($studentUnit->verified ?? false),
                        ] : null,
                        'lessons' => [],
                        'config' => null,
                    ],
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
            $courseDate = null;

            // Prefer today's CourseDate for this course_id, regardless of InstUnit (needed for Waiting Room).
            $today = now()->format('Y-m-d');
            $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
                ->whereDate('starts_at', $today)
                ->whereHas('CourseUnit', function ($q) use ($courseAuth) {
                    $q->where('course_id', $courseAuth->course_id);
                })
                ->orderBy('starts_at', 'asc')
                ->first();

            // Fallback to legacy behavior for non-today access paths.
            if (!$courseDate) {
                $courseDate = $courseAuth->ClassroomCourseDate();
            }

            // Get instructor unit (if instructor started class)
            $instUnit = null;
            if ($courseDate) {
                $instUnit = $courseDate->InstUnit;
            }

            // Ensure StudentUnit exists as soon as there is a CourseDate (waiting or active).
            $studentUnit = null;
            if ($courseDate) {
                try {
                    $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);
                } catch (\Throwable $e) {
                    $studentUnit = null;
                }
            }

            // -----------------------------------------------------------------
            // Validations payload (polling is source of truth)
            // - idcard: once per courseAuth (derive from any StudentUnit for this courseAuth)
            // - headshot: daily per studentUnit/courseDate
            // -----------------------------------------------------------------

            $idCardUrl = null;
            $headshotUrl = null;
            $headshotByDay = [];

            // Find any uploaded ID card for this courseAuth across all dates.
            $recentUnitWithId = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->orderByDesc('course_date_id')
                ->limit(25)
                ->get()
                ->first(function ($unit) {
                    $verified = $this->decodeVerifiedData($unit->getRawOriginal('verified'));
                    return !empty($verified['id_card_path']);
                });

            if ($recentUnitWithId) {
                $verified = $this->decodeVerifiedData($recentUnitWithId->getRawOriginal('verified'));
                if (!empty($verified['id_card_path'])) {
                    $idCardUrl = Storage::disk('public')->url($verified['id_card_path']);
                }
            }

            if ($studentUnit) {
                $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
                if (!empty($verified['headshot_path'])) {
                    $headshotUrl = Storage::disk('public')->url($verified['headshot_path']);
                }
            }

            // Provide headshot keyed by the class date's weekday (matches CaptureDevices.tsx expectation)
            if ($courseDate) {
                try {
                    $dayKey = strtolower(now()->format('l'));
                    if (!empty($courseDate->class_date)) {
                        $dayKey = strtolower((string) \Carbon\Carbon::parse($courseDate->class_date)->format('l'));
                    }
                    $headshotByDay[$dayKey] = $headshotUrl;
                } catch (\Throwable $e) {
                    // fallback: no headshot mapping
                }
            }

            $identityVerified = (bool) ($idCardUrl && $headshotUrl);

            // Determine modality: ONLINE (live class) or OFFLINE (self-study)
            $isOnline = $courseDate && $instUnit;

            // Zoom readiness (used by student iframe screen share)
            // Infer Zoom credentials based on instructor role and course title pattern
            $zoomCreds = $this->inferZoomCredentials($courseAuth->course, $instUnit);
            $zoomStatus = $zoomCreds?->zoom_status ?? 'disabled';
            $isZoomReady = ($zoomStatus === 'enabled');

            $screenShareUrl = null;
            if ($courseDate) {
                try {
                    $screenShareUrl = route('classroom.zoom.screen-share', [
                        'courseAuthId' => (int) $courseAuth->id,
                        'courseDateId' => (int) $courseDate->id,
                    ], false);
                } catch (\Throwable $e) {
                    $screenShareUrl = null;
                }
            }

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
                        // Agreement is once per course.
                        'agreed_at' => $courseAuth->agreed_at?->toISOString(),
                    ],
                    'courseDate' => $courseDate ? [
                        'id' => $courseDate->id,
                        'course_name' => $courseAuth->course?->title ?? $courseAuth->course?->title_long ?? $courseAuth->course?->name ?? 'N/A',
                        // Frontend expects these keys; derive from starts_at/ends_at.
                        'class_date' => optional($courseDate->starts_at)->format('Y-m-d'),
                        'class_time' => optional($courseDate->starts_at)->format('H:i:s'),
                        'duration_minutes' => ($courseDate->starts_at && $courseDate->ends_at)
                            ? (int) $courseDate->starts_at->diffInMinutes($courseDate->ends_at)
                            : null,
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
                        'course_auth_id' => (int) ($studentUnit->course_auth_id ?? 0),
                        'course_date_id' => (int) ($studentUnit->course_date_id ?? 0),
                        'joined_at' => $studentUnit->created_at,
                        'terms_accepted' => (bool) ($studentUnit->terms_accepted ?? false),
                        'rules_accepted' => (bool) ($studentUnit->rules_accepted ?? false),
                        'onboarding_completed' => (bool) ($studentUnit->onboarding_completed ?? false),
                        // True only when (idcard once per courseAuth) AND (headshot for this course_date)
                        'verified' => $identityVerified,
                    ] : null,
                    'validations' => [
                        'idcard' => $idCardUrl,
                        'headshot' => $headshotByDay,
                        'message' => null,
                    ],
                    'lessons' => $lessons,
                    'modality' => $isOnline ? 'online' : 'offline',
                    'active_lesson_id' => $activeLessonId,
                    'completed_lessons_count' => count($completedLessons),
                    'total_lessons_count' => count($lessons),
                    'zoom' => [
                        'status' => $zoomStatus,
                        'is_ready' => $isZoomReady,
                        'screen_share_url' => $screenShareUrl,
                    ],
                    'active_self_study_session' => $activeSelfStudySession ? [
                        'session_id' => $activeSelfStudySession->session_id,
                        'lesson_id' => $activeSelfStudySession->lesson_id,
                        'started_at' => $activeSelfStudySession->created_at,
                        'expires_at' => $activeSelfStudySession->session_expires_at,
                        'time_remaining_minutes' => max(0, now()->diffInMinutes($activeSelfStudySession->session_expires_at, false)),
                        'pause_remaining_minutes' => $activeSelfStudySession->total_pause_minutes_allowed - $activeSelfStudySession->total_pause_minutes_used,
                        'completion_percentage' => $activeSelfStudySession->completion_percentage ?? 0,
                        'pause_allocation' => PauseAllocationService::calculatePauseAllocation($activeSelfStudySession->lesson_duration_minutes ?? 60),
                    ] : null,
                    'settings' => [
                        'completion_threshold' => config('self_study.completion_threshold', 80),
                        'pause_warning_seconds' => PauseAllocationService::getWarningSeconds(),
                        'pause_alert_sound' => PauseAllocationService::getAlertSoundPath(),
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

    /**
     * Get class status for student waiting room
     * Returns: waiting, onboarding, or active-classroom
     *
     * @param int $courseDateId
     * @return JsonResponse
     */
    public function getClassStatus(int $courseDateId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Find the CourseDate
            $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
                ->find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date not found'
                ], 404);
            }

            // Ensure this CourseDate belongs to the student.
            // CourseDates do not reliably carry course_auth_id; validate by CourseUnit->course_id.
            $courseId = $courseDate->CourseUnit?->course_id;
            if (!$courseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course date is missing course unit relationship'
                ], 500);
            }

            $courseAuth = CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->orderByDesc('id')
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to course date'
                ], 403);
            }

            // Check if student has a StudentUnit for this course date
            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDateId)
                ->first();

            // Determine class status
            $status = 'waiting'; // Default: waiting for instructor to start class
            $instUnitId = null;
            $needsOnboarding = false;
            $onboardingStatus = null;

            $instUnit = $courseDate->InstUnit;
            if ($instUnit) {
                $instUnitId = $instUnit->id;

                // Class has started - check if student needs onboarding
                if ($studentUnit) {
                    // Check onboarding status
                    $onboardingComplete = $studentUnit->onboarding_completed ?? false;

                    if (!$onboardingComplete) {
                        $status = 'onboarding';
                        $needsOnboarding = true;

                        // Get onboarding progress
                        $onboardingStatus = [
                            // Agreement is once per course (course_auth.agreed_at), rules are daily.
                            'terms_accepted' => (bool) ($courseAuth->agreed_at !== null) || (bool) ($studentUnit->terms_accepted ?? false),
                            'rules_accepted' => $studentUnit->rules_accepted ?? false,
                            'identity_verified' => $studentUnit->verified ?? false,
                        ];
                    } else {
                        $status = 'active';
                    }
                } else {
                    // Student not enrolled yet - needs onboarding
                    $status = 'onboarding';
                    $needsOnboarding = true;
                }
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'inst_unit_id' => $instUnitId,
                'needs_onboarding' => $needsOnboarding,
                'onboarding_status' => $onboardingStatus,
                'course_date' => [
                    'id' => $courseDate->id,
                    'course_name' => $courseDate->GetCourse()?->name ?? 'N/A',
                    'class_date' => $courseDate->class_date,
                    'class_time' => $courseDate->class_time,
                    'duration_minutes' => $courseDate->duration_minutes,
                ],
                'instructor' => $instUnit ? (function () use ($instUnit) {
                    $instructor = \App\Models\User::find($instUnit->created_by);
                    if (!$instructor) {
                        return null;
                    }
                    return [
                        'id' => $instructor->id,
                        'name' => $instructor->fname . ' ' . $instructor->lname,
                        'email' => $instructor->email,
                    ];
                })() : null,
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching class status', [
                'course_date_id' => $courseDateId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch class status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * POST /student/onboarding/accept-terms
     */
    public function acceptTerms(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $studentUnit->terms_accepted = true;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'Terms accepted',
        ]);
    }

    /**
     * GET /student/onboarding/check-agreement/{courseAuthId}
     */
    public function checkAgreementStatus(int $courseAuthId): JsonResponse
    {
        $user = Auth::user();
        $courseAuth = CourseAuth::where('id', $courseAuthId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'success' => true,
            'terms_accepted' => (bool) ($studentUnit?->terms_accepted ?? false),
        ]);
    }

    /**
     * POST /student/onboarding/accept-rules
     */
    public function acceptRules(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $studentUnit->rules_accepted = true;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'Rules accepted',
        ]);
    }

    /**
     * GET /student/onboarding/check-rules/{courseAuthId}/{courseDateId}
     */
    public function checkRulesStatus(int $courseAuthId, int $courseDateId): JsonResponse
    {
        $user = Auth::user();
        $courseAuth = CourseAuth::where('id', $courseAuthId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
            ->where('course_date_id', $courseDateId)
            ->first();

        return response()->json([
            'success' => true,
            'already_agreed' => (bool) ($studentUnit?->rules_accepted ?? false),
        ]);
    }

    /**
     * POST /student/onboarding/complete
     */
    public function completeOnboarding(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $courseAgreed = false;
        if (!empty($studentUnit->course_auth_id)) {
            $courseAuth = CourseAuth::where('id', (int) $studentUnit->course_auth_id)
                ->where('user_id', $user->id)
                ->first();
            $courseAgreed = (bool) ($courseAuth?->agreed_at !== null);
        }

        // Agreement is per-course; StudentUnit terms_accepted is a daily fallback.
        $termsAccepted = $courseAgreed || (bool) ($studentUnit->terms_accepted ?? false);
        $rulesAccepted = (bool) ($studentUnit->rules_accepted ?? false);
        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));

        // Identity is complete only when:
        // - ID card exists for this courseAuth (one-time)
        // - Headshot exists for this studentUnit/courseDate (daily)
        $idCardExists = false;
        if (!empty($studentUnit->course_auth_id)) {
            $recentUnitWithId = StudentUnit::where('course_auth_id', (int) $studentUnit->course_auth_id)
                ->orderByDesc('course_date_id')
                ->limit(25)
                ->get()
                ->first(function ($unit) {
                    $verified = $this->decodeVerifiedData($unit->getRawOriginal('verified'));
                    return !empty($verified['id_card_path']);
                });
            $idCardExists = (bool) $recentUnitWithId;
        }

        $headshotExists = !empty($verified['headshot_path']);
        $identityVerified = (bool) ($idCardExists && $headshotExists);

        if (!$termsAccepted || !$rulesAccepted || !$identityVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Onboarding is not complete',
                'onboarding_status' => [
                    'terms_accepted' => $termsAccepted,
                    'rules_accepted' => $rulesAccepted,
                    'identity_verified' => $identityVerified,
                ],
            ], 422);
        }

        $studentUnit->onboarding_completed = true;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed',
            'student_unit_id' => $studentUnit->id,
        ]);
    }

    /**
     * POST /classroom/id-verification/start
     * Upload ID card image.
     */
    public function startIdVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $path = $request->file('id_document')->store('id-verification/id-cards', 'public');

        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
        $verified['id_card_uploaded'] = true;
        $verified['id_card_path'] = $path;
        $verified['id_card_uploaded_at'] = now()->toISOString();
        $studentUnit->verified = $verified;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'ID card uploaded',
            'data' => [
                'id_card_uploaded' => true,
                'headshot_uploaded' => (bool) ($verified['headshot_uploaded'] ?? false),
                'identity_verified' => (bool) (!empty($verified['id_card_path']) && !empty($verified['headshot_path'] ?? null)),
            ],
        ]);
    }

    /**
     * POST /id-verification/upload-headshot
     */
    public function uploadHeadshot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'headshot' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $path = $request->file('headshot')->store('id-verification/headshots', 'public');

        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
        $verified['headshot_uploaded'] = true;
        $verified['headshot_path'] = $path;
        $verified['headshot_uploaded_at'] = now()->toISOString();
        $studentUnit->verified = $verified;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'Headshot uploaded',
            'data' => [
                'id_card_uploaded' => (bool) ($verified['id_card_uploaded'] ?? false),
                'headshot_uploaded' => true,
                'identity_verified' => (bool) (!empty($verified['id_card_path'] ?? null) && !empty($verified['headshot_path'] ?? null)),
            ],
        ]);
    }

    /**
     * POST /classroom/student/offline-onboarding
     * Backward-compatible endpoint for offline onboarding flows.
     */
    public function completeOfflineOnboarding(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_date_id' => 'required|integer|exists:course_dates,id',
            'id_document' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            'headshot' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $user = Auth::user();
        $courseDate = CourseDate::with(['instUnit'])->findOrFail((int) $validated['course_date_id']);
        $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);

        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));

        if ($request->hasFile('id_document')) {
            $idPath = $request->file('id_document')->store('id-verification/id-cards', 'public');
            $verified['id_card_uploaded'] = true;
            $verified['id_card_path'] = $idPath;
            $verified['id_card_uploaded_at'] = now()->toISOString();
        }

        $headshotPath = $request->file('headshot')->store('id-verification/headshots', 'public');
        $verified['headshot_uploaded'] = true;
        $verified['headshot_path'] = $headshotPath;
        $verified['headshot_uploaded_at'] = now()->toISOString();

        $studentUnit->verified = $verified;
        $studentUnit->save();

        return response()->json([
            'success' => true,
            'message' => 'Offline onboarding submitted',
            'data' => [
                'student_unit_id' => $studentUnit->id,
                'id_card_uploaded' => (bool) ($verified['id_card_uploaded'] ?? false),
                'headshot_uploaded' => true,
                'identity_verified' => (bool) ($studentUnit->verified ?? false),
            ],
        ]);
    }

    /**
     * POST /classroom/portal/student/agreement
     * Original agreement form handler.
     */
    public function postStudentAgreement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agreement' => 'required|boolean|in:1,true',
            'dob' => 'required|string',
            'fname' => 'required|string|max:255',
            'initial' => 'nullable|string|max:10',
            'lname' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'student_id' => 'required|integer',
            'course_auth_id' => 'required|integer|exists:course_auths,id',
            'course_date_id' => 'nullable|integer|exists:course_dates,id',
        ]);

        $user = Auth::user();
        if ((int) $validated['student_id'] !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $courseAuth = CourseAuth::where('id', (int) $validated['course_auth_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $studentInfo = is_array($user->student_info) ? $user->student_info : [];
            $studentInfo['dob'] = $validated['dob'];
            $studentInfo['phone'] = $validated['phone'];
            $studentInfo['legal_fname'] = $validated['fname'];
            $studentInfo['legal_initial'] = $validated['initial'] ?? '';
            $studentInfo['legal_lname'] = $validated['lname'];
            $studentInfo['legal_suffix'] = $validated['suffix'] ?? '';

            $user->fname = $validated['fname'];
            $user->lname = $validated['lname'];
            $user->student_info = $studentInfo;
            $user->save();

            if ($courseAuth->agreed_at === null) {
                $courseAuth->agreed_at = now();
                $courseAuth->save();
            }

            $courseDateId = (int) ($validated['course_date_id'] ?? 0);
            if ($courseDateId > 0) {
                $courseDate = CourseDate::with(['instUnit'])->findOrFail($courseDateId);
                $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);
                $studentUnit->terms_accepted = true;
                $studentUnit->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agreement saved',
                'data' => [
                    'course_auth_id' => $courseAuth->id,
                    'agreed_at' => $courseAuth->agreed_at?->toISOString(),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save agreement',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * GET /id-verification/status/{studentId}
     */
    public function getIdVerificationStatus(int $studentId, Request $request): JsonResponse
    {
        $user = Auth::user();
        if ((int) $user->id !== (int) $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $courseDateId = (int) $request->query('course_date_id', 0);
        if ($courseDateId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'course_date_id is required',
            ], 422);
        }

        $courseDate = CourseDate::with(['course'])->findOrFail($courseDateId);
        $courseId = (int) ($courseDate->course_id ?? ($courseDate->course?->id ?? 0));
        if ($courseId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to resolve course for this class date',
            ], 422);
        }

        $courseAuth = CourseAuth::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
            ->where('course_date_id', $courseDateId)
            ->first();

        $verified = $studentUnit ? $this->decodeVerifiedData($studentUnit->getRawOriginal('verified')) : [];

        return response()->json([
            'success' => true,
            'data' => [
                'id_card_uploaded' => (bool) ($verified['id_card_uploaded'] ?? false),
                'headshot_uploaded' => (bool) ($verified['headshot_uploaded'] ?? false),
                'identity_verified' => (bool) ($studentUnit?->verified ?? false),
            ],
        ]);
    }

    // Legacy endpoints referenced by routes; keep non-fatal responses.
    public function getIdVerificationSummary(int $verificationId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Not implemented in this build',
        ], 501);
    }

    public function checkHeadshotStatus(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Not implemented in this build',
        ], 501);
    }

    public function checkIdCardStatus(int $courseAuthId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Not implemented in this build',
        ], 501);
    }

    public function getCourseDatesWithHeadshots(int $courseAuthId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Not implemented in this build',
        ], 501);
    }

    /**
     * Zoom Screen Share Portal - Iframe isolated Zoom SDK
     * Route: GET /classroom/portal/zoom/screen_share/{courseAuthId}/{courseDateId}
     */
    public function zoomScreenShare(int $courseAuthId, int $courseDateId): View
    {
        $user = Auth::user();

        $courseAuth = CourseAuth::with(['course', 'course.ZoomCreds'])
            ->where('id', $courseAuthId)
            ->where('user_id', $user?->id)
            ->first();

        if (!$user || !$courseAuth) {
            return view('frontend.students.zoom_screen_share', [
                'error' => 'Authentication required',
                'zoom' => null,
            ]);
        }

        // course_dates does NOT have course_auth_id. Validate access by ensuring the CourseDate's
        // CourseUnit belongs to the same Course as the CourseAuth the student owns.
        $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
            ->where('id', $courseDateId)
            ->whereHas('CourseUnit', function ($q) use ($courseAuth) {
                $q->where('course_id', $courseAuth->course_id);
            })
            ->first();

        if (!$courseDate) {
            return view('frontend.students.zoom_screen_share', [
                'error' => 'Class session not found',
                'zoom' => null,
            ]);
        }

        // Get instructor unit to check role
        $instUnit = $courseDate->InstUnit;

        // Infer Zoom credentials based on instructor role and course title pattern
        $zoomCreds = $this->inferZoomCredentials($courseAuth->course, $instUnit);
        $zoomStatus = $zoomCreds?->zoom_status ?? 'disabled';
        $isZoomReady = ($zoomStatus === 'enabled');

        $meetingNumber = $zoomCreds?->pmi;
        $meetingPasscode = null;
        if ($zoomCreds?->zoom_passcode) {
            try {
                $meetingPasscode = Crypt::decrypt($zoomCreds->zoom_passcode);
            } catch (\Throwable $e) {
                $meetingPasscode = null;
            }
        }

        return view('frontend.students.zoom_screen_share', [
            'error' => $isZoomReady ? null : 'Waiting for instructor to enable Zoom',
            'zoom' => [
                'is_ready' => $isZoomReady,
                'status' => $zoomStatus,
                'sdk_key' => config('zoom.sdk_key'),
                'meeting_number' => $meetingNumber,
                'meeting_passcode' => $meetingPasscode,
                'user_name' => trim(($user->fname ?? '') . ' ' . ($user->lname ?? '')) ?: ($user->email ?? 'Student'),
                'signature_url' => route('classroom.zoom.generate-signature', [], false),
            ],
        ]);
    }

    /**
     * Generate a Zoom Meeting SDK signature for the iframe Zoom portal.
     * Route: POST /classroom/portal/zoom/generate-signature
     */
    public function generateZoomSignature(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }

            $validated = $request->validate([
                'meeting_number' => 'required|string',
                'role' => 'required|integer|in:0,1',
            ]);

            $sdkKey = (string) config('zoom.sdk_key');
            $sdkSecret = (string) config('zoom.sdk_secret');

            Log::info('Zoom signature generation attempt', [
                'user_id' => $user->id,
                'meeting_number' => $validated['meeting_number'],
                'sdk_key_present' => !empty($sdkKey),
                'sdk_secret_present' => !empty($sdkSecret),
                'sdk_key_value' => $sdkKey,
                'config_zoom_sdk_key' => config('zoom.sdk_key'),
                'env_zoom_sdk_key' => env('ZOOM_SDK_KEY'),
                'env_zoom_meeting_sdk' => env('ZOOM_MEETING_SDK'),
            ]);

            if ($sdkKey === '' || $sdkSecret === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Zoom SDK is not configured',
                ], 500);
            }

            $meetingNumber = (string) $validated['meeting_number'];
            $role = (int) $validated['role'];

            // Zoom signature is a JWT (HS256)
            $iat = time() - 30;
            $exp = $iat + (2 * 60 * 60); // 2 hours

            $header = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = [
                'sdkKey' => $sdkKey,
                'mn' => $meetingNumber,
                'role' => $role,
                'iat' => $iat,
                'exp' => $exp,
                'appKey' => $sdkKey,
                'tokenExp' => $exp,
            ];

            $base64UrlEncode = static function (string $data): string {
                return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
            };

            $segments = [];
            $segments[] = $base64UrlEncode(json_encode($header));
            $segments[] = $base64UrlEncode(json_encode($payload));
            $signingInput = implode('.', $segments);
            $signature = hash_hmac('sha256', $signingInput, $sdkSecret, true);
            $segments[] = $base64UrlEncode($signature);

            return response()->json([
                'success' => true,
                'signature' => implode('.', $segments),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Zoom signature generation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Zoom signature',
            ], 500);
        }
    }

    /**
     * Find active course date for student
     * Used to automatically route student to waiting room or classroom
     *
     * @return JsonResponse
     */
    public function findActiveClass(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Find today's active course dates for this student
            $today = now()->format('Y-m-d');

            // Get student's course IDs (CourseDate links through CourseUnit->course_id)
            $courseIds = $user->courseAuths()->pluck('course_id')->filter()->unique();

            if ($courseIds->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'has_active_class' => false,
                    'message' => 'No course enrollments found'
                ]);
            }

            // Find today's course dates (by course ID via CourseUnit)
            $activeCourseDate = CourseDate::with(['CourseUnit', 'InstUnit'])
                ->whereDate('starts_at', $today)
                ->whereHas('CourseUnit', function ($q) use ($courseIds) {
                    $q->whereIn('course_id', $courseIds);
                })
                ->orderBy('starts_at', 'asc')
                ->first();

            if (!$activeCourseDate) {
                return response()->json([
                    'success' => true,
                    'has_active_class' => false,
                    'message' => 'No classes scheduled for today'
                ]);
            }

            $courseId = $activeCourseDate->CourseUnit?->course_id;
            if (!$courseId) {
                return response()->json([
                    'success' => true,
                    'has_active_class' => false,
                    'message' => 'Active class found but missing course relationship'
                ]);
            }

            $courseAuthId = CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->orderByDesc('id')
                ->value('id');

            if (!$courseAuthId) {
                return response()->json([
                    'success' => true,
                    'has_active_class' => false,
                    'message' => 'Active class found but student is not enrolled'
                ]);
            }

            // Determine status
            $status = $activeCourseDate->InstUnit ? 'active' : 'waiting';

            return response()->json([
                'success' => true,
                'has_active_class' => true,
                'status' => $status,
                'course_auth_id' => (int) $courseAuthId,
                'course_date_id' => $activeCourseDate->id,
                'course_date' => [
                    'id' => $activeCourseDate->id,
                    'course_name' => $activeCourseDate->GetCourse()?->name ?? 'N/A',
                    'class_date' => optional($activeCourseDate->starts_at)->format('Y-m-d'),
                    'class_time' => optional($activeCourseDate->starts_at)->format('H:i:s'),
                    'duration_minutes' => ($activeCourseDate->starts_at && $activeCourseDate->ends_at)
                        ? (int) $activeCourseDate->starts_at->diffInMinutes($activeCourseDate->ends_at)
                        : null,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error finding active class', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to find active class',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
