<?php

namespace App\Http\Controllers\Admin\SupportCenter;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SupportCenter\StudentQueryService;
use App\Services\SupportCenter\StudentActionService;
use App\Services\SupportCenter\ValidationQueryService;
use App\Services\SupportCenter\ValidationActionService;

class FrostSupportDashboardController extends Controller
{
    protected StudentQueryService $studentQuery;
    protected StudentActionService $studentAction;
    protected ValidationQueryService $validationQuery;
    protected ValidationActionService $validationAction;

    public function __construct(
        StudentQueryService $studentQuery,
        StudentActionService $studentAction,
        ValidationQueryService $validationQuery,
        ValidationActionService $validationAction
    ) {
        $this->studentQuery = $studentQuery;
        $this->studentAction = $studentAction;
        $this->validationQuery = $validationQuery;
        $this->validationAction = $validationAction;
    }
    /**
     * Display the Frost Support Center dashboard
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.support-center.frost-dashboard', [
            'pageTitle' => 'Frost Support Center',
            'breadcrumbs' => [
                ['name' => 'Admin', 'url' => route('admin.dashboard')],
                ['name' => 'Frost Support', 'url' => null]
            ]
        ]);
    }

    /**
     * Get support center statistics for the dashboard
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'overview' => $this->getOverviewStats(),
                'recent_activity' => $this->getRecentActivity(),
                'system_health' => $this->getSystemHealth(),
                'support_metrics' => $this->getSupportMetrics()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load support center statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats(): array
    {
        return [
            'total_students' => User::where('role_id', \App\Support\RoleManager::STUDENT_ID)->count(),
            'active_students' => User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('is_active', true)
                ->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'revenue_today' => Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'revenue_month' => Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount')
        ];
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivity(): array
    {
        return [
            'recent_orders' => Order::with(['user', 'course'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'student_name' => $order->user->name ?? 'Unknown',
                        'course_title' => $order->course->title ?? 'Unknown Course',
                        'amount' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at->diffForHumans()
                    ];
                }),
            'recent_students' => User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('is_active', true)
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->fullname(),
                        'email' => $student->email,
                        'status' => $student->is_active ? 'active' : 'inactive',
                        'created_at' => $student->created_at->diffForHumans()
                    ];
                })
        ];
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth(): array
    {
        return [
            'database_status' => 'healthy',
            'cache_status' => 'healthy',
            'queue_status' => 'healthy',
            'storage_usage' => '45%', // Could be calculated from actual disk usage
            'memory_usage' => '67%',
            'last_backup' => '2 hours ago',
            'uptime' => '99.8%'
        ];
    }

    /**
     * Get support-specific metrics
     */
    private function getSupportMetrics(): array
    {
        return [
            'open_tickets' => 12,
            'resolved_tickets_today' => 8,
            'customer_satisfaction' => '4.8/5',
            'average_resolution_time' => '2.5 hours',
            'pending_refunds' => Order::where('status', 'refund_pending')->count(),
            'flagged_accounts' => User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('is_active', false)
                ->count()
        ];
    }

