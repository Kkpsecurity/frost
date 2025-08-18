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
 * Test Routes (for debugging)
 */
Route::get('/test/blade-directives', function () {
    return view('test.blade-directive-test');
})->middleware(['admin']);

Route::get('/demo/topbar', function () {
    return view('demo.topbar-demo');
})->middleware(['auth'])->name('demo.topbar');





/////////////////////////////////////////////
<?php

/**
 * Admin Master Route File
 * Parent Route load for all admin routes.
 * This file is included in the main web.php file.
 * It sets up the admin routes with the following configurations:
 * - Prefix: admin
 * - Name: admin.
 * - Middleware: admin, verified
 */

use Illuminate\Support\Facades\Route;



// Admin API Config endpoint
Route::get('/api/config', [
    App\Http\Controllers\Admin\AdminDashboardController::class,
    'config'
])->name('api.config');

// Test endpoint to verify admin authentication
Route::get('/api/test', function () {
    $admin = auth('admin')->user();
    return response()->json([
        'success' => true,
        'authenticated' => !!$admin,
        'user' => $admin ? [
            'id' => $admin->id,
            'name' => $admin->name ?? 'Admin User',
            'guard' => 'admin'
        ] : null,
        'timestamp' => now()->toISOString()
    ]);
})->name('api.test');

/**
 * Admin Messaging Routes
 * These routes handle messaging for admin users using the admin guard
 */
Route::prefix('messaging')->name('messaging.')->group(function () {
    // Get notifications for admin users
    Route::get('/notifications', function () {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return empty array for now - can be implemented when notification system is ready
        return response()->json([]);
    })->name('notifications');

    // Get message threads for admin users
    Route::get('/threads', function () {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return empty array for now - can be implemented when messaging system is ready
        return response()->json([]);
    })->name('threads');
});

// Instructor Dashboard
Route::get('/instructors', [
    App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
    'dashboard'
])->name('instructors.dashboard');

// Instructor Validation API endpoint
Route::get('/instructors/validate', [
    App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
    'validateInstructorSession'
])->name('instructors.validate');

// Instructor Data Streams API endpoints
Route::prefix('/instructors/data')->name('instructors.data.')->group(function () {
    // Stream 1: Instructor data (already handled by validate endpoint)

    // Stream 2: Classroom data
    Route::get('/classroom', [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'getClassroomData'
    ])->name('classroom');

    // Stream 3: Students data
    Route::get('/students', [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'getStudentsData'
    ])->name('students');
});

// Support Dashboard
Route::get('/support', [
    App\Http\Controllers\Admin\SupportDashboardController::class,
    'index'
])->name('support.dashboard');

// FilePond Demo (legacy route - redirects to admin center)
Route::get('/filepond-demo', function () {
    return redirect()->route('admin.media-manager.index');
})->name('filepond.demo');

// Media Upload Routes (FilePond integration)
Route::post('/upload', [
    App\Http\Controllers\Admin\MediaController::class,
    'upload'
])->name('media.upload');

Route::delete('/upload/revert', [
    App\Http\Controllers\Admin\MediaController::class,
    'revert'
])->name('media.revert');

Route::get('/upload/{uploadId}', [
    App\Http\Controllers\Admin\MediaController::class,
    'getUploadInfo'
])->name('media.info');

Route::post('/upload/finalize', [
    App\Http\Controllers\Admin\MediaController::class,
    'finalize'
])->name('media.finalize');

// Admin Center - Settings (already set up)
require __DIR__ . '/admin/settings_routes.php';

// Admin Center - Media Manager
require __DIR__ . '/admin/media_manager_routes.php';

// Admin Center - Admin Users
require __DIR__ . '/admin/admin_user_routes.php';

// Student Management
require __DIR__ . '/admin/student_routes.php';

// Course Management
require __DIR__ . '/admin/course_routes.php';

// Lesson Management
require __DIR__ . '/admin/lesson_routes.php';

