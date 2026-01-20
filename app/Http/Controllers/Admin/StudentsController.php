<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseAuth;
use App\Models\StudentUnit;
use Illuminate\Http\Request;

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
            ->with(['courseAuths'])
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

        // Pagination
        $students = $query->paginate(25);

        return view('admin.students.index', compact('students'));
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
        ]);

        $student->update($validated);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully');
    }

    /**
     * Display student details
     */
    public function show($id)
    {
        $student = User::where('role_id', 5)
            ->with(['courseAuths'])
            ->findOrFail($id);

        // Get student units
        $studentUnits = StudentUnit::where('course_auth_id', function($query) use ($id) {
            $query->select('id')
                ->from('course_auth')
                ->where('user_id', $id);
        })
        ->with(['courseUnit', 'courseDate', 'instUnit'])
        ->get();

        return view('admin.students.show', compact('student', 'studentUnits'));
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
