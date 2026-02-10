<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentPending;
use App\Notifications\Payment\PaymentPendingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentPendingNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentPending $event): void
    {
        $user = $event->order->User;

        if ($user) {
            $user->notify(new PaymentPendingNotification($event->order, $event->payment));
        }
    }
}
