<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseAuth;
use App\Models\StudentUnit;
use App\Models\StudentLesson;
use App\Models\Order;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentsController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        // Get users with student role (role_id = 5)
        $query = User::query()
            ->where('role_id', 5)
            ->with(['courseAuths.course'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                    ->orWhere('lname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Email verified filter
        if ($request->has('verified') && $request->verified !== 'all') {
            if ($request->verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Enrollment filter
        if ($request->has('enrollment') && $request->enrollment !== 'all') {
            if ($request->enrollment === 'enrolled') {
                $query->has('courseAuths');
            } else {
                $query->doesntHave('courseAuths');
            }
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Pagination
        $students = $query->paginate(25);

        // Statistics
        $stats = [
            'total' => User::where('role_id', 5)->count(),
            'active' => User::where('role_id', 5)->where('is_active', true)->count(),
            'inactive' => User::where('role_id', 5)->where('is_active', false)->count(),
            'verified' => User::where('role_id', 5)->whereNotNull('email_verified_at')->count(),
            'enrolled' => User::where('role_id', 5)->has('courseAuths')->count(),
            'new_this_month' => User::where('role_id', 5)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    /**
     * Show the form for editing a student
     */
    public function edit($id)
    {
        $student = User::where('role_id', 5)->findOrFail($id);

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update a student
     */
    public function update(Request $request, $id)
    {
        $student = User::where('role_id', 5)->findOrFail($id);

        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'is_active' => 'nullable|boolean',
            'email_opt_in' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:2',
            'initials' => 'nullable|string|max:3',
            'suffix' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update basic fields
            $student->fname = $validated['fname'];
            $student->lname = $validated['lname'];
            $student->email = $validated['email'];
            $student->is_active = $validated['is_active'] ?? $student->is_active;
            $student->email_opt_in = $validated['email_opt_in'] ?? $student->email_opt_in;

            // Update password if provided
            if (!empty($validated['password'])) {
                $student->password = Hash::make($validated['password']);
            }

            // Update student_info JSON field
            $studentInfo = $student->student_info ?? [];
            $studentInfo['phone'] = $validated['phone'] ?? null;
            $studentInfo['dob'] = $validated['dob'] ?? null;
            $studentInfo['address'] = $validated['address'] ?? null;
            $studentInfo['address2'] = $validated['address2'] ?? null;
            $studentInfo['city'] = $validated['city'] ?? null;
            $studentInfo['state'] = $validated['state'] ? strtoupper($validated['state']) : null;
            $studentInfo['zip'] = $validated['zip'] ?? null;
            $studentInfo['country'] = $validated['country'] ? strtoupper($validated['country']) : null;
            $studentInfo['initials'] = $validated['initials'] ? strtoupper($validated['initials']) : null;
            $studentInfo['suffix'] = $validated['suffix'] ?? null;
            $studentInfo['notes'] = $validated['notes'] ?? null;

            $student->student_info = $studentInfo;
            $student->save();

            DB::commit();

            return redirect()
                ->route('admin.students.show', $student->id)
                ->with('success', 'Student updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Display student details
     */
    public function show($id)
    {
        $student = User::where('role_id', 5)
            ->with([
                'courseAuths.course',
                'courseAuths' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->findOrFail($id);

        // Get student units with progress
        $studentUnits = StudentUnit::whereIn('course_auth_id', function ($query) use ($id) {
            $query->select('id')
                ->from('course_auths')
                ->where('user_id', $id);
        })
            ->with(['courseUnit', 'courseDate', 'instUnit'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get student lessons progress
        $lessonsProgress = StudentLesson::whereIn('student_unit_id', $studentUnits->pluck('id'))
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed')
            ->first();

        // Get payment history
        $orders = Order::where('user_id', $id)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'total_courses' => $student->courseAuths->count(),
            'active_courses' => $student->courseAuths->filter(function ($ca) {
                return !$ca->disabled_at && (!$ca->expire_date || Carbon::parse($ca->expire_date)->isFuture());
            })->count(),
            'completed_courses' => $student->courseAuths->whereNotNull('completed_at')->count(),
            'total_units' => $studentUnits->count(),
            'completed_units' => $studentUnits->where('unit_completed', true)->count(),
            'total_lessons' => $lessonsProgress->total ?? 0,
            'completed_lessons' => $lessonsProgress->completed ?? 0,
            'total_spent' => $orders->where('completed_at', '!=', null)->sum('total_price') ?? 0,
            'pending_payments' => $orders->whereNull('completed_at')->count(),
        ];

        return view('admin.students.show', compact('student', 'studentUnits', 'orders', 'stats'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.students.create', compact('courses'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction();
        try {
            // Create student user
            $student = User::create([
                'fname' => $validated['fname'],
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => 5, // Student role
                'is_active' => $validated['is_active'] ?? true,
                'student_info' => [
                    'phone' => $validated['phone'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'zip' => $validated['zip'] ?? null,
                ],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.students.show', $student->id)
                ->with('success', 'Student created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    /**
     * Bulk activate/deactivate students
     */
    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
            'status' => 'required|boolean',
        ]);

        $count = User::where('role_id', 5)
            ->whereIn('id', $validated['student_ids'])
            ->update(['is_active' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => "Updated {$count} student(s) successfully",
            'count' => $count,
        ]);
    }

    /**
     * Export students to CSV
     */
    public function export(Request $request)
    {
        $query = User::where('role_id', 5)
            ->with(['courseAuths.course'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                    ->orWhere('lname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        $filename = 'students_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Email', 'Status', 'Email Verified', 'Courses Enrolled', 'Created At', 'Last Updated']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->fname,
                    $student->lname,
                    $student->email,
                    $student->is_active ? 'Active' : 'Inactive',
                    $student->email_verified_at ? 'Yes' : 'No',
                    $student->courseAuths->count(),
                    $student->created_at->format('Y-m-d H:i:s'),
                    $student->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get student activity log
     */
    public function activity($id)
    {
        $student = User::where('role_id', 5)->findOrFail($id);

        // Gather activity data
        $activities = collect();

        // Course enrollments
        foreach ($student->courseAuths as $courseAuth) {
            $activities->push([
                'type' => 'enrollment',
                'title' => 'Enrolled in course',
                'description' => $courseAuth->course->title ?? 'Unknown Course',
                'timestamp' => $courseAuth->created_at,
                'icon' => 'fa-graduation-cap',
                'color' => 'primary',
            ]);

            if ($courseAuth->completed_at) {
                $activities->push([
                    'type' => 'completion',
                    'title' => 'Completed course',
                    'description' => $courseAuth->course->title ?? 'Unknown Course',
                    'timestamp' => Carbon::parse($courseAuth->completed_at),
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                ]);
            }
        }

        // Email verification
        if ($student->email_verified_at) {
            $activities->push([
                'type' => 'verification',
                'title' => 'Email verified',
                'description' => 'Student verified their email address',
                'timestamp' => Carbon::parse($student->email_verified_at),
                'icon' => 'fa-envelope-open',
                'color' => 'info',
            ]);
        }

        // Account creation
        $activities->push([
            'type' => 'registration',
            'title' => 'Account created',
            'description' => 'Student registered on the platform',
            'timestamp' => $student->created_at,
            'icon' => 'fa-user-plus',
            'color' => 'success',
        ]);

        // Sort by timestamp descending
        $activities = $activities->sortByDesc('timestamp')->values();

        return view('admin.students.activity', compact('student', 'activities'));
    }

    /**
     * Get students data for API/AJAX
     */
    public function getData(Request $request)
    {
        $query = User::query()
            ->where('role_id', 5)
            ->with(['courseAuths'])
            ->select(['id', 'fname', 'lname', 'email', 'created_at', 'updated_at', 'is_active']);

        // DataTables server-side processing
        if ($request->has('draw')) {
            $draw = $request->input('draw');
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);
            $searchValue = $request->input('search.value');

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('fname', 'like', "%{$searchValue}%")
                        ->orWhere('lname', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%")
                        ->orWhere('id', 'like', "%{$searchValue}%");
                });
            }

            $totalRecords = User::where('role_id', 5)->count();
            $filteredRecords = $query->count();

            $students = $query
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $students,
            ]);
        }

        return response()->json([
            'students' => $query->get(),
        ]);
    }
}
