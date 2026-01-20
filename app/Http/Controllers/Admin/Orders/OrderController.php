<?php

namespace App\Http\Controllers\Admin\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Course;
use App\Traits\PageMetaDataTrait;

class OrderController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $query = Order::with(['User', 'Course', 'PaymentType', 'DiscountCode']);

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at')->whereNull('refunded_at');
            } elseif ($request->status === 'cancelled') {
                $query->whereNotNull('refunded_at');
            } elseif ($request->status === 'processing') {
                $query->whereNull('completed_at')->whereNull('refunded_at');
            }
        }

        // Filter by course
        if ($request->has('course_id') && $request->course_id !== '') {
            $query->where('course_id', $request->course_id);
        }

        // Search by user
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('User', function($q) use ($search) {
                $q->where('fname', 'ILIKE', "%{$search}%")
                  ->orWhere('lname', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get courses for filter
        $courses = Course::where('is_active', true)->orderBy('title')->get(['id', 'title']);

        // Statistics
        $stats = [
            'total' => Order::count(),
            'completed' => Order::whereNotNull('completed_at')->whereNull('refunded_at')->count(),
            'processing' => Order::whereNull('completed_at')->whereNull('refunded_at')->count(),
            'cancelled' => Order::whereNotNull('refunded_at')->count(),
            'total_revenue' => Order::whereNotNull('completed_at')
                                   ->whereNull('refunded_at')
                                   ->sum('total_price'),
        ];

        $content = array_merge([
            'orders' => $orders,
            'courses' => $courses,
            'stats' => $stats,
            'filters' => [
                'status' => $request->status,
                'course_id' => $request->course_id,
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
        ], self::renderPageMeta('Orders Management'));

        return view('admin.orders.index', compact('content'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $content = array_merge([], self::renderPageMeta('Create Order'));

        return view('admin.orders.create', compact('content'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Order creation not yet implemented.');
    }

    /**
     * Display the specified order
     */
    public function show($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $order = Order::with(['User', 'Course', 'PaymentType', 'DiscountCode', 'CourseAuth', 'RefundedBy'])
                     ->findOrFail($orderId);

        $content = array_merge([
            'order' => $order,
        ], self::renderPageMeta("Order #{$order->order_number}"));

        return view('admin.orders.show', compact('content'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $content = array_merge([
            'order_id' => $orderId,
        ], self::renderPageMeta("Edit Order #$orderId"));

        return view('admin.orders.edit', compact('content'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, $orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Order update not yet implemented.');
    }

    /**
     * Remove the specified order
     */
    public function destroy($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('admin.orders.index')
                        ->with('info', 'Order deletion not yet implemented.');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Status update not yet implemented.');
    }

    /**
     * Mark order as complete
     */
    public function markComplete($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Mark complete not yet implemented.');
    }

    /**
     * Cancel order
     */
    public function cancel($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Order cancellation not yet implemented.');
    }

    /**
     * Process refund
     */
    public function refund(Request $request, $orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Refund processing not yet implemented.');
    }

    /**
     * Duplicate order
     */
    public function duplicate($orderId)
    {
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        return back()->with('info', 'Order duplication not yet implemented.');
    }
}
