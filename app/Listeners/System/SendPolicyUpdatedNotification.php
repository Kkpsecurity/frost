<?php

namespace App\Listeners\System;

use App\Events\System\PolicyUpdated;
use App\Models\User;
use App\Notifications\System\PolicyUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a PolicyUpdated notification to every active student.
 *
 * Non-controllable — always sent regardless of notification preferences.
 * Uses chunking to avoid memory pressure on large student sets.
 */
class SendPolicyUpdatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PolicyUpdated $event): void
    {
        $notification = new PolicyUpdatedNotification(
            $event->policyName,
            $event->summary,
            $event->url,
        );

        User::where('role_id', 5)
            ->where('is_active', true)
            ->select('id')
            ->chunkById(200, function ($users) use ($notification) {
                foreach ($users as $user) {
                    $fullUser = User::find($user->id);
                    if (! $fullUser) {
                        continue;
                    }
                    try {
                        $fullUser->notify($notification);
                    } catch (\Throwable $e) {
                        Log::error('PolicyUpdated notification failed', [
                            'user_id' => $user->id,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            });
    }
}
