<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages Web Push subscriptions for the authenticated user.
 *
 * Routes (web.php):
 *   POST   /push/subscribe    → store()
 *   DELETE /push/subscribe    → destroy()
 *   GET    /push/subscription → status()
 */
class PushSubscriptionController extends Controller
{
    /**
     * Store (or update) a push subscription for the current user.
     *
     * Expected JSON body:
     * {
     *   "endpoint":  "https://fcm.googleapis.com/fcm/send/...",
     *   "keys": {
     *       "p256dh": "...",
     *       "auth":   "..."
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'      => ['required', 'string', 'max:2048'],
            'keys.p256dh'   => ['required', 'string'],
            'keys.auth'     => ['required', 'string'],
        ]);

        $user = Auth::user();

        PushSubscription::updateOrCreate(
            [
                'user_id'  => $user->id,
                'endpoint' => $validated['endpoint'],
            ],
            [
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            ]
        );

        return response()->json(['status' => 'subscribed'], 201);
    }

    /**
     * Remove all push subscriptions for the current user that match
     * the given endpoint.  Called when the user unsubscribes a device.
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $validated['endpoint'])
            ->delete();

        return response()->json(['status' => 'unsubscribed']);
    }

    /**
     * Returns whether the current user has any active push subscriptions.
     * Used by the JS UI to reflect the current subscription state.
     */
    public function status(): JsonResponse
    {
        $count = PushSubscription::where('user_id', Auth::id())->count();

        return response()->json([
            'subscribed'   => $count > 0,
            'count'        => $count,
            'vapid_public' => config('webpush.vapid.public_key'),
        ]);
    }
}
