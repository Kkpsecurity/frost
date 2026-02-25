<?php

namespace App\Listeners\System;

use App\Events\System\SupportResponseSent;
use App\Notifications\System\SupportResponseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Sends a SupportResponse notification to the specific student who raised the ticket.
 */
class SendSupportResponseNotification implements ShouldQueue
{
    public function handle(SupportResponseSent $event): void
    {
        $event->student->notify(
            new SupportResponseNotification(
                $event->subject,
                $event->preview,
                $event->ticketUrl,
            )
        );
    }
}
