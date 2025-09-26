<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Course;
use App\Models\PaymentType;
use App\Models\DiscountCode;
use App\Support\Enum\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $filters = [
            'status' => $request->get('status', ''),
            'customer_id' => $request->get('customer_id', ''),
            'course_id' => $request->get('course_id', ''),
            'date_from' => $request->get('date_from', ''),
            'date_to' => $request->get('date_to', ''),
            'payment_type' => $request->get('payment_type', ''),
            'search' => $request->get('search', ''),
            'date_range' => $request->get('date_range', 'month'), // month, week, year, all
        ];

        // Build query with relationships
        $query = Order::with([
            'User',
            'Course', // Use the relationship instead of GetCourse
            'PaymentType',
            'DiscountCode',
            'RefundedBy'
        ]);

        // Apply status filter
        if ($filters['status']) {
            switch($filters['status']) {
                case 'Completed':
                    $query->whereNotNull('completed_at')->whereNull('refunded_at');
                    break;
                case 'Cancelled':
                    $query->whereNotNull('refunded_at');
                    break;
                case 'Processing':
                    $query->whereNull('completed_at')->whereNull('refunded_at');
                    break;
                case 'Active':
                    $query->whereNotNull('completed_at')->whereNull('refunded_at');
                    break;
            }
        }

        // Apply customer filter
        if ($filters['customer_id']) {
            $query->where('user_id', $filters['customer_id']);
        }

        // Apply course filter
        if ($filters['course_id']) {
            $query->where('course_id', $filters['course_id']);
        }

        // Apply payment type filter
        if ($filters['payment_type']) {
            $query->where('payment_type_id', $filters['payment_type']);
        }

        // Apply date range filter
        $this->applyDateRangeFilter($query, $filters['date_range'], $filters['date_from'], $filters['date_to']);

        // Apply search filter
        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('id', 'like', "%{$filters['search']}%") // Search by order ID
                  ->orWhereHas('User', function($userQuery) use ($filters) {
                      $userQuery->where('fname', 'like', "%{$filters['search']}%")
                               ->orWhere('lname', 'like', "%{$filters['search']}%")
                               ->orWhere('email', 'like', "%{$filters['search']}%");
                  })
                  ->orWhere('total_price', 'like', "%{$filters['search']}%");
            });
        }

        // Get paginated results
        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate(25)
                       ->appends($filters);

        // Get filter options
        $customers = User::orderBy('fname')
                        ->get(['id', 'fname', 'lname', 'email']);

        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get(['id', 'title']);

        $paymentTypes = PaymentType::orderBy('name')
                                  ->get(['id', 'name']);

        // Calculate statistics
        $stats = $this->calculateStatistics();

        $content = [
            'title' => 'Order Management',
            'orders' => $orders,
            'customers' => $customers,
            'courses' => $courses,
            'payment_types' => $paymentTypes,
            'order_statuses' => OrderStatus::lists(),
            'stats' => $stats,
            'filters' => $filters,
        ];

        return view('admin.orders.index', compact('content'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create(): View
    {
        $customers = User::orderBy('fname')
                        ->get(['id', 'fname', 'lname', 'email']);

        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get(['id', 'title', 'price']);

        $paymentTypes = PaymentType::orderBy('name')
                                  ->get(['id', 'name']);

        $discountCodes = DiscountCode::where('expires_at', '>=', now())
                                   ->orWhereNull('expires_at')
                                   ->orderBy('code')
                                   ->get(['id', 'code', 'percent', 'set_price']);

        $content = [
            'title' => 'Create New Order',
            'customers' => $customers,
            'courses' => $courses,
            'payment_types' => $paymentTypes,
            'discount_codes' => $discountCodes,
            'order_statuses' => OrderStatus::lists(),
        ];

        return view('admin.orders.create', compact('content'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id', // Changed from user_id to match the form
            'course_id' => 'required|exists:courses,id',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'discount_code_id' => 'nullable|exists:discount_codes,id',
            'custom_discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            // Notes removed since database doesn't have notes field
        ]);

        DB::beginTransaction();
        try {
            // Get course to calculate pricing
            $course = Course::findOrFail($validated['course_id']);

            // Calculate pricing with discount if applicable
            $coursePrice = $course->price ?? 0;
            $discountAmount = 0;

            // Handle custom discount first, then discount code
            if (!empty($validated['custom_discount'])) {
                $discountAmount = min($validated['custom_discount'], $coursePrice);
            } elseif ($validated['discount_code_id']) {
                $discountCode = DiscountCode::findOrFail($validated['discount_code_id']);
                if ($discountCode->percent && $discountCode->percent > 0) {
                    $discountAmount = ($coursePrice * $discountCode->percent) / 100;
                } elseif ($discountCode->set_price && $discountCode->set_price > 0) {
                    $discountAmount = $discountCode->set_price;
                }
                $discountAmount = min($discountAmount, $coursePrice);
            }

            $total = max(0, $coursePrice - $discountAmount);

            // Create order data matching existing table structure
            $orderData = [
                'user_id' => $validated['customer_id'], // Map customer_id to user_id
                'course_id' => $validated['course_id'],
                'course_price' => $coursePrice,
                'total_price' => $total, // Use total_price field from migration
            ];

            // Add payment type if provided
            if (!empty($validated['payment_type_id'])) {
                $orderData['payment_type_id'] = $validated['payment_type_id'];
            }

            // Add discount code if used
            if ($validated['discount_code_id'] && empty($validated['custom_discount'])) {
                $orderData['discount_code_id'] = $validated['discount_code_id'];
            }

            // Set status using timestamps
            $status = $validated['status'] ?? 'Processing';
            switch($status) {
                case 'Completed':
                case 'Active':
                    $orderData['completed_at'] = now();
                    break;
                case 'Cancelled':
                    $orderData['refunded_at'] = now();
                    break;
                // Processing is default (no timestamps set)
            }

            $order = Order::create($orderData);

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'total' => $order->total_price
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): View
    {
        $order->load([
            'User',
            'PaymentType',
            'DiscountCode',
            'RefundedBy'
        ]);

        $title = "Order #{$order->order_number}";

        // Define order statuses for the status dropdown
        $orderStatuses = [
            'New' => 'New',
            'Processing' => 'Processing',
            'Active' => 'Active',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled'
        ];

        return view('admin.orders.show', compact('order', 'title', 'orderStatuses'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order): View
    {
        $customers = User::orderBy('fname')
                        ->get(['id', 'fname', 'lname', 'email']);

        $courses = Course::where('is_active', true)
                        ->orderBy('title')
                        ->get(['id', 'title', 'price']);

        $paymentTypes = PaymentType::orderBy('name')
                                  ->get(['id', 'name']);

        $discountCodes = DiscountCode::where('expires_at', '>=', now())
                                   ->orWhereNull('expires_at')
                                   ->orderBy('code')
                                   ->get(['id', 'code', 'percent', 'set_price']);

        $content = [
            'title' => "Edit Order #{$order->order_number}",
            'order' => $order,
            'customers' => $customers,
            'courses' => $courses,
            'payment_types' => $paymentTypes,
            'discount_codes' => $discountCodes,
            'order_statuses' => OrderStatus::lists(),
        ];

        return view('admin.orders.edit', compact('content'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'discount_code_id' => 'nullable|exists:discount_codes,id',
            'custom_discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            // Notes removed since database doesn't have notes field
        ]);

        DB::beginTransaction();
        try {
            $updateData = [];

            // Update basic fields
            $updateData['user_id'] = $validated['customer_id'];
            $updateData['course_id'] = $validated['course_id'];

            if (!empty($validated['payment_type_id'])) {
                $updateData['payment_type_id'] = $validated['payment_type_id'];
            }

            // Recalculate pricing if course or discount changed
            if ($order->course_id != $validated['course_id'] ||
                $order->discount_code_id != ($validated['discount_code_id'] ?? null)) {

                $course = Course::findOrFail($validated['course_id']);
                $coursePrice = $course->price ?? 0;
                $discountAmount = 0;

                # Handle custom discount first, then discount code
                if (!empty($validated['custom_discount'])) {
                    $discountAmount = min($validated['custom_discount'], $coursePrice);
                    $updateData['discount_code_id'] = null; // Clear discount code if custom discount used
                } elseif ($validated['discount_code_id']) {
                    $discountCode = DiscountCode::findOrFail($validated['discount_code_id']);
                    if ($discountCode->percent && $discountCode->percent > 0) {
                        $discountAmount = ($coursePrice * $discountCode->percent) / 100;
                    } elseif ($discountCode->set_price && $discountCode->set_price > 0) {
                        $discountAmount = $discountCode->set_price;
                    }
                    $discountAmount = min($discountAmount, $coursePrice);
                    $updateData['discount_code_id'] = $validated['discount_code_id'];
                } else {
                    $updateData['discount_code_id'] = null;
                }

                $total = max(0, $coursePrice - $discountAmount);

                $updateData['course_price'] = $coursePrice;
                $updateData['total_price'] = $total;
            }

            // Handle status updates using timestamps
            if ($validated['status']) {
                switch($validated['status']) {
                    case 'Completed':
                    case 'Active':
                        $updateData['completed_at'] = now();
                        $updateData['refunded_at'] = null;
                        break;
                    case 'Cancelled':
                        $updateData['refunded_at'] = now();
                        break;
                    case 'Processing':
                        $updateData['completed_at'] = null;
                        $updateData['refunded_at'] = null;
                        break;
                }
            }

            $order->update($updateData);

            DB::commit();

            Log::info('Order updated successfully', [
                'order_id' => $order->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()
                           ->route('admin.orders.show', $order)
                           ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order update failed: ' . $e->getMessage());
            return back()
                        ->withInput()
                        ->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order): RedirectResponse
    {
        try {
            $orderNumber = $order->order_number;
            $order->delete();

            Log::info('Order deleted successfully', [
                'order_number' => $orderNumber
            ]);

            return redirect()->route('admin.orders.index')
                           ->with('success', "Order #{$orderNumber} deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Order deletion failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string',
            // Notes removed since database doesn't have notes field
        ]);

        try {
            $oldStatus = $order->status;
            $updateData = [];

            // Map status to appropriate timestamp fields
            switch($request->status) {
                case 'Completed':
                case 'Active':
                    $updateData['completed_at'] = now();
                    $updateData['refunded_at'] = null;
                    break;
                case 'Cancelled':
                    $updateData['refunded_at'] = now();
                    break;
                case 'Processing':
                    $updateData['completed_at'] = null;
                    $updateData['refunded_at'] = null;
                    break;
            }

            // Skip notes handling since database doesn't have notes field
            // Notes can be handled in a separate system if needed

            $order->update($updateData);

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]);

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update order status: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark order as complete
     */
    public function markComplete(Order $order): RedirectResponse
    {
        try {
            $order->update([
                'completed_at' => now(),
                'refunded_at' => null, // Clear refunded status if it was cancelled
            ]);

            Log::info('Order marked as complete', ['order_id' => $order->id]);

            return back()->with('success', 'Order marked as completed.');

        } catch (\Exception $e) {
            Log::error('Order completion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to complete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order): RedirectResponse
    {
        try {
            $order->update([
                'refunded_at' => now(),
            ]);

            Log::info('Order cancelled', ['order_id' => $order->id]);

            return back()->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
    }

    /**
     * Customer search for AJAX
     */
    public function customerSearch(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $customers = User::where(function($query) use ($search) {
            $query->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })
        ->limit(20)
        ->get(['id', 'fname', 'lname', 'email'])
        ->map(function($user) {
            return [
                'id' => $user->id,
                'text' => "{$user->fname} {$user->lname} ({$user->email})"
            ];
        });

        return response()->json($customers);
    }

    /**
     * Apply date range filter to query
     */
    private function applyDateRangeFilter($query, $dateRange, $dateFrom = null, $dateTo = null)
    {
        switch ($dateRange) {
            case 'week':
                $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereBetween('created_at', [
                    now()->startOfYear(),
                    now()->endOfYear()
                ]);
                break;
            case 'custom':
                if ($dateFrom && $dateTo) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($dateFrom)->startOfDay(),
                        Carbon::parse($dateTo)->endOfDay()
                    ]);
                }
                break;
            case 'all':
                // No date filter - show all orders
                break;
        }
    }

    /**
     * Calculate order statistics
     */
    private function calculateStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'active_orders' => Order::whereNotNull('completed_at')->whereNull('refunded_at')->count(),
            'completed_orders' => Order::whereNotNull('completed_at')->whereNull('refunded_at')->count(),
            'cancelled_orders' => Order::whereNotNull('refunded_at')->count(),
            'processing_orders' => Order::whereNull('completed_at')->whereNull('refunded_at')->count(),
            'total_revenue' => Order::whereNotNull('completed_at')->whereNull('refunded_at')->sum('total_price'),
            'month_revenue' => Order::whereNotNull('completed_at')
                                   ->whereNull('refunded_at')
                                   ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                                   ->sum('total_price'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'week_orders' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_orders' => Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Generate invoice for an order
     */
    public function generateInvoice(Order $order)
    {
        // For now, redirect to show page or return a placeholder
        return redirect()->route('admin.orders.show', $order)
            ->with('info', 'Invoice generation feature coming soon.');
    }

    /**
     * Generate receipt for an order
     */
    public function generateReceipt(Order $order)
    {
        // For now, redirect to show page or return a placeholder
        return redirect()->route('admin.orders.show', $order)
            ->with('info', 'Receipt generation feature coming soon.');
    }

    /**
     * Download invoice for an order
     */
    public function downloadInvoice(Order $order)
    {
        return redirect()->route('admin.orders.show', $order)
            ->with('info', 'Invoice download feature coming soon.');
    }

    /**
     * Download receipt for an order
     */
    public function downloadReceipt(Order $order)
    {
        return redirect()->route('admin.orders.show', $order)
            ->with('info', 'Receipt download feature coming soon.');
    }

    /**
     * Send invoice via email
     */
    public function sendInvoice(Order $order)
    {
        return redirect()->route('admin.orders.show', $order)
            ->with('info', 'Invoice email feature coming soon.');
    }

    /**
     * Duplicate an existing order
     */
    public function duplicate(Order $order)
    {
        // Load the order with its relationships
        $order->load(['User', 'Course', 'PaymentType', 'DiscountCode']);

        // For now, redirect to create page with query parameters to pre-fill the form
        $queryParams = [
            'user_id' => $order->user_id,
            'course_id' => $order->course_id,
            'payment_type_id' => $order->payment_type_id,
            'course_price' => $order->course_price,
            'discount_code_id' => $order->discount_code_id,
            'duplicate_from' => $order->id,
        ];

        return redirect()->route('admin.orders.create', $queryParams)
            ->with('info', 'Pre-filling form from Order #' . ($order->order_number ?? 'ORD-' . $order->id));
    }
}
