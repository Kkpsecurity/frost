<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Notifications\Account\ProfileUpdatedNotification;
use App\Events\Payment\PaymentMethodAdded;
use App\Events\Payment\PaymentMethodRemoved;
use App\Notifications\Payment\DefaultPaymentUpdatedNotification;
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
     * Add Stripe payment method
     */
    public function addStripePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'set_default' => 'boolean'
        ]);

        $user = Auth::user();

        try {
            // Initialize Stripe with secret key from settings
            $stripeSecretKey = setting('payments.stripe.test_secret_key'); // Get from admin settings
            if (empty($stripeSecretKey)) {
                return response()->json(['success' => false, 'message' => 'Stripe is not configured']);
            }

            \Stripe\Stripe::setApiKey($stripeSecretKey);

            // Retrieve the payment method from Stripe
            $paymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);

            // Get current saved payment methods
            $userPrefs = $user->UserPrefs->pluck('value', 'key')->toArray();
            $savedMethods = isset($userPrefs['saved_payment_methods'])
                ? json_decode($userPrefs['saved_payment_methods'], true) ?? []
                : [];

            // If setting as default, unset other defaults
            if ($request->boolean('set_default')) {
                foreach ($savedMethods as &$method) {
                    $method['is_default'] = false;
                }
            }

            // Add new payment method
            $newMethod = [
                'id' => 'stripe_' . $paymentMethod->id,
                'stripe_id' => $paymentMethod->id,
                'type' => 'card',
                'brand' => $paymentMethod->card->brand,
                'last4' => $paymentMethod->card->last4,
                'exp_month' => $paymentMethod->card->exp_month,
                'exp_year' => $paymentMethod->card->exp_year,
                'is_default' => $request->boolean('set_default') || empty($savedMethods),
                'created_at' => now()->format('c')
            ];

            $savedMethods[] = $newMethod;

            // Save to user preferences
            $user->UserPrefs()->updateOrCreate(
                ['key' => 'saved_payment_methods'],
                ['value' => json_encode($savedMethods)]
            );

            // Dispatch event to send notification
            event(new PaymentMethodAdded($user, $newMethod));

            return response()->json(['success' => true, 'message' => 'Payment method added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add payment method: ' . $e->getMessage()]);
        }
    }

    /**
     * Connect PayPal account
     */
    public function connectPayPal(Request $request)
    {
        // TODO: Implement PayPal OAuth flow
        // For now, redirect back with a message
        return redirect()->route('account.index', ['section' => 'payments'])
            ->with('error', 'PayPal integration is coming soon!');
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod(Request $request)
    {
        $request->validate([
            'method_id' => 'required|string'
        ]);

        $user = Auth::user();

        try {
            // Get current saved payment methods
            $userPrefs = $user->UserPrefs->pluck('value', 'key')->toArray();
            $savedMethods = isset($userPrefs['saved_payment_methods'])
                ? json_decode($userPrefs['saved_payment_methods'], true) ?? []
                : [];

            $found = false;
            foreach ($savedMethods as &$method) {
                if ($method['id'] === $request->method_id) {
                    $method['is_default'] = true;
                    $found = true;
                } else {
                    $method['is_default'] = false;
                }
            }

            if (!$found) {
                return response()->json(['success' => false, 'message' => 'Payment method not found']);
            }

            // Save updated methods
            $user->UserPrefs()->updateOrCreate(
                ['key' => 'saved_payment_methods'],
                ['value' => json_encode($savedMethods)]
            );

            // Send notification about default payment method change
            // Find the method that was set as default
            $defaultMethod = null;
            foreach ($savedMethods as $method) {
                if ($method['is_default']) {
                    $defaultMethod = $method;
                    break;
                }
            }
            if ($defaultMethod) {
                $user->notify(new DefaultPaymentUpdatedNotification($defaultMethod));
            }

            return response()->json(['success' => true, 'message' => 'Default payment method updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update default payment method: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete payment method
     */
    public function deletePaymentMethod(Request $request)
    {
        $request->validate([
            'method_id' => 'required|string'
        ]);

        $user = Auth::user();

        try {
            // Get current saved payment methods
            $userPrefs = $user->UserPrefs->pluck('value', 'key')->toArray();
            $savedMethods = isset($userPrefs['saved_payment_methods'])
                ? json_decode($userPrefs['saved_payment_methods'], true) ?? []
                : [];

            $methodToDelete = null;
            foreach ($savedMethods as $index => $method) {
                if ($method['id'] === $request->method_id) {
                    $methodToDelete = $method;
                    unset($savedMethods[$index]);
                    break;
                }
            }

            if (!$methodToDelete) {
                return response()->json(['success' => false, 'message' => 'Payment method not found']);
            }

            // If this was a Stripe payment method, detach it from Stripe
            if (isset($methodToDelete['stripe_id'])) {
                $stripeSecretKey = setting('payments.stripe.test_secret_key');
                if (!empty($stripeSecretKey)) {
                    \Stripe\Stripe::setApiKey($stripeSecretKey);
                    try {
                        $paymentMethod = \Stripe\PaymentMethod::retrieve($methodToDelete['stripe_id']);
                        $paymentMethod->detach();
                    } catch (\Exception $stripeError) {
                        // Log the error but don't fail the whole operation
                        \Log::warning('Failed to detach Stripe payment method: ' . $stripeError->getMessage());
                    }
                }
            }

            // Re-index array and save
            $savedMethods = array_values($savedMethods);

            $user->UserPrefs()->updateOrCreate(
                ['key' => 'saved_payment_methods'],
                ['value' => json_encode($savedMethods)]
            );

            // Dispatch event to send notification
            event(new PaymentMethodRemoved($user, $methodToDelete));

            return response()->json(['success' => true, 'message' => 'Payment method deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete payment method: ' . $e->getMessage()]);
        }
    }

    /**  {
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
        $notificationsData = $this->getNotificationsData($user);
        $ordersData = $this->getOrdersData($user);
        $paymentsData = $this->getPaymentsData($user); // New payments section

        // Payment gateway configuration
        $stripeEnabled = !empty(setting('payments.stripe.test_secret_key')) || !empty(setting('payments.stripe.live_secret_key'));
        $paypalEnabled = !empty(setting('payments.paypal.client_id'));

        return view('frontend.account.index', compact(
            'user',
            'activeSection',
            'profileData',
            'settingsData',
            'notificationsData',
            'ordersData',
            'paymentsData',
            'stripeEnabled',
            'paypalEnabled'
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
                'current_avatar' => $user->getAvatar('regular'),
                'has_custom_avatar' => $user->hasCustomAvatar(),
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
            'preferences' => $user->UserPrefs->pluck('pref_value', 'pref_name')->toArray(),
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
     * Get notifications preferences data
     */
    private function getNotificationsData($user)
    {
        // Get user preferences
        $userPrefs = $user->UserPrefs->pluck('pref_value', 'pref_name')->toArray();

        // Get channel preferences (default all enabled)
        $channels = [
            'database' => isset($userPrefs['notification_channel_database']) ? (bool)$userPrefs['notification_channel_database'] : true,
            'mail' => isset($userPrefs['notification_channel_mail']) ? (bool)$userPrefs['notification_channel_mail'] : true,
            'browser' => isset($userPrefs['notification_channel_browser']) ? (bool)$userPrefs['notification_channel_browser'] : true,
        ];

        // Get individual notification preferences
        $notifications = [];
        $notificationConfig = config('user_notifications.notifications', []);

        foreach ($notificationConfig as $category => $categoryNotifications) {
            foreach ($categoryNotifications as $notification) {
                if ($notification['user_controllable'] ?? false) {
                    $key = $notification['key'];
                    // Default to enabled if not set
                    $notifications[$key] = isset($userPrefs['notification_' . $key]) ? (bool)$userPrefs['notification_' . $key] : true;
                }
            }
        }

        return [
            'channels' => $channels,
            'notifications' => $notifications,
        ];
    }

    /**
     * Get alerts tab data
     */
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

        // Get saved payment methods (this will be expanded with actual Stripe/PayPal integrations)
        $paymentMethods = [];
        $savedMethods = [];

        // TODO: Get actual saved payment methods from Stripe and PayPal
        // For now, we'll check if user has stored payment method preferences
        $userPrefs = $user->UserPrefs->pluck('value', 'key')->toArray();

        // Example saved payment methods (you'll replace this with real Stripe/PayPal data)
        if (isset($userPrefs['saved_payment_methods'])) {
            $savedMethodsData = json_decode($userPrefs['saved_payment_methods'], true) ?? [];

            foreach ($savedMethodsData as $method) {
                $savedMethods[] = [
                    'id' => $method['id'],
                    'type' => $method['type'], // 'card' or 'paypal'
                    'brand' => $method['brand'] ?? 'visa',
                    'last4' => $method['last4'] ?? '0000',
                    'exp_month' => $method['exp_month'] ?? null,
                    'exp_year' => $method['exp_year'] ?? null,
                    'email' => $method['email'] ?? null, // for PayPal
                    'is_default' => $method['is_default'] ?? false,
                    'created_at' => $method['created_at'] ?? now()
                ];
            }
        }

        // Check if user has any completed payments to show they have payment history
        $hasPaymentHistory = $orders->where('completed_at', '!=', null)->count() > 0;

        if ($hasPaymentHistory && empty($savedMethods)) {
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
            'saved_methods' => $savedMethods, // New: actual saved payment methods
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'student_info.initials' => 'nullable|string|max:5',
            'student_info.suffix' => 'nullable|string|max:10',
            'student_info.dob' => 'nullable|date|before:today',
            'student_info.phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'use_gravatar' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Update basic fields
        $user->fname = $request->first_name;
        $user->lname = $request->last_name;
        $user->email = $request->email;

        // Update student_info JSON field
        $studentInfo = $user->student_info ?? [];
        if ($request->has('student_info')) {
            $studentInfo['initials'] = $request->input('student_info.initials');
            $studentInfo['suffix'] = $request->input('student_info.suffix');
            $studentInfo['dob'] = $request->input('student_info.dob');
            $studentInfo['phone'] = $request->input('student_info.phone');
        }
        $user->student_info = $studentInfo;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->use_gravatar = false;
        }

        // Handle gravatar preference
        if ($request->has('use_gravatar')) {
            $user->use_gravatar = $request->boolean('use_gravatar');
            if ($user->use_gravatar) {
                $user->avatar = null; // Clear uploaded avatar if using gravatar
            }
        }

        $user->save();

        // Track what was updated for notification
        $updatedFields = [];
        if ($request->filled('fname') || $request->filled('lname')) {
            $updatedFields[] = 'name';
        }
        if ($request->filled('student_info')) {
            $updatedFields[] = 'student_info';
        }
        if ($request->hasFile('avatar') || $request->has('use_gravatar')) {
            $updatedFields[] = 'avatar';
        }

        // Send notification
        $user->notify(new ProfileUpdatedNotification($updatedFields));

        return redirect()->route('account.index', ['section' => 'profile'])
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // Update email preferences
        $user->email_opt_in = $request->boolean('email_opt_in');

        // Update notification settings (stored in user_prefs)
        if ($request->has('notifications')) {
            foreach ($request->notifications as $key => $value) {
                \App\Models\UserPref::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'pref_name' => 'notification_' . $key,
                    ],
                    [
                        'pref_value' => $value ? '1' : '0',
                    ]
                );
            }
        }

        // Update preferences (stored in user_prefs)
        if ($request->has('preferences')) {
            foreach ($request->preferences as $key => $value) {
                \App\Models\UserPref::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'pref_name' => $key,
                    ],
                    [
                        'pref_value' => $value,
                    ]
                );
            }
        }

        // Update privacy settings (stored in user_prefs)
        if ($request->has('profile_visibility')) {
            \App\Models\UserPref::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'pref_name' => 'profile_visibility',
                ],
                [
                    'pref_value' => $request->profile_visibility,
                ]
            );
        }

        $user->save();

        return redirect()->route('account.index', ['section' => 'settings'])
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        // Get all notification definitions from config
        $notificationConfig = config('user_notifications.notifications', []);
        $allNotifications = [];

        // Flatten all notifications from all categories
        foreach ($notificationConfig as $category => $notifications) {
            foreach ($notifications as $notification) {
                if ($notification['user_controllable'] ?? false) {
                    $allNotifications[$notification['key']] = $notification;
                }
            }
        }

        // Update global channel preferences
        if ($request->has('channels')) {
            foreach ($request->channels as $channel => $enabled) {
                \App\Models\UserPref::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'pref_name' => 'notification_channel_' . $channel,
                    ],
                    [
                        'pref_value' => $enabled ? '1' : '0',
                    ]
                );
            }
        }

        // Update individual notification preferences
        // Only allow updating notifications that are user_controllable
        if ($request->has('notifications')) {
            foreach ($request->notifications as $key => $enabled) {
                // Verify this notification is user_controllable
                if (isset($allNotifications[$key])) {
                    \App\Models\UserPref::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'pref_name' => 'notification_' . $key,
                        ],
                        [
                            'pref_value' => $enabled ? '1' : '0',
                        ]
                    );
                }
            }

            // Disable any notifications that weren't checked (user unchecked them)
            foreach ($allNotifications as $key => $notification) {
                if (!isset($request->notifications[$key])) {
                    \App\Models\UserPref::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'pref_name' => 'notification_' . $key,
                        ],
                        [
                            'pref_value' => '0',
                        ]
                    );
                }
            }
        } else {
            // If no notifications are checked, disable all user_controllable notifications
            foreach ($allNotifications as $key => $notification) {
                \App\Models\UserPref::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'pref_name' => 'notification_' . $key,
                    ],
                    [
                        'pref_value' => '0',
                    ]
                );
            }
        }

        return redirect()->route('account.index', ['section' => 'notifications'])
            ->with('success', 'Notification preferences updated successfully!');
    }

    /**
     * Download invoice for an order
     */
    public function downloadInvoice($orderId)
    {
        $user = Auth::user();

        // Find the order and ensure it belongs to the authenticated user
        $order = \App\Models\Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with(['Course', 'PaymentType'])
            ->firstOrFail();

        // For now, redirect back to account with a message
        // In the future, this could generate and return a PDF invoice
        return redirect()->route('account.index', ['tab' => 'orders'])
            ->with('info', 'Invoice download feature coming soon. Order #' . $order->id . ' details are available in your order history.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Mark a single notification as read and redirect
     */
    public function markNotificationRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);

        // Mark as read
        $notification->markAsRead();

        // Redirect to the notification's URL if available, otherwise to notifications page
        $redirectUrl = $notification->data['url'] ?? route('account.index', ['section' => 'notifications']);

        return redirect($redirectUrl);
    }
}