// Course Dates (Scheduling)
Route::prefix('course-dates')->name('course-dates.')->group(function () {
    Route::get('/', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'index'
    ])->name('index');

    Route::get('/create', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'create'
    ])->name('create');

    Route::post('/', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'store'
    ])->name('store');

    Route::get('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'show'
    ])->name('show');

    Route::get('/{courseDate}/edit', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'edit'
    ])->name('edit');

    Route::put('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'update'
    ])->name('update');

    Route::delete('/{courseDate}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'destroy'
    ])->name('destroy');

    Route::post('/{courseDate}/toggle-active', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'toggleActive'
    ])->name('toggle-active');

    // API route for loading course units by course
    Route::get('/course-units/{course}', [
        App\Http\Controllers\Admin\CourseDates\CourseDateController::class,
        'getCourseUnits'
    ])->name('course-units');
});

// Admin Services (search, tools, etc.)
require __DIR__ . '/admin/services_routes.php';


// Admin API Config endpoint
Route::get('/api/config', [
    AdminDashboardController::class,
    'config'
])->name('api.config');

// Test endpoint to verify admin authentication
Route::get('/api/test', function () {
    $admin = auth('admin')->user();
    return response()->json([
        'success' => true,
        'authenticated' => !!$admin,
        'user' => $admin ? [
            'id' => $admin->id,
            'name' => $admin->name ?? 'Admin User',
            'guard' => 'admin'
        ] : null,
        'timestamp' => now()->toISOString()
    ]);
})->name('api.test');

/**
 * Admin Messaging Routes
 * These routes handle messaging for admin users using the admin guard
 */
Route::prefix('messaging')->name('messaging.')->group(function () {
    // Get notifications for admin users
    Route::get('/notifications', function () {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return empty array for now - can be implemented when notification system is ready
        return response()->json([]);
    })->name('notifications');

    // Get message threads for admin users
    Route::get('/threads', function () {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return empty array for now - can be implemented when messaging system is ready
        return response()->json([]);
    })->name('threads');
});

// Instructor Dashboard
Route::get('/instructors', [
    App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
    'dashboard'
])->name('instructors.dashboard');

// Instructor Validation API endpoint
Route::get('/instructors/validate', [
    App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
    'validateInstructorSession'
])->name('instructors.validate');

// Instructor Data Streams API endpoints
Route::prefix('/instructors/data')->name('instructors.data.')->group(function () {
    // Stream 1: Instructor data (already handled by validate endpoint)

    // Stream 2: Classroom data
    Route::get('/classroom', [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'getClassroomData'
    ])->name('classroom');

    // Stream 3: Students data
    Route::get('/students', [
        App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class,
        'getStudentsData'
    ])->name('students');
});

// Support Dashboard
Route::get('/support', [
    App\Http\Controllers\Admin\SupportDashboardController::class,
    'index'
])->name('support.dashboard');

// FilePond Demo (legacy route - redirects to admin center)
Route::get('/filepond-demo', function () {
    return redirect()->route('admin.media-manager.index');
})->name('filepond.demo');

// Media Upload Routes (FilePond integration)
Route::post('/upload', [
    App\Http\Controllers\Admin\MediaController::class,
    'upload'
])->name('media.upload');

Route::delete('/upload/revert', [
    App\Http\Controllers\Admin\MediaController::class,
    'revert'
])->name('media.revert');

Route::get('/upload/{uploadId}', [
    App\Http\Controllers\Admin\MediaController::class,
    'getUploadInfo'
])->name('media.info');

Route::post('/upload/finalize', [
    App\Http\Controllers\Admin\MediaController::class,
    'finalize'
])->name('media.finalize');

// Admin Center - Settings (already set up)
require __DIR__ . '/admin/settings_routes.php';

// Admin Center - Media Manager
require __DIR__ . '/admin/media_manager_routes.php';

// Admin Center - Admin Users
require __DIR__ . '/admin/admin_user_routes.php';

// Student Management
require __DIR__ . '/admin/student_routes.php';

// Course Management
require __DIR__ . '/admin/course_routes.php';

// Lesson Management
require __DIR__ . '/admin/lesson_routes.php';

// Admin Services (search, tools, etc.)
require __DIR__ . '/admin/services_routes.php';
