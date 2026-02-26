<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\DiscountCode;
use App\Models\CourseAuth;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    /**
     * Handle admin navbar search requests.
     * Searches across Users, Courses, DiscountCodes, and CourseAuths.
     */
    public function index(Request $request)
    {
        $query = trim($request->get('adminlteSearch', ''));

        if (strlen($query) < 2) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Search query too short — enter at least 2 characters.');
        }

        $users        = User::search($query)->get();
        $courses      = Course::search($query)->get();
        $discounts    = DiscountCode::search($query)->get();
        $courseAuths  = CourseAuth::search($query)->get();

        return view('admin.search', compact('query', 'users', 'courses', 'discounts', 'courseAuths'));
    }
}
