<?php
namespace App\Http\Controllers\React;

use App\Models\InstUnit;
use App\Models\User;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use App\Classes\ChatLogCache;
use App\Classes\MiscQueries;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use KKP\TextTk;

class ChatController extends Controller
{
    /**
     *
     * @param Request $requestx
     */
    public function manageChatMessages(Request $request, $course_date_id = null, $user_id = null)
    {

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'course_date_id' => 'required|string',
                'user_id' => 'required|integer',
                'message' => 'required|string|max:255',
                'user_type' => 'required|string|max:25'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed: ' . $validator->errors()->first()
                ], 400);
            }

            $chat = new ChatLog();

            // Store a chat message
            $content = $request->input('message');
            if ($request->user_type == 'instructor') {
                $chat->inst_id = $user_id;
            } else {
                $chat->student_id = $user_id;
            }
            $chat->course_date_id = $course_date_id;

            $chat->body = TextTk::Sanitize($content);
            $chat->save();

            return response()->json(['success' => true, 'message' => 'Message sent'], 200);

        } else if ($request->isMethod('get')) {

            if (!ChatLogCache::IsEnabled($course_date_id)) {
                return response()->json(['success' => true, 'message' => 'Chat System Disabled'], 200);
            }

            #logger( "ChatLog UserID {$user_id}" );

            $chat_messages = MiscQueries::RecentChatMessages( $course_date_id, $user_id );

            if (!$chat_messages->count()) {
                return response()->json(['success' => true, 'message' => 'No messages found'], 200);
            }

            $chats = [];

            foreach ($chat_messages as $chat_message) {
                $user = User::find($chat_message->student_id ?? $chat_message->inst_id);
                $chat = [
                    'id' => $chat_message->id,
                    'user' => [
                        "user_id" => $chat_message->student_id ?? $chat_message->inst_id,
                        "user_name" => $user->fname . ' ' . $user->lname,
                        "user_avatar" => $user->getAvatar('thumb'),
                        "user_type" => $chat_message->student_id ? 'student' : 'instructor'
                    ],
                    'body' => $chat_message->body,
                    'created_at' => $chat_message->CreatedAt( 'HH:mm:ss' ),
                ];
                $chats[] = $chat;
            }

            return response()->json($chats);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid Action Request'], 400);
        }
    }

    public function enableChatSystem($course_date_id)
    {
        if (!ChatLogCache::IsEnabled($course_date_id)) {
            ChatLogCache::Enable($course_date_id);
        } else {
            ChatLogCache::Disable($course_date_id);
        }

        return response()->json(['success' => true, 'message' => 'Chat System ' . (ChatLogCache::IsEnabled($course_date_id) ? 'Enabled' : 'Disabled')]);
    }

}
