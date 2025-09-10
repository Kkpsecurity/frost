<?php

namespace App\Http\Controllers\Admin\Students;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use App\Models\Role;
use App\Models\CourseAuth;
use App\Models\Order;
use App\Support\RoleManager;
use App\RCache;

class StudentController extends Controller
{
    /**
     * Get student role ID safely without RCache dependency
     */
    private function getStudentRoleId(): int
    {
        try {
            $role = \App\Models\Role::where('name', 'Student')->first();
            return $role ? $role->id : 5; // Fallback to role ID 5
        } catch (\Throwable $e) {
            return 5; // Safe fallback
        }
    }

    /**
     * Display a listing of students with DataTables
     */
    public function index()
    {
        return view('admin.students.index');
    }

    /**
     * Get students data for DataTables
     */
    public function getData(Request $request): JsonResponse
    {
        // Handle stats only request
        if ($request->get('stats_only')) {
            return $this->getOverviewStats();
        }

        // Handle different view types
        $viewType = $request->get('view_type', 'accounts');

        switch ($viewType) {
            case 'educational':
                return $this->getEducationalData($request);
            case 'enrollments':
                return $this->getEnrollmentsData($request);
            case 'financial':
                return $this->getFinancialData($request);
            default:
                return $this->getAccountsData($request);
        }
    }

