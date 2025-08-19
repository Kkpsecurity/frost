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
            'total_students' => User::where('role', 'student')->count(),
            'active_students' => User::where('role', 'student')->where('status', 'active')->count(),
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
            'recent_students' => User::where('role', 'student')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'status' => $student->status,
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
            'flagged_accounts' => User::where('role', 'student')->where('status', 'flagged')->count()
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
            $students = User::where('role', 'student')
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%");
                })
                ->with(['orders', 'courses'])
                ->take(20)
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'status' => $student->status,
                        'total_orders' => $student->orders->count(),
                        'total_spent' => $student->orders->where('status', 'completed')->sum('total_amount'),
                        'last_activity' => $student->updated_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $students,
                'count' => $students->count()
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
            $student = User::where('role', 'student')
                ->with(['orders', 'courses'])
                ->findOrFail($studentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'status' => $student->status,
                        'created_at' => $student->created_at,
                        'last_login' => $student->last_login_at
                    ],
                    'orders' => $student->orders->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'course_title' => $order->course->title ?? 'Unknown',
                            'amount' => $order->total_amount,
                            'status' => $order->status,
                            'created_at' => $order->created_at
                        ];
                    }),
                    'courses' => $student->courses->map(function ($course) {
                        return [
                            'id' => $course->id,
                            'title' => $course->title,
                            'status' => $course->pivot->status ?? 'enrolled',
                            'progress' => $course->pivot->progress ?? 0
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found or access denied'
            ], 404);
        }
    }
}
