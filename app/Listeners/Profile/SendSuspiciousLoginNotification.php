<?php

namespace App\Listeners\Profile;

use App\Events\Profile\SuspiciousLoginDetected;
use App\Notifications\Profile\SuspiciousLoginNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSuspiciousLoginNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SuspiciousLoginDetected $event): void
    {
        $event->user->notify(
            new SuspiciousLoginNotification($event->ipAddress, $event->knownIp, $event->userAgent)
        );
    }
}
