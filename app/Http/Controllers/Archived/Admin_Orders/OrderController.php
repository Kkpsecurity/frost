<?php

namespace App\Http\Controllers\Admin\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\PaymentType;
use App\Models\DiscountCode;
use App\Traits\PageMetaDataTrait;
use App\Services\RCache;

class OrderController extends Controller
{
    use PageMetaDataTrait;

    /**
     * Display a listing of orders
     */
    public function index(): View
    {
        return view('admin.orders.index');
    }

    /**
     * Get orders data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        $query = Order::with(['User', 'Course', 'PaymentType', 'CourseAuth'])
            ->select('orders.*');

        // Apply filters
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->status_filter === 'pending') {
                $query->whereNull('completed_at');
            } elseif ($request->status_filter === 'refunded') {
                $query->whereNotNull('refunded_at');
            }
        }

        if ($request->filled('payment_type_filter')) {
            $query->where('payment_type_id', $request->payment_type_filter);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('order_id', function ($order) {
                return '<a href="' . route('admin.orders.show', $order->id) . '" class="font-weight-bold">' . $order->id . '</a>';
            })
            ->addColumn('customer_name', function ($order) {
                return $order->User->fname . ' ' . $order->User->lname;
            })
            ->addColumn('course_name', function ($order) {
                return $order->Course->title ?? 'N/A';
            })
            ->addColumn('payment_status', function ($order) {
                if ($order->refunded_at) {
                    return '<span class="badge badge-danger">Refunded</span>';
                } elseif ($order->completed_at) {
                    return '<span class="badge badge-success">Paid</span>';
                } else {
                    return '<span class="badge badge-warning">Pending</span>';
                }
            })
            ->addColumn('course_auth_status', function ($order) {
                if ($order->CourseAuth) {
                    if ($order->CourseAuth->IsActive()) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-secondary">Inactive</span>';
                    }
                }
                return '<span class="badge badge-light">Not Created</span>';
            })
            ->addColumn('total_display', function ($order) {
                return '$' . number_format($order->total_price, 2);
            })
            ->addColumn('payment_method', function ($order) {
                return $order->PaymentType->name ?? 'N/A';
            })
            ->addColumn('created_date', function ($order) {
                return $order->created_at->format('M d, Y H:i');
            })
            ->addColumn('actions', function ($order) {
                $actions = '
                    <div class="btn-group" role="group">
                        <a href="' . route('admin.orders.show', $order->id) . '" class="btn btn-sm btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>';

                if (!$order->completed_at) {
                    $actions .= '
                        <button type="button" class="btn btn-sm btn-success" onclick="markAsPaid(' . $order->id . ')" title="Mark as Paid">
                            <i class="fas fa-check"></i>
                        </button>';
                }

                if ($order->CanRefund()) {
                    $actions .= '
                        <button type="button" class="btn btn-sm btn-warning" onclick="processRefund(' . $order->id . ')" title="Process Refund">
                            <i class="fas fa-undo"></i>
                        </button>';
                }

                $actions .= '
                        <a href="' . route('admin.orders.edit', $order->id) . '" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>';

                return $actions;
            })
            ->rawColumns(['order_id', 'payment_status', 'course_auth_status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new order
     */
    public function create(): View
    {
        $users = User::where('role_id', '>', 4)->get(); // Students only
        $courses = RCache::Courses();
        $paymentTypes = RCache::PaymentTypes();
        $discountCodes = RCache::DiscountCodes();

        return view('admin.orders.create', compact('users', 'courses', 'paymentTypes', 'discountCodes'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'payment_type_id' => 'required|integer|exists:payment_types,id',
            'course_price' => 'required|numeric|min:0',
            'discount_code_id' => 'nullable|integer|exists:discount_codes,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order = Order::create([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'payment_type_id' => $request->payment_type_id,
            'course_price' => $request->course_price,
            'discount_code_id' => $request->discount_code_id,
            'total_price' => $request->total_price,
        ]);

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): View
    {
        $order->load(['User', 'Course', 'PaymentType', 'DiscountCode', 'CourseAuth', 'RefundedBy']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order): View
    {
        $users = User::where('role_id', '>', 4)->get();
        $courses = RCache::Courses();
        $paymentTypes = RCache::PaymentTypes();
        $discountCodes = RCache::DiscountCodes();

        return view('admin.orders.edit', compact('order', 'users', 'courses', 'paymentTypes', 'discountCodes'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        // Prevent editing completed orders
        if ($order->completed_at) {
            return redirect()->back()->with('error', 'Cannot edit completed orders. Use refunds instead.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'payment_type_id' => 'required|integer|exists:payment_types,id',
            'course_price' => 'required|numeric|min:0',
            'discount_code_id' => 'nullable|integer|exists:discount_codes,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order->update($request->only([
            'user_id',
            'course_id',
            'payment_type_id',
            'course_price',
            'discount_code_id',
            'total_price'
        ]));

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order): JsonResponse
    {
        if ($order->completed_at) {
            return response()->json([
                'success' => false,
                'message' => 'Order is already marked as paid.'
            ]);
        }

        $order->update(['completed_at' => now()]);

        // Create CourseAuth if it doesn't exist
        if (!$order->CourseAuth) {
            $courseAuth = CourseAuth::create([
                'user_id' => $order->user_id,
                'course_id' => $order->course_id,
                'start_date' => now()->toDateString(),
            ]);

            $order->update(['course_auth_id' => $courseAuth->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order marked as paid and CourseAuth created.'
        ]);
    }

    /**
     * Process refund
     */
    public function processRefund(Request $request, Order $order): JsonResponse
    {
        if (!$order->CanRefund()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be refunded.'
            ]);
        }

        $order->update([
            'refunded_at' => now(),
            'refunded_by' => auth('admin')->user()->id,
        ]);

        // Revoke CourseAuth if exists
        if ($order->CourseAuth && $order->CourseAuth->IsActive()) {
            $order->CourseAuth->update([
                'disabled_at' => now(),
                'disabled_reason' => 'Order refunded',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order refunded successfully and CourseAuth revoked.'
        ]);
    }

    /**
     * Grant manual CourseAuth (separate from order)
     */
    public function grantManualCourseAuth(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'expire_date' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if user already has active CourseAuth for this course
        $existingAuth = CourseAuth::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->where(function ($query) {
                $query->whereNull('disabled_at')
                    ->whereNull('completed_at');
            })
            ->first();

        if ($existingAuth) {
            return redirect()->back()->with('error', 'User already has active CourseAuth for this course.');
        }

        CourseAuth::create([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'start_date' => now()->toDateString(),
            'expire_date' => $request->expire_date,
            'submitted_by' => auth('admin')->user()->id,
        ]);

        return redirect()->back()->with('success', 'Manual CourseAuth granted successfully.');
    }

    /**
     * Export orders data
     */
    public function export(Request $request, string $format): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Order::with(['User', 'Course', 'PaymentType', 'DiscountCode'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as DataTables
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->status_filter === 'pending') {
                $query->whereNull('completed_at');
            } elseif ($request->status_filter === 'refunded') {
                $query->whereNotNull('refunded_at');
            }
        }

        $orders = $query->get();

        $headers = [
            'Order ID',
            'Customer Name',
            'Course',
            'Payment Method',
            'Course Price',
            'Total Price',
            'Status',
            'Created Date',
            'Completed Date',
            'Refunded Date'
        ];

        $callback = function () use ($orders, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($orders as $order) {
                $status = 'Pending';
                if ($order->refunded_at) {
                    $status = 'Refunded';
                } elseif ($order->completed_at) {
                    $status = 'Completed';
                }

                fputcsv($file, [
                    $order->id,
                    $order->User->fname . ' ' . $order->User->lname,
                    $order->Course->title ?? 'N/A',
                    $order->PaymentType->name ?? 'N/A',
                    '$' . number_format($order->course_price, 2),
                    '$' . number_format($order->total_price, 2),
                    $status,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->completed_at?->format('Y-m-d H:i:s') ?? '',
                    $order->refunded_at?->format('Y-m-d H:i:s') ?? ''
                ]);
            }

            fclose($file);
        };

        $filename = 'orders_export_' . date('Y_m_d_H_i_s') . '.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
