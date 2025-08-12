<?php

/**
 * Frost Routes
 *
 * This file defines the web routes for the Frost application.
 * It includes routes for the admin settings, services, and other application features.
 */

use Illuminate\Support\Facades\Route;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use App\Support\Settings;

/**
 * Home Route - Redirect to Admin
 */
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

/**
 * Test AdminLTE Notifications
 */
Route::get('/test-notifications', function () {
    return view('test-notifications');
})->name('test.notifications')->middleware('auth');

/**
 * Admin Authentication Routes
 * These routes handle admin login/logout and are accessible without admin middleware
 */
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin/auth_routes.php';
    });

/**
 * Protected Admin Routes
 * These routes are prefixed with 'admin' and require admin middleware.
 * They include settings management, services, and other admin functionalities.
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin'])
    ->group(function () {
        require __DIR__ . '/admin.php';
    });

/**
 * Frontend User Account Routes
 * These routes handle user account management functionality
 */
Route::middleware(['auth'])
    ->group(function () {
        require __DIR__ . '/frontend/account_routes.php';
    });

/**
 * Student Classroom Routes
 * These routes handle student learning functionality
 */
Route::prefix('classroom')
    ->name('classroom.')
    ->middleware(['auth'])
    ->group(function () {
        // Student Dashboard
        Route::get('/', [
            App\Http\Controllers\Student\ClassroomController::class,
            'dashboard'
        ])->name('dashboard');
    });

/**
 * Messaging Routes
 * These routes handle the messaging system functionality
 */
Route::middleware('auth')->prefix('messaging')->group(function () {
    // Get all threads for current user
    Route::get('/threads', function () {
        $user = auth()->user();
        return Thread::forUser($user->id)->latest('updated_at')
            ->with(['participants.user:id,name'])
            ->get()->map(fn($thread) => [
                'id' => $thread->id,
                'subject' => $thread->subject,
                'participants' => $thread->participants->pluck('user.name'),
                'unread' => $thread->userUnreadMessagesCount($user->id),
                'last_message_at' => optional($thread->latestMessage)->created_at,
            ]);
    });

    // Get specific thread with messages
    Route::get('/threads/{thread}', function (Thread $thread) {
        abort_unless($thread->hasParticipant(auth()->id()), 403);
        $thread->markAsRead(auth()->id());
        $thread->load(['participants.user:id,name', 'messages.user:id,name']);

        return [
            'id' => $thread->id,
            'subject' => $thread->subject,
            'participants' => $thread->participants->map(fn($p) => [
                'id' => $p->user->id,
                'name' => $p->user->name
            ]),
            'messages' => $thread->messages()->latest()->take(100)->get()
                ->reverse()->values()->map(fn($m) => [
                    'id' => $m->id,
                    'body' => $m->body,
                    'author' => $m->user->name,
                    'created_at' => $m->created_at,
                ]),
        ];
    });

    // Create new thread
    Route::post('/threads', function (\Illuminate\Http\Request $request) {
        $settings = Settings::get('messaging');
        abort_unless(in_array($request->user()->role, $settings['allow_new_threads_roles'] ?? []), 403);

        $data = $request->validate([
            'subject' => 'nullable|string',
            'participant_ids' => 'required|array|min:1',
            'message' => 'required|string'
        ]);

        $thread = Thread::create(['subject' => $data['subject'] ?? null]);
        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'body' => $data['message']
        ]);

        $ids = array_unique(array_merge($data['participant_ids'], [$request->user()->id]));
        $thread->addParticipants($ids);

        // Send notifications to participants (except the sender)
        $participants = \App\Models\User::whereIn('id', $data['participant_ids'])->get();
        foreach ($participants as $participant) {
            $participant->notify(new \App\Notifications\NewMessageNotification($message));
        }

        return response()->json(['id' => $thread->id], 201);
    });

    // Send message to thread
    Route::post('/threads/{thread}/message', function (\Illuminate\Http\Request $request, Thread $thread) {
        abort_unless($thread->hasParticipant(auth()->id()), 403);
        $request->validate(['body' => 'required|string']);

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'body' => $request->body
        ]);

        // Send notifications to other participants
        $otherParticipants = $thread->participants()
            ->where('user_id', '!=', $request->user()->id)
            ->with('user')
            ->get();

        foreach ($otherParticipants as $participant) {
            $participant->user->notify(new \App\Notifications\NewMessageNotification($message));
        }

        // Dispatch event for real-time updates (optional)
        // event(new \App\Events\MessageSent($message));

        return response()->json(['id' => $message->id], 201);
    });

    // Mark thread as read
    Route::post('/threads/{thread}/read', function (Thread $thread) {
        abort_unless($thread->hasParticipant(auth()->id()), 403);
        $thread->markAsRead(auth()->id());
        return response()->noContent();
    });

    // Get user notifications related to messaging
    Route::get('/notifications', function (\Illuminate\Http\Request $request) {
        $notifications = $request->user()->unreadNotifications()
            ->where('type', 'App\\Notifications\\NewMessageNotification')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->data['type'],
                'thread_id' => $n->data['thread_id'],
                'thread_subject' => $n->data['thread_subject'],
                'sender_name' => $n->data['sender_name'],
                'message_preview' => $n->data['message_preview'],
                'created_at' => $n->created_at,
            ]);

        return response()->json($notifications);
    });

    // Mark notification as read
    Route::post('/notifications/{id}/read', function (\Illuminate\Http\Request $request, $id) {
        $notification = $request->user()->unreadNotifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->noContent();
    });

    // Search users for new message participants
    Route::get('/users/search', function (\Illuminate\Http\Request $request) {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = \App\Models\User::where(function($q) use ($query) {
            $q->where('fname', 'ILIKE', "%{$query}%")
              ->orWhere('lname', 'ILIKE', "%{$query}%")
              ->orWhere('email', 'ILIKE', "%{$query}%");
        })
        ->where('id', '!=', $request->user()->id)
        ->limit(10)
        ->get(['id', 'fname', 'lname', 'email'])
        ->map(fn($user) => [
            'id' => $user->id,
            'name' => trim($user->fname . ' ' . $user->lname),
            'email' => $user->email,
        ]);

        return response()->json($users);
    });
});

/**
 * User Authentication Routes
 * These routes handle user login/logout, registration, and password reset
 */
require __DIR__ . '/auth.php';

/**
 * Test Routes (for debugging)
 */
Route::get('/test/blade-directives', function () {
    return view('test.blade-directive-test');
})->middleware(['admin']);

Route::get('/demo/topbar', function () {
    return view('demo.topbar-demo');
})->middleware(['auth'])->name('demo.topbar');
