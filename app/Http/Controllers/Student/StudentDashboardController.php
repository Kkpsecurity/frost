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
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\StudentUnit;
use App\Models\User;
use App\Models\Validation;
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
use App\Classes\Challenger;
use App\Models\StudentLesson;

// NOTE: Some legacy services were referenced here historically but do not exist in this repo.

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    private function repairValidationsIdSequenceIfNeeded(): void
    {
        // This is a defensive fix for Postgres sequences getting out-of-sync
        // (common after manual inserts/restores). It is a no-op for non-pgsql.
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        try {
            DB::statement("SELECT setval(pg_get_serial_sequence('validations','id'), (SELECT COALESCE(MAX(id),0) FROM validations)+1, false)");
        } catch (\Throwable $e) {
            // If this fails, let the original error surface on retry.
            \Log::warning('repairValidationsIdSequenceIfNeeded failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function saveNewValidationWithSequenceRepair(Validation $validation): void
    {
        try {
            $validation->save();
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;

            // Postgres duplicate key violation.
            if (
                DB::getDriverName() === 'pgsql'
                && $sqlState === '23505'
                && str_contains($e->getMessage(), 'validations_pkey')
            ) {
                $this->repairValidationsIdSequenceIfNeeded();
                $validation->save();
                return;
            }

            throw $e;
        }
    }

    private function buildStudentValidationsForCourseAuth(CourseAuth $courseAuth): array
    {
        $idCardUrl = null;
        $headshotUrl = null;
        $headshotByDay = [];

        // Prefer Validation model if present (admin review system).
        $idCardValidation = Validation::where('course_auth_id', $courseAuth->id)->first();
        if ($idCardValidation) {
            $idCardUrl = $idCardValidation->URL(false);
        }

        // Fallback to any uploaded ID card path in verified JSON.
        if (!$idCardUrl) {
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
                    // Check if the file actually exists before returning URL
                    $relativePath = ltrim((string) $verified['id_card_path'], '/');
                    if (\Storage::disk('public')->exists($relativePath)) {
                        $idCardUrl = url('storage/' . $relativePath);
                    }
                }
            }
        }

        // Headshot: prefer today's StudentUnit for this courseAuth (onboarding is day-specific).
        // Fallback to the most recent unit that actually has a headshot.
        $todayKey = strtolower(now()->format('l'));
        $headshotUnit = null;

        try {
            $today = now()->format('Y-m-d');
            $todayCourseDate = CourseDate::with(['CourseUnit'])
                ->whereDate('starts_at', $today)
                ->whereHas('CourseUnit', function ($q) use ($courseAuth) {
                    $q->where('course_id', (int) $courseAuth->course_id);
                })
                ->orderBy('starts_at', 'asc')
                ->first();

            if ($todayCourseDate) {
                $headshotUnit = StudentUnit::where('course_auth_id', (int) $courseAuth->id)
                    ->where('course_date_id', (int) $todayCourseDate->id)
                    ->first();

                // Use the class date for weekday key when available.
                try {
                    $todayKey = strtolower((string) optional($todayCourseDate->starts_at)->format('l'));
                } catch (\Throwable $e) {
                    // keep default
                }
            }
        } catch (\Throwable $e) {
            // Non-fatal; fallback below.
        }

        if (!$headshotUnit) {
            $recentUnits = StudentUnit::where('course_auth_id', (int) $courseAuth->id)
                ->orderByDesc('course_date_id')
                ->limit(60)
                ->get();

            $headshotUnit = $recentUnits->first(function ($unit) {
                // Prefer an actual Validation record.
                $hasValidation = Validation::where('student_unit_id', (int) $unit->id)->exists();
                if ($hasValidation) {
                    return true;
                }

                $verified = $this->decodeVerifiedData($unit->getRawOriginal('verified'));
                return !empty($verified['headshot_path']);
            });
        }

        $headshotValidation = null;
        if ($headshotUnit) {
            $headshotValidation = Validation::where('student_unit_id', (int) $headshotUnit->id)->first();
            if ($headshotValidation) {
                $headshotUrl = $headshotValidation->URL(false);
            }

            if (!$headshotUrl) {
                $verified = $this->decodeVerifiedData($headshotUnit->getRawOriginal('verified'));
                if (!empty($verified['headshot_path'])) {
                    // Check if the file actually exists before returning URL
                    $relativePath = ltrim((string) $verified['headshot_path'], '/');
                    if (\Storage::disk('public')->exists($relativePath)) {
                        $headshotUrl = url('storage/' . $relativePath);
                    }
                }
            }
        }

        // Keyed by weekday (frontend expects validations.headshot[today]).
        $headshotByDay[$todayKey] = $headshotUrl;

        // Calculate onboarding requirements
        $termsAccepted = (bool) ($courseAuth->agreed_at !== null);
        $rulesAccepted = false;
        $identityVerified = (bool) ($idCardUrl && $headshotUrl);
        $onboardingCompleted = false;

        // Check if student has a StudentUnit for today to verify rules and onboarding completion
        if ($headshotUnit) {
            $rulesAccepted = $this->hasAcceptedRules($courseAuth->user_id, $headshotUnit->id);
            $onboardingCompleted = $this->hasCompletedOnboarding($courseAuth->user_id, $headshotUnit->id);
        }

        return [
            // Backward-compatible fields used by onboarding UI.
            'idcard' => $idCardUrl,
            'headshot' => $headshotByDay,

            // Explicit review statuses (optional for now).
            // IMPORTANT: Only report approved/rejected if the file actually exists
            'idcard_status' => $idCardUrl
                ? ($idCardValidation && $idCardValidation->status > 0 ? 'approved' : ($idCardValidation && $idCardValidation->status < 0 ? 'rejected' : 'uploaded'))
                : 'missing',
            'headshot_status' => $headshotUrl
                ? ($headshotValidation && $headshotValidation->status > 0 ? 'approved' : ($headshotValidation && $headshotValidation->status < 0 ? 'rejected' : 'uploaded'))
                : 'missing',
            'message' => null,

            // Onboarding status management
            'terms_accepted' => $termsAccepted,
            'rules_accepted' => $rulesAccepted,
            'identity_verified' => $identityVerified,
            'onboarding_completed' => $onboardingCompleted,
        ];
    }

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

    /**
     * Get Zoom meeting data for classroom poll
     * Returns Zoom status, credentials, and screen share URL for students
     */
    private function getZoomDataForClassroom($courseDate, $instUnit, $courseAuthId): array
    {
        try {
            // If no active classroom session, Zoom is not available
            if (!$instUnit || !$courseDate) {
                return [
                    'status' => 'disabled',
                    'is_active' => false,
                    'is_ready' => false,
                    'screen_share_url' => null,
                    'message' => 'No active classroom session',
                ];
            }

            // Get course to determine which Zoom account
            $course = $courseDate->course ?? $courseDate->CourseUnit->Course ?? null;

            if (!$course) {
                return [
                    'status' => 'disabled',
                    'is_active' => false,
                    'is_ready' => false,
                    'screen_share_url' => null,
                    'message' => 'Course not found',
                ];
            }

            // Infer which Zoom credentials to use based on course and instructor
            $zoomCreds = $this->inferZoomCredentials($course, $instUnit);

            if (!$zoomCreds) {
                return [
                    'status' => 'disabled',
                    'is_active' => false,
                    'is_ready' => false,
                    'screen_share_url' => null,
                    'message' => 'No Zoom credentials configured',
                ];
            }

            // Check if Zoom is enabled for this account
            $isEnabled = ($zoomCreds->zoom_status ?? 'disabled') === 'enabled';

            // Generate screen share URL for students
            $screenShareUrl = null;
            if ($isEnabled) {
                $screenShareUrl = url("/classroom/portal/zoom/screen_share/{$courseAuthId}/{$courseDate->id}");
            }

            return [
                'status' => $zoomCreds->zoom_status ?? 'disabled',
                'is_active' => $isEnabled,
                'is_ready' => $isEnabled,
                'screen_share_url' => $screenShareUrl,
                'meeting_id' => $zoomCreds->pmi ?? null,
                'email' => $zoomCreds->zoom_email ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Zoom data for classroom', [
                'error' => $e->getMessage(),
                'course_date_id' => $courseDate->id ?? null,
                'inst_unit_id' => $instUnit->id ?? null,
            ]);

            return [
                'status' => 'error',
                'is_active' => false,
                'is_ready' => false,
                'screen_share_url' => null,
                'message' => 'Error loading Zoom status',
            ];
        }
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

    private function findOrCreateStudentUnitForCourseDate(CourseDate $courseDate, User $user, ?CourseAuth $courseAuthOverride = null): StudentUnit
    {
        // Prefer an explicit CourseAuth when available (prevents mismatches on refresh).
        if ($courseAuthOverride) {
            if ((int) $courseAuthOverride->user_id !== (int) $user->id) {
                abort(403, 'Course auth does not belong to this user');
            }

            $courseId = $courseDate->CourseUnit?->course_id;
            if (!$courseId) {
                abort(500, 'Course date is missing course unit relationship');
            }

            if ((int) $courseAuthOverride->course_id !== (int) $courseId) {
                abort(422, 'Course auth does not match course date');
            }

            $courseAuth = $courseAuthOverride;
        } else {
            // Ensure the CourseDate belongs to this student.
            // CourseDates do not reliably carry course_auth_id; validate by CourseUnit->course_id.
            $courseId = $courseDate->CourseUnit?->course_id;
            if (!$courseId) {
                abort(500, 'Course date is missing course unit relationship');
            }

            // Fallback: pick the most recent CourseAuth for this user+course.
            $courseAuth = CourseAuth::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->orderByDesc('id')
                ->firstOrFail();
        }

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
            // Note: terms_accepted, rules_accepted, onboarding_completed are tracked in student_activity table
        ]);
    }

    /**
     * Check if student has accepted rules for a given StudentUnit
     */
    private function hasAcceptedRules(int $userId, int $studentUnitId): bool
    {
        return \App\Models\StudentActivity::where('user_id', $userId)
            ->where('student_unit_id', $studentUnitId)
            ->where('activity_type', \App\Models\StudentActivity::TYPE_RULES_ACCEPTED)
            ->exists();
    }

    /**
     * Check if student has completed onboarding for a given StudentUnit
     */
    private function hasCompletedOnboarding(int $userId, int $studentUnitId): bool
    {
        return \App\Models\StudentActivity::where('user_id', $userId)
            ->where('student_unit_id', $studentUnitId)
            ->where('activity_type', 'onboarding_completed')
            ->exists();
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
                ->with(['Course'])
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
                    'course_name' => $courseAuth->Course?->title ?? $courseAuth->Course?->title_long ?? 'N/A',
                    'start_date' => $classroomCourseDate?->starts_at?->format('Y-m-d') ?? $courseAuth->start_date,
                    'agreed_at' => $courseAuth->agreed_at?->toISOString(), // Add agreement timestamp for onboarding check
                    'status' => $status,
                    'completion_status' => $courseAuth->is_passed ? 'Passed' : ($courseAuth->completed_at ? 'Completed' : 'In Progress'),
                ];
            })->toArray();

            // Student progress: validations per enrollment (courseAuth).
            $validationsByCourseAuth = [];
            foreach ($courseAuths as $courseAuth) {
                try {
                    $validationsByCourseAuth[(int) $courseAuth->id] = $this->buildStudentValidationsForCourseAuth($courseAuth);
                } catch (\Throwable $e) {
                    $validationsByCourseAuth[(int) $courseAuth->id] = [
                        'idcard' => null,
                        'headshot' => [strtolower(now()->format('l')) => null],
                        'idcard_status' => 'unknown',
                        'headshot_status' => 'unknown',
                        'message' => 'Unable to load validations',
                    ];
                }
            }

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
            $activeStudentUnit = null;
            $activeStudentLessons = [];
            try {
                $today = now()->format('Y-m-d');
                $courseIds = $courseAuths->pluck('course_id')->filter()->unique();

                if ($courseIds->isNotEmpty()) {
                    // Prefer today's CourseDate for this course_id, regardless of InstUnit (needed for Waiting Room).
                    $today = now()->format('Y-m-d');
                    $courseDate = CourseDate::with(['CourseUnit', 'InstUnit', 'InstUnit.instLessons.Lesson'])
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

                        // Use latest InstUnit (including completed) so students don't see "waiting" after End Day.
                        $latestInstUnit = null;
                        try {
                            $latestInstUnit = \App\Models\InstUnit::where('course_date_id', $courseDate->id)
                                ->orderByDesc('id')
                                ->first();
                        } catch (\Throwable $e) {
                            $latestInstUnit = null;
                        }

                        $classroomStatus = 'waiting';
                        if ($latestInstUnit) {
                            $classroomStatus = $latestInstUnit->completed_at ? 'ended' : 'active';
                        }

                        // CLASSROOM-LEVEL DATA (shared by all students)
                        $activeClassroom = [
                            'status' => $classroomStatus,
                            'course_id' => $courseId,
                            'course_date_id' => (int) $courseDate->id,
                            'inst_unit_id' => $latestInstUnit?->id,
                        ];

                        // STUDENT-SPECIFIC DATA (for this individual student)
                        $studentUnit = null;
                        $completedLessonIds = [];
                        if ($courseAuthId > 0) {
                            $studentUnit = StudentUnit::where('course_auth_id', $courseAuthId)
                                ->where('course_date_id', $courseDate->id)
                                ->first();

                            if ($studentUnit) {
                                $activeStudentUnit = $studentUnit;

                                $completedLessonIds = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                                    ->whereNotNull('completed_at')
                                    ->pluck('lesson_id')
                                    ->toArray();

                                // Provide full student lesson records for UI completion status.
                                $activeStudentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                                    ->get()
                                    ->map(function ($sl) {
                                        return [
                                            'id' => (int) $sl->id,
                                            'lesson_id' => (int) $sl->lesson_id,
                                            'completed_at' => $sl->completed_at?->toISOString(),
                                            'is_completed' => $sl->completed_at !== null,
                                        ];
                                    })
                                    ->toArray();
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: keep poll responsive even if this optional summary fails.
            }

            // -----------------------------------------------------------------
            // CHALLENGE HISTORY (student-owned)
            // - Uses $studentUnit variable from above if it exists
            // -----------------------------------------------------------------
            $challenges = [];
            try {
                if (isset($studentUnit) && $studentUnit) {
                    // Get all StudentLessons for this StudentUnit
                    $studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                        ->pluck('id');

                    if ($studentLessons->isNotEmpty()) {
                        // Get challenges (completed, failed, or expired only - not pending)
                        $challenges = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)
                            ->where(function ($q) {
                                $q->whereNotNull('completed_at')
                                    ->orWhereNotNull('failed_at')
                                    ->orWhere('expires_at', '<', now());
                            })
                            ->orderBy('created_at', 'desc')
                            ->limit(20)
                            ->get()
                            ->map(function ($challenge) {
                                $isExpired = $challenge->expires_at && $challenge->expires_at < now()
                                    && !$challenge->completed_at && !$challenge->failed_at;

                                return [
                                    'id' => $challenge->id,
                                    'student_lesson_id' => $challenge->student_lesson_id,
                                    'lesson_name' => $challenge->StudentLesson?->Lesson?->title ?? 'Unknown',
                                    'type' => $challenge->type,
                                    'created_at' => $challenge->created_at?->toISOString(),
                                    'completed_at' => $challenge->completed_at?->toISOString(),
                                    'failed_at' => $challenge->failed_at?->toISOString(),
                                    'expired_at' => $isExpired ? $challenge->expires_at?->toISOString() : null,
                                    'is_final' => (bool) $challenge->is_final,
                                    'is_eol' => (bool) $challenge->is_eol,
                                ];
                            })
                            ->toArray();
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: keep challenges empty if this fails
                Log::warning('Failed to load challenge history in student poll: ' . $e->getMessage());
            }

            // Return student data with all courses
            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $user->id,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'role_id' => $user->role_id,
                        'is_active' => $user->is_active,
                        'student_info' => $user->student_info ? [
                            'fname' => $user->student_info['fname'] ?? $user->fname,
                            'middle_initial' => $user->student_info['middle_initial'] ?? null,
                            'lname' => $user->student_info['lname'] ?? $user->lname,
                            'email' => $user->student_info['email'] ?? $user->email,
                            'suffix' => $user->student_info['suffix'] ?? null,
                            'dob' => $user->student_info['dob'] ?? null,
                            'phone' => $user->student_info['phone'] ?? null,
                        ] : null,
                    ],
                    'courses' => $courses,
                    'progress' => [
                        'total_courses' => $courseAuths->count(),
                        'completed' => $courseAuths->where('completed_at', '!=', null)->count(),
                        'in_progress' => $coursesWithDates,
                    ],
                    'validations_by_course_auth' => $validationsByCourseAuth,
                    'active_classroom' => $activeClassroom,
                    // Student-owned classroom participation (if joined today)
                    'studentUnit' => $activeStudentUnit ? [
                        'id' => (int) $activeStudentUnit->id,
                        'course_auth_id' => (int) $activeStudentUnit->course_auth_id,
                        'inst_unit_id' => (int) $activeStudentUnit->inst_unit_id,
                        'course_date_id' => (int) $activeStudentUnit->course_date_id,
                        'joined_at' => $activeStudentUnit->joined_at?->toISOString(),
                    ] : null,
                    // Student-owned per-lesson completion for today
                    'studentLessons' => $activeStudentLessons,
                    'notifications' => [],
                    'assignments' => [],
                    'challenges' => $challenges,
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
            $courseDateId = $request->input('course_date_id');
            $currentDayOnly = $request->input('current_day_only', false);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                ], 401);
            }

            // If no course_date_id is provided, find today's class for this student's enrollments:
            // "Is the school open today?" (i.e., is there a CourseDate for any of this student's courses)
            // This keeps CourseDate ownership in the classroom domain.
            if (!$courseDateId) {
                $today = now()->format('Y-m-d');
                $courseIds = $user->CourseAuths()->pluck('course_id')->filter()->unique();

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
                            'courseDate' => null,
                            'courseUnit' => null,
                            'instUnit' => null,
                            'lessons' => [],
                        ],
                    ]);
                }

                // Use this course_date_id for the rest of the method
                $courseDateId = $courseDate->id;
            }

            // Get the CourseDate by ID
            if (!isset($courseDate)) {
                $courseDate = CourseDate::with(['CourseUnit', 'InstUnit', 'InstUnit.instLessons.Lesson'])
                    ->find($courseDateId);

                if (!$courseDate) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Classroom not found',
                    ], 404);
                }
            }

            $courseId = $courseDate->CourseUnit?->course_id;
            $courseUnit = $courseDate->GetCourseUnit();
            $course = $courseDate->GetCourse();

            // Get instructor unit (if instructor started class)
            $instUnit = \App\Models\InstUnit::where('course_date_id', $courseDate->id)
                ->orderByDesc('id')
                ->first();

            // Get lessons based on CourseUnit
            $lessons = [];
            if ($courseUnit) {
                $allLessons = $courseUnit->GetLessons();
                $lessons = $allLessons ? $allLessons->toArray() : [];
            }

            // Get today's InstLessons to determine lesson status
            $todaysInstLessons = collect();
            if ($instUnit) {
                // Eager-load current (un-ended) breaks so pause timers stay stable across polls.
                $todaysInstLessons = \App\Models\InstLesson::with([
                    'Breaks' => function ($query) {
                        $query->whereNull('ended_at')->orderByDesc('break_number');
                    },
                ])->where('inst_unit_id', $instUnit->id)
                    ->get()
                    ->keyBy('lesson_id');
            }

            // Determine active lesson (InstLesson exists but NOT completed, INCLUDES paused lessons, InstUnit NOT completed)
            $activeLessonId = null;
            $activeInstLesson = null;
            $isInstUnitCompleted = $instUnit && $instUnit->completed_at;

            if (!$isInstUnitCompleted) {
                foreach ($todaysInstLessons as $lessonId => $instLesson) {
                    if (!$instLesson->completed_at) {
                        $activeLessonId = $lessonId;
                        // Keep eager-loaded breaks attached; don't refresh() which would drop relations.
                        $activeInstLesson = $instLesson;
                        break; // Only one lesson can be active at a time
                    }
                }
            }

            // Enhance lessons with status information (classroom-level only)
            $lessons = collect($lessons)->map(function ($lesson) use ($todaysInstLessons, $activeLessonId) {
                $lessonId = $lesson['id'];

                // Check if InstLesson exists for this lesson today
                $instLesson = $todaysInstLessons->get($lessonId);

                // Determine status based ONLY on instructor actions (not student progress)
                $status = 'not_started'; // Default: instructor hasn't started this lesson yet
                $isActive = false;

                if (!$instLesson) {
                    // No InstLesson → not started by instructor
                    $status = 'not_started';
                } elseif ($lessonId === $activeLessonId && !$instLesson->completed_at && $instLesson->is_paused) {
                    // This is the ACTIVE lesson but it's PAUSED (on break)
                    $status = 'paused';
                    $isActive = true;
                } elseif ($lessonId === $activeLessonId && !$instLesson->completed_at && !$instLesson->is_paused) {
                    // This is the ACTIVE lesson (instructor is currently teaching)
                    $status = 'active';
                    $isActive = true;
                } elseif ($instLesson->completed_at) {
                    // Instructor completed this lesson
                    $status = 'completed';
                } else {
                    // InstLesson exists but not active or completed
                    $status = 'not_started';
                }

                $isPausedFlag = $instLesson ? $instLesson->is_paused : false;

                return [
                    'id' => $lessonId,
                    'title' => $lesson['name'] ?? $lesson['title'] ?? 'Lesson ' . $lessonId,
                    'description' => $lesson['description'] ?? '',
                    'duration_minutes' => $lesson['credit_minutes'] ?? $lesson['duration_minutes'] ?? $lesson['progress_minutes'] ?? 0,
                    'order' => $lesson['order'] ?? $lesson['order_by'] ?? 0,
                    'status' => $status,
                    'is_active' => $isActive,
                    'is_paused' => $isPausedFlag,
                ];
            })->sortBy('order')->values()->toArray();

            // Build final response with ONLY classroom data (no student-specific data)
            // -----------------------------------------------------------------
            // STUDENT ACTIVITY: LESSON PAUSED (student-owned)
            // - Safe to run here even though this is a classroom endpoint because we can
            //   derive the current student's StudentUnit from course_auth_id + course_date_id.
            // - Idempotent: logs once per pause session (break started_at).
            // -----------------------------------------------------------------
            try {
                if ($user && $courseDate && $activeInstLesson && $activeInstLesson->is_paused) {
                    $currentBreak = $activeInstLesson->CurrentBreak();
                    $pausedAt = $currentBreak?->started_at;
                    if ($pausedAt) {
                        $courseAuthId = (int) ($user->CourseAuths()->where('course_id', $courseId)->value('id') ?? 0);
                        if ($courseAuthId > 0) {
                            $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuthId)
                                ->where('course_date_id', $courseDate->id)
                                ->first();

                            if ($studentUnit) {
                                $breakId = (int) ($currentBreak?->id ?? 0);
                                $activityType = \App\Models\StudentActivity::suffixType(
                                    \App\Models\StudentActivity::TYPE_LESSON_PAUSED,
                                    $breakId
                                );

                                $alreadyLogged = \App\Models\StudentActivity::query()
                                    ->where('user_id', $user->id)
                                    ->where('student_unit_id', $studentUnit->id)
                                    ->where('activity_type', $activityType)
                                    ->whereBetween('created_at', [
                                        $pausedAt->copy()->subSecond(),
                                        $pausedAt->copy()->addSecond(),
                                    ])
                                    ->exists();

                                if (!$alreadyLogged) {
                                    $activity = new \App\Models\StudentActivity([
                                        'user_id' => $user->id,
                                        'course_auth_id' => $studentUnit->course_auth_id,
                                        'course_date_id' => $studentUnit->course_date_id,
                                        'student_unit_id' => $studentUnit->id,
                                        'inst_unit_id' => $studentUnit->inst_unit_id,
                                        'category' => \App\Models\StudentActivity::CATEGORY_INTERACTION,
                                        'activity_type' => $activityType,
                                        'description' => 'Lesson paused',
                                        'data' => [
                                            'base_activity_type' => \App\Models\StudentActivity::TYPE_LESSON_PAUSED,
                                            'lesson_id' => (int) $activeInstLesson->lesson_id,
                                            'inst_lesson_id' => (int) $activeInstLesson->id,
                                            'paused_at' => $pausedAt->toIso8601String(),
                                            'break_id' => $breakId,
                                            'break_number' => (int) ($activeInstLesson->CurrentBreak()?->break_number ?? 0),
                                        ],
                                    ]);
                                    $activity->created_at = $pausedAt;
                                    $activity->updated_at = $pausedAt;
                                    $activity->save();
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }

            // -----------------------------------------------------------------
            // STUDENT ACTIVITY: LESSON UNPAUSED (student-owned)
            // - When instructor resumes, the most recent break gets an ended_at.
            // - Log once per break session using ended_at as the event timestamp.
            // -----------------------------------------------------------------
            try {
                if ($user && $courseDate && $activeInstLesson && !$activeInstLesson->is_paused) {
                    $lastEndedBreak = $activeInstLesson->Breaks()
                        ->whereNotNull('ended_at')
                        ->orderByDesc('break_number')
                        ->first();

                    $unpausedAt = $lastEndedBreak?->ended_at;
                    if ($unpausedAt) {
                        $courseAuthId = (int) ($user->CourseAuths()->where('course_id', $courseId)->value('id') ?? 0);
                        if ($courseAuthId > 0) {
                            $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuthId)
                                ->where('course_date_id', $courseDate->id)
                                ->first();

                            if ($studentUnit) {
                                $breakId = (int) ($lastEndedBreak?->id ?? 0);
                                $activityType = \App\Models\StudentActivity::suffixType(
                                    \App\Models\StudentActivity::TYPE_LESSON_UNPAUSED,
                                    $breakId
                                );

                                $alreadyLogged = \App\Models\StudentActivity::query()
                                    ->where('user_id', $user->id)
                                    ->where('student_unit_id', $studentUnit->id)
                                    ->where('activity_type', $activityType)
                                    ->whereBetween('created_at', [
                                        $unpausedAt->copy()->subSecond(),
                                        $unpausedAt->copy()->addSecond(),
                                    ])
                                    ->exists();

                                if (!$alreadyLogged) {
                                    $activity = new \App\Models\StudentActivity([
                                        'user_id' => $user->id,
                                        'course_auth_id' => $studentUnit->course_auth_id,
                                        'course_date_id' => $studentUnit->course_date_id,
                                        'student_unit_id' => $studentUnit->id,
                                        'inst_unit_id' => $studentUnit->inst_unit_id,
                                        'category' => \App\Models\StudentActivity::CATEGORY_INTERACTION,
                                        'activity_type' => $activityType,
                                        'description' => 'Lesson resumed',
                                        'data' => [
                                            'base_activity_type' => \App\Models\StudentActivity::TYPE_LESSON_UNPAUSED,
                                            'lesson_id' => (int) $activeInstLesson->lesson_id,
                                            'inst_lesson_id' => (int) $activeInstLesson->id,
                                            'paused_at' => $lastEndedBreak?->started_at?->toIso8601String(),
                                            'unpaused_at' => $unpausedAt->toIso8601String(),
                                            'break_id' => $breakId,
                                            'break_number' => (int) ($lastEndedBreak?->break_number ?? 0),
                                        ],
                                    ]);
                                    $activity->created_at = $unpausedAt;
                                    $activity->updated_at = $unpausedAt;
                                    $activity->save();
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'courseDate' => $courseDate ? [
                        'id' => $courseDate->id,
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
                        'course_id' => (int) ($courseId ?? 0),
                    ] : null,
                    'instUnit' => $instUnit ? [
                        'id' => $instUnit->id,
                        'status' => $instUnit->completed_at ? 'ended' : 'active',
                        'started_at' => $instUnit->start_time,
                        'completed_at' => $instUnit->completed_at,
                    ] : null,
                    'instructor' => $instUnit ? $instUnit->GetCreatedBy() : null,
                    'lessons' => $lessons,
                    'modality' => 'instructor_led', // Classroom context is always instructor-led
                    'activeLesson' => $activeInstLesson ? [
                        'id' => $activeInstLesson->id,
                        'lesson_id' => $activeInstLesson->lesson_id,
                        'inst_unit_id' => $activeInstLesson->inst_unit_id,
                        'started_at' => $activeInstLesson->started_at ?? $activeInstLesson->created_at,
                        'completed_at' => $activeInstLesson->completed_at,
                        'is_paused' => $activeInstLesson->is_paused,
                        // IMPORTANT: stable pause timestamp for student countdown.
                        // Do NOT use now() here or the student timer resets on refresh/poll.
                        'paused_at' => $activeInstLesson->is_paused
                            ? ($activeInstLesson->CurrentBreak()?->started_at?->toIso8601String())
                            : null,
                    ] : null,
                    'zoom' => $this->getZoomDataForClassroom(
                        $courseDate,
                        $instUnit,
                        $user->CourseAuths()->where('course_id', $courseId)->value('id') ?? 0
                    ),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Class data error', [
                'error' => $e->getMessage(),
                'course_date_id' => $request->input('course_date_id'),
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
                'instUnit.instLessons.Lesson', // Load Lesson relationship for inst_lessons
                'studentUnits.StudentLessons', // Load student lessons with completion status
            ])->find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'courseDate' => null,
                    'courseUnit' => null,
                    'course' => null,
                    'lessons' => [],
                    'instUnit' => null,
                    'studentUnit' => null,
                    'studentLessons' => [],
                    'config' => [],
                ]);
            }

            // Find the current student's StudentUnit for today
            $studentUnit = $courseDate->studentUnits->first(function ($su) use ($user) {
                return $su->CourseAuth && $su->CourseAuth->user_id === $user->id;
            });

            // Get student lessons with completion status
            $studentLessons = $studentUnit ? $studentUnit->StudentLessons->map(function ($sl) {
                return [
                    'id' => $sl->id,
                    'lesson_id' => $sl->lesson_id,
                    'completed_at' => $sl->completed_at?->toISOString(),
                    'is_completed' => !is_null($sl->completed_at),
                ];
            })->toArray() : [];

            // Enhance lessons with status information (copied from main dashboard logic)
            $lessons = [];
            $todaysInstLessons = collect();
            $todaysStudentLessons = collect();
            $activeLessonId = null;
            $isInstUnitCompleted = $courseDate->instUnit && $courseDate->instUnit->completed_at;
            $isOnline = $courseDate->instUnit !== null;
            if ($courseDate->course && $courseDate->course->courseUnit) {
                $allLessons = $courseDate->course->courseUnit->courseUnitLessons;
                $lessons = $allLessons ? $allLessons->toArray() : [];
            }
            if ($courseDate->instUnit) {
                // Eager-load the current (un-ended) break so paused_at stays stable across polls.
                $todaysInstLessons = \App\Models\InstLesson::with([
                    'Breaks' => function ($query) {
                        $query->whereNull('ended_at')->orderByDesc('break_number');
                    },
                ])->where('inst_unit_id', $courseDate->instUnit->id)
                    ->get()
                    ->keyBy('lesson_id');
            }
            if ($studentUnit && $courseDate->instUnit && $todaysInstLessons->isNotEmpty()) {
                $todaysStudentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                    ->whereIn('inst_lesson_id', $todaysInstLessons->pluck('id'))
                    ->get()
                    ->keyBy('lesson_id');
            }
            if (!$isInstUnitCompleted) {
                foreach ($todaysInstLessons as $lessonId => $instLesson) {
                    if (!$instLesson->completed_at) {
                        $activeLessonId = $lessonId;
                        break;
                    }
                }
            }

            // -----------------------------------------------------------------
            // STUDENT ACTIVITY: LESSON PAUSED (student-owned)
            // - When the instructor pauses the active lesson, students see the pause overlay.
            // - Log a single activity row per pause session, keyed by the stable break started_at.
            // - This runs inside the poll so it records even if the student refreshes.
            // -----------------------------------------------------------------
            try {
                if ($user && $studentUnit && $activeLessonId) {
                    $activeInstLesson = $todaysInstLessons->get($activeLessonId);
                    $isPaused = (bool) ($activeInstLesson?->is_paused);

                    if ($isPaused && $activeInstLesson) {
                        $currentBreak = $activeInstLesson->Breaks?->first();
                        $pausedAt = $currentBreak?->started_at;

                        if ($pausedAt) {
                            $breakId = (int) ($currentBreak?->id ?? 0);
                            $activityType = \App\Models\StudentActivity::suffixType(
                                \App\Models\StudentActivity::TYPE_LESSON_PAUSED,
                                $breakId
                            );

                            $alreadyLogged = \App\Models\StudentActivity::query()
                                ->where('user_id', $user->id)
                                ->where('student_unit_id', $studentUnit->id)
                                ->where('activity_type', $activityType)
                                ->whereBetween('created_at', [
                                    $pausedAt->copy()->subSecond(),
                                    $pausedAt->copy()->addSecond(),
                                ])
                                ->exists();

                            if (!$alreadyLogged) {
                                $activity = new \App\Models\StudentActivity([
                                    'user_id' => $user->id,
                                    'course_auth_id' => $studentUnit->course_auth_id,
                                    'course_date_id' => $studentUnit->course_date_id,
                                    'student_unit_id' => $studentUnit->id,
                                    'inst_unit_id' => $studentUnit->inst_unit_id,
                                    'category' => \App\Models\StudentActivity::CATEGORY_INTERACTION,
                                    'activity_type' => $activityType,
                                    'description' => 'Lesson paused',
                                    'data' => [
                                        'base_activity_type' => \App\Models\StudentActivity::TYPE_LESSON_PAUSED,
                                        'lesson_id' => (int) $activeLessonId,
                                        'inst_lesson_id' => (int) $activeInstLesson->id,
                                        'paused_at' => $pausedAt->toIso8601String(),
                                        'break_id' => $breakId,
                                        'break_number' => (int) ($currentBreak?->break_number ?? 0),
                                    ],
                                ]);

                                // Make this event timestamp match the pause start time for idempotence.
                                $activity->created_at = $pausedAt;
                                $activity->updated_at = $pausedAt;
                                $activity->save();
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal: never break polling if activity tracking fails.
            }

            // -----------------------------------------------------------------
            // STUDENT ACTIVITY: LESSON UNPAUSED (student-owned)
            // - When instructor resumes, the most recent break gets an ended_at.
            // - Log once per break session using ended_at as the event timestamp.
            // -----------------------------------------------------------------
            try {
                if ($user && $studentUnit && $activeLessonId) {
                    $activeInstLesson = $todaysInstLessons->get($activeLessonId);
                    $isPaused = (bool) ($activeInstLesson?->is_paused);

                    if (!$isPaused && $activeInstLesson) {
                        $lastEndedBreak = $activeInstLesson->Breaks()
                            ->whereNotNull('ended_at')
                            ->orderByDesc('break_number')
                            ->first();

                        $unpausedAt = $lastEndedBreak?->ended_at;
                        if ($unpausedAt) {
                            $breakId = (int) ($lastEndedBreak?->id ?? 0);
                            $activityType = \App\Models\StudentActivity::suffixType(
                                \App\Models\StudentActivity::TYPE_LESSON_UNPAUSED,
                                $breakId
                            );

                            $alreadyLogged = \App\Models\StudentActivity::query()
                                ->where('user_id', $user->id)
                                ->where('student_unit_id', $studentUnit->id)
                                ->where('activity_type', $activityType)
                                ->whereBetween('created_at', [
                                    $unpausedAt->copy()->subSecond(),
                                    $unpausedAt->copy()->addSecond(),
                                ])
                                ->exists();

                            if (!$alreadyLogged) {
                                $activity = new \App\Models\StudentActivity([
                                    'user_id' => $user->id,
                                    'course_auth_id' => $studentUnit->course_auth_id,
                                    'course_date_id' => $studentUnit->course_date_id,
                                    'student_unit_id' => $studentUnit->id,
                                    'inst_unit_id' => $studentUnit->inst_unit_id,
                                    'category' => \App\Models\StudentActivity::CATEGORY_INTERACTION,
                                    'activity_type' => $activityType,
                                    'description' => 'Lesson resumed',
                                    'data' => [
                                        'base_activity_type' => \App\Models\StudentActivity::TYPE_LESSON_UNPAUSED,
                                        'lesson_id' => (int) $activeLessonId,
                                        'inst_lesson_id' => (int) $activeInstLesson->id,
                                        'paused_at' => $lastEndedBreak?->started_at?->toIso8601String(),
                                        'unpaused_at' => $unpausedAt->toIso8601String(),
                                        'break_id' => $breakId,
                                        'break_number' => (int) ($lastEndedBreak?->break_number ?? 0),
                                    ],
                                ]);

                                $activity->created_at = $unpausedAt;
                                $activity->updated_at = $unpausedAt;
                                $activity->save();
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }

            $lessons = collect($lessons)->map(function ($lesson) use ($todaysInstLessons, $todaysStudentLessons, $isOnline, $isInstUnitCompleted, $activeLessonId) {
                $lessonId = $lesson['id'];
                $instLesson = $todaysInstLessons->get($lessonId);
                $studentLesson = $todaysStudentLessons->get($lessonId);
                $status = 'incomplete';
                $isCompleted = false;
                $isActive = false;
                if (!$instLesson) {
                    $status = 'incomplete';
                } elseif ($lessonId === $activeLessonId && !$instLesson->completed_at && $instLesson->is_paused) {
                    $status = $isOnline ? 'paused_live' : 'paused_fstb';
                    $isActive = true;
                } elseif ($lessonId === $activeLessonId && !$instLesson->completed_at && !$instLesson->is_paused) {
                    $status = $isOnline ? 'active_live' : 'active_fstb';
                    $isActive = true;
                } elseif ($studentLesson && $studentLesson->completed_at) {
                    $status = 'completed';
                    $isCompleted = true;
                } else {
                    $status = 'incomplete';
                }
                $isPausedFlag = $instLesson ? $instLesson->is_paused : false;

                // IMPORTANT: Don't use now() here.
                // If we reset paused_at every poll, the client countdown never ticks.
                $pausedAtIso = null;
                if ($isPausedFlag && $instLesson) {
                    $currentBreak = $instLesson->Breaks?->first();
                    $pausedAtIso = $currentBreak?->started_at?->toIso8601String();
                }

                return [
                    'id' => $lessonId,
                    'title' => $lesson['name'] ?? $lesson['title'] ?? 'Lesson ' . $lessonId,
                    'description' => $lesson['description'] ?? '',
                    'duration_minutes' => $lesson['credit_minutes'] ?? $lesson['duration_minutes'] ?? $lesson['progress_minutes'] ?? 0,
                    'order' => $lesson['order'] ?? $lesson['order_by'] ?? 0,
                    'status' => $status,
                    'is_completed' => $isCompleted,
                    'is_active' => $isActive,
                    'is_paused' => $isPausedFlag,
                    'paused_at' => $pausedAtIso,
                ];
            })->sortBy('order')->values()->toArray();

            // -----------------------------------------------------------------
            // STEP 1: CHALLENGE SYSTEM INTEGRATION
            // Check if student has an active challenge for current lesson
            // -----------------------------------------------------------------
            $challengeData = null;

            if ($studentUnit && $activeLessonId) {
                try {
                    // Find the active StudentLesson for current lesson
                    $activeStudentLesson = StudentLesson::where('student_unit_id', $studentUnit->id)
                        ->where('lesson_id', $activeLessonId)
                        ->first();

                    if ($activeStudentLesson) {
                        // Get completed lesson IDs for this student
                        $completedLessonIds = $todaysStudentLessons
                            ->filter(fn($sl) => $sl->completed_at !== null)
                            ->pluck('lesson_id')
                            ->toArray();

                        // Initialize Challenger system and check if challenge should be shown
                        Challenger::init($activeStudentLesson);
                        $challengerResponse = Challenger::Ready($activeStudentLesson, $completedLessonIds);

                        if ($challengerResponse && $challengerResponse->challenge_id) {
                            // Load the actual Challenge model to get full data
                            $challenge = \App\Models\Challenge::find($challengerResponse->challenge_id);

                            if ($challenge && !$challenge->completed_at && !$challenge->failed_at) {
                                $now = now();
                                $expiresAt = \Carbon\Carbon::parse($challenge->expires_at);
                                $timeRemaining = max(0, $now->diffInSeconds($expiresAt, false));

                                $challengeData = [
                                    'challenge_id' => $challenge->id,
                                    'student_lesson_id' => $challenge->student_lesson_id,
                                    'is_final' => (bool) $challengerResponse->is_final,
                                    'is_eol' => (bool) $challengerResponse->is_eol,
                                    'expires_at' => $challenge->expires_at->toISOString(),
                                    'time_remaining' => (int) $timeRemaining,
                                    'created_at' => $challenge->created_at->toISOString(),
                                ];

                                Log::info('Challenge active for student', [
                                    'student_id' => $user->id,
                                    'challenge_id' => $challenge->id,
                                    'is_final' => $challengerResponse->is_final,
                                    'time_remaining' => $timeRemaining,
                                ]);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Non-fatal: log error but don't break polling
                    Log::error('Challenge check failed in classroom poll', [
                        'error' => $e->getMessage(),
                        'student_unit_id' => $studentUnit?->id,
                        'active_lesson_id' => $activeLessonId,
                    ]);
                }
            }

            // Return classroom data with enhanced lessons and challenge data
            return response()->json([
                'success' => true,
                'courseDate' => $courseDate,
                'courseUnit' => $courseDate->course?->courseUnit,
                'course' => $courseDate->course,
                'lessons' => $lessons,
                'instUnit' => $courseDate->instUnit,
                'studentUnit' => $studentUnit,
                'studentLessons' => $studentLessons,
                'challenge' => $challengeData, // NEW: Active challenge (null if none)
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
                    'course_name' => $courseAuth->Course ? $courseAuth->Course->name : 'N/A',
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
                    $onboardingComplete = false; // tracked in student_activity

                    if (!$onboardingComplete) {
                        $status = 'onboarding';
                        $needsOnboarding = true;

                        // Get onboarding progress
                        $onboardingStatus = [
                            // Agreement is once per course (course_auth.agreed_at), rules are daily.
                            'terms_accepted' => (bool) ($courseAuth->agreed_at !== null),
                            'rules_accepted' => $this->hasAcceptedRules($user->id, $studentUnit->id),
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

        // Track terms acceptance in student_activity table
        \App\Models\StudentActivity::create([
            'user_id' => $user->id,
            'student_unit_id' => $studentUnit->id,
            'category' => \App\Models\StudentActivity::CATEGORY_AGREEMENT,
            'activity_type' => \App\Models\StudentActivity::TYPE_TERMS_ACCEPTED,
            'description' => 'Student accepted course terms and conditions',
            'data' => [
                'course_date_id' => $courseDate->id,
                'accepted_at' => now()->toIso8601String()
            ]
        ]);

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

        // Track rules acceptance in student_activity table
        \App\Models\StudentActivity::create([
            'user_id' => $user->id,
            'student_unit_id' => $studentUnit->id,
            'category' => \App\Models\StudentActivity::CATEGORY_AGREEMENT,
            'activity_type' => \App\Models\StudentActivity::TYPE_RULES_ACCEPTED,
            'description' => 'Student accepted classroom rules',
            'data' => [
                'course_date_id' => $courseDate->id,
                'accepted_at' => now()->toIso8601String()
            ]
        ]);

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

        // Check if rules have been accepted via student_activity
        $rulesAccepted = false;
        if ($studentUnit) {
            $rulesAccepted = \App\Models\StudentActivity::where('user_id', $user->id)
                ->where('student_unit_id', $studentUnit->id)
                ->where('activity_type', \App\Models\StudentActivity::TYPE_RULES_ACCEPTED)
                ->exists();
        }

        return response()->json([
            'success' => true,
            'already_agreed' => $rulesAccepted,
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
        $termsAccepted = $courseAgreed;
        $rulesAccepted = $this->hasAcceptedRules($user->id, $studentUnit->id);
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
                    'id_card_exists' => $idCardExists,
                    'headshot_exists' => $headshotExists,
                ],
            ], 422);
        }

        // Track onboarding completion in student_activity
        \App\Models\StudentActivity::create([
            'user_id' => $user->id,
            'student_unit_id' => $studentUnit->id,
            'category' => \App\Models\StudentActivity::CATEGORY_AGREEMENT,
            'activity_type' => 'onboarding_completed',
            'description' => 'Student completed onboarding process',
            'data' => [
                'course_date_id' => $courseDate->id,
                'completed_at' => now()->toIso8601String()
            ]
        ]);

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

        $file = $request->file('id_document');
        $extension = $file?->extension() ?: ($file?->getClientOriginalExtension() ?: 'jpg');

        $validation = null;
        if (!empty($studentUnit->course_auth_id)) {
            $validation = Validation::where('course_auth_id', (int) $studentUnit->course_auth_id)->first();
            if (!$validation) {
                $validation = new Validation();
                $validation->uuid = (string) Str::uuid();
                $validation->course_auth_id = (int) $studentUnit->course_auth_id;
                $validation->status = 0;
                $this->saveNewValidationWithSequenceRepair($validation);
            } else {
                // Reset to pending on re-upload.
                $validation->status = 0;
                $validation->id_type = null;
                $validation->reject_reason = null;
                $validation->save();
            }
        }

        $path = $validation
            ? $validation->RelPathForExtension($extension)
            : ('validations/idcards/' . $file->hashName());

        Storage::disk('public')->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
        $verified['id_card_uploaded'] = true;
        $verified['id_card_path'] = $path;
        $verified['id_card_uploaded_at'] = now()->toISOString();
        $studentUnit->verified = $verified;
        $studentUnit->save();

        // Track ID card upload in student_activity table
        \App\Models\StudentActivity::create([
            'user_id' => $user->id,
            'student_unit_id' => $studentUnit->id,
            'category' => \App\Models\StudentActivity::CATEGORY_AGREEMENT,
            'activity_type' => \App\Models\StudentActivity::TYPE_ID_CARD_UPLOADED,
            'description' => 'Student uploaded ID card for verification',
            'data' => [
                'course_date_id' => $courseDate->id,
                'file_path' => $path,
                'uploaded_at' => now()->toIso8601String()
            ]
        ]);

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

        $file = $request->file('headshot');
        $extension = $file?->extension() ?: ($file?->getClientOriginalExtension() ?: 'jpg');

        $validation = Validation::where('student_unit_id', (int) $studentUnit->id)->first();
        if (!$validation) {
            $validation = new Validation();
            $validation->uuid = (string) Str::uuid();
            $validation->student_unit_id = (int) $studentUnit->id;
            $validation->status = 0;
            $this->saveNewValidationWithSequenceRepair($validation);
        } else {
            // Reset to pending on re-upload.
            $validation->status = 0;
            $validation->id_type = null;
            $validation->reject_reason = null;
            $validation->save();
        }

        $path = $validation->RelPathForExtension($extension);
        Storage::disk('public')->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
        $verified['headshot_uploaded'] = true;
        $verified['headshot_path'] = $path;
        $verified['headshot_uploaded_at'] = now()->toISOString();
        $studentUnit->verified = $verified;
        $studentUnit->save();

        // Track headshot upload in student_activity table
        \App\Models\StudentActivity::create([
            'user_id' => $user->id,
            'student_unit_id' => $studentUnit->id,
            'category' => \App\Models\StudentActivity::CATEGORY_AGREEMENT,
            'activity_type' => \App\Models\StudentActivity::TYPE_HEADSHOT_UPLOADED,
            'description' => 'Student uploaded headshot photo for verification',
            'data' => [
                'course_date_id' => $courseDate->id,
                'file_path' => $path,
                'uploaded_at' => now()->toIso8601String()
            ]
        ]);

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
     * POST /classroom/upload-student-photo
     * Handle student photo uploads from the React webcam capture component
     */
    public function uploadStudentPhoto(Request $request): JsonResponse
    {
        \Log::error('uploadStudentPhoto: METHOD STARTED');
        try {
            \Log::error('uploadStudentPhoto: Request received', [
                'has_file' => $request->hasFile('file'),
                'file_info' => $request->file('file') ? [
                    'original_name' => $request->file('file')->getClientOriginalName(),
                    'size' => $request->file('file')->getSize(),
                    'mime_type' => $request->file('file')->getMimeType(),
                ] : null,
                'all_input' => $request->all(),
            ]);

            $validated = $request->validate([
                'course_auth_id' => 'nullable|integer|exists:course_auths,id',
                'course_date_id' => 'nullable|integer|exists:course_dates,id',
                'student_id' => 'required|integer',
                'photoType' => 'required|string|in:id_card,idcard,headshot',
                'file' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            ]);

            // Headshots are per-day (course_date_id / StudentUnit). Enforce course_date_id for headshot uploads.
            if (($validated['photoType'] ?? null) === 'headshot' && empty($validated['course_date_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'course_date_id is required for headshot uploads',
                ], 422);
            }

            \Log::error('uploadStudentPhoto: Validation passed', [
                'validated_data' => $validated,
            ]);

            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                \Log::error('uploadStudentPhoto: Invalid file', [
                    'file_exists' => $file ? true : false,
                    'file_valid' => $file ? $file->isValid() : false,
                    'file_errors' => $file ? $file->getError() : null,
                    'file_error_message' => $file ? $file->getErrorMessage() : null,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload',
                ], 400);
            }

            $user = Auth::user();

            // Verify that the student_id matches the authenticated user
            if ((int) $validated['student_id'] !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Student ID mismatch',
                ], 403);
            }

            // Find StudentUnit - prefer course_auth_id if available, otherwise use course_date_id
            $studentUnit = null;
            $courseAuth = null;
            $courseDate = null;

            // If the client didn't send course_auth_id, infer it from course_date_id + authenticated user.
            // This keeps uploads aligned with the same courseAuth that polling uses for validations.idcard.
            if (empty($validated['course_auth_id']) && !empty($validated['course_date_id'])) {
                $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])->find((int) $validated['course_date_id']);
                $courseId = $courseDate?->CourseUnit?->course_id;

                if ($courseId) {
                    $courseAuth = CourseAuth::where('user_id', $user->id)
                        ->where('course_id', $courseId)
                        ->orderByDesc('id')
                        ->first();

                    if ($courseAuth) {
                        $validated['course_auth_id'] = $courseAuth->id;

                        \Log::info('uploadStudentPhoto: Inferred course_auth_id from course_date_id', [
                            'course_date_id' => (int) $validated['course_date_id'],
                            'course_id' => (int) $courseId,
                            'course_auth_id' => (int) $courseAuth->id,
                            'user_id' => (int) $user->id,
                        ]);
                    }
                }
            }

            if (!empty($validated['course_auth_id'])) {
                // Try to find by course_auth_id
                $courseAuth = CourseAuth::where('id', (int) $validated['course_auth_id'])
                    ->where('user_id', $user->id)
                    ->first();

                \Log::info('uploadStudentPhoto: CourseAuth lookup', [
                    'course_auth_id' => $validated['course_auth_id'],
                    'user_id' => $user->id,
                    'course_auth_found' => $courseAuth ? true : false,
                ]);

                if ($courseAuth) {
                    // If course_date_id is provided, ALWAYS target that specific day/session.
                    if (!empty($validated['course_date_id'])) {
                        $courseDate = CourseDate::with(['CourseUnit', 'InstUnit'])->find((int) $validated['course_date_id']);

                        if ($courseDate) {
                            $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                                ->where('course_date_id', (int) $validated['course_date_id'])
                                ->first();

                            if (!$studentUnit) {
                                // Create/find the correct StudentUnit for this course date (ensures headshot persists correctly).
                                $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user, $courseAuth);
                            }
                        }
                    }

                    // Fallback: without course_date_id, update the most recent unit for this course_auth_id.
                    if (!$studentUnit) {
                        $studentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                            ->orderByDesc('course_date_id')
                            ->first();
                    }

                    \Log::info('uploadStudentPhoto: StudentUnit lookup by course_auth_id', [
                        'course_auth_id' => $courseAuth->id,
                        'student_unit_found' => $studentUnit ? true : false,
                        'student_unit_id' => $studentUnit ? $studentUnit->id : null,
                    ]);
                }
            } elseif (!empty($validated['course_date_id'])) {
                // Fallback to course_date_id approach like uploadHeadshot
                $courseDate = CourseDate::with(['instUnit'])->find((int) $validated['course_date_id']);
                if ($courseDate) {
                    $studentUnit = $this->findOrCreateStudentUnitForCourseDate($courseDate, $user);
                }

                \Log::info('uploadStudentPhoto: StudentUnit lookup by course_date', [
                    'course_date_id' => $validated['course_date_id'],
                    'course_date_found' => $courseDate ? true : false,
                    'student_unit_found' => $studentUnit ? true : false,
                ]);
            }

            // Determine storage path based on photo type
            $photoType = $validated['photoType'];
            $storageFolder = 'validations/photos';
            if ($photoType === 'headshot') {
                $storageFolder = 'validations/headshots';
            } elseif ($photoType === 'id_card' || $photoType === 'idcard') {
                $storageFolder = 'validations/idcards';
            }

            \Log::error('uploadStudentPhoto: Storage folder determination', [
                'photo_type' => $photoType,
                'storage_folder' => $storageFolder,
            ]);

            // Store the uploaded file
            \Log::error('uploadStudentPhoto: About to store file', [
                'storage_folder' => $storageFolder,
                'storage_folder_empty' => empty($storageFolder),
                'photo_type' => $photoType,
            ]);

            if (empty($storageFolder)) {
                \Log::error('uploadStudentPhoto: Storage folder is empty', [
                    'photo_type' => $photoType,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid photo type configuration',
                ], 500);
            }

            try {
                $file = $request->file('file');
                if (!$file) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No file uploaded',
                    ], 400);
                }

                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file: ' . $file->getErrorMessage(),
                    ], 400);
                }

                $tmpPath = $file->getPathname();
                if (!is_string($tmpPath) || $tmpPath === '' || !file_exists($tmpPath)) {
                    \Log::error('uploadStudentPhoto: Uploaded file temp path missing', [
                        'tmp_path' => $tmpPath,
                        'original_name' => $file->getClientOriginalName(),
                        'photo_type' => $validated['photoType'],
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Uploaded file is missing its temporary path. Please try again.',
                    ], 400);
                }

                // If we have a real StudentUnit, store deterministically via Validation model.
                $relativePath = null;
                if ($studentUnit) {
                    $extension = $file->extension() ?: ($file->getClientOriginalExtension() ?: 'png');

                    if ($photoType === 'headshot') {
                        $validation = Validation::where('student_unit_id', (int) $studentUnit->id)->first();
                        if (!$validation) {
                            $validation = new Validation();
                            $validation->uuid = (string) Str::uuid();
                            $validation->student_unit_id = (int) $studentUnit->id;
                            $validation->status = 0;
                            $this->saveNewValidationWithSequenceRepair($validation);
                        } else {
                            $validation->status = 0;
                            $validation->id_type = null;
                            $validation->reject_reason = null;
                            $validation->save();
                        }

                        $relativePath = $validation->RelPathForExtension($extension);
                    } elseif (in_array($photoType, ['id_card', 'idcard'])) {
                        $courseAuthId = (int) ($studentUnit->course_auth_id ?? 0);
                        if ($courseAuthId > 0) {
                            $validation = Validation::where('course_auth_id', $courseAuthId)->first();
                            if (!$validation) {
                                $validation = new Validation();
                                $validation->uuid = (string) Str::uuid();
                                $validation->course_auth_id = $courseAuthId;
                                $validation->status = 0;
                                $this->saveNewValidationWithSequenceRepair($validation);
                            } else {
                                $validation->status = 0;
                                $validation->id_type = null;
                                $validation->reject_reason = null;
                                $validation->save();
                            }

                            $relativePath = $validation->RelPathForExtension($extension);
                        }
                    }
                }

                if (!$relativePath) {
                    $filename = $file->hashName();
                    $relativePath = trim($storageFolder, '/\\') . '/' . $filename;
                }

                $stream = fopen($tmpPath, 'r');
                if ($stream === false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to read uploaded file. Please try again.',
                    ], 400);
                }

                try {
                    \Storage::disk('public')->put($relativePath, $stream);
                } finally {
                    fclose($stream);
                }

                $path = $relativePath;
                \Log::info('uploadStudentPhoto: File stored successfully', [
                    'storage_folder' => $storageFolder,
                    'file_path' => $path,
                    'photo_type' => $validated['photoType'],
                ]);
            } catch (\Throwable $e) {
                \Log::error('uploadStudentPhoto: File storage failed', [
                    'error' => $e->getMessage(),
                    'exception' => $e,
                    'storage_folder' => $storageFolder,
                    'photo_type' => $validated['photoType'],
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File storage failed: ' . $e->getMessage(),
                ], 500);
            }

            // If we don't have a student unit, just store the file and return with warning
            if (!$studentUnit) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($validated['photoType']) . ' uploaded successfully (no active session found)',
                    'data' => [
                        'file_path' => $path,
                        'photo_type' => $validated['photoType'],
                        'course_auth_id' => $validated['course_auth_id'] ?? null,
                        'course_date_id' => $validated['course_date_id'] ?? null,
                        'warning' => 'No active student unit found for this course',
                    ],
                ]);
            }

            // Update the StudentUnit's verified data
            try {
                $currentVerified = $studentUnit->getRawOriginal('verified');
                $verified = $this->decodeVerifiedData($currentVerified);

                // Lightweight tracker: keep an append-only audit trail in the verified JSON.
                // This avoids relying on file existence checks and creates an explicit DB record.
                $events = [];
                if (isset($verified['events']) && is_array($verified['events'])) {
                    $events = $verified['events'];
                }

                $events[] = [
                    'event' => 'photo_uploaded',
                    'photo_type' => $validated['photoType'],
                    'path' => $path,
                    'at' => now()->toISOString(),
                    'course_auth_id' => $validated['course_auth_id'] ?? null,
                    'course_date_id' => $validated['course_date_id'] ?? null,
                    'student_unit_id' => $studentUnit->id,
                    'source' => 'classroom.upload-student-photo',
                ];

                // Cap events to last 50 to avoid unbounded growth.
                if (count($events) > 50) {
                    $events = array_slice($events, -50);
                }

                $verified['events'] = $events;

                if ($validated['photoType'] === 'headshot') {
                    $verified['headshot_uploaded'] = true;
                    $verified['headshot_path'] = $path;
                    $verified['headshot_uploaded_at'] = now()->toISOString();
                } elseif (in_array($validated['photoType'], ['id_card', 'idcard'])) {
                    $verified['id_card_uploaded'] = true;
                    $verified['id_card_path'] = $path;
                    $verified['id_card_uploaded_at'] = now()->toISOString();
                }

                $studentUnit->verified = $verified;
                $studentUnit->save();

                \Log::info('uploadStudentPhoto: StudentUnit verified data updated', [
                    'student_unit_id' => $studentUnit->id,
                    'photo_type' => $validated['photoType'],
                    'verified_data' => $verified,
                ]);
            } catch (\Exception $e) {
                // If updating verified data fails, still save the file but log the error
                \Log::error('uploadStudentPhoto: Failed to update StudentUnit verified data', [
                    'student_unit_id' => $studentUnit->id,
                    'photo_type' => $validated['photoType'],
                    'error' => $e->getMessage(),
                    'raw_verified' => $studentUnit->getRawOriginal('verified') ?? 'null',
                ]);

                // Return success but with warning about verification update failure
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($validated['photoType']) . ' uploaded successfully (verification update failed)',
                    'data' => [
                        'file_path' => $path,
                        'photo_type' => $validated['photoType'],
                        'student_unit_id' => $studentUnit->id,
                        'course_auth_id' => $validated['course_auth_id'] ?? null,
                        'course_date_id' => $validated['course_date_id'] ?? null,
                        'warning' => 'File uploaded but verification status could not be updated',
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['photoType']) . ' uploaded successfully',
                'data' => [
                    'file_path' => $path,
                    'photo_type' => $validated['photoType'],
                    'student_unit_id' => $studentUnit->id,
                    'course_auth_id' => $validated['course_auth_id'] ?? null,
                    'id_card_uploaded' => (bool) ($verified['id_card_uploaded'] ?? false),
                    'headshot_uploaded' => (bool) ($verified['headshot_uploaded'] ?? false),
                    'identity_verified' => (bool) (!empty($verified['id_card_path'] ?? null) && !empty($verified['headshot_path'] ?? null)),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Student photo upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['file']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
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
            $file = $request->file('id_document');
            $extension = $file?->extension() ?: ($file?->getClientOriginalExtension() ?: 'jpg');

            $validation = null;
            if (!empty($studentUnit->course_auth_id)) {
                $validation = Validation::where('course_auth_id', (int) $studentUnit->course_auth_id)->first();
                if (!$validation) {
                    $validation = new Validation();
                    $validation->uuid = (string) Str::uuid();
                    $validation->course_auth_id = (int) $studentUnit->course_auth_id;
                    $validation->status = 0;
                    $this->saveNewValidationWithSequenceRepair($validation);
                } else {
                    $validation->status = 0;
                    $validation->id_type = null;
                    $validation->reject_reason = null;
                    $validation->save();
                }
            }

            $idPath = $validation
                ? $validation->RelPathForExtension($extension)
                : ('validations/idcards/' . $file->hashName());

            Storage::disk('public')->putFileAs(dirname($idPath), $file, basename($idPath));
            $verified['id_card_uploaded'] = true;
            $verified['id_card_path'] = $idPath;
            $verified['id_card_uploaded_at'] = now()->toISOString();
        }

        $headshotFile = $request->file('headshot');
        $headshotExt = $headshotFile?->extension() ?: ($headshotFile?->getClientOriginalExtension() ?: 'jpg');

        $headshotValidation = Validation::where('student_unit_id', (int) $studentUnit->id)->first();
        if (!$headshotValidation) {
            $headshotValidation = new Validation();
            $headshotValidation->uuid = (string) Str::uuid();
            $headshotValidation->student_unit_id = (int) $studentUnit->id;
            $headshotValidation->status = 0;
            $this->saveNewValidationWithSequenceRepair($headshotValidation);
        } else {
            $headshotValidation->status = 0;
            $headshotValidation->id_type = null;
            $headshotValidation->reject_reason = null;
            $headshotValidation->save();
        }

        $headshotPath = $headshotValidation->RelPathForExtension($headshotExt);
        Storage::disk('public')->putFileAs(dirname($headshotPath), $headshotFile, basename($headshotPath));
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

        $courseDate = CourseDate::with(['CourseUnit'])->findOrFail($courseDateId);
        $courseId = (int) ($courseDate->course_id ?? ($courseDate->CourseUnit?->course_id ?? 0));
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
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $courseAuthId = (int) $request->query('course_auth_id', 0);
        $courseDateId = (int) $request->query('course_date_id', 0);

        // If no explicit course_auth_id was provided, infer it from the course_date_id.
        if ($courseAuthId <= 0 && $courseDateId > 0) {
            $courseDate = CourseDate::with(['CourseUnit'])->find($courseDateId);
            $courseId = (int) ($courseDate?->CourseUnit?->course_id ?? 0);
            if ($courseId > 0) {
                $courseAuthId = (int) (CourseAuth::where('user_id', $user->id)
                    ->where('course_id', $courseId)
                    ->orderByDesc('id')
                    ->value('id') ?? 0);
            }
        }

        if ($courseAuthId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'course_auth_id is required',
            ], 422);
        }

        $courseAuth = CourseAuth::where('id', $courseAuthId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $studentUnitQuery = StudentUnit::where('course_auth_id', $courseAuth->id);
        if ($courseDateId > 0) {
            $studentUnitQuery->where('course_date_id', $courseDateId);
        }
        $studentUnit = $studentUnitQuery->orderByDesc('course_date_id')->first();

        $url = null;
        $status = 'missing';
        $rejectReason = null;

        if ($studentUnit) {
            $validation = Validation::where('student_unit_id', (int) $studentUnit->id)->first();
            if ($validation) {
                $url = $validation->URL(false);
                $rejectReason = $validation->reject_reason;
                $status = $validation->status > 0 ? 'approved' : ($validation->status < 0 ? 'rejected' : 'pending');
            }

            if (!$url) {
                $verified = $this->decodeVerifiedData($studentUnit->getRawOriginal('verified'));
                if (!empty($verified['headshot_path'])) {
                    // Check if the file actually exists before returning URL
                    $relativePath = ltrim((string) $verified['headshot_path'], '/');
                    if (\Storage::disk('public')->exists($relativePath)) {
                        $url = url('storage/' . $relativePath);
                        $status = 'uploaded';
                    } else {
                        // File missing - clear the path from database
                        $verified['headshot_path'] = null;
                        $studentUnit->verified = json_encode($verified);
                        $studentUnit->save();
                        $status = 'missing';
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course_auth_id' => (int) $courseAuth->id,
                'course_date_id' => $courseDateId > 0 ? $courseDateId : null,
                'headshot_url' => $url,
                'status' => $status,
                'reject_reason' => $rejectReason,
            ],
        ]);
    }

    public function checkIdCardStatus(int $courseAuthId): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $courseAuth = CourseAuth::where('id', (int) $courseAuthId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $url = null;
        $status = 'missing';
        $rejectReason = null;

        $validation = Validation::where('course_auth_id', (int) $courseAuth->id)->first();
        if ($validation) {
            $url = $validation->URL(false);
            $rejectReason = $validation->reject_reason;
            $status = $validation->status > 0 ? 'approved' : ($validation->status < 0 ? 'rejected' : 'pending');
        }

        if (!$url) {
            $recentUnitWithId = StudentUnit::where('course_auth_id', (int) $courseAuth->id)
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
                    $url = url('storage/' . ltrim((string) $verified['id_card_path'], '/'));
                    $status = 'uploaded';
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'course_auth_id' => (int) $courseAuth->id,
                'id_card_url' => $url,
                'status' => $status,
                'reject_reason' => $rejectReason,
            ],
        ]);
    }

    public function getCourseDatesWithHeadshots(int $courseAuthId): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $courseAuth = CourseAuth::where('id', (int) $courseAuthId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $units = StudentUnit::where('course_auth_id', (int) $courseAuth->id)
            ->orderByDesc('course_date_id')
            ->limit(60)
            ->get();

        $rows = $units->map(function ($unit) {
            $url = null;
            $status = 'missing';
            $rejectReason = null;

            $validation = Validation::where('student_unit_id', (int) $unit->id)->first();
            if ($validation) {
                $url = $validation->URL(false);
                $rejectReason = $validation->reject_reason;
                $status = $validation->status > 0 ? 'approved' : ($validation->status < 0 ? 'rejected' : 'pending');
            }

            if (!$url) {
                $verified = $this->decodeVerifiedData($unit->getRawOriginal('verified'));
                if (!empty($verified['headshot_path'])) {
                    $url = url('storage/' . ltrim((string) $verified['headshot_path'], '/'));
                    $status = 'uploaded';
                }
            }

            return [
                'student_unit_id' => (int) $unit->id,
                'course_date_id' => (int) ($unit->course_date_id ?? 0),
                'headshot_url' => $url,
                'status' => $status,
                'reject_reason' => $rejectReason,
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'course_auth_id' => (int) $courseAuth->id,
                'course_dates' => $rows,
            ],
        ]);
    }

    /**
     * Zoom Screen Share Portal - Iframe isolated Zoom SDK
     * Route: GET /classroom/portal/zoom/screen_share/{courseAuthId}/{courseDateId}
     */
    public function zoomScreenShare(int $courseAuthId, int $courseDateId): View
    {
        $user = Auth::user();

        $courseAuth = CourseAuth::with(['Course', 'Course.ZoomCreds'])
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
        $zoomCreds = $this->inferZoomCredentials($courseAuth->Course, $instUnit);
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
