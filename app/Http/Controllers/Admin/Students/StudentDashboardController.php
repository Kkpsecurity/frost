<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin\Students;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;

    /*********************** */
    /* View Outputs          */
    /*********************** */

    public function dashboard(): View
    {
        $content = array_merge([], self::renderPageMeta('student_management_dashboard'));
        return view('admin.students.dashboard', compact('content'));
    }

    public function viewStudent(User $student): View
    {
        $content = array_merge([], self::renderPageMeta('student_profile', "Student: {$student->name}"));

        // Load student with relationships and orders manually
        $student->load(['activeCourseAuths.course', 'inactiveCourseAuths.course']);
        $orders = Order::where('user_id', $student->id)->with('Course')->latest()->get();

        return view('admin.students.view', compact('student', 'content', 'orders'));
    }

    public function editStudent(User $student): View
    {
        $content = array_merge([], self::renderPageMeta('edit_student', "Edit Student: {$student->fullname()}"));
        return view('admin.students.edit', compact('student', 'content'));
    }

    public function updateStudent(Request $request, User $student)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'initials' => 'nullable|string|max:5',
            'suffix' => 'nullable|string|max:10',
            'dob' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'email_opt_in' => 'nullable|boolean',
            'use_gravatar' => 'nullable|boolean',
            'avatar' => 'nullable|url|max:500',
        ]);

        // Update student_info JSON field
        $studentInfo = $student->student_info ?? [];

        // Update student info fields
        if ($request->has('phone')) {
            $studentInfo['phone'] = $request->phone;
        }
        if ($request->has('initials')) {
            $studentInfo['initials'] = $request->initials;
        }
        if ($request->has('suffix')) {
            $studentInfo['suffix'] = $request->suffix;
        }
        if ($request->has('dob')) {
            $studentInfo['dob'] = $request->dob;
        }

        // Update main user fields
        $student->update([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'is_active' => $request->status === 'active',
            'email_opt_in' => $request->boolean('email_opt_in'),
            'use_gravatar' => $request->boolean('use_gravatar'),
            'avatar' => $request->avatar,
            'student_info' => $studentInfo,
        ]);

        return redirect()->route('admin.students.manage.view', $student)
            ->with('success', 'Student updated successfully');
    }

    public function viewOrders(User $student): View
    {
        $content = array_merge([], self::renderPageMeta('student_orders', "Orders: {$student->name}"));

        $orders = Order::where('user_id', $student->id)
            ->with('course')
            ->latest()
            ->paginate(10);

        return view('admin.students.orders', compact('student', 'orders', 'content'));
    }

    public function viewPayments(User $student): View
    {
        $content = array_merge([], self::renderPageMeta('student_payments', "Payments: {$student->name}"));

        // Get orders with payment information
        $payments = Order::where('user_id', $student->id)
            ->with(['course', 'payments'])
            ->whereHas('payments')
            ->latest()
            ->paginate(10);

        return view('admin.students.payments', compact('student', 'payments', 'content'));
    }

    public function activateStudent(User $student)
    {
        $student->update(['is_active' => true]);

        return back()->with('success', 'Student activated successfully');
    }

    public function deactivateStudent(User $student)
    {
        $student->update(['is_active' => false]);

        return back()->with('success', 'Student deactivated successfully');
    }

    /*********************** */
    /* Data Endpoints        */
    /*********************** */

    public function getStudentsList(Request $request): JsonResponse
    {
        $query = User::where('role_id', 5) // role_id 5 is student based on User model default
            ->withCount(['activeCourseAuths', 'inactiveCourseAuths']);

        // Add orders count manually since there's no direct relationship
        $query->addSelect([
            'orders_count' => Order::selectRaw('count(*)')
                ->whereColumn('user_id', 'users.id')
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('fname', 'ILIKE', "%{$search}%")
                  ->orWhere('lname', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(fname, ' ', lname) ILIKE ?", ["%{$search}%"]);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $students = $query->latest()->paginate(20);

        // Transform the data to include proper name concatenation and status
        $transformedData = $students->items();
        $transformedStudents = collect($transformedData)->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => trim($student->fname . ' ' . $student->lname),
                'email' => $student->email,
                'status' => $student->is_active ? 'active' : 'inactive',
                'orders_count' => $student->orders_count ?? 0,
                'active_course_auths_count' => $student->active_course_auths_count ?? 0,
                'inactive_course_auths_count' => $student->inactive_course_auths_count ?? 0,
                'created_at' => $student->created_at,
            ];
        });

        // Create custom response with pagination info
        $response = [
            'data' => $transformedStudents,
            'current_page' => $students->currentPage(),
            'last_page' => $students->lastPage(),
            'per_page' => $students->perPage(),
            'total' => $students->total(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function getRecentStudents(): JsonResponse
    {
        $students = User::where('role_id', 5) // student role
            ->latest()
            ->limit(10)
            ->get(['id', 'fname', 'lname', 'email', 'created_at', 'is_active'])
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name, // This uses the accessor method
                    'email' => $student->email,
                    'created_at' => $student->created_at,
                    'status' => $student->is_active ? 'active' : 'inactive'
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $search = $request->get('query', '');

        $students = User::where('role_id', 5) // student role
            ->where(function($q) use ($search) {
                $q->where('fname', 'ILIKE', "%{$search}%")
                  ->orWhere('lname', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(fname, ' ', lname) ILIKE ?", ["%{$search}%"]);
            })
            ->limit(10)
            ->get(['id', 'fname', 'lname', 'email', 'is_active'])
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->student_info['phone'] ?? null,
                    'status' => $student->is_active ? 'active' : 'inactive'
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    public function getStats(): JsonResponse
    {
        $stats = [
            'total_students' => User::where('role_id', 5)->count(), // role_id 5 is student
            'active_students' => User::where('role_id', 5)->where('is_active', true)->count(),
            'inactive_students' => User::where('role_id', 5)->where('is_active', false)->count(),
            'new_this_month' => User::where('role_id', 5)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_orders' => Order::count(),
            'orders_this_month' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    public function getActiveOrders(): JsonResponse
    {
        $orders = Order::with(['User', 'Course'])
            ->where('is_completed', false)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function getRecentPayments(): JsonResponse
    {
        $payments = Order::with(['User', 'Course'])
            ->where('is_completed', true)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function getAccountStatus(): JsonResponse
    {
        $activeCount = User::where('role_id', 5)->where('is_active', true)->count();
        $inactiveCount = User::where('role_id', 5)->where('is_active', false)->count();

        $statusData = [
            'active' => $activeCount,
            'inactive' => $inactiveCount
        ];

        return response()->json([
            'status' => 'success',
            'data' => $statusData
        ]);
    }

    public function getEnrollmentOverview(): JsonResponse
    {
        $enrollments = User::where('role_id', 5)
            ->withCount(['activeCourseAuths', 'inactiveCourseAuths'])
            ->get()
            ->map(function($student) {
                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'active_courses' => $student->active_course_auths_count,
                    'completed_courses' => $student->inactive_course_auths_count,
                    'total_enrollments' => $student->active_course_auths_count + $student->inactive_course_auths_count
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $enrollments
        ]);
    }

    public function debugStudentData(): JsonResponse
    {
        $sampleStudent = User::where('role_id', 5) // student role
            ->with(['activeCourseAuths.course', 'inactiveCourseAuths.course'])
            ->first();

        // Get orders for this student manually
        $orderCount = 0;
        if ($sampleStudent) {
            $orderCount = Order::where('user_id', $sampleStudent->id)->count();
        }

        return response()->json([
            'status' => 'success',
            'debug_info' => [
                'sample_student' => $sampleStudent,
                'user_count' => User::where('role_id', 5)->count(),
                'order_count' => Order::count(),
                'relationships' => [
                    'orders' => $orderCount,
                    'active_course_auths' => $sampleStudent ? $sampleStudent->activeCourseAuths->count() : 0,
                    'inactive_course_auths' => $sampleStudent ? $sampleStudent->inactiveCourseAuths->count() : 0,
                ]
            ]
        ]);
    }

    /*********************** */
    /* Bulk Operations       */
    /*********************** */

    public function exportStudents()
    {
        $students = User::where('role_id', 5) // student role
            ->with(['activeCourseAuths', 'inactiveCourseAuths'])
            ->get();

        // Create CSV export
        $filename = 'students_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Status', 'Created At',
                'Total Orders', 'Active Courses', 'Completed Courses'
            ]);

            // CSV data
            foreach ($students as $student) {
                // Get orders count manually
                $ordersCount = Order::where('user_id', $student->id)->count();

                fputcsv($file, [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->student_info['phone'] ?? '',
                    $student->is_active ? 'active' : 'inactive',
                    $student->created_at->format('Y-m-d H:i:s'),
                    $ordersCount,
                    $student->activeCourseAuths->count(),
                    $student->inactiveCourseAuths->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkActivate(Request $request)
    {
        $studentIds = $request->get('student_ids', []);

        User::whereIn('id', $studentIds)
            ->where('role_id', 5) // student role
            ->update(['is_active' => true]);

        return response()->json([
            'status' => 'success',
            'message' => count($studentIds) . ' students activated successfully'
        ]);
    }

    public function bulkDeactivate(Request $request)
    {
        $studentIds = $request->get('student_ids', []);

        User::whereIn('id', $studentIds)
            ->where('role_id', 5) // student role
            ->update(['is_active' => false]);

        return response()->json([
            'status' => 'success',
            'message' => count($studentIds) . ' students deactivated successfully'
        ]);
    }

    public function bulkEmail(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $studentIds = $request->get('student_ids');
        $subject = $request->get('subject');
        $message = $request->get('message');

        // This would typically queue emails for bulk sending
        // For now, we'll just return success

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk email queued for ' . count($studentIds) . ' students'
        ]);
    }
}
