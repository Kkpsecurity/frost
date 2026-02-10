<?php

namespace App\Listeners;

use App\Notifications\Account\EmailVerifiedNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailVerifiedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // Send notification to the user
        $event->user->notify(new EmailVerifiedNotification());
    }
}
