<?php

namespace App\Listeners\Profile;

use App\Events\Profile\PasswordChanged;
use App\Notifications\Profile\PasswordChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPasswordChangedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event): void
    {
        $event->user->notify(
            new PasswordChangedNotification($event->ipAddress, $event->userAgent)
        );
    }
}