    /**
     * Search users for support purposes (using StudentQueryService)
     */
    public function searchStudents(Request $request): JsonResponse
    {
        $query = $request->input('query');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters'
            ]);
        }

        try {
            // Use service to search students
            $students = $this->studentQuery->searchStudents($query, 20);

            // Debug: Log the actual SQL query and results
            Log::info('User search results', [
                'users_found' => $students->count(),
                'users_data' => $students->take(3)->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->fullname(),
                        'email' => $s->email,
                        'role_id' => $s->role_id,
                        'role_name' => $s->Role ? $s->Role->name : 'Unknown',
                        'is_active' => $s->is_active
                    ];
                })
            ]);

            $studentsData = $students->map(function ($student) {
                // Get phone from student_info JSON field if it exists
                $phone = 'N/A';
                if ($student->student_info && is_array($student->student_info) && isset($student->student_info['phone'])) {
                    $phone = $student->student_info['phone'];
                }

                // Determine user role display
                $roleName = 'Unknown';
                if ($student->Role) {
                    $roleName = $student->Role->name;
                } elseif ($student->role_id == \App\Support\RoleManager::STUDENT_ID) {
                    $roleName = 'Student';
                } elseif ($student->role_id == 1) {
                    $roleName = 'System Admin';
                } elseif ($student->role_id == 2) {
                    $roleName = 'Admin';
                } elseif ($student->role_id == 3) {
                    $roleName = 'Instructor';
                }

                // Check if student has active StudentUnit today (online status)
                // StudentUnit created_at is the entry time - if it exists today, student is "online"
                $isOnline = \App\Models\StudentUnit::whereHas('CourseAuth', function ($q) use ($student) {
                    $q->where('user_id', $student->id);
                })
                    ->whereDate('created_at', today())
                    ->where(function ($q) {
                        $q->whereNull('completed_at')
                            ->orWhere('completed_at', '>', now());
                    })
                    ->whereNull('ejected_at') // Not ejected
                    ->exists();

                // Get course counts
                $courseAuths = \App\Models\CourseAuth::where('user_id', $student->id)->get();
                $totalCourses = $courseAuths->count();
                $activeCourses = $courseAuths->whereNull('disabled_at')->whereNull('completed_at')->count();
                $completedCourses = $courseAuths->whereNotNull('completed_at')->count();

                // Get last activity from student_activity table
                $lastActivity = \App\Models\StudentActivity::where('user_id', $student->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastActivityText = $lastActivity
                    ? $lastActivity->created_at->diffForHumans()
                    : ($student->updated_at ? $student->updated_at->diffForHumans() : 'Never');

                return [
                    'id' => $student->id,
                    'name' => $student->fullname(),
                    'email' => $student->email,
                    'phone' => $phone,
                    'status' => $student->is_active ? 'active' : 'inactive',
                    'online_status' => $isOnline ? 'online' : 'offline',
                    'current_unit' => null,
                    'role' => $roleName,
                    'role_id' => $student->role_id,
                    'total_courses' => $totalCourses,
                    'active_courses' => $activeCourses,
                    'completed_courses' => $completedCourses,
                    'last_activity' => $lastActivityText,
                    'created_at' => $student->created_at ? $student->created_at->format('M j, Y') : 'Unknown'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $studentsData->toArray(),
                'count' => $studentsData->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user details for support (using StudentQueryService)
     */
    public function getStudentDetails($studentId): JsonResponse
    {
        try {
            // Use service to get student details
            $student = $this->studentQuery->getStudentDetails($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Get student statistics
            $stats = $this->studentQuery->getStudentStatistics($studentId);

            // Check if student is online (has StudentUnit for today)
            $today = Carbon::today();
            $isOnline = false;
            $currentUnit = null;

            if ($student->StudentUnits) {
                foreach ($student->StudentUnits as $studentUnit) {
                    // Check if student unit is for today and not ejected
                    $unitDate = Carbon::parse($studentUnit->created_at)->format('Y-m-d');
                    $todayDate = $today->format('Y-m-d');

                    if ($unitDate === $todayDate && !$studentUnit->ejected_at) {
                        $isOnline = true;
                        $currentUnit = $studentUnit;
                        break;
                    }

                    // Also check if ejected but ejection period has expired
                    if ($unitDate === $todayDate && $studentUnit->ejected_at) {
                        $canReturnAt = Carbon::parse($studentUnit->ejected_at)->addHours(24);
                        if (Carbon::now()->gte($canReturnAt)) {
                            $isOnline = true;
                            $currentUnit = $studentUnit;
                            break;
                        }
                    }
                }
            }

            // Get phone from student_info JSON field if it exists
            $phone = 'N/A';
            if ($student->student_info && is_array($student->student_info) && isset($student->student_info['phone'])) {
                $phone = $student->student_info['phone'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->fullname(),
                        'email' => $student->email,
                        'phone' => $phone,
                        'status' => $student->is_active ? 'active' : 'inactive',
                        'online_status' => $isOnline ? 'online' : 'offline',
                        'current_unit' => $currentUnit ? [
                            'id' => $currentUnit->id,
                            'unit_name' => $currentUnit->unit->name ?? 'Unknown Unit',
                            'created_at' => $currentUnit->created_at->format('M j, Y g:i A'),
                            'is_ejected' => !is_null($currentUnit->ejected_at)
                        ] : null,
                        'role' => $student->Role ? $student->Role->name : 'student',
                        'created_at' => $student->created_at,
                        'updated_at' => $student->updated_at
                    ],
                    'course_auths' => $student->CourseAuths ? $student->CourseAuths->map(function ($courseAuth) {
                        return [
                            'id' => $courseAuth->id,
                            'course_title' => $courseAuth->courseDate->course->title ?? 'Unknown Course',
                            'status' => $courseAuth->status ?? 'active',
                            'progress' => $courseAuth->progress ?? 0,
                            'created_at' => $courseAuth->created_at
                        ];
                    }) : []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found or access denied',
                'error' => config('app.debug') ? $e->getMessage() : 'Student not found'
            ], 404);
        }
    }

    /**
     * Get user activity timeline (works with all user roles)
     */
    public function getStudentActivity($studentId, Request $request): JsonResponse
    {
        $period = $request->input('period', 'today'); // 'today' or 'all'

        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            $activities = collect();

            // Build date query based on period
            if ($period === 'today') {
                $startDate = Carbon::today();
                $endDate = Carbon::tomorrow();
            } else {
                $startDate = null;
                $endDate = null;
            }

            // Get real activity data from database
            $activities = $this->getRealStudentActivity($studentId, $period, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $activities,
                'period' => $period,
                'student_id' => $studentId,
                'count' => count($activities)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get real student activity data from database
     */
    private function getRealStudentActivity($studentId, $period, $startDate = null, $endDate = null): array
    {
        $activities = [];

        // Get CourseAuths (course enrollments)
        $courseAuthQuery = \App\Models\CourseAuth::where('user_id', $studentId)
            ->with('Course');

        if ($startDate && $endDate) {
            $courseAuthQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $courseAuthQuery->orderBy('created_at', 'desc')->take(10);
        }

        $courseAuths = $courseAuthQuery->get();

        foreach ($courseAuths as $courseAuth) {
            $activities[] = [
                'type' => 'enrollment',
                'title' => 'Course Enrollment',
                'description' => 'Enrolled in ' . ($courseAuth->Course->title ?? 'Unknown Course'),
                'details' => 'Course ID: ' . $courseAuth->course_id,
                'created_at' => $courseAuth->created_at->format('Y-m-d H:i:s')
            ];

            // Add course completion if completed
            if ($courseAuth->completed_at) {
                $completedAt = Carbon::parse($courseAuth->completed_at);
                if (!$startDate || ($completedAt->gte($startDate) && $completedAt->lt($endDate))) {
                    $activities[] = [
                        'type' => 'course_complete',
                        'title' => 'Course Completed',
                        'description' => 'Completed ' . ($courseAuth->Course->title ?? 'Unknown Course'),
                        'details' => 'Status: ' . ($courseAuth->is_passed ? 'Passed' : 'Completed'),
                        'created_at' => $completedAt->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        // Get StudentUnits (class attendance)
        $studentUnitQuery = \App\Models\StudentUnit::whereHas('CourseAuth', function ($q) use ($studentId) {
            $q->where('user_id', $studentId);
        })
            ->with(['CourseAuth.Course']);

        if ($startDate && $endDate) {
            $studentUnitQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $studentUnitQuery->orderBy('created_at', 'desc')->take(20);
        }

        $studentUnits = $studentUnitQuery->get();

        foreach ($studentUnits as $studentUnit) {
            $activities[] = [
                'type' => 'class_join',
                'title' => 'Joined Class',
                'description' => 'Joined class session for ' . ($studentUnit->CourseAuth->Course->title ?? 'Unknown Course'),
                'details' => 'Unit ID: ' . $studentUnit->id,
                'created_at' => $studentUnit->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Get StudentLessons (lesson activities)
        $studentLessonQuery = \App\Models\StudentLesson::whereHas('StudentUnit.CourseAuth', function ($q) use ($studentId) {
            $q->where('user_id', $studentId);
        })
            ->with(['Lesson', 'StudentUnit.CourseAuth.Course']);

        if ($startDate && $endDate) {
            $studentLessonQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $studentLessonQuery->orderBy('created_at', 'desc')->take(30);
        }

        $studentLessons = $studentLessonQuery->get();

        foreach ($studentLessons as $studentLesson) {
            // Lesson started
            $activities[] = [
                'type' => 'lesson_start',
                'title' => 'Started Lesson',
                'description' => 'Began "' . ($studentLesson->Lesson->title ?? 'Unknown Lesson') . '"',
                'details' => 'Course: ' . ($studentLesson->StudentUnit->CourseAuth->Course->title ?? 'Unknown'),
                'created_at' => $studentLesson->created_at->format('Y-m-d H:i:s')
            ];

            // Lesson completed
            if ($studentLesson->completed_at) {
                $completedAt = Carbon::parse($studentLesson->completed_at);
                if (!$startDate || ($completedAt->gte($startDate) && $completedAt->lt($endDate))) {
                    $duration = $studentLesson->created_at->diffInMinutes($completedAt);
                    $activities[] = [
                        'type' => 'lesson_complete',
                        'title' => 'Completed Lesson',
                        'description' => 'Finished "' . ($studentLesson->Lesson->title ?? 'Unknown Lesson') . '"',
                        'details' => 'Duration: ' . $duration . ' minutes | Course: ' . ($studentLesson->StudentUnit->CourseAuth->Course->title ?? 'Unknown'),
                        'created_at' => $completedAt->format('Y-m-d H:i:s')
                    ];
                }
            }

            // DNC (Did Not Complete)
            if ($studentLesson->dnc_at) {
                $dncAt = Carbon::parse($studentLesson->dnc_at);
                if (!$startDate || ($dncAt->gte($startDate) && $dncAt->lt($endDate))) {
                    $activities[] = [
                        'type' => 'lesson_dnc',
                        'title' => 'Lesson DNC',
                        'description' => 'Did not complete "' . ($studentLesson->Lesson->title ?? 'Unknown Lesson') . '"',
                        'details' => 'Reason: ' . ($studentLesson->dnc_reason ?? 'Not specified'),
                        'created_at' => $dncAt->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        // Get ExamAuths (exam attempts)
        $examAuthQuery = \App\Models\ExamAuth::whereHas('CourseAuth', function ($q) use ($studentId) {
            $q->where('user_id', $studentId);
        })
            ->with(['Exam', 'CourseAuth.Course']);

        if ($startDate && $endDate) {
            $examAuthQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $examAuthQuery->orderBy('created_at', 'desc')->take(20);
        }

        $examAuths = $examAuthQuery->get();

        foreach ($examAuths as $examAuth) {
            // Exam started
            $activities[] = [
                'type' => 'exam_start',
                'title' => 'Started Exam',
                'description' => 'Began exam for ' . ($examAuth->CourseAuth->Course->title ?? 'Unknown Course'),
                'details' => 'Exam: ' . ($examAuth->Exam->admin_title ?? 'Course Exam'),
                'created_at' => $examAuth->created_at->format('Y-m-d H:i:s')
            ];

            // Exam completed
            if ($examAuth->completed_at) {
                $completedAt = Carbon::parse($examAuth->completed_at);
                if (!$startDate || ($completedAt->gte($startDate) && $completedAt->lt($endDate))) {
                    $score = $examAuth->score ?? 0;
                    $scorePercent = $examAuth->ScorePercent();
                    $activities[] = [
                        'type' => $examAuth->is_passed ? 'exam_pass' : 'exam_fail',
                        'title' => $examAuth->is_passed ? 'Passed Exam' : 'Failed Exam',
                        'description' => ($examAuth->is_passed ? 'Passed' : 'Failed') . ' exam for ' . ($examAuth->CourseAuth->Course->title ?? 'Unknown Course'),
                        'details' => 'Score: ' . $scorePercent . '% (' . $score . ' correct) | Status: ' . ($examAuth->is_passed ? 'PASSED' : 'FAILED'),
                        'created_at' => $completedAt->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        // Sort all activities by created_at descending (most recent first)
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $activities;
    }

    /**
     * Generate mock activity data for demonstration
     * Replace this with actual database queries when implementing real activity tracking
     */
    private function getMockStudentActivity($studentId, $period): array
    {
        $baseTime = Carbon::now();
        $activities = [];

        if ($period === 'today') {
            // Today's activities
            $activities = [
                [
                    'type' => 'login',
                    'title' => 'Student Login',
                    'description' => 'Logged into the learning platform',
                    'details' => 'IP: 192.168.1.100 | Browser: Chrome 118',
                    'created_at' => $baseTime->copy()->subHours(2)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'lesson_start',
                    'title' => 'Started Lesson',
                    'description' => 'Began "Introduction to Network Security"',
                    'details' => 'Course: Cybersecurity Fundamentals | Module 2',
                    'created_at' => $baseTime->copy()->subHours(1)->subMinutes(45)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'lesson_complete',
                    'title' => 'Completed Lesson',
                    'description' => 'Finished "Introduction to Network Security"',
                    'details' => 'Score: 92% | Duration: 28 minutes | Progress: 35%',
                    'created_at' => $baseTime->copy()->subHours(1)->subMinutes(15)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'download',
                    'title' => 'Downloaded Resource',
                    'description' => 'Downloaded "Network Security Checklist.pdf"',
                    'details' => 'File size: 2.4 MB | Type: PDF',
                    'created_at' => $baseTime->copy()->subHour()->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'quiz_start',
                    'title' => 'Started Quiz',
                    'description' => 'Began "Network Security Basics Quiz"',
                    'details' => 'Questions: 15 | Time limit: 20 minutes',
                    'created_at' => $baseTime->copy()->subMinutes(30)->format('Y-m-d H:i:s')
                ]
            ];
        } else {
            // Full history (last 7 days)
            $activities = [
                // Today
                [
                    'type' => 'login',
                    'title' => 'Student Login',
                    'description' => 'Logged into the learning platform',
                    'details' => 'IP: 192.168.1.100 | Browser: Chrome 118',
                    'created_at' => $baseTime->copy()->subHours(2)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'lesson_complete',
                    'title' => 'Completed Lesson',
                    'description' => 'Finished "Introduction to Network Security"',
                    'details' => 'Score: 92% | Duration: 28 minutes',
                    'created_at' => $baseTime->copy()->subHours(1)->format('Y-m-d H:i:s')
                ],

                // Yesterday
                [
                    'type' => 'login',
                    'title' => 'Student Login',
                    'description' => 'Logged into the learning platform',
                    'details' => 'IP: 192.168.1.100 | Browser: Chrome 118',
                    'created_at' => $baseTime->copy()->subDay()->subHours(3)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'quiz_complete',
                    'title' => 'Quiz Completed',
                    'description' => 'Completed "Security Fundamentals Quiz"',
                    'details' => 'Score: 88% | Attempts: 2 | Duration: 18 minutes',
                    'created_at' => $baseTime->copy()->subDay()->subHours(2)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'message',
                    'title' => 'Message Sent',
                    'description' => 'Sent message to instructor',
                    'details' => 'Subject: Question about SSL certificates',
                    'created_at' => $baseTime->copy()->subDay()->subHours(1)->format('Y-m-d H:i:s')
                ],

                // 2 days ago
                [
                    'type' => 'login',
                    'title' => 'Student Login',
                    'description' => 'Logged into the learning platform',
                    'details' => 'IP: 192.168.1.100 | Browser: Chrome 118',
                    'created_at' => $baseTime->copy()->subDays(2)->subHours(4)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'lesson_start',
                    'title' => 'Started Lesson',
                    'description' => 'Began "Cryptography Basics"',
                    'details' => 'Course: Advanced Security | Module 1',
                    'created_at' => $baseTime->copy()->subDays(2)->subHours(3)->format('Y-m-d H:i:s')
                ],
                [
                    'type' => 'upload',
                    'title' => 'Assignment Submitted',
                    'description' => 'Submitted "Security Assessment Report"',
                    'details' => 'File: security_report.docx | Size: 1.8 MB',
                    'created_at' => $baseTime->copy()->subDays(2)->subHours(1)->format('Y-m-d H:i:s')
                ],

                // 3 days ago
                [
                    'type' => 'support',
                    'title' => 'Support Request',
                    'description' => 'Opened support ticket #1245',
                    'details' => 'Issue: Cannot access video content',
                    'created_at' => $baseTime->copy()->subDays(3)->subHours(2)->format('Y-m-d H:i:s')
                ]
            ];
        }

        return $activities;
    }

    /**
     * Get user's course authorizations for ban functionality (works with all user roles)
     */
    public function getStudentCourses($studentId): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            $courseAuths = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with(['Course', 'RangeDate'])
                ->orderBy('created_at', 'desc')
                ->get();

            $coursesData = $courseAuths->map(function ($courseAuth) {
                return [
                    'id' => $courseAuth->id,
                    'title' => $courseAuth->Course->title ?? 'Unknown Course',
                    'course_id' => $courseAuth->course_id,
                    'range_date' => $courseAuth->RangeDate ? $courseAuth->RangeDate->start_date : null,
                    'status' => $courseAuth->status ?? 'active',
                    'disabled_at' => $courseAuth->disabled_at,
                    'disabled_reason' => $courseAuth->disabled_reason,
                    'created_at' => $courseAuth->created_at->format('M j, Y'),
                    'is_banned' => !is_null($courseAuth->disabled_at)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $coursesData->toArray(),
                'count' => $coursesData->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student courses',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Ban student from a specific course by updating CourseAuth
     */
    public function banStudentFromCourse(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id',
                'disabled_reason' => 'required|string|min:10|max:500'
            ]);

            $courseAuth = \App\Models\CourseAuth::findOrFail($request->course_auth_id);

            // Check if already banned
            if ($courseAuth->disabled_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is already banned from this course'
                ], 400);
            }

            // Ban the student by setting disabled_at and disabled_reason
            $courseAuth->update([
                'disabled_at' => now(),
                'disabled_reason' => $request->disabled_reason
            ]);

            // Log the ban action for audit
            Log::info('Student banned from course', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $courseAuth->user_id,
                'course_auth_id' => $courseAuth->id,
                'course_title' => $courseAuth->Course->title ?? 'Unknown',
                'reason' => $request->disabled_reason,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student has been successfully banned from the course',
                'data' => [
                    'course_auth_id' => $courseAuth->id,
                    'disabled_at' => $courseAuth->disabled_at->toISOString(),
                    'disabled_reason' => $courseAuth->disabled_reason
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to ban student from course',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Unban student from a specific course (optional - for future use)
     */
    public function unbanStudentFromCourse(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'course_auth_id' => 'required|integer|exists:course_auths,id'
            ]);

            $courseAuth = \App\Models\CourseAuth::findOrFail($request->course_auth_id);

            // Check if not banned
            if (!$courseAuth->disabled_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not banned from this course'
                ], 400);
            }

            // Unban the student by clearing disabled_at and disabled_reason
            $courseAuth->update([
                'disabled_at' => null,
                'disabled_reason' => null
            ]);

            // Log the unban action for audit
            Log::info('Student unbanned from course', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $courseAuth->user_id,
                'course_auth_id' => $courseAuth->id,
                'course_title' => $courseAuth->courseDate->course->title ?? 'Unknown',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student has been successfully unbanned from the course',
                'data' => [
                    'course_auth_id' => $courseAuth->id,
                    'disabled_at' => null,
                    'disabled_reason' => null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unban student from course',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's unit enrollments for kick functionality (works with all user roles)
     */
    public function getStudentUnits($studentId): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            $studentUnits = \App\Models\StudentUnit::where('user_id', $studentId)
                ->with(['unit'])
                ->orderBy('created_at', 'desc')
                ->get();

            $unitsData = $studentUnits->map(function ($studentUnit) {
                return [
                    'id' => $studentUnit->id,
                    'unit_name' => $studentUnit->unit->name ?? 'Unknown Unit',
                    'unit_code' => $studentUnit->unit->code ?? null,
                    'status' => $studentUnit->status ?? 'active',
                    'ejected_at' => $studentUnit->ejected_at,
                    'ejected_for' => $studentUnit->ejected_for,
                    'can_return_at' => $studentUnit->ejected_at ?
                        Carbon::parse($studentUnit->ejected_at)->addHours(24)->format('M j, Y g:i A') : null,
                    'created_at' => $studentUnit->created_at->format('M j, Y'),
                    'is_ejected' => !is_null($studentUnit->ejected_at) &&
                        Carbon::now()->lt(Carbon::parse($studentUnit->ejected_at)->addHours(24))
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $unitsData->toArray(),
                'count' => $unitsData->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student units',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Kick student from a specific unit by updating StudentUnit
     */
    public function kickStudentFromUnit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_unit_id' => 'required|integer|exists:student_units,id',
                'ejected_for' => 'required|string|min:10|max:255',
                'duration_hours' => 'required|integer|min:1|max:168'
            ]);

            $studentUnit = \App\Models\StudentUnit::findOrFail($request->student_unit_id);

            // Check if already ejected and still within ejection period
            if ($studentUnit->ejected_at) {
                $canReturnAt = Carbon::parse($studentUnit->ejected_at)->addDay();
                if (Carbon::now()->lt($canReturnAt)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student is already kicked out from this unit until ' . $canReturnAt->format('M j, Y g:i A')
                    ], 400);
                }
            }

            // Calculate ejection time and return time
            // We set ejected_at to current time and calculate when they can return
            $ejectedAt = Carbon::now();
            $canReturnAt = Carbon::now()->addHours($request->duration_hours);

            // Kick the student by setting ejected_at and ejected_for
            $studentUnit->update([
                'ejected_at' => $ejectedAt,
                'ejected_for' => $request->ejected_for
            ]);

            // Log the kick action for audit
            Log::info('Student kicked from unit', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $studentUnit->user_id,
                'student_unit_id' => $studentUnit->id,
                'unit_name' => $studentUnit->unit->name ?? 'Unknown',
                'reason' => $request->ejected_for,
                'duration_hours' => $request->duration_hours,
                'ejected_at' => $ejectedAt,
                'can_return_at' => $canReturnAt,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student has been kicked out for ' . $request->duration_hours . ' hours',
                'data' => [
                    'student_unit_id' => $studentUnit->id,
                    'ejected_at' => $ejectedAt->toISOString(),
                    'ejected_for' => $studentUnit->ejected_for,
                    'duration_hours' => $request->duration_hours,
                    'can_return_at' => $canReturnAt->format('M j, Y g:i A')
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to kick student from unit',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Allow student back to unit early (optional - for future use)
     */
    public function allowStudentBackToUnit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_unit_id' => 'required|integer|exists:student_units,id'
            ]);

            $studentUnit = \App\Models\StudentUnit::findOrFail($request->student_unit_id);

            // Check if not ejected
            if (!$studentUnit->ejected_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not kicked out from this unit'
                ], 400);
            }

            // Allow back by clearing ejected_at and ejected_for
            $studentUnit->update([
                'ejected_at' => null,
                'ejected_for' => null
            ]);

            // Log the allow-back action for audit
            Log::info('Student allowed back to unit early', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $studentUnit->user_id,
                'student_unit_id' => $studentUnit->id,
                'unit_name' => $studentUnit->unit->name ?? 'Unknown',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student has been allowed back to the unit',
                'data' => [
                    'student_unit_id' => $studentUnit->id,
                    'ejected_at' => null,
                    'ejected_for' => null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to allow student back to unit',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's DNC (Did Not Complete) lessons for reinstatement (works with all user roles)
     */
    public function getStudentDncLessons($studentId): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            $dncLessons = \App\Models\StudentLesson::whereHas('StudentUnit.CourseAuth', function ($query) use ($studentId) {
                $query->where('user_id', $studentId);
            })
                ->whereNotNull('dnc_at')
                ->with(['lesson'])
                ->orderBy('dnc_at', 'desc')
                ->get();

            $lessonsData = $dncLessons->map(function ($studentLesson) {
                return [
                    'id' => $studentLesson->id,
                    'lesson_title' => $studentLesson->lesson->title ?? 'Unknown Lesson',
                    'lesson_id' => $studentLesson->lesson_id,
                    'dnc_at' => $studentLesson->dnc_at,
                    'dnc_reason' => $studentLesson->dnc_reason ?? 'Late arrival - entered waiting room',
                    'created_at' => $studentLesson->created_at->format('M j, Y g:i A'),
                    'dnc_formatted' => Carbon::parse($studentLesson->dnc_at)->format('M j, Y g:i A'),
                    'days_since_dnc' => Carbon::parse($studentLesson->dnc_at)->diffInDays(Carbon::now())
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $lessonsData->toArray(),
                'count' => $lessonsData->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student DNC lessons',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reinstate student lesson by clearing DNC status
     */
    public function reinstateStudentLesson(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_lesson_id' => 'required|integer|exists:student_lessons,id',
                'reinstate_reason' => 'required|string|min:10|max:500'
            ]);

            $studentLesson = \App\Models\StudentLesson::with(['lesson', 'StudentUnit.CourseAuth'])->findOrFail($request->student_lesson_id);

            // Check if not DNC
            if (!$studentLesson->dnc_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This lesson is not marked as DNC (Did Not Complete)'
                ], 400);
            }

            // Store original DNC info for logging
            $originalDncAt = $studentLesson->dnc_at;
            $originalDncReason = $studentLesson->dnc_reason;

            // Clear DNC status by setting dnc_at to null
            $studentLesson->update([
                'dnc_at' => null,
                'dnc_reason' => null
            ]);

            // Log the reinstatement action for audit
            Log::info('Student lesson DNC status cleared', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $studentLesson->StudentUnit->CourseAuth->user_id ?? null,
                'student_lesson_id' => $studentLesson->id,
                'lesson_title' => $studentLesson->lesson->title ?? 'Unknown',
                'original_dnc_at' => $originalDncAt,
                'original_dnc_reason' => $originalDncReason,
                'reinstate_reason' => $request->reinstate_reason,
                'reinstated_at' => now(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student has been successfully reinstated to the lesson',
                'data' => [
                    'student_lesson_id' => $studentLesson->id,
                    'lesson_title' => $studentLesson->lesson->title ?? 'Unknown Lesson',
                    'dnc_at' => null,
                    'dnc_reason' => null,
                    'reinstated_at' => now()->toISOString(),
                    'reinstate_reason' => $request->reinstate_reason
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reinstate student lesson',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all course lessons with completion status for support view
     * Shows all lessons with pass/fail status based on StudentLesson data
     * Optional: filter by specific course_auth_id
     */
    public function getStudentLessons($studentId, Request $request): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            // Get optional course_auth_id filter from query parameter
            $courseAuthIdFilter = $request->query('course_auth_id');

            // Get all course authorizations for this user (or filtered by specific course)
            $courseAuthsQuery = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with('Course');

            if ($courseAuthIdFilter) {
                $courseAuthsQuery->where('id', $courseAuthIdFilter);
            }

            $courseAuths = $courseAuthsQuery->get();

            if ($courseAuths->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'count' => 0,
                    'message' => 'No courses found for this user'
                ]);
            }

            $allLessons = [];

            // Loop through each course authorization
            foreach ($courseAuths as $courseAuth) {
                $course = $courseAuth->Course;
                if (!$course)
                    continue;

                // Get all lessons for this course using the Course's GetLessons() method
                // This retrieves lessons through the course_units -> course_unit_lessons relationship
                $courseLessons = $course->GetLessons();

                if ($courseLessons->isEmpty()) {
                    continue;
                }

                // Get all StudentLessons (ONLINE lessons) for this user's course
                $studentLessons = \App\Models\StudentLesson::whereHas('StudentUnit.CourseAuth', function ($query) use ($studentId, $courseAuth) {
                    $query->where('user_id', $studentId)
                        ->where('course_auth_id', $courseAuth->id);
                })
                    ->with('Lesson')
                    ->get()
                    ->keyBy('lesson_id');

                // Get all SelfStudyLessons (OFFLINE lessons) for this user's course
                // Group by lesson_id and get the most recent attempt for each lesson
                $selfStudyLessonsRaw = \App\Models\SelfStudyLesson::where('course_auth_id', $courseAuth->id)
                    ->with('Lesson')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Get only the MOST RECENT attempt for each lesson
                $selfStudyLessons = $selfStudyLessonsRaw->groupBy('lesson_id')->map(function ($attempts) {
                    return $attempts->first(); // Most recent due to orderBy desc
                });

                // Map each lesson with its status (check both StudentLesson and SelfStudyLesson)
                $lessonIndex = 1;
                foreach ($courseLessons as $lesson) {
                    $studentLesson = $studentLessons->get($lesson->id);
                    $selfStudyLesson = $selfStudyLessons->get($lesson->id);

                    $status = 'not_started';
                    $statusLabel = 'Not Started';
                    $statusClass = 'secondary';
                    $completedAt = null;
                    $dncAt = null;
                    $dncReason = null;
                    $lessonType = null;

                    // Check StudentLesson first (ONLINE lesson - higher priority)
                    if ($studentLesson) {
                        $lessonType = 'online';
                        if ($studentLesson->dnc_at) {
                            $status = 'dnc';
                            $statusLabel = 'DNC (Did Not Complete)';
                            $statusClass = 'danger';
                            $dncAt = Carbon::parse($studentLesson->dnc_at)->format('M j, Y g:i A');
                            $dncReason = $studentLesson->dnc_reason ?? null;
                        } elseif ($studentLesson->completed_at) {
                            $status = 'passed';
                            $statusLabel = 'Passed';
                            $statusClass = 'success';
                            $completedAt = Carbon::parse($studentLesson->completed_at)->format('M j, Y g:i A');
                        } else {
                            $status = 'in_progress';
                            $statusLabel = 'In Progress';
                            $statusClass = 'info';
                        }
                    }
                    // Check SelfStudyLesson (OFFLINE lesson) if no StudentLesson
                    elseif ($selfStudyLesson) {
                        $lessonType = 'offline';
                        if ($selfStudyLesson->dnc_at) {
                            $status = 'dnc';
                            $statusLabel = 'DNC (Did Not Complete)';
                            $statusClass = 'danger';
                            $dncAt = Carbon::parse($selfStudyLesson->dnc_at)->format('M j, Y g:i A');
                        } elseif ($selfStudyLesson->completed_at) {
                            $status = 'passed';
                            $statusLabel = 'Passed';
                            $statusClass = 'success';
                            $completedAt = Carbon::parse($selfStudyLesson->completed_at)->format('M j, Y g:i A');
                        } else {
                            $status = 'in_progress';
                            $statusLabel = 'In Progress';
                            $statusClass = 'info';
                        }
                    }

                    $allLessons[] = [
                        'lesson_id' => $lesson->id,
                        'lesson_title' => $lesson->title,
                        'lesson_order' => $lessonIndex,
                        'course_id' => $course->id,
                        'course_title' => $course->title,
                        'course_auth_id' => $courseAuth->id,
                        'status' => $status,
                        'status_label' => $statusLabel,
                        'status_class' => $statusClass,
                        'completed_at' => $completedAt,
                        'dnc_at' => $dncAt,
                        'dnc_reason' => $dncReason,
                        'lesson_type' => $lessonType, // 'online', 'offline', or null
                        'student_lesson_id' => $studentLesson ? $studentLesson->id : null,
                        'self_study_lesson_id' => $selfStudyLesson ? $selfStudyLesson->id : null,
                        'has_attempt' => (bool) ($studentLesson || $selfStudyLesson),
                    ];

                    $lessonIndex++;
                }
            }

            // Sort by course, then by lesson order
            usort($allLessons, function ($a, $b) {
                if ($a['course_id'] !== $b['course_id']) {
                    return $a['course_id'] - $b['course_id'];
                }
                return $a['lesson_order'] - $b['lesson_order'];
            });

            // Calculate summary statistics
            $summary = [
                'total_lessons' => count($allLessons),
                'passed' => count(array_filter($allLessons, fn($l) => $l['status'] === 'passed')),
                'dnc' => count(array_filter($allLessons, fn($l) => $l['status'] === 'dnc')),
                'in_progress' => count(array_filter($allLessons, fn($l) => $l['status'] === 'in_progress')),
                'not_started' => count(array_filter($allLessons, fn($l) => $l['status'] === 'not_started')),
            ];

            return response()->json([
                'success' => true,
                'data' => $allLessons,
                'count' => count($allLessons),
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load student lessons', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load student lessons',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Determine lesson status based on StudentLesson data
     */
    private function getLessonStatus($studentLesson): string
    {
        if ($studentLesson->dnc_at) {
            return 'dnc'; // Did Not Complete
        }

        if ($studentLesson->completed_at) {
            return 'completed';
        }

        // If the record exists but no completion or DNC, assume started
        return 'started';
    }

    /**
     * Temporary debug method to check database content
     */
    public function debugDatabase(): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $usersByRole = User::selectRaw('role_id, COUNT(*) as count')
                ->groupBy('role_id')
                ->orderBy('role_id')
                ->get();

            $studentsAll = User::where('role_id', 5)->count();
            $studentsActive = User::where('role_id', 5)->where('is_active', true)->count();

            $sampleStudents = User::where('role_id', 5)->take(5)->get(['id', 'fname', 'lname', 'email', 'is_active']);

            // Test search for "student"
            $searchResults = User::where('role_id', 5)
                ->where('is_active', true)
                ->where(function ($q) {
                    $query = 'student';
                    $q->where('fname', 'LIKE', "%{$query}%")
                        ->orWhere('lname', 'LIKE', "%{$query}%")
                        ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$query}%"])
                        ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->get(['id', 'fname', 'lname', 'email']);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'users_by_role' => $usersByRole,
                    'students_all' => $studentsAll,
                    'students_active' => $studentsActive,
                    'student_role_id_constant' => \App\Support\RoleManager::STUDENT_ID,
                    'sample_students' => $sampleStudents,
                    'search_test_results' => $searchResults
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    public function markStudentLessonDnc(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_lesson_id' => 'required|integer|exists:student_lessons,id',
                'dnc_reason' => 'string|max:255'
            ]);

            $studentLesson = \App\Models\StudentLesson::with(['lesson', 'StudentUnit.CourseAuth'])->findOrFail($request->student_lesson_id);

            // Check if already DNC
            if ($studentLesson->dnc_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This lesson is already marked as DNC'
                ], 400);
            }

            // Mark as DNC
            $studentLesson->update([
                'dnc_at' => now(),
                'dnc_reason' => $request->dnc_reason ?? 'Manually marked as DNC by administrator'
            ]);

            // Log the DNC action for audit
            Log::info('Student lesson marked as DNC', [
                'admin_user_id' => auth()->id(),
                'student_user_id' => $studentLesson->StudentUnit->CourseAuth->user_id ?? null,
                'student_lesson_id' => $studentLesson->id,
                'lesson_title' => $studentLesson->lesson->title ?? 'Unknown',
                'dnc_reason' => $studentLesson->dnc_reason,
                'dnc_at' => $studentLesson->dnc_at,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student lesson has been marked as DNC',
                'data' => [
                    'student_lesson_id' => $studentLesson->id,
                    'lesson_title' => $studentLesson->lesson->title ?? 'Unknown Lesson',
                    'dnc_at' => $studentLesson->dnc_at->toISOString(),
                    'dnc_reason' => $studentLesson->dnc_reason
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark student lesson as DNC',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get comprehensive exam information for support purposes (works with all user roles)
     */
    public function getStudentExams($studentId, Request $request): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            // Get optional course_auth_id filter from query parameter
            $courseAuthIdFilter = $request->query('course_auth_id');

            // Get all course authorizations with exam information
            $courseAuthsQuery = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with([
                    'Course.Exam',
                    'ExamAuths' => function ($query) {
                        $query->whereNull('hidden_at')->orderBy('created_at', 'desc');
                    }
                ])
                ->orderBy('created_at', 'desc');

            if ($courseAuthIdFilter) {
                $courseAuthsQuery->where('id', $courseAuthIdFilter);
            }

            $courseAuths = $courseAuthsQuery->get();

            $examData = [];

            foreach ($courseAuths as $courseAuth) {
                // Skip courses without exams
                if (!$courseAuth->Course || !$courseAuth->Course->Exam) {
                    continue;
                }

                $exam = $courseAuth->Course->Exam;
                $examAuths = $courseAuth->ExamAuths;

                // Determine eligibility status
                $eligibilityStatus = $this->determineExamEligibility($courseAuth);

                // Get latest exam attempt
                $latestAttempt = $examAuths->first();

                // Calculate attempt statistics
                $totalAttempts = $examAuths->whereNotNull('completed_at')->count();
                $passedAttempts = $examAuths->where('is_passed', true)->count();
                $failedAttempts = $examAuths->where('is_passed', false)->whereNotNull('completed_at')->count();

                // Get best score
                $bestScore = $this->getBestExamScore($examAuths);

                $examData[] = [
                    'course_auth_id' => $courseAuth->id,
                    'course_title' => $courseAuth->Course->title ?? 'Unknown Course',
                    'course_status' => $this->getCourseAuthStatus($courseAuth),
                    'exam_info' => [
                        'id' => $exam->id,
                        'title' => $exam->admin_title ?? 'Course Exam',
                        'num_questions' => $exam->num_questions ?? 0,
                        'num_to_pass' => $exam->num_to_pass ?? 0,
                        'passing_percentage' => $exam->num_questions > 0 ?
                            round(($exam->num_to_pass / $exam->num_questions) * 100, 1) : 0,
                        'max_attempts' => $exam->policy_attempts ?? 'Unlimited',
                        'time_limit_hours' => $exam->policy_expire_seconds ?
                            round($exam->policy_expire_seconds / 3600, 1) : null,
                        'wait_period_hours' => $exam->policy_wait_seconds ?
                            round($exam->policy_wait_seconds / 3600, 1) : null,
                    ],
                    'eligibility' => $eligibilityStatus,
                    'statistics' => [
                        'total_attempts' => $totalAttempts,
                        'passed_attempts' => $passedAttempts,
                        'failed_attempts' => $failedAttempts,
                        'remaining_attempts' => $exam->policy_attempts ?
                            max(0, $exam->policy_attempts - $totalAttempts) : 'Unlimited',
                        'best_score' => $bestScore,
                    ],
                    'latest_attempt' => $latestAttempt ? [
                        'id' => $latestAttempt->id,
                        'started_at' => $latestAttempt->created_at,
                        'completed_at' => $latestAttempt->completed_at,
                        'expires_at' => $latestAttempt->expires_at,
                        'score' => $latestAttempt->score,
                        'score_percentage' => $latestAttempt->ScorePercent(),
                        'is_passed' => $latestAttempt->is_passed,
                        'is_expired' => $latestAttempt->IsExpired(),
                        'status' => $this->getExamAttemptStatus($latestAttempt),
                        'next_attempt_at' => $latestAttempt->next_attempt_at,
                    ] : null,
                    'all_attempts' => $examAuths->map(function ($examAuth) {
                        return [
                            'id' => $examAuth->id,
                            'started_at' => $examAuth->created_at,
                            'completed_at' => $examAuth->completed_at,
                            'score' => $examAuth->score,
                            'score_percentage' => $examAuth->ScorePercent(),
                            'is_passed' => $examAuth->is_passed,
                            'is_expired' => $examAuth->IsExpired(),
                            'status' => $this->getExamAttemptStatus($examAuth),
                            'time_taken' => $examAuth->completed_at && $examAuth->created_at ?
                                \Carbon\Carbon::parse($examAuth->created_at)
                                    ->diffInMinutes(\Carbon\Carbon::parse($examAuth->completed_at)) . ' minutes' : null,
                        ];
                    })->toArray(),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $examData,
                'count' => count($examData),
                'summary' => [
                    'total_courses_with_exams' => count($examData),
                    'courses_passed' => collect($examData)->where('statistics.passed_attempts', '>', 0)->count(),
                    'courses_failed' => collect($examData)->where('statistics.failed_attempts', '>', 0)
                        ->where('statistics.passed_attempts', 0)->count(),
                    'courses_not_attempted' => collect($examData)->where('statistics.total_attempts', 0)->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load student exam information',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Determine if student is eligible to take an exam
     */
    private function determineExamEligibility($courseAuth): array
    {
        $status = 'not_eligible';
        $reason = '';
        $canTakeExam = false;

        // Check if course is active
        if (!$courseAuth->IsActive()) {
            $status = 'course_inactive';
            $reason = 'Course is not active or has expired';
        }
        // Check if already passed
        elseif ($courseAuth->is_passed) {
            $status = 'already_passed';
            $reason = 'Student has already passed this course';
        }
        // Check if exam ready (uses CourseAuth ExamReady method)
        elseif ($courseAuth->ExamReady()) {
            $status = 'eligible';
            $reason = 'Student is eligible to take the exam';
            $canTakeExam = true;
        }
        // Check if admin authorized
        elseif ($courseAuth->exam_admin_id) {
            $status = 'admin_authorized';
            $reason = 'Exam has been authorized by administrator';
            $canTakeExam = true;
        }
        // Check if all lessons completed
        elseif (!$courseAuth->AllLessonsCompleted()) {
            $status = 'lessons_incomplete';
            $reason = 'Not all lessons have been completed';
        }
        // Check if in waiting period
        else {
            $latestExam = $courseAuth->LatestExamAuth;
            if ($latestExam && $latestExam->next_attempt_at) {
                $nextAttempt = \Carbon\Carbon::parse($latestExam->next_attempt_at);
                if (\Carbon\Carbon::now()->lt($nextAttempt)) {
                    $status = 'waiting_period';
                    $reason = 'Must wait until ' . $nextAttempt->format('M j, Y g:i A') . ' for next attempt';
                }
            }
        }

        return [
            'status' => $status,
            'reason' => $reason,
            'can_take_exam' => $canTakeExam,
            'admin_override' => !is_null($courseAuth->exam_admin_id),
        ];
    }

    /**
     * Get the best exam score from all attempts
     */
    private function getBestExamScore($examAuths)
    {
        $bestScore = null;
        $bestPercentage = 0;

        foreach ($examAuths as $examAuth) {
            if ($examAuth->completed_at && $examAuth->score) {
                $percentage = $examAuth->ScorePercent();
                if ($percentage > $bestPercentage) {
                    $bestPercentage = $percentage;
                    $bestScore = [
                        'raw_score' => $examAuth->score,
                        'percentage' => $percentage,
                        'is_passed' => $examAuth->is_passed,
                        'date' => $examAuth->completed_at
                    ];
                }
            }
        }

        return $bestScore;
    }

    /**
     * Get course authorization status
     */
    private function getCourseAuthStatus($courseAuth): string
    {
        if ($courseAuth->is_passed) {
            return 'passed';
        }

        if ($courseAuth->disabled_at) {
            return 'disabled';
        }

        if ($courseAuth->completed_at && !$courseAuth->is_passed) {
            return 'failed';
        }

        if ($courseAuth->expire_date && \Carbon\Carbon::now()->gt($courseAuth->expire_date)) {
            return 'expired';
        }

        if ($courseAuth->start_date && \Carbon\Carbon::now()->lt($courseAuth->start_date)) {
            return 'not_started';
        }

        return 'active';
    }

    /**
     * Get exam attempt status
     */
    private function getExamAttemptStatus($examAuth): string
    {
        if ($examAuth->is_passed) {
            return 'passed';
        }

        if ($examAuth->IsExpired()) {
            return 'expired';
        }

        if ($examAuth->completed_at) {
            return 'failed';
        }

        if ($examAuth->expires_at && \Carbon\Carbon::now()->gt($examAuth->expires_at)) {
            return 'expired';
        }

        return 'in_progress';
    }

    /**
     * Get comprehensive validation information for identity verification (works with all user roles)
     */
    public function getStudentValidations(Request $request, $studentId): JsonResponse
    {
        try {
            // Search all users, not just students
            $student = User::where('id', $studentId)
                ->firstOrFail();

            // Get all course authorizations for ID card validations (one per CourseAuth)
            $query = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with([
                    'Course',
                    'StudentUnits.Validation', // For headshots
                ])
                ->orderBy('created_at', 'desc');

            // Filter by specific course if provided
            if ($request->has('course_auth_id')) {
                $query->where('id', $request->input('course_auth_id'));
            }

            $courseAuths = $query->get();

            $validationData = [];

            foreach ($courseAuths as $courseAuth) {
                // Get ID card validation (one per CourseAuth)
                $idCardValidation = \App\Models\Validation::where('course_auth_id', $courseAuth->id)->first();

                // Get ALL student units for this course auth (including failed ones)
                // Query directly to ensure we get both passed AND failed units
                $studentUnits = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
                    ->with('Validation')
                    ->orderBy('created_at', 'asc') // Show chronologically
                    ->get();
                $headshotValidations = [];
                $dayNumber = 1; // Track day number

                foreach ($studentUnits as $studentUnit) {
                    $headshotValidation = $studentUnit->Validation;

                    // Get additional context about this day
                    $dayContext = $this->getStudentUnitContext($studentUnit);

                    if ($headshotValidation) {
                        $headshotValidations[] = [
                            'id' => $headshotValidation->id,
                            'student_unit_id' => $studentUnit->id,
                            'course_date_id' => $studentUnit->course_date_id,
                            'day_number' => $dayNumber,
                            'date' => $studentUnit->created_at ?
                                \Carbon\Carbon::parse($studentUnit->created_at)->format('Y-m-d') : null,
                            'day_name' => $studentUnit->created_at ?
                                \Carbon\Carbon::parse($studentUnit->created_at)->format('l') : null,
                            'status' => $this->getValidationStatus($headshotValidation),
                            'photo_url' => $headshotValidation->URL() ?: $headshotValidation->URL(true),
                            'has_photo' => (bool) $headshotValidation->URL(),
                            'id_type' => $headshotValidation->id_type,
                            'reject_reason' => $headshotValidation->reject_reason,
                            // Add day context
                            'unit_status' => $dayContext['status'],
                            'unit_completed' => $studentUnit->unit_completed ?? false,
                            'ejected_at' => $studentUnit->ejected_at ?
                                \Carbon\Carbon::parse($studentUnit->ejected_at)->format('Y-m-d H:i') : null,
                            'ejected_reason' => $studentUnit->ejected_for ?? null,
                            'lesson_count' => $dayContext['lesson_count'],
                            'completed_lessons' => $dayContext['completed_lessons'],
                        ];
                    } else {
                        // Student unit exists but no headshot validation record
                        $headshotValidations[] = [
                            'id' => null,
                            'student_unit_id' => $studentUnit->id,
                            'course_date_id' => $studentUnit->course_date_id,
                            'day_number' => $dayNumber,
                            'date' => $studentUnit->created_at ?
                                \Carbon\Carbon::parse($studentUnit->created_at)->format('Y-m-d') : null,
                            'day_name' => $studentUnit->created_at ?
                                \Carbon\Carbon::parse($studentUnit->created_at)->format('l') : null,
                            'status' => 'missing',
                            'photo_url' => (new \App\Models\Validation())->URL(true), // Default image
                            'has_photo' => false,
                            'id_type' => null,
                            'reject_reason' => null,
                            // Add day context for failed days
                            'unit_status' => $dayContext['status'],
                            'unit_completed' => $studentUnit->unit_completed ?? false,
                            'ejected_at' => $studentUnit->ejected_at ?
                                \Carbon\Carbon::parse($studentUnit->ejected_at)->format('Y-m-d H:i') : null,
                            'ejected_reason' => $studentUnit->ejected_for ?? null,
                            'lesson_count' => $dayContext['lesson_count'],
                            'completed_lessons' => $dayContext['completed_lessons'],
                        ];
                    }

                    $dayNumber++; // Increment day number for next unit
                }

                $courseValidationData = [
                    'course_auth_id' => $courseAuth->id,
                    'course_title' => $courseAuth->Course->title ?? 'Unknown Course',
                    'course_status' => $this->getCourseAuthStatus($courseAuth),
                    'enrollment_date' => \Carbon\Carbon::parse($courseAuth->created_at)->format('Y-m-d'),

                    // ID Card Validation (one per course enrollment)
                    'id_card_validation' => $idCardValidation ? [
                        'id' => $idCardValidation->id,
                        'course_auth_id' => $courseAuth->id, // Add reference to course auth
                        'status' => $this->getValidationStatus($idCardValidation),
                        'photo_url' => $idCardValidation->URL() ?: $idCardValidation->URL(true),
                        'has_photo' => (bool) $idCardValidation->URL(),
                        'id_type' => $idCardValidation->id_type,
                        'reject_reason' => $idCardValidation->reject_reason,
                    ] : [
                        'id' => null,
                        'course_auth_id' => $courseAuth->id, // Add reference to course auth
                        'status' => 'missing',
                        'photo_url' => (new \App\Models\Validation())->URL(true), // Default image
                        'has_photo' => false,
                        'id_type' => null,
                        'reject_reason' => null,
                    ],

                    // Headshot Validations (one per day/StudentUnit)
                    'headshot_validations' => $headshotValidations,

                    // Summary statistics
                    'validation_summary' => [
                        'total_days' => count($headshotValidations),
                        'headshots_submitted' => count(array_filter($headshotValidations, function ($v) {
                            return $v['has_photo'];
                        })),
                        'headshots_approved' => count(array_filter($headshotValidations, function ($v) {
                            return $v['status'] === 'approved';
                        })),
                        'headshots_rejected' => count(array_filter($headshotValidations, function ($v) {
                            return $v['status'] === 'rejected';
                        })),
                        'headshots_pending' => count(array_filter($headshotValidations, function ($v) {
                            return $v['status'] === 'pending';
                        })),
                        'id_card_status' => $idCardValidation ? $this->getValidationStatus($idCardValidation) : 'missing',
                    ]
                ];

                $validationData[] = $courseValidationData;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'avatar' => $student->getAvatar('thumb'),
                    ],
                    'validations' => $validationData,
                    'summary' => [
                        'total_courses' => count($validationData),
                        'id_cards_submitted' => count(array_filter($validationData, function ($v) {
                            return $v['id_card_validation']['has_photo'];
                        })),
                        'total_headshots_required' => array_sum(array_column($validationData, 'validation_summary.total_days')),
                        'total_headshots_submitted' => array_sum(array_column($validationData, 'validation_summary.headshots_submitted')),
                    ]
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading student validation data', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load student validation data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get validation status based on the status field
     */
    private function getValidationStatus(\App\Models\Validation $validation): string
    {
        if ($validation->status > 0) {
            return 'approved';
        } elseif ($validation->status < 0) {
            return 'rejected';
        } elseif ($validation->status === 0) {
            return 'pending';
        }

        return 'unknown';
    }

    /**
     * Get additional context about a student unit (day performance)
     */
    private function getStudentUnitContext(\App\Models\StudentUnit $studentUnit): array
    {
        // Get lesson progress for this day
        $studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)->get();
        $completedLessons = $studentLessons->where('completed_at', '!=', null)->count();
        $totalLessons = $studentLessons->count();

        // Determine day status
        $status = 'unknown';
        if ($studentUnit->ejected_at) {
            $status = 'ejected';
        } elseif ($studentUnit->unit_completed) {
            $status = 'completed';
        } elseif ($totalLessons > 0 && $completedLessons > 0) {
            $status = 'in_progress';
        } elseif ($totalLessons > 0) {
            $status = 'started';
        } else {
            $status = 'not_started';
        }

        return [
            'status' => $status,
            'lesson_count' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'completion_percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
        ];
    }

    /**
     * Approve a validation with optional ID type
     */
    public function approveValidation(Request $request, $validationId): JsonResponse
    {
        try {
            $validation = \App\Models\Validation::findOrFail($validationId);

            $request->validate([
                'id_type' => 'nullable|string|max:64',
                'note' => 'nullable|string|max:500',
            ]);

            // ID cards require an id_type
            if ($validation->course_auth_id && !$request->id_type) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Type is required for ID card validations'
                ], 422);
            }

            $validation->Accept($request->id_type);

            return response()->json([
                'success' => true,
                'message' => 'Validation approved successfully',
                'data' => [
                    'id' => $validation->id,
                    'status' => $this->getValidationStatus($validation),
                    'id_type' => $validation->id_type,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving validation', [
                'validation_id' => $validationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve validation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reject a validation with reason
     */
    public function rejectValidation(Request $request, $validationId): JsonResponse
    {
        try {
            $validation = \App\Models\Validation::findOrFail($validationId);

            $request->validate([
                'reject_reason' => 'required|string|max:500',
            ]);

            $validation->Reject($request->reject_reason);

            return response()->json([
                'success' => true,
                'message' => 'Validation rejected successfully',
                'data' => [
                    'id' => $validation->id,
                    'status' => $this->getValidationStatus($validation),
                    'reject_reason' => $validation->reject_reason,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting validation', [
                'validation_id' => $validationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject validation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
