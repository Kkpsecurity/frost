<?php

namespace App\Http\Controllers\Admin\Services;


use Auth; 
use Carbon\Carbon;
use App\Models\CourseAuth;
use App\Models\StudentUnit;
use Illuminate\Http\Request;
use App\Models\StudentLesson;
use App\Classes\TrackingQueries;
use App\Http\Controllers\Controller;

class StudentToolActionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Ejects a student todays class
     */
    public function ejectStudent(Request $request)
    {
        $studentUnit = StudentUnit::find($request->studentUnitId);

        if (!$studentUnit) {
            return response()->json([
                'success' => false,
                'message' => "Student Unit not found for given ID",
            ]);
        }

        $studentUnit->ejected_at = Carbon::now();
        $studentUnit->ejected_for = $request->ejectReason;
        $studentUnit->save();

        return response()->json(['success' => true, 'message' => "Student has been Ejected for the day"]);
    }

    /**
     * Re-instate a student for the day
     */
    public function reInState(Request $request)
    {
        $studentUnit = StudentUnit::find($request->studentUnitId);

        if (!$studentUnit) {
            return response()->json([
                'success' => false,
                'message' => "Student Unit not found for given ID",
            ]);
        }

        $studentUnit->ejected_at = null;
        $studentUnit->ejected_for = $request->reInstateReason;
        $studentUnit->save();

        return response()->json(['success' => true, 'message' => "Student has been Re-instated"]);
    }

    /**
     * Remove a student course and the student nust re pruchase the course
     */
    public function banStudent(Request $request)
    {

        $studentAuth = CourseAuth::find($request->course_auth_id);

        if (!$studentAuth) {
            return response()->json([
                'success' => false,
                'message' => "Student Course Auth not found for given ID",
            ]);
        }

        $studentAuth->disabled_at = Carbon::now();
        $studentAuth->disabled_by = Auth::user()->id;
        $studentAuth->save();

        return response()->json(['success' => true, 'message' => "Student has been ejected"]);
    }

    /**
     * Mark a student as DNC for the current lesson
     */
    public function revokeDNCStudent(Request $request)
    {
        if (!$request->lessonId || !$request->studentUnitId) {
            return response()->json([
                'success' => false,
                'message' => "Missing required parameters",
            ]);
        }

        $studentLesson = StudentLesson::where('lesson_id', $request->lessonId)
            ->where('student_unit_id', $request->studentUnitId)->first();

        if (!$studentLesson) {
            return response()->json([
                'success' => false,
                'message' => "Student Lesson not found for given ID",
            ]);
        }        

        if($error = $studentLesson->ClearDNC()) {
            return response()->json(['success' => false, 'message' => $error]);    
        }

        return response()->json(['success' => true, 'message' => "Student DNC'ed has been Revoked"]);
    }
   
    public function reEnterAccess(Request $request)
    {
        // Validate the request input
        $validated = $request->validate([
            'studentUnitId' => 'required|exists:student_unit,id',
        ]);

        $studentUnitId = $validated['studentUnitId'];

        // Find the active lesson for the student unit
        $instLesson = TrackingQueries::ActiveInstLesson(StudentUnit::find($studentUnitId)->InstUnit);
        if (!$instLesson) {
            return response()->json([
                'success' => false,
                'message' => "No active lesson found for given student unit",
            ]);
        }

        // Check if the student already has access
        if (
            StudentLesson::where('student_unit_id', $studentUnitId)
                ->where('lesson_id', $instLesson->lesson_id)
                ->where('inst_lesson_id', $instLesson->id)
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => "Student already has access to this lesson",
            ]);
        }

        // Grant access
        StudentLesson::create([
            'student_unit_id' => $studentUnitId,
            'lesson_id' => $instLesson->lesson_id,
            'inst_lesson_id' => $instLesson->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Student has been granted access to this lesson",
        ]);
    }

}