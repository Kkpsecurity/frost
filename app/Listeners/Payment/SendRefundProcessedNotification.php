<?php

namespace App\Listeners\Payment;

use App\Events\Payment\RefundProcessed;
use App\Notifications\Payment\RefundProcessedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRefundProcessedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(RefundProcessed $event): void
    {
        $user = $event->order->User;

        if ($user) {
            $user->notify(new RefundProcessedNotification($event->order, $event->refundAmount));
        }
    }
}
