<?php

namespace App\Listeners\System;

use App\Events\System\MaintenanceScheduled;
use App\Models\User;
use App\Notifications\System\MaintenanceScheduledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a MaintenanceScheduled notification to every active student.
 *
 * Uses chunking to avoid loading every student record into memory at once.
 */
class SendMaintenanceScheduledNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MaintenanceScheduled $event): void
    {
        $notification = new MaintenanceScheduledNotification(
            $event->scheduledAt,
            $event->message,
        );

        User::where('role_id', 5)
            ->where('is_active', true)
            ->select('id')
            ->chunkById(200, function ($users) use ($notification) {
                foreach ($users as $user) {
                    // Re-fetch with light load so notification prefs are available.
                    $fullUser = User::find($user->id);
                    if (! $fullUser) {
                        continue;
                    }
                    try {
                        $fullUser->notify($notification);
                    } catch (\Throwable $e) {
                        Log::error('MaintenanceScheduled notification failed', [
                            'user_id' => $user->id,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            });
    }
}
