<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentFailed;
use App\Notifications\Payment\PaymentFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentFailedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        $user = $event->order->User;

        if ($user) {
            $user->notify(new PaymentFailedNotification(
                $event->order,
                $event->payment,
                $event->reason
            ));
        }
    }
}
