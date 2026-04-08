<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;

class SupportController extends Controller
{
    private function toCarbon($value): ?Carbon
    {
        if ($value instanceof \Carbon\CarbonInterface) {
            return Carbon::instance($value);
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_int($value) || (is_string($value) && is_numeric($value))) {
            $timestamp = (int) $value;

            // Heuristic: handle millisecond timestamps
            if ($timestamp > 9_999_999_999) {
                $timestamp = (int) floor($timestamp / 1000);
            }

            return $timestamp > 0 ? Carbon::createFromTimestamp($timestamp) : null;
        }

        if (is_string($value) && trim($value) !== '') {
            return Carbon::parse($value);
        }

        return null;
    }

    /**
     * Search for users (students, admins, etc.) based on support staff permissions
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('query');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Search all users regardless of role — admins/instructors are valid test accounts
            $users = User::query()
                ->where(function ($q) use ($query) {
                    $q->where('fname', 'ilike', "%{$query}%")
                        ->orWhere('lname', 'ilike', "%{$query}%")
                        ->orWhere('email', 'ilike', "%{$query}%")
                        ->orWhereRaw("CONCAT(fname, ' ', lname) ILIKE ?", ["%{$query}%"])
                        ->orWhereRaw("CONCAT(lname, ' ', fname) ILIKE ?", ["%{$query}%"]);
                })
                ->limit(20)
                ->get();

            $results = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first() ?? 'user',
                    'avatar' => $user->getAvatar('small')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Support search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Poll endpoint for real-time support dashboard data
     */
    public function pollData(Request $request)
    {
        try {
            $studentId = $request->input('student_id');
            $courseId = $request->input('course_id', null);

            \Log::info('Support poll request', [
                'student_id' => $studentId,
                'course_id' => $courseId
            ]);

            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID is required'
                ], 400);
            }

            // Get student
            $student = User::find($studentId);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $courses = $this->getStudentCourses($studentId);

            \Log::info('Support poll courses result', [
                'student_id' => $studentId,
                'courses_count' => count($courses),
                'courses' => $courses
            ]);

            $adminUser = auth('admin')->user();
            $toolPermissions = [
                'ban-course-auth' => false,
                'day-ban'         => false,
                'grant-lesson'    => false,
                'reverse-dnc'     => false,
            ];
            if ($adminUser) {
                try {
                    $toolPermissions = [
                        'ban-course-auth' => $adminUser->hasPermissionTo('student-tools.ban-course-auth'),
                        'day-ban'         => $adminUser->hasPermissionTo('student-tools.day-ban'),
                        'grant-lesson'    => $adminUser->hasPermissionTo('student-tools.grant-lesson'),
                        'reverse-dnc'     => $adminUser->hasPermissionTo('student-tools.reverse-dnc'),
                    ];
                } catch (\Exception $e) {
                    // Permissions not yet seeded — show all buttons until configured
                    \Log::warning('student-tools permissions not found (run PermissionsSeeder): ' . $e->getMessage());
                    $toolPermissions = [
                        'ban-course-auth' => true,
                        'day-ban'         => true,
                        'grant-lesson'    => true,
                        'reverse-dnc'     => true,
                    ];
                }
            }

            $data = [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'avatar' => $student->getAvatar('medium'),
                    'status' => 'online', // TODO: Implement real status
                ],
                'courses' => $courses,
                'courseActivity' => $courseId ? $this->getCourseActivity($studentId, $courseId) : null,
                'weeklyAttendance' => $courseId ? $this->getWeeklyAttendance($studentId, $courseId) : [],
                'lessons' => $courseId ? $this->getCourseLessons($studentId, $courseId) : [],
                'classHistory' => $courseId ? $this->getClassHistory($studentId, $courseId) : [],
                'photos' => $courseId ? $this->getStudentPhotos($studentId, $courseId) : [],
                'examResults' => $courseId ? $this->getExamResults($studentId, $courseId) : [],
                'studentDetails' => $this->getStudentDetails($studentId, $courseId),
                'toolPermissions' => $toolPermissions,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Support poll error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's enrolled courses
     */
    private function getStudentCourses($studentId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return [];
        }

        // Get all course auths with course relationship
        $courseAuths = $user->courseAuths()
            ->with('course')
            ->orderBy('id', 'asc')
            ->get();

        return $courseAuths->map(function ($courseAuth) {
            return [
                'id' => $courseAuth->id,
                'course_id' => $courseAuth->course_id,
                'name' => $courseAuth->course ? $courseAuth->course->title : 'Unknown Course',
                'status' => $courseAuth->completed_at ? 'completed' : 'active',
                'start_date' => $courseAuth->start_date ? \Carbon\Carbon::parse($courseAuth->start_date)->format('Y-m-d') : null,
                'expire_date' => $courseAuth->expire_date ? \Carbon\Carbon::parse($courseAuth->expire_date)->format('Y-m-d') : null,
                'is_passed' => $courseAuth->is_passed,
                'created_at' => $courseAuth->created_at->format('Y-m-d H:i:s'),
                'disabled_at' => $courseAuth->disabled_at ? $courseAuth->disabled_at->format('Y-m-d H:i:s') : null,
                'disabled_reason' => $courseAuth->disabled_reason,
            ];
        })->toArray();
    }

    /**
     * Get course activity metrics
     */
    private function getCourseActivity($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return ['activities' => []];
        }

        // Find the CourseAuth for this student and course
        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->first();

        if (!$courseAuth) {
            return ['activities' => []];
        }

        $activities = [];

        // Get student units (class attendance)
        $studentUnits = $courseAuth->StudentUnits()
            ->with(['CourseDate', 'CourseUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        foreach ($studentUnits as $studentUnit) {
            $courseDate = $studentUnit->CourseDate;
            $courseUnit = $studentUnit->CourseUnit;

            if ($courseDate && $courseUnit) {
                $joinedAt = $this->toCarbon($studentUnit->created_at)?->timezone(config('app.timezone'));

                // Class joined activity
                $activities[] = [
                    'id' => 'unit_' . $studentUnit->id,
                    'date' => $joinedAt ? $joinedAt->format('Y-m-d') : null,
                    'type' => 'login',
                    'description' => 'Joined classroom session',
                    'details' => $courseUnit->title . ' - ' . \Carbon\Carbon::parse($courseDate->date)->format('M d, Y'),
                    'timestamp' => $joinedAt ? $joinedAt->toIso8601String() : null,
                ];

                // Class completed activity
                if ($studentUnit->completed_at) {
                    $completedAt = $this->toCarbon($studentUnit->completed_at)?->timezone(config('app.timezone'));
                    $activities[] = [
                        'id' => 'unit_completed_' . $studentUnit->id,
                        'date' => $completedAt?->format('Y-m-d'),
                        'type' => 'lesson_completed',
                        'description' => 'Completed classroom session',
                        'details' => $courseUnit->title,
                        'timestamp' => $completedAt?->toIso8601String(),
                    ];
                }
            }
        }

        // Get student lessons (lesson progress)
        $studentLessons = \App\Models\StudentLesson::whereIn(
            'student_unit_id',
            $studentUnits->pluck('id')
        )
            ->with(['Lesson'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        foreach ($studentLessons as $studentLesson) {
            $lesson = $studentLesson->Lesson;

            if ($lesson) {
                $lessonStartedAt = $this->toCarbon($studentLesson->created_at)?->timezone(config('app.timezone'));

                // Lesson started
                $activities[] = [
                    'id' => 'lesson_' . $studentLesson->id,
                    'date' => $lessonStartedAt ? $lessonStartedAt->format('Y-m-d') : null,
                    'type' => 'lesson_started',
                    'description' => 'Started lesson',
                    'details' => $lesson->title,
                    'timestamp' => $lessonStartedAt ? $lessonStartedAt->toIso8601String() : null,
                ];

                // Lesson completed
                if ($studentLesson->completed_at) {
                    $lessonCompletedAt = $this->toCarbon($studentLesson->completed_at)?->timezone(config('app.timezone'));
                    $activities[] = [
                        'id' => 'lesson_completed_' . $studentLesson->id,
                        'date' => $lessonCompletedAt?->format('Y-m-d'),
                        'type' => 'lesson_completed',
                        'description' => 'Completed lesson',
                        'details' => $lesson->title,
                        'timestamp' => $lessonCompletedAt?->toIso8601String(),
                    ];
                }
            }
        }

        // Get detailed student activity tracking (onboarding, agreements, interactions)
        // IMPORTANT: Waiting room tracking can happen before a StudentUnit exists.
        // Include activities for this enrollment via course_auth_id, and also include
        // student_unit-linked activities for any units that do exist.
        $studentUnitIds = $studentUnits->pluck('id');

        $studentActivities = \App\Models\StudentActivity::where('user_id', $studentId)
            ->where(function ($q) use ($courseAuth, $studentUnitIds) {
                $q->where('course_auth_id', $courseAuth->id);

                if ($studentUnitIds->isNotEmpty()) {
                    $q->orWhereIn('student_unit_id', $studentUnitIds);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        foreach ($studentActivities as $activity) {
            // Format the description based on activity type
            $description = $activity->description ?: $this->formatActivityDescription($activity->activity_type);

            // Include relevant activities (skip low-level tracking like tab visibility)
            $includedCategories = [
                \App\Models\StudentActivity::CATEGORY_ENTRY,
                \App\Models\StudentActivity::CATEGORY_NAVIGATION,
                \App\Models\StudentActivity::CATEGORY_AGREEMENT,
                \App\Models\StudentActivity::CATEGORY_INTERACTION,
            ];

            if (in_array($activity->category, $includedCategories)) {
                $activityAt = $this->toCarbon($activity->created_at)?->timezone(config('app.timezone'));
                $activities[] = [
                    'id' => 'activity_' . $activity->id,
                    'date' => $activityAt ? $activityAt->format('Y-m-d') : null,
                    'type' => $activity->activity_type,
                    'description' => $description,
                    'details' => $activity->data ? json_encode($activity->data) : null,
                    'timestamp' => $activityAt ? $activityAt->toIso8601String() : null,
                ];
            }
        }

        // Sort activities by timestamp (most recent first)
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return [
            'activities' => $activities,
            'summary' => [
                'total_activities' => count($activities),
                'classes_attended' => $studentUnits->count(),
                'lessons_completed' => $studentLessons->where('completed_at', '!=', null)->count(),
            ]
        ];
    }

    /**
     * Get weekly attendance for the next 5 days
     */
    private function getWeeklyAttendance($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            // Still return 5 days even if user not found
            return $this->generateWeekDays([]);
        }

        // Find the CourseAuth
        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->first();

        if (!$courseAuth) {
            // Still return 5 days even if no course auth
            return $this->generateWeekDays([]);
        }

        // Get all StudentUnits for this CourseAuth
        $studentUnits = $courseAuth->StudentUnits()
            ->with('CourseDate')
            ->get();

        // Create a map of dates that have StudentUnits
        $attendedDates = [];
        foreach ($studentUnits as $studentUnit) {
            if ($studentUnit->CourseDate && $studentUnit->CourseDate->starts_at) {
                $date = \Carbon\Carbon::parse($studentUnit->CourseDate->starts_at)->format('Y-m-d');
                $attendedDates[$date] = [
                    'course_date_id' => $studentUnit->course_date_id,
                    'student_unit_id' => $studentUnit->id,
                ];
            }
        }

        return $this->generateWeekDays($attendedDates);
    }

    /**
     * Generate Monday-Friday of current week with attendance status
     */
    private function generateWeekDays($attendedDates)
    {
        $weeklyAttendance = [];

        // Get Monday of current week
        $monday = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);

        // Generate Monday through Friday (5 days)
        for ($i = 0; $i < 5; $i++) {
            $date = $monday->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            $weeklyAttendance[] = [
                'date' => $dateStr,
                'dayName' => $date->format('l'), // Full day name (Monday, Tuesday, etc.)
                'isPresent' => isset($attendedDates[$dateStr]),
                'courseDateId' => $attendedDates[$dateStr]['course_date_id'] ?? null,
            ];
        }

        return $weeklyAttendance;
    }

    /**
     * Get course lessons progress
     */
    private function getCourseLessons($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return [];
        }

        // Find the CourseAuth
        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->with('course')
            ->first();

        if (!$courseAuth || !$courseAuth->course) {
            return [];
        }

        // Get all CourseUnits for this course
        $courseUnits = \App\Models\CourseUnit::where('course_id', $courseAuth->course_id)
            ->orderBy('ordering', 'asc')
            ->get();

        // Get all lesson IDs through the pivot table
        $lessonIds = \App\Models\CourseUnitLesson::whereIn('course_unit_id', $courseUnits->pluck('id'))
            ->orderBy('ordering', 'asc')
            ->pluck('lesson_id')
            ->unique();

        // Get all lessons
        $courseLessons = \App\Models\Lesson::whereIn('id', $lessonIds)
            ->get()
            ->keyBy('id');

        // Get all StudentUnits for this CourseAuth
        $studentUnits = $courseAuth->StudentUnits()->get();

        // Get all StudentLessons from these StudentUnits (live/presence lessons)
        $studentLessonsMap = \App\Models\StudentLesson::whereIn(
            'student_unit_id',
            $studentUnits->pluck('id')
        )
            ->get()
            ->keyBy('lesson_id');

        // Get all SelfStudyLessons for this CourseAuth
        $selfStudyLessonsMap = \App\Models\SelfStudyLesson::where('course_auth_id', $courseAuth->id)
            ->get()
            ->keyBy('lesson_id');

        // Build lessons array with status - maintain order from CourseUnitLesson
        // Priority: If either self study OR live lesson passed, show passed
        // Only show failed if both exist and both failed, or only one exists and it failed
        $lessons = [];
        foreach ($lessonIds as $lessonId) {
            $lesson = $courseLessons->get($lessonId);
            if (!$lesson) {
                continue;
            }

            $studentLesson = $studentLessonsMap->get($lesson->id);
            $selfStudyLesson = $selfStudyLessonsMap->get($lesson->id);

            $status = 'pending';
            $completedAt = null;
            $dncAt = null;
            $source = null; // Track which source provided the status

            // Check self study first
            $selfStudyPassed = $selfStudyLesson && $selfStudyLesson->completed_at;
            $selfStudyFailed = $selfStudyLesson && $selfStudyLesson->dnc_at;

            // Check live/presence lesson
            $livePassed = $studentLesson && $studentLesson->completed_at;
            $liveFailed = $studentLesson && $studentLesson->dnc_at;

            // Priority logic: If EITHER passed, show passed
            if ($selfStudyPassed) {
                $status = 'passed';
                $completedAt = \Carbon\Carbon::parse($selfStudyLesson->completed_at)->format('Y-m-d H:i:s');
                $source = 'self_study';
            } elseif ($livePassed) {
                $status = 'passed';
                $completedAt = \Carbon\Carbon::parse($studentLesson->completed_at)->format('Y-m-d H:i:s');
                $source = 'live';
            } elseif ($selfStudyFailed && $liveFailed) {
                // Both failed - show failed with most recent date
                $status = 'failed';
                $selfStudyDnc = \Carbon\Carbon::parse($selfStudyLesson->dnc_at);
                $liveDnc = \Carbon\Carbon::parse($studentLesson->dnc_at);
                if ($liveDnc->gt($selfStudyDnc)) {
                    $dncAt = $liveDnc->format('Y-m-d H:i:s');
                    $source = 'live';
                } else {
                    $dncAt = $selfStudyDnc->format('Y-m-d H:i:s');
                    $source = 'self_study';
                }
            } elseif ($selfStudyFailed) {
                // Only self study failed, no live attempt
                $status = 'failed';
                $dncAt = \Carbon\Carbon::parse($selfStudyLesson->dnc_at)->format('Y-m-d H:i:s');
                $source = 'self_study';
            } elseif ($liveFailed) {
                // Only live failed, no self study attempt
                $status = 'failed';
                $dncAt = \Carbon\Carbon::parse($studentLesson->dnc_at)->format('Y-m-d H:i:s');
                $source = 'live';
            }
            // Otherwise remains 'pending' - no attempts in either system

            $lessons[] = [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'status' => $status,
                'completed_at' => $completedAt,
                'dnc_at' => $dncAt,
                'source' => $source, // 'self_study', 'live', or null
            ];
        }

        return $lessons;
    }

    /**
     * Get class attendance history - ALL attendance days for the course
     */
    private function getClassHistory($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return [];
        }

        // Find the CourseAuth
        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->first();

        if (!$courseAuth) {
            return [];
        }

        // Get all StudentUnits for this CourseAuth with their CourseDates
        $studentUnits = $courseAuth->StudentUnits()
            ->with('CourseDate')
            ->orderBy('created_at', 'desc')
            ->get();

        $history = [];
        foreach ($studentUnits as $studentUnit) {
            if (!$studentUnit->CourseDate) {
                continue;
            }

            $courseDate = $studentUnit->CourseDate;
            $startDate = \Carbon\Carbon::parse($courseDate->starts_at);

            $history[] = [
                'id' => $studentUnit->id,
                'date' => $startDate->format('Y-m-d'),
                'day_name' => $startDate->format('l'), // Monday, Tuesday, etc.
                'formatted_date' => $startDate->format('M j, Y'), // Jan 5, 2026
                'time' => $startDate->format('g:i A'), // 9:00 AM
                'status'       => $studentUnit->ejected_at ? 'ejected' : 'present',
                'course_date_id' => $courseDate->id,
                'created_at'   => \Carbon\Carbon::parse($studentUnit->created_at)->format('Y-m-d H:i:s'),
                'ejected_at'   => $studentUnit->ejected_at ? $studentUnit->ejected_at->format('Y-m-d H:i:s') : null,
                'ejected_for'  => $studentUnit->ejected_for,
            ];
        }

        return $history;
    }

    /**
     * Get student photos for validation
     */
    private function getStudentPhotos($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return null;
        }

        // Find the CourseAuth
        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->with('course')
            ->first();

        if (!$courseAuth) {
            return null;
        }

        // Use existing buildStudentValidationsForCourseAuth logic
        $studentDashboardController = app(\App\Http\Controllers\Frontend\Student\StudentDashboardController::class);
        $reflection = new \ReflectionClass($studentDashboardController);
        $method = $reflection->getMethod('buildStudentValidationsForCourseAuth');
        $method->setAccessible(true);
        $validations = $method->invoke($studentDashboardController, $courseAuth);

        // Get validation records for status and IDs
        $idCardValidation = \App\Models\Validation::where('course_auth_id', $courseAuth->id)->first();

        // Get the most recent StudentUnit for this CourseAuth to get headshot
        $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $headshotValidation = $studentUnit ?
            \App\Models\Validation::where('student_unit_id', $studentUnit->id)->first() : null;

        // Format headshot URL (handle array or string)
        $headshotUrl = is_array($validations['headshot']) ?
            (reset($validations['headshot']) ?: null) : $validations['headshot'];

        // Build response with photo validation data
        return [
            'student' => [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'email' => $user->email,
                'student_number' => $user->student_num ?? null,
            ],
            'idcard' => [
                'validation_id' => $idCardValidation ? $idCardValidation->id : null,
                'image_url' => $validations['idcard'],
                'status' => $validations['idcard_status'],
                'uploaded_at' => $idCardValidation ? $idCardValidation->created_at : null,
                'reject_reason' => $idCardValidation ? $idCardValidation->reject_reason : null,
            ],
            'headshot' => [
                'validation_id' => $headshotValidation ? $headshotValidation->id : null,
                'image_url' => $headshotUrl,
                'status' => $validations['headshot_status'],
                'captured_at' => $headshotValidation ? $headshotValidation->created_at : null,
                'reject_reason' => $headshotValidation ? $headshotValidation->reject_reason : null,
            ],
            'fully_verified' => ($validations['idcard_status'] === 'approved' && $validations['headshot_status'] === 'approved'),
        ];
    }

    /**
     * Get exam results and status for the support exam tab
     */
    private function getExamResults($studentId, $courseId)
    {
        $user = User::find($studentId);
        if (!$user) {
            return null;
        }

        $courseAuth = $user->courseAuths()
            ->where('id', $courseId)
            ->first();

        if (!$courseAuth) {
            return null;
        }

        // Determine exam readiness via the model trait
        $readinessFailure = $courseAuth->ExamReadinessFailureReason();
        $examReady        = $readinessFailure === null;

        // Grab all non-hidden attempts, newest first
        $examAuths = \App\Models\ExamAuth::where('course_auth_id', $courseAuth->id)
            ->whereNull('hidden_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Determine max attempts from the exam policy (default 2 if no exam linked)
        $maxAttempts = 2;
        try {
            $course = $courseAuth->course;
            if ($course && $course->exam_id) {
                $exam = \App\Models\Exam::find($course->exam_id);
                if ($exam) {
                    $maxAttempts = $exam->policy_attempts ?? 2;
                }
            }
        } catch (\Exception $e) {
            // fallback to 2
        }

        $attemptsUsed = $examAuths->whereNotNull('completed_at')->count();
        $attemptsRemaining = max(0, $maxAttempts - $attemptsUsed);

        $attempts = $examAuths->map(function ($ea) {
            $isExpired   = $ea->expires_at && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($ea->expires_at));
            $isCompleted = !is_null($ea->completed_at);

            $scorePercent = null;
            if ($ea->score && str_contains($ea->score, ' / ')) {
                [$correct, $total] = explode(' / ', $ea->score);
                $scorePercent = $total > 0 ? (int) floor((int)$correct / (int)$total * 100) : null;
            }

            return [
                'id'               => $ea->id,
                'created_at'       => \Carbon\Carbon::parse($ea->created_at)->format('Y-m-d H:i:s'),
                'completed_at'     => $ea->completed_at
                    ? \Carbon\Carbon::parse($ea->completed_at)->format('Y-m-d H:i:s')
                    : null,
                'expires_at'       => $ea->expires_at
                    ? \Carbon\Carbon::parse($ea->expires_at)->format('Y-m-d H:i:s')
                    : null,
                'next_attempt_at'  => $ea->next_attempt_at
                    ? \Carbon\Carbon::parse($ea->next_attempt_at)->format('Y-m-d H:i:s')
                    : null,
                'score'            => $ea->score,
                'score_percent'    => $scorePercent,
                'is_passed'        => (bool) $ea->is_passed,
                'is_expired'       => $isExpired,
                'is_completed'     => $isCompleted,
                'is_in_progress'   => !$isCompleted && !$isExpired,
                'can_review'       => $isCompleted,
                'has_answers'      => !is_null($ea->answers) && count((array)$ea->answers) > 0,
            ];
        })->values()->toArray();

        // Overall status string
        $examPassed = $examAuths->where('is_passed', true)->isNotEmpty();
        if ($examPassed) {
            $status = 'passed';
        } elseif ($attemptsRemaining === 0) {
            $status = 'no_attempts';
        } elseif ($examReady) {
            $status = 'ready';
        } elseif ($readinessFailure && ($readinessFailure['reason'] ?? '') === 'cooldown') {
            $status = 'cooldown';
        } elseif ($readinessFailure && ($readinessFailure['reason'] ?? '') === 'lessons') {
            $status = 'lessons_incomplete';
        } else {
            $status = 'not_ready';
        }

        return [
            'status'             => $status,
            'exam_ready'         => $examReady,
            'readiness_reason'   => $readinessFailure,
            'exam_passed'        => $examPassed,
            'exam_admin_override' => !is_null($courseAuth->exam_admin_id),
            'max_attempts'       => $maxAttempts,
            'attempts_used'      => $attemptsUsed,
            'attempts_remaining' => $attemptsRemaining,
            'next_attempt_at'    => $readinessFailure['next_attempt_at'] ?? null,
            'attempts'           => $attempts,
        ];
    }

    /**
     * Reset (hide) an exam attempt so the student can re-take it.
     * POST /support/reset-exam/{examAuthId}
     */
    public function resetExam(Request $request, $examAuthId)
    {
        try {
            $examAuth = \App\Models\ExamAuth::findOrFail($examAuthId);

            \Log::info('Support: resetting exam attempt', [
                'exam_auth_id'   => $examAuthId,
                'support_admin'  => auth('admin')->id(),
                'course_auth_id' => $examAuth->course_auth_id,
            ]);

            $examAuth->forceFill([
                'hidden_at' => \Carbon\Carbon::now(),
                'hidden_by' => auth('admin')->id(),
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Exam attempt reset. The student may now take the exam again.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Exam attempt not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: resetExam failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Return a completed exam's questions + student answers for review.
     * GET /support/exam-review/{examAuthId}
     */
    public function getExamReview(Request $request, $examAuthId)
    {
        try {
            $examAuth = \App\Models\ExamAuth::findOrFail($examAuthId);

            if (!$examAuth->completed_at) {
                return response()->json(['success' => false, 'message' => 'Exam not yet completed.'], 422);
            }

            $questionIds = (array) ($examAuth->question_ids ?? []);
            $answers     = (array) ($examAuth->answers     ?? []);  // [ exam_question_id => answer_number ]
            $incorrect   = (array) ($examAuth->incorrect   ?? []);  // [ lesson_id => count ]

            // Load questions in the order they were presented
            $questions = \App\Models\ExamQuestion::whereIn('id', $questionIds)->get()->keyBy('id');

            $reviewQuestions = array_map(function ($qId) use ($questions, $answers) {
                $q = $questions->get($qId);
                if (!$q) {
                    return null;
                }
                $studentAnswer = isset($answers[$qId]) ? (int) $answers[$qId] : null;
                $correctAnswer = (int) $q->correct;
                $answerOptions = array_filter([
                    1 => $q->answer_1,
                    2 => $q->answer_2,
                    3 => $q->answer_3,
                    4 => $q->answer_4,
                    5 => $q->answer_5,
                ]);

                return [
                    'id'             => $q->id,
                    'question'       => $q->question,
                    'answer_options' => $answerOptions,
                    'correct_answer' => $correctAnswer,
                    'student_answer' => $studentAnswer,
                    'is_correct'     => $studentAnswer !== null && $studentAnswer === $correctAnswer,
                ];
            }, $questionIds);

            // Remove nulls (questions that no longer exist)
            $reviewQuestions = array_values(array_filter($reviewQuestions));

            $scorePercent = null;
            if ($examAuth->score && str_contains($examAuth->score, ' / ')) {
                [$correct, $total] = explode(' / ', $examAuth->score);
                $scorePercent = $total > 0 ? (int) floor((int)$correct / (int)$total * 100) : null;
            }

            return response()->json([
                'success'        => true,
                'data'           => [
                    'exam_auth_id'   => $examAuth->id,
                    'score'          => $examAuth->score,
                    'score_percent'  => $scorePercent,
                    'is_passed'      => (bool) $examAuth->is_passed,
                    'completed_at'   => \Carbon\Carbon::parse($examAuth->completed_at)->format('Y-m-d H:i:s'),
                    'total_questions' => count($reviewQuestions),
                    'questions'      => $reviewQuestions,
                    'incorrect_by_lesson' => $incorrect,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Exam attempt not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: getExamReview failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get detailed student information for editing
     */
    private function getStudentDetails($studentId, $courseId = null)
    {
        $user = User::find($studentId);
        if (!$user) {
            return null;
        }

        // Get student_info JSON data
        $studentInfo = $user->student_info ?? [];

        return [
            'id' => $user->id,
            'email' => $user->email,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'student_num' => $user->student_num,
            'is_active' => $user->is_active,
            'role_id' => $user->role_id,
            'email_opt_in' => $user->email_opt_in,
            'use_gravatar' => $user->use_gravatar,
            'created_at' => $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i:s') : null,
            'updated_at' => $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('Y-m-d H:i:s') : null,
            'student_info' => $studentInfo,
        ];
    }

    /**
     * Update student details
     */
    public function updateStudentDetails(Request $request, $studentId)
    {
        try {
            $user = User::findOrFail($studentId);

            $validated = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $studentId,
                'student_num' => 'nullable|string|max:50',
                'email_opt_in' => 'boolean',
                'student_info' => 'nullable|array',
            ]);

            // Update user fields
            $user->fname = $validated['fname'];
            $user->lname = $validated['lname'];
            $user->email = $validated['email'];
            $user->student_num = $validated['student_num'] ?? null;
            $user->email_opt_in = $validated['email_opt_in'] ?? false;

            // Update student_info JSON
            if (isset($validated['student_info'])) {
                $user->student_info = $validated['student_info'];
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Student details updated successfully',
                'student' => $this->getStudentDetails($studentId),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to update student details', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update student details: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle ban / reinstate a student's CourseAuth enrollment.
     *
     * POST /support/student-tools/toggle-ban/{courseAuthId}
     * Body (ban only): { reason: string }
     */
    public function toggleBan(Request $request, int $courseAuthId)
    {
        try {
            $courseAuth = \App\Models\CourseAuth::findOrFail($courseAuthId);

            // Verify the enrollment belongs to a Student-role user.
            $student = User::find($courseAuth->user_id);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
            }

            $isBanned = !is_null($courseAuth->disabled_at);

            if ($isBanned) {
                // Reinstate
                $courseAuth->disabled_at     = null;
                $courseAuth->disabled_reason = null;
                $courseAuth->save();

                \Log::info('Support: CourseAuth reinstated', [
                    'course_auth_id' => $courseAuth->id,
                    'student_id'     => $courseAuth->user_id,
                    'admin_id'       => auth('admin')->id(),
                ]);

                $action = 'reinstated';
            } else {
                // Ban — reason required
                $request->validate([
                    'reason' => 'required|string|max:500',
                ]);

                $courseAuth->disabled_at     = now();
                $courseAuth->disabled_reason = $request->input('reason');
                $courseAuth->save();

                \Log::info('Support: CourseAuth banned', [
                    'course_auth_id' => $courseAuth->id,
                    'student_id'     => $courseAuth->user_id,
                    'reason'         => $courseAuth->disabled_reason,
                    'admin_id'       => auth('admin')->id(),
                ]);

                $action = 'banned';
            }

            return response()->json([
                'success'     => true,
                'action'      => $action,
                'course_auth' => [
                    'id'              => $courseAuth->id,
                    'disabled_at'     => $courseAuth->disabled_at ? $courseAuth->disabled_at->format('Y-m-d H:i:s') : null,
                    'disabled_reason' => $courseAuth->disabled_reason,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: toggleBan failed', ['course_auth_id' => $courseAuthId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle day-ban / reinstate a student from a specific class day (StudentUnit).
     *
     * POST /support/student-tools/toggle-day-ban/{studentUnitId}
     * Body (ban only): { reason: string }
     */
    public function toggleDayBan(Request $request, int $studentUnitId)
    {
        try {
            $studentUnit = \App\Models\StudentUnit::findOrFail($studentUnitId);

            // Verify the StudentUnit belongs to a Student-role user.
            $courseAuth = \App\Models\CourseAuth::find($studentUnit->course_auth_id);
            if (!$courseAuth) {
                return response()->json(['success' => false, 'message' => 'CourseAuth not found.'], 404);
            }

            $student = User::find($courseAuth->user_id);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
            }

            $isEjected = !is_null($studentUnit->ejected_at);

            if ($isEjected) {
                // Reinstate
                $studentUnit->ejected_at  = null;
                $studentUnit->ejected_for = null;
                $studentUnit->save();

                \Log::info('Support: StudentUnit day reinstated', [
                    'student_unit_id' => $studentUnit->id,
                    'student_id'      => $courseAuth->user_id,
                    'admin_id'        => auth('admin')->id(),
                ]);

                $action = 'day_reinstated';
            } else {
                // Eject — reason required
                $request->validate([
                    'reason' => 'required|string|max:500',
                ]);

                $studentUnit->ejected_at  = now();
                $studentUnit->ejected_for = $request->input('reason');
                $studentUnit->save();

                \Log::info('Support: StudentUnit day banned', [
                    'student_unit_id' => $studentUnit->id,
                    'student_id'      => $courseAuth->user_id,
                    'reason'          => $studentUnit->ejected_for,
                    'admin_id'        => auth('admin')->id(),
                ]);

                $action = 'day_banned';
            }

            return response()->json([
                'success'      => true,
                'action'       => $action,
                'student_unit' => [
                    'id'          => $studentUnit->id,
                    'ejected_at'  => $studentUnit->ejected_at ? $studentUnit->ejected_at->format('Y-m-d H:i:s') : null,
                    'ejected_for' => $studentUnit->ejected_for,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Student day record not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: toggleDayBan failed', ['student_unit_id' => $studentUnitId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List all InstLessons for a student's class day, with StudentLesson status per row.
     *
     * GET /support/student-tools/lessons-for-day/{studentUnitId}
     */
    public function getLessonsForDay(Request $request, int $studentUnitId)
    {
        try {
            $studentUnit = \App\Models\StudentUnit::findOrFail($studentUnitId);

            // Load all InstLessons for this class day's InstUnit
            $instLessons = \App\Models\InstLesson::with('Lesson')
                ->where('inst_unit_id', $studentUnit->inst_unit_id)
                ->orderBy('created_at')
                ->get();

            // Index existing StudentLessons by inst_lesson_id for fast lookup
            $existing = \App\Models\StudentLesson::where('student_unit_id', $studentUnitId)
                ->get()
                ->keyBy('inst_lesson_id');

            $lessons = $instLessons->map(function ($instLesson) use ($existing) {
                $studentLesson = $existing->get($instLesson->id);

                $canReverseDnc = $studentLesson
                    && !is_null($studentLesson->dnc_at)
                    && is_null($studentLesson->completed_at);

                return [
                    'inst_lesson_id'    => $instLesson->id,
                    'lesson_id'         => $instLesson->lesson_id,
                    'lesson_title'      => $instLesson->Lesson?->title ?? 'Lesson ' . $instLesson->lesson_id,
                    'started_at'        => $instLesson->created_at ? $instLesson->created_at->format('Y-m-d H:i:s') : null,
                    'student_lesson_id' => $studentLesson?->id,
                    'dnc_at'            => $studentLesson?->dnc_at?->format('Y-m-d H:i:s'),
                    'completed_at'      => $studentLesson?->completed_at?->format('Y-m-d H:i:s'),
                    'can_reverse_dnc'   => $canReverseDnc,
                ];
            });

            return response()->json(['success' => true, 'lessons' => $lessons]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Student day record not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: getLessonsForDay failed', ['student_unit_id' => $studentUnitId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Manually grant a missing StudentLesson for a student's class day.
     *
     * POST /support/student-tools/grant-lesson/{studentUnitId}
     * Body: { inst_lesson_id: int }
     */
    public function grantLesson(Request $request, int $studentUnitId)
    {
        try {
            $request->validate(['inst_lesson_id' => 'required|integer']);

            $studentUnit = \App\Models\StudentUnit::findOrFail($studentUnitId);

            // Verify the StudentUnit belongs to a Student-role user.
            $courseAuth = \App\Models\CourseAuth::find($studentUnit->course_auth_id);
            if (!$courseAuth) {
                return response()->json(['success' => false, 'message' => 'CourseAuth not found.'], 404);
            }

            $student = User::find($courseAuth->user_id);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
            }

            // Verify InstLesson belongs to this StudentUnit's InstUnit (prevents cross-day grants)
            $instLesson = \App\Models\InstLesson::where('id', $request->inst_lesson_id)
                ->where('inst_unit_id', $studentUnit->inst_unit_id)
                ->first();

            if (!$instLesson) {
                return response()->json(['success' => false, 'message' => 'Lesson not found for this class day.'], 404);
            }

            // Idempotent guard — do not create a duplicate
            $existing = \App\Models\StudentLesson::where('student_unit_id', $studentUnitId)
                ->where('inst_lesson_id', $instLesson->id)
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Student already has this lesson.'], 409);
            }

            $studentLesson = \App\Models\StudentLesson::create([
                'lesson_id'       => $instLesson->lesson_id,
                'student_unit_id' => $studentUnitId,
                'inst_lesson_id'  => $instLesson->id,
            ]);

            \Log::info('Support: lesson manually granted', [
                'student_unit_id' => $studentUnitId,
                'inst_lesson_id'  => $instLesson->id,
                'lesson_id'       => $instLesson->lesson_id,
                'student_id'      => $courseAuth->user_id,
                'admin_id'        => auth('admin')->id(),
            ]);

            return response()->json([
                'success'        => true,
                'student_lesson' => [
                    'id'             => $studentLesson->id,
                    'lesson_id'      => $studentLesson->lesson_id,
                    'inst_lesson_id' => $studentLesson->inst_lesson_id,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Student day record not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: grantLesson failed', ['student_unit_id' => $studentUnitId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reverse a DNC on a StudentLesson, bypassing the InstLesson guard.
     *
     * POST /support/student-tools/reverse-lesson-dnc/{studentLessonId}
     * Body (optional): { reason: string }
     */
    public function reverseLessonDnc(Request $request, int $studentLessonId)
    {
        try {
            $studentLesson = \App\Models\StudentLesson::findOrFail($studentLessonId);

            if (!is_null($studentLesson->completed_at)) {
                return response()->json(['success' => false, 'message' => 'Lesson is already completed — nothing to reverse.'], 409);
            }

            if (is_null($studentLesson->dnc_at)) {
                return response()->json(['success' => false, 'message' => 'Lesson is not in DNC state.'], 409);
            }

            // Clear DNC directly — do NOT call ClearDNC() trait (blocked by InstLesson->completed_at)
            $studentLesson->update(['dnc_at' => null]);

            // Null out failed_at on any associated Challenges
            $studentLesson->Challenges()->whereNotNull('failed_at')->update(['failed_at' => null]);

            \Log::info('Support: StudentLesson DNC reversed', [
                'student_lesson_id' => $studentLesson->id,
                'lesson_id'         => $studentLesson->lesson_id,
                'student_unit_id'   => $studentLesson->student_unit_id,
                'admin_id'          => auth('admin')->id(),
                'reason'            => $request->input('reason'),
            ]);

            return response()->json([
                'success'        => true,
                'student_lesson' => [
                    'id'         => $studentLesson->id,
                    'dnc_at'     => null,
                    'lesson_id'  => $studentLesson->lesson_id,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Student lesson not found.'], 404);
        } catch (\Exception $e) {
            \Log::error('Support: reverseLessonDnc failed', ['student_lesson_id' => $studentLessonId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Format activity type into human-readable description
     */
    private function formatActivityDescription($activityType)
    {
        $descriptions = [
            'onboarding_completed' => 'Completed onboarding',
            \App\Models\StudentActivity::TYPE_RULES_ACCEPTED => 'Accepted classroom rules',
            \App\Models\StudentActivity::TYPE_AGREEMENT_ACCEPTED => 'Accepted agreement',
            \App\Models\StudentActivity::TYPE_WAITING_ROOM_ENTRY => 'Entered waiting room',
            \App\Models\StudentActivity::TYPE_LESSON_STARTED => 'Started lesson',
            \App\Models\StudentActivity::TYPE_LESSON_COMPLETED => 'Completed lesson',
            \App\Models\StudentActivity::TYPE_LESSON_PAUSED => 'Paused lesson',
            \App\Models\StudentActivity::TYPE_LESSON_UNPAUSED => 'Resumed lesson',
            \App\Models\StudentActivity::TYPE_EXAM_STARTED => 'Started exam',
            \App\Models\StudentActivity::TYPE_EXAM_SUBMITTED => 'Submitted exam',
            \App\Models\StudentActivity::TYPE_BUTTON_CLICK => 'Interacted with interface',
            \App\Models\StudentActivity::TYPE_TAB_HIDDEN => 'Tab became inactive',
            \App\Models\StudentActivity::TYPE_TAB_VISIBLE => 'Tab became active',
        ];

        return $descriptions[$activityType] ?? $activityType;
    }
}
