<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend\Student;

use App\Classes\ClassroomSessionModeCache;
use App\Http\Controllers\Controller;
use App\Models\InstructorQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AskInstructorController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'course_date_id' => 'required|integer',
            'topic' => 'required|string|max:80',
            'urgency' => 'required|string|in:Normal,Urgent',
            'question' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
            ], 422);
        }

        $courseDateId = (int) $request->input('course_date_id');

        $q = InstructorQuestion::create([
            'course_date_id' => $courseDateId,
            'student_id' => (int) $user->id,
            'topic' => (string) $request->input('topic'),
            'urgency' => (string) $request->input('urgency'),
            'question' => (string) $request->input('question'),
            'status' => 'received',
        ]);

        return response()->json([
            'success' => true,
            'question_id' => (int) $q->id,
            'message' => 'Received. Instructor will respond.',
        ]);
    }

    public function myQueue(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $courseDateId = $request->query('course_date_id');
        $courseDateId = is_numeric($courseDateId) ? (int) $courseDateId : null;
        if (!$courseDateId) {
            return response()->json(['success' => true, 'questions' => []]);
        }

        $questions = InstructorQuestion::query()
            ->where('course_date_id', $courseDateId)
            ->where('student_id', (int) $user->id)
            ->orderByDesc('id')
            ->limit(25)
            ->get([
                'id',
                'topic',
                'urgency',
                'question',
                'status',
                'answer_visibility',
                'answer_text',
                'answered_at',
                'ai_status',
                'ai_answer_student',
                'ai_sources',
                'created_at',
            ])
            ->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'topic' => (string) $row->topic,
                    'urgency' => (string) $row->urgency,
                    'question' => (string) $row->question,
                    'status' => (string) $row->status,
                    'answer_visibility' => $row->answer_visibility,
                    'answer_text' => $row->answer_text,
                    'answered_at' => optional($row->answered_at)->format('c'),
                    'ai_status' => $row->ai_status,
                    'ai_answer_student' => $row->ai_answer_student,
                    'ai_sources' => $row->ai_sources,
                    'created_at' => optional($row->created_at)->format('c'),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }

    public function getSessionMode(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $courseDateId = $request->query('course_date_id');
        $courseDateId = is_numeric($courseDateId) ? (int) $courseDateId : null;
        if (!$courseDateId) {
            return response()->json(['success' => true, 'mode' => ClassroomSessionModeCache::MODE_TEACHING]);
        }

        return response()->json([
            'success' => true,
            'mode' => ClassroomSessionModeCache::Get($courseDateId),
        ]);
    }
}
