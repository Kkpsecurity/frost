<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Classes\ChatLogCache;
use App\Classes\MiscQueries;
use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use App\Models\InstUnit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClassroomChatController extends Controller
{
    public function getChat(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'course_date_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
            ], 422);
        }

        $courseDateId = (int) $request->input('course_date_id');

        // If class is online, auto-enable chat so student UI works out of the box.
        try {
            $instUnit = InstUnit::where('course_date_id', $courseDateId)->first();
            if ($instUnit && !ChatLogCache::IsEnabled($courseDateId)) {
                ChatLogCache::Enable($courseDateId);
            }
        } catch (\Throwable $e) {
            // Non-fatal.
        }

        $enabled = ChatLogCache::IsEnabled($courseDateId);

        $messages = [];
        if ($enabled) {
            $chatMessages = MiscQueries::RecentChatMessages($courseDateId, (int) $user->id);

            foreach ($chatMessages as $chatMessage) {
                $authorId = (int) ($chatMessage->student_id ?? $chatMessage->inst_id ?? 0);
                $author = $authorId > 0 ? User::find($authorId) : null;

                $createdAt = null;
                try {
                    // Preferred (existing presenter helper)
                    $createdAt = $chatMessage->CreatedAt('HH:mm:ss');
                } catch (\Throwable $e) {
                    $createdAt = optional($chatMessage->created_at)->format('H:i:s');
                }

                $messages[] = [
                    'id' => (int) $chatMessage->id,
                    'user' => [
                        'user_id' => $authorId,
                        'user_name' => $author ? trim(($author->fname ?? '') . ' ' . ($author->lname ?? '')) : 'Unknown',
                        'user_avatar' => $author ? $author->getAvatar('thumb') : null,
                        'user_type' => $chatMessage->student_id ? 'student' : 'instructor',
                    ],
                    'body' => (string) ($chatMessage->body ?? ''),
                    'created_at' => $createdAt,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'messages' => $messages,
        ]);
    }

    public function postChatMessage(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'course_date_id' => 'required|integer',
            'message' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
            ], 422);
        }

        $courseDateId = (int) $request->input('course_date_id');

        // Auto-enable chat when class is online.
        $instUnit = InstUnit::where('course_date_id', $courseDateId)->first();
        if ($instUnit && !ChatLogCache::IsEnabled($courseDateId)) {
            ChatLogCache::Enable($courseDateId);
        }

        if (!ChatLogCache::IsEnabled($courseDateId)) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is disabled for this class',
            ], 403);
        }

        $chat = new ChatLog();
        $chat->course_date_id = $courseDateId;

        // Determine whether this authenticated user is the instructor for this class.
        // This allows instructors (if present under auth()) to chat with everyone.
        $isInstructor = $instUnit && (int) $instUnit->created_by === (int) $user->id;
        if ($isInstructor) {
            $chat->inst_id = (int) $user->id;
        } else {
            $chat->student_id = (int) $user->id;
        }

        $chat->body = (string) $request->input('message');
        $chat->save();

        return response()->json([
            'success' => true,
        ]);
    }
}
