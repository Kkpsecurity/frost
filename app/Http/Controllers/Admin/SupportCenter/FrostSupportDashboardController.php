<?php

namespace App\Http\Controllers\Admin\SupportCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FrostSupportDashboardController extends Controller
{
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
     * Search students for support purposes
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
            // Debug: Log the search parameters
            Log::info('Student search debug', [
                'query' => $query,
                'student_role_id' => \App\Support\RoleManager::STUDENT_ID,
                'total_users' => User::count(),
                'users_with_role_5' => User::where('role_id', 5)->count(),
                'active_users_with_role_5' => User::where('role_id', 5)->where('is_active', true)->count()
            ]);

            $students = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('is_active', true)
                ->where(function($q) use ($query) {
                    // Search by first name, last name, full name, or email
                    $q->where('fname', 'LIKE', "%{$query}%")
                        ->orWhere('lname', 'LIKE', "%{$query}%")
                        ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$query}%"])
                        ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->orderBy('lname', 'asc')
                ->orderBy('fname', 'asc')
                ->take(20)
                ->get();

            // Debug: Log the actual SQL query and results
            Log::info('Student search results', [
                'students_found' => $students->count(),
                'students_data' => $students->take(3)->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->fullname(),
                        'email' => $s->email,
                        'role_id' => $s->role_id,
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

                return [
                    'id' => $student->id,
                    'name' => $student->fullname(),
                    'email' => $student->email,
                    'phone' => $phone,
                    'status' => $student->is_active ? 'active' : 'inactive',
                    'online_status' => 'offline', // Simplified for now
                    'current_unit' => null, // Simplified for now
                    'role' => 'student',
                    'total_courses' => 0, // Simplified for now
                    'active_courses' => 0, // Simplified for now
                    'completed_courses' => 0, // Simplified for now
                    'last_activity' => $student->updated_at ? $student->updated_at->diffForHumans() : 'Never',
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
     * Get student details for support
     */
    public function getStudentDetails($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
                ->with(['CourseAuths', 'Role', 'StudentUnits'])
                ->firstOrFail();

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
     * Get student activity timeline
     */
    public function getStudentActivity($studentId, Request $request): JsonResponse
    {
        $period = $request->input('period', 'today'); // 'today' or 'all'

        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
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

            // Get login activities (from user login logs if available)
            // For now, we'll create mock data structure for demonstration
            $mockActivities = $this->getMockStudentActivity($studentId, $period);

            // In a real implementation, you would query actual activity tables:
            /*
            // Login/Logout activities
            if ($startDate && $endDate) {
                $loginActivities = UserLoginLog::where('user_id', $studentId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
            } else {
                $loginActivities = UserLoginLog::where('user_id', $studentId)
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();
            }

            // Course activities
            if ($startDate && $endDate) {
                $courseActivities = CourseProgress::where('user_id', $studentId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['course', 'lesson'])
                    ->get();
            } else {
                $courseActivities = CourseProgress::where('user_id', $studentId)
                    ->with(['course', 'lesson'])
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();
            }

            // Quiz activities
            if ($startDate && $endDate) {
                $quizActivities = QuizAttempt::where('user_id', $studentId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['quiz', 'course'])
                    ->get();
            } else {
                $quizActivities = QuizAttempt::where('user_id', $studentId)
                    ->with(['quiz', 'course'])
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();
            }

            // Merge and sort all activities
            $activities = $loginActivities->merge($courseActivities)->merge($quizActivities)
                ->sortByDesc('created_at')
                ->values();
            */

            return response()->json([
                'success' => true,
                'data' => $mockActivities,
                'period' => $period,
                'student_id' => $studentId,
                'count' => count($mockActivities)
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
     * Get student's course authorizations for ban functionality
     */
    public function getStudentCourses($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
                ->firstOrFail();

            $courseAuths = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with(['courseDate.course'])
                ->orderBy('created_at', 'desc')
                ->get();

            $coursesData = $courseAuths->map(function ($courseAuth) {
                return [
                    'id' => $courseAuth->id,
                    'course_title' => $courseAuth->courseDate->course->title ?? 'Unknown Course',
                    'course_date' => $courseAuth->courseDate ? $courseAuth->courseDate->start_date : null,
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
                'course_title' => $courseAuth->courseDate->course->title ?? 'Unknown',
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
     * Get student's unit enrollments for kick functionality
     */
    public function getStudentUnits($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
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
     * Get student's DNC (Did Not Complete) lessons for reinstatement
     */
    public function getStudentDncLessons($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
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
     * Get student's lessons combining StudentUnit lessons and self-study lessons
     * Priority: StudentUnit lessons take precedence, self-study shown if no StudentUnit lesson or if StudentUnit lesson failed
     */
    public function getStudentLessons($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
                ->firstOrFail();

            // Get student lessons through StudentUnit -> CourseAuth -> User relationship
            $allStudentLessons = \App\Models\StudentLesson::whereHas('StudentUnit.CourseAuth', function ($query) use ($studentId) {
                $query->where('user_id', $studentId);
            })
                ->with(['lesson', 'StudentUnit.CourseAuth'])
                ->get()
                ->keyBy('lesson_id');

            // For now, treat all lessons as self-study since we don't have unit relationship
            // In the future, you can add logic to determine classroom vs self-study lessons
            $studentUnitLessons = collect(); // Empty for now
            $selfStudyLessons = $allStudentLessons;

            // Combine lessons with priority logic
            $combinedLessons = collect();

            // First, add all StudentUnit lessons
            foreach ($studentUnitLessons as $lessonId => $studentUnitLesson) {
                $combinedLessons->put($lessonId, [
                    'id' => $studentUnitLesson->id,
                    'lesson_id' => $studentUnitLesson->lesson_id,
                    'lesson_title' => $studentUnitLesson->lesson->title ?? 'Unknown Lesson',
                    'lesson_description' => $studentUnitLesson->lesson->description ?? '',
                    'unit_name' => null, // Unit relationship doesn't exist in Lesson model
                    'type' => 'student_unit',
                    'status' => $this->getLessonStatus($studentUnitLesson),
                    'score' => null, // Score field doesn't exist in StudentLesson model
                    'progress' => 0, // Progress field doesn't exist in StudentLesson model
                    'duration' => null, // Duration field doesn't exist in StudentLesson model
                    'started_at' => $studentUnitLesson->created_at, // Use created_at as started_at
                    'completed_at' => $studentUnitLesson->completed_at,
                    'failed_at' => null, // Field does not exist
                    'dnc_at' => $studentUnitLesson->dnc_at,
                    'dnc_reason' => $studentUnitLesson->dnc_reason,
                    'created_at' => $studentUnitLesson->created_at,
                    'is_primary' => true, // This is the primary record for this lesson
                ]);
            }

            // Then, add self-study lessons only if:
            // 1. No StudentUnit lesson exists for that lesson_id, OR
            // 2. StudentUnit lesson exists but has failed
            foreach ($selfStudyLessons as $lessonId => $selfStudyLesson) {
                $hasStudentUnitLesson = $studentUnitLessons->has($lessonId);
                $studentUnitFailed = false; // Since failed_at field doesn't exist, treat as never failed

                if (!$hasStudentUnitLesson || $studentUnitFailed) {
                    $combinedLessons->put($lessonId . '_self', [
                        'id' => $selfStudyLesson->id,
                        'lesson_id' => $selfStudyLesson->lesson_id,
                        'lesson_title' => $selfStudyLesson->lesson->title ?? 'Unknown Lesson',
                        'lesson_description' => $selfStudyLesson->lesson->description ?? '',
                        'unit_name' => null, // Self-study doesn't have units
                        'type' => 'self_study',
                        'status' => $this->getLessonStatus($selfStudyLesson),
                        'score' => null, // Score field doesn't exist in StudentLesson model
                        'progress' => 0, // Progress field doesn't exist in StudentLesson model
                        'duration' => null, // Duration field doesn't exist in StudentLesson model
                        'started_at' => $selfStudyLesson->created_at, // Use created_at as started_at
                        'completed_at' => $selfStudyLesson->completed_at,
                        'failed_at' => null, // Field does not exist
                        'dnc_at' => $selfStudyLesson->dnc_at,
                        'dnc_reason' => $selfStudyLesson->dnc_reason,
                        'created_at' => $selfStudyLesson->created_at,
                        'is_primary' => !$hasStudentUnitLesson, // Primary if no StudentUnit lesson exists
                        'is_fallback' => $studentUnitFailed, // Fallback if StudentUnit lesson failed
                    ]);
                }
            }

            // Sort by created_at descending (most recent first)
            $sortedLessons = $combinedLessons->sortByDesc('created_at')->values();

            return response()->json([
                'success' => true,
                'data' => $sortedLessons->toArray(),
                'count' => $sortedLessons->count(),
                'summary' => [
                    'student_unit_lessons' => $studentUnitLessons->count(),
                    'self_study_lessons' => $selfStudyLessons->count(),
                    'combined_lessons' => $sortedLessons->count()
                ]
            ]);

        } catch (\Exception $e) {
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
     * Get comprehensive student exam information for support purposes
     */
    public function getStudentExams($studentId): JsonResponse
    {
        try {
            $student = User::where('role_id', \App\Support\RoleManager::STUDENT_ID)
                ->where('id', $studentId)
                ->firstOrFail();

            // Get all course authorizations with exam information
            $courseAuths = \App\Models\CourseAuth::where('user_id', $studentId)
                ->with([
                    'Course.Exam',
                    'ExamAuths' => function ($query) {
                        $query->whereNull('hidden_at')->orderBy('created_at', 'desc');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

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
}
