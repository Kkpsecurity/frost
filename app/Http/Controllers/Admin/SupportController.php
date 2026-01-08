<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SupportController extends Controller
{
    /**
     * Search for users (students, admins, etc.) based on support staff permissions
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('query');
            $searchAll = $request->input('searchAll', false);

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Get current user
            $user = auth('admin')->user();

            // Determine if user can search all users
            $canSearchAll = $searchAll && $user && ($user->hasRole('admin') || $user->hasRole('sys-admin'));

            // Build query
            $usersQuery = User::query()
                ->where(function($q) use ($query) {
                    $q->where('fname', 'ilike', "%{$query}%")
                      ->orWhere('lname', 'ilike', "%{$query}%")
                      ->orWhere('email', 'ilike', "%{$query}%")
                      ->orWhereRaw("CONCAT(fname, ' ', lname) ILIKE ?", ["%{$query}%"])
                      ->orWhereRaw("CONCAT(lname, ' ', fname) ILIKE ?", ["%{$query}%"]);
                });

            // If not admin/sys-admin, filter out admin roles
            if (!$canSearchAll) {
                $usersQuery->whereDoesntHave('roles', function($q) {
                    $q->whereIn('name', ['admin', 'sys-admin', 'support']);
                });
            }

            $users = $usersQuery->limit(20)->get();

            $results = $users->map(function($user) {
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

        return $courseAuths->map(function($courseAuth) {
            return [
                'id' => $courseAuth->id,
                'course_id' => $courseAuth->course_id,
                'name' => $courseAuth->course ? $courseAuth->course->title : 'Unknown Course',
                'status' => $courseAuth->completed_at ? 'completed' : 'active',
                'start_date' => $courseAuth->start_date ? \Carbon\Carbon::parse($courseAuth->start_date)->format('Y-m-d') : null,
                'expire_date' => $courseAuth->expire_date ? \Carbon\Carbon::parse($courseAuth->expire_date)->format('Y-m-d') : null,
                'is_passed' => $courseAuth->is_passed,
                'created_at' => $courseAuth->created_at->format('Y-m-d H:i:s'),
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
                // Class joined activity
                $activities[] = [
                    'id' => 'unit_' . $studentUnit->id,
                    'date' => \Carbon\Carbon::parse($studentUnit->created_at)->format('Y-m-d'),
                    'type' => 'login',
                    'description' => 'Joined classroom session',
                    'details' => $courseUnit->title . ' - ' . \Carbon\Carbon::parse($courseDate->date)->format('M d, Y'),
                    'timestamp' => \Carbon\Carbon::parse($studentUnit->created_at)->toIso8601String(),
                ];

                // Class completed activity
                if ($studentUnit->completed_at) {
                    $activities[] = [
                        'id' => 'unit_completed_' . $studentUnit->id,
                        'date' => \Carbon\Carbon::parse($studentUnit->completed_at)->format('Y-m-d'),
                        'type' => 'lesson_completed',
                        'description' => 'Completed classroom session',
                        'details' => $courseUnit->title,
                        'timestamp' => \Carbon\Carbon::parse($studentUnit->completed_at)->toIso8601String(),
                    ];
                }
            }
        }

        // Get student lessons (lesson progress)
        $studentLessons = \App\Models\StudentLesson::whereIn('student_unit_id',
            $studentUnits->pluck('id')
        )
        ->with(['Lesson'])
        ->orderBy('created_at', 'desc')
        ->limit(100)
        ->get();

        foreach ($studentLessons as $studentLesson) {
            $lesson = $studentLesson->Lesson;

            if ($lesson) {
                // Lesson started
                $activities[] = [
                    'id' => 'lesson_' . $studentLesson->id,
                    'date' => \Carbon\Carbon::parse($studentLesson->created_at)->format('Y-m-d'),
                    'type' => 'lesson_started',
                    'description' => 'Started lesson',
                    'details' => $lesson->title,
                    'timestamp' => \Carbon\Carbon::parse($studentLesson->created_at)->toIso8601String(),
                ];

                // Lesson completed
                if ($studentLesson->completed_at) {
                    $activities[] = [
                        'id' => 'lesson_completed_' . $studentLesson->id,
                        'date' => \Carbon\Carbon::parse($studentLesson->completed_at)->format('Y-m-d'),
                        'type' => 'lesson_completed',
                        'description' => 'Completed lesson',
                        'details' => $lesson->title,
                        'timestamp' => \Carbon\Carbon::parse($studentLesson->completed_at)->toIso8601String(),
                    ];
                }
            }
        }

        // Sort activities by timestamp (most recent first)
        usort($activities, function($a, $b) {
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
        $studentLessonsMap = \App\Models\StudentLesson::whereIn('student_unit_id',
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
                'status' => 'present', // StudentUnit exists = present
                'course_date_id' => $courseDate->id,
                'created_at' => \Carbon\Carbon::parse($studentUnit->created_at)->format('Y-m-d H:i:s'),
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
        $studentDashboardController = app(\App\Http\Controllers\Student\StudentDashboardController::class);
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
     * Get exam results
     */
    private function getExamResults($studentId, $courseId)
    {
        // TODO: Query exam scores
        return [];
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
}
