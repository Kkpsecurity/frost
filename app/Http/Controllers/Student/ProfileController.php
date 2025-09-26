<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user account dashboard with sidebar navigation
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeSection = $request->get('section', 'profile'); // Default to profile section

        // Load user relationships
        $user->load(['CourseAuths.course', 'UserPrefs', 'Role']);

        // Get user data for each section
        $profileData = $this->getProfileData($user);
        $settingsData = $this->getSettingsData($user);
        $alertsData = $this->getAlertsData($user);
        $ordersData = $this->getOrdersData($user);
        $paymentsData = $this->getPaymentsData($user); // New payments section

        return view('student.account.index', compact(
            'user',
            'activeSection',
            'profileData',
            'settingsData',
            'alertsData',
            'ordersData',
            'paymentsData'
        ));
    }

    /**
     * Get profile tab data
     */
    private function getProfileData($user)
    {
        return [
            'basic_info' => [
                'first_name' => $user->fname,
                'last_name' => $user->lname,
                'email' => $user->email,
                'full_name' => $user->fullname(),
                'role' => $user->GetRole()->title ?? 'Student',
                'member_since' => $user->created_at->format('F Y'),
                'last_login' => $user->updated_at->format('M j, Y g:i A'),
            ],
            'avatar' => [
                'current_avatar' => $user->avatar_url ?? null,
                'use_gravatar' => $user->use_gravatar ?? false,
            ],
            'student_info' => $user->student_info ?? [],
            'email_verified' => $user->email_verified_at !== null,
            'is_active' => $user->is_active,
        ];
    }

    /**
     * Get settings tab data
     */
    private function getSettingsData($user)
    {
        return [
            'email_preferences' => [
                'email_opt_in' => $user->email_opt_in ?? false,
            ],
            'preferences' => $user->UserPrefs->pluck('value', 'key')->toArray(),
            'privacy_settings' => [
                'profile_visibility' => 'private', // default
            ],
            'notification_settings' => [
                'course_updates' => true,
                'assignment_reminders' => true,
                'system_announcements' => true,
            ],
        ];
    }

    /**
     * Get alerts tab data
     */
    private function getAlertsData($user)
    {
        // Get recent alerts/notifications
        $recentAlerts = collect([
            [
                'id' => 1,
                'type' => 'info',
                'title' => 'Course Enrollment',
                'message' => 'You have been enrolled in a new course',
                'created_at' => now()->subDays(2),
                'read' => false,
            ],
            [
                'id' => 2,
                'type' => 'warning',
                'title' => 'Profile Update Required',
                'message' => 'Please update your profile information',
                'created_at' => now()->subWeek(),
                'read' => true,
            ],
        ]);

        return [
            'recent_alerts' => $recentAlerts,
            'unread_count' => $recentAlerts->where('read', false)->count(),
            'alert_preferences' => [
                'email_alerts' => true,
                'browser_notifications' => false,
            ],
        ];
    }

    /**
     * Get orders tab data
     */
    private function getOrdersData($user)
    {
        // Get course enrollments and orders
        $courseAuths = $user->CourseAuths()->with('course')->get();

        $orders = $courseAuths->map(function ($courseAuth) {
            // Handle Unix timestamp format for created_at
            $enrolledDate = 'Unknown';
            if ($courseAuth->created_at) {
                if (is_numeric($courseAuth->created_at)) {
                    // Unix timestamp
                    $enrolledDate = \Carbon\Carbon::createFromTimestamp($courseAuth->created_at)->format('M j, Y');
                } else {
                    // Already a Carbon instance
                    $enrolledDate = $courseAuth->created_at->format('M j, Y');
                }
            }

            return [
                'id' => $courseAuth->id,
                'course_name' => $courseAuth->course->title ?? 'Unknown Course',
                'course_code' => $courseAuth->course->title ?? 'N/A',
                'status' => $courseAuth->IsActive() ? 'Active' : 'Inactive',
                'enrolled_date' => $enrolledDate,
                'price' => '$299.00', // Placeholder - add actual pricing
                'completion_status' => $courseAuth->completed_at ? 'Completed' : 'In Progress',
            ];
        });

        return [
            'course_enrollments' => $orders,
            'total_courses' => $courseAuths->count(),
            'active_courses' => $courseAuths->filter(function ($courseAuth) {
                return $courseAuth->IsActive();
            })->count(),
            'completed_courses' => $courseAuths->whereNotNull('completed_at')->count(),
            'order_history' => [], // Placeholder for future order system
        ];
    }

    /**
     * Get payments section data
     */
    private function getPaymentsData($user)
    {
        // Get actual user orders from database
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with(['Course', 'PaymentType'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Build payment history from actual orders
        $paymentHistory = $orders->map(function ($order) {
            // Get the payment details
            $payment = $order->GetPayment();

            // Format the order data
            return [
                'id' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'date' => $order->created_at->format('M j, Y'),
                'description' => 'Course Enrollment - ' . ($order->Course->title ?? 'Unknown Course'),
                'amount' => '$' . number_format($order->total_price, 2),
                'status' => $order->completed_at ? 'paid' : 'pending',
                'payment_method' => $order->PaymentType->name ?? 'Unknown',
                'refunded' => $order->refunded_at !== null,
                'refund_date' => $order->refunded_at ? $order->refunded_at->format('M j, Y') : null,
                'download_url' => route('student.invoice', $order->id) ?? '#'
            ];
        })->toArray();

        // Get user's billing info from student_info if available
        $studentInfo = $user->student_info ?? [];
        $billingAddress = [
            'line1' => $studentInfo['address'] ?? '',
            'line2' => $studentInfo['address2'] ?? '',
            'city' => $studentInfo['city'] ?? '',
            'state' => $studentInfo['state'] ?? '',
            'postal_code' => $studentInfo['zip'] ?? '',
            'country' => $studentInfo['country'] ?? 'US',
            'phone' => $studentInfo['phone'] ?? ''
        ];

        // For now, payment methods are placeholder until you implement stored payment methods
        // You can expand this when you add payment method storage
        $paymentMethods = [];

        // Check if user has any completed payments to show they have payment history
        $hasPaymentHistory = $orders->where('completed_at', '!=', null)->count() > 0;

        if ($hasPaymentHistory) {
            $paymentMethods[] = [
                'id' => 'historical',
                'type' => 'historical',
                'brand' => 'various',
                'last4' => '****',
                'exp_month' => null,
                'exp_year' => null,
                'is_default' => false,
                'note' => 'Payment methods from completed orders'
            ];
        }

        // Calculate order statistics
        $totalSpent = $orders->where('completed_at', '!=', null)->sum('total_price');
        $totalRefunded = $orders->where('refunded_at', '!=', null)->sum('total_price');

        return [
            'payment_methods' => $paymentMethods,
            'payment_history' => $paymentHistory,
            'billing_address' => $billingAddress,
            'order_stats' => [
                'total_orders' => $orders->count(),
                'completed_orders' => $orders->where('completed_at', '!=', null)->count(),
                'pending_orders' => $orders->where('completed_at', null)->count(),
                'refunded_orders' => $orders->where('refunded_at', '!=', null)->count(),
                'total_spent' => '$' . number_format($totalSpent, 2),
                'total_refunded' => '$' . number_format($totalRefunded, 2)
            ],
            'subscription_status' => [
                'active' => false, // Set to true if you have subscription system
                'plan' => null,
                'next_billing_date' => null,
                'amount' => null
            ]
        ];
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
        ]);

        return redirect()->route('account.index', ['tab' => 'profile'])
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // Update email preferences
        $user->update([
            'email_opt_in' => $request->boolean('email_opt_in'),
        ]);

        return redirect()->route('account.index', ['tab' => 'settings'])
            ->with('success', 'Settings updated successfully!');
    }
}