    /**
     * Get accounts data for DataTables
     */
    protected function getAccountsData(Request $request): JsonResponse
    {
        $query = User::with('Role')->select('users.*')
            ->where('role_id', $this->getStudentRoleId());

        // Apply filters
        if ($request->has('account_status_filter') && !empty($request->account_status_filter)) {
            if ($request->account_status_filter === 'active') {
                $query->where('is_active', true);
            } elseif ($request->account_status_filter === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('email_verified_filter') && !empty($request->email_verified_filter)) {
            if ($request->email_verified_filter === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->email_verified_filter === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        if ($request->has('registration_date_filter') && !empty($request->registration_date_filter)) {
            $query->whereDate('created_at', $request->registration_date_filter);
        }

        return DataTables::of($query)
            ->addColumn('full_name', function ($student) {
                return $student->fname . ' ' . $student->lname;
            })
            ->addColumn('status', function ($student) {
                return $student->is_active ?
                    '<span class="badge badge-success">Active</span>' :
                    '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('email_verified_status', function ($student) {
                return $student->email_verified_at ?
                    '<span class="badge badge-success">Verified</span>' :
                    '<span class="badge badge-warning">Unverified</span>';
            })
            ->addColumn('avatar_display', function ($student) {
                if ($student->avatar) {
                    return '<img src="' . asset('storage/' . $student->avatar) . '" class="img-circle" width="32" height="32">';
                } elseif ($student->use_gravatar) {
                    $gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower($student->email)) . '?s=32&d=identicon';
                    return '<img src="' . $gravatar . '" class="img-circle" width="32" height="32">';
                }
                return '<i class="fas fa-user-circle fa-2x text-muted"></i>';
            })
            ->addColumn('formatted_created_at', function ($student) {
                return RoleManager::formatDate($student->created_at, 'datetime_medium');
            })
            ->addColumn('last_login_formatted', function ($student) {
                return $student->last_login ? RoleManager::formatDate($student->last_login, 'datetime_medium') : 'Never';
            })
            ->addColumn('actions', function ($student) {
                return view('admin.students.partials.actions', compact('student'))->render();
            })
            ->rawColumns(['status', 'email_verified_status', 'avatar_display', 'actions'])
            ->make(true);
    }

    /**
     * Get educational data for DataTables
     */
    protected function getEducationalData(Request $request): JsonResponse
    {
        $query = User::with(['courseAuths.course'])
            ->where('role_id', $this->getStudentRoleId());

        return DataTables::of($query)
            ->addColumn('full_name', function ($student) {
                return $student->fname . ' ' . $student->lname;
            })
            ->addColumn('active_courses_count', function ($student) {
                return $student->courseAuths()->whereNull('expired_at')->count();
            })
            ->addColumn('completed_courses_count', function ($student) {
                return $student->courseAuths()->whereNotNull('completed_at')->count();
            })
            ->addColumn('overall_progress', function ($student) {
                $total = $student->courseAuths()->count();
                $completed = $student->courseAuths()->whereNotNull('completed_at')->count();
                $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                return '<div class="progress">
                    <div class="progress-bar" style="width: ' . $percentage . '%">' . $percentage . '%</div>
                </div>';
            })
            ->addColumn('last_activity', function ($student) {
                $lastAuth = $student->courseAuths()->latest('updated_at')->first();
                return $lastAuth ? RoleManager::formatDate($lastAuth->updated_at, 'datetime_medium') : 'No activity';
            })
            ->addColumn('performance_badge', function ($student) {
                $total = $student->courseAuths()->count();
                $completed = $student->courseAuths()->whereNotNull('completed_at')->count();
                $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

                if ($percentage >= 90) return '<span class="badge badge-success">Excellent</span>';
                if ($percentage >= 70) return '<span class="badge badge-info">Good</span>';
                if ($percentage >= 50) return '<span class="badge badge-warning">Average</span>';
                return '<span class="badge badge-danger">Needs Help</span>';
            })
            ->addColumn('educational_actions', function ($student) {
                return '<div class="btn-group">
                    <button class="btn btn-sm btn-info" onclick="viewProgress(' . $student->id . ')">
                        <i class="fas fa-chart-line"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="assignCourse(' . $student->id . ')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['overall_progress', 'performance_badge', 'educational_actions'])
            ->make(true);
    }

    /**
     * Get enrollments data for DataTables
     */
    protected function getEnrollmentsData(Request $request): JsonResponse
    {
        $query = CourseAuth::with(['user', 'course'])
            ->whereHas('user', function($q) {
                $q->where('role_id', $this->getStudentRoleId());
            });

        // Apply filters
        if ($request->has('enrollment_status_filter') && !empty($request->enrollment_status_filter)) {
            switch ($request->enrollment_status_filter) {
                case 'active':
                    $query->whereNull('expired_at')->whereNull('completed_at');
                    break;
                case 'completed':
                    $query->whereNotNull('completed_at');
                    break;
                case 'expired':
                    $query->whereNotNull('expired_at');
                    break;
            }
        }

        return DataTables::of($query)
            ->addColumn('student_name', function ($enrollment) {
                return $enrollment->user->fname . ' ' . $enrollment->user->lname;
            })
            ->addColumn('course_name', function ($enrollment) {
                return $enrollment->course ? $enrollment->course->name : 'N/A';
            })
            ->addColumn('enrollment_date', function ($enrollment) {
                return RoleManager::formatDate($enrollment->created_at, 'date_medium');
            })
            ->addColumn('enrollment_status', function ($enrollment) {
                if ($enrollment->expired_at) return '<span class="badge badge-danger">Expired</span>';
                if ($enrollment->completed_at) return '<span class="badge badge-success">Completed</span>';
                return '<span class="badge badge-primary">Active</span>';
            })
            ->addColumn('progress_bar', function ($enrollment) {
                $progress = $enrollment->progress ?? 0;
                return '<div class="progress">
                    <div class="progress-bar" style="width: ' . $progress . '%">' . $progress . '%</div>
                </div>';
            })
            ->addColumn('expiry_date', function ($enrollment) {
                return $enrollment->expired_at ?
                    RoleManager::formatDate($enrollment->expired_at, 'date_medium') :
                    'No expiry';
            })
            ->addColumn('enrollment_actions', function ($enrollment) {
                return '<div class="btn-group">
                    <button class="btn btn-sm btn-info" onclick="viewEnrollment(' . $enrollment->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editEnrollment(' . $enrollment->id . ')">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['enrollment_status', 'progress_bar', 'enrollment_actions'])
            ->make(true);
    }

    /**
     * Get financial data for DataTables
     */
    protected function getFinancialData(Request $request): JsonResponse
    {
        $query = Order::with(['user'])
            ->whereHas('user', function($q) {
                $q->where('role_id', $this->getStudentRoleId());
            });

        // Apply filters
        if ($request->has('payment_status_filter') && !empty($request->payment_status_filter)) {
            $query->where('status', $request->payment_status_filter);
        }

        if ($request->has('order_date_filter') && !empty($request->order_date_filter)) {
            $query->whereDate('created_at', $request->order_date_filter);
        }

        return DataTables::of($query)
            ->addColumn('order_id', function ($order) {
                return '#' . $order->id;
            })
            ->addColumn('student_name', function ($order) {
                return $order->user->fname . ' ' . $order->user->lname;
            })
            ->addColumn('product_name', function ($order) {
                return $order->product_name ?? 'Course Purchase';
            })
            ->addColumn('formatted_amount', function ($order) {
                return '$' . number_format($order->total ?? 0, 2);
            })
            ->addColumn('payment_status_badge', function ($order) {
                $status = $order->status ?? 'pending';
                $badgeClass = [
                    'paid' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    'refunded' => 'info'
                ][$status] ?? 'secondary';

                return '<span class="badge badge-' . $badgeClass . '">' . ucfirst($status) . '</span>';
            })
            ->addColumn('payment_method', function ($order) {
                return $order->payment_method ?? 'N/A';
            })
            ->addColumn('formatted_order_date', function ($order) {
                return RoleManager::formatDate($order->created_at, 'datetime_medium');
            })
            ->addColumn('financial_actions', function ($order) {
                return '<div class="btn-group">
                    <button class="btn btn-sm btn-info" onclick="viewOrder(' . $order->id . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="processRefund(' . $order->id . ')">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['payment_status_badge', 'financial_actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new student
     */
    public function create(): View
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student in storage
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $studentData = $request->only(['fname', 'lname', 'email', 'is_active']);
        $studentData['password'] = Hash::make($request->password);
        $studentData['role_id'] = $this->getStudentRoleId();

        if ($request->hasFile('avatar')) {
            $studentData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($studentData);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student
     */
    public function show(User $student, Request $request)
    {
        // Ensure the user is actually a student
        if ($student->role_id !== $this->getStudentRoleId()) {
            abort(404);
        }

        $enrollments = $student->courseAuths()->with('course')->get();
        $orders = $student->orders()->get();

        $stats = [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => $enrollments->whereNull('expired_at')->count(),
            'completed_courses' => $enrollments->whereNotNull('completed_at')->count(),
            'total_orders' => $orders->count(),
        ];

        // If this is an AJAX request, return the details view for modal
        if ($request->ajax()) {
            return view('admin.students.details', compact('student', 'enrollments', 'orders', 'stats'))->render();
        }

        // Otherwise return the full page view
        return view('admin.students.show', compact('student', 'enrollments', 'orders', 'stats'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(User $student): View
    {
        // Ensure the user is actually a student
        if ($student->role_id !== $this->getStudentRoleId()) {
            abort(404);
        }

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage
     */
    public function update(Request $request, User $student): RedirectResponse
    {
        // Ensure the user is actually a student
        if ($student->role_id !== $this->getStudentRoleId()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $studentData = $request->only(['fname', 'lname', 'email', 'is_active']);

        if ($request->filled('password')) {
            $studentData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($student->avatar) {
                Storage::disk('public')->delete($student->avatar);
            }
            $studentData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $student->update($studentData);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage
     */
    public function destroy(User $student): RedirectResponse
    {
        // Ensure the user is actually a student
        if ($student->role_id !== $this->getStudentRoleId()) {
            abort(404);
        }

        // Delete avatar if exists
        if ($student->avatar) {
            Storage::disk('public')->delete($student->avatar);
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Get student details for AJAX requests
     */
    public function getStudentDetails(Request $request, $studentId): JsonResponse
    {
        $student = User::with(['courseAuths.course', 'orders'])
            ->where('role_id', $this->getStudentRoleId())
            ->findOrFail($studentId);

        $enrollments = $student->courseAuths()->with('course')->get();
        $orders = $student->orders()->get();

        $data = [
            'student' => $student,
            'enrollments' => $enrollments,
            'orders' => $orders,
            'stats' => [
                'total_enrollments' => $enrollments->count(),
                'active_enrollments' => $enrollments->whereNull('expired_at')->count(),
                'completed_courses' => $enrollments->whereNotNull('completed_at')->count(),
                'total_orders' => $orders->count(),
            ]
        ];

        return response()->json($data);
    }

    /**
     * Get overview statistics for dashboard
     */
    private function getOverviewStats(): JsonResponse
    {
        $studentRoleId = $this->getStudentRoleId();

        $students = User::where('role_id', $studentRoleId);

        $totalStudents = $students->count();
        $activeStudents = $students->where('is_active', true)->count();
        $verifiedStudents = $students->whereNotNull('email_verified_at')->count();

        // Course enrollments stats
        $totalEnrollments = CourseAuth::count();
        $activeEnrollments = CourseAuth::whereNull('expired_at')->count();
        $completedCourses = CourseAuth::whereNotNull('completed_at')->count();

        // Financial stats
        $totalOrders = Order::where('user_type', 'student')->count();
        $totalRevenue = Order::where('user_type', 'student')
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'recordsTotal' => $totalStudents,
            'activeStudents' => $activeStudents,
            'verifiedStudents' => $verifiedStudents,
            'totalEnrollments' => $totalEnrollments,
            'activeEnrollments' => $activeEnrollments,
            'completedCourses' => $completedCourses,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
