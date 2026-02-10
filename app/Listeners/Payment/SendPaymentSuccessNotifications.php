<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentCompleted;
use App\Notifications\Payment\PaymentSuccessNotification;
use App\Notifications\Payment\InvoiceGeneratedNotification;
use App\Notifications\Payment\ReceiptEmailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentSuccessNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentCompleted $event): void
    {
        $user = $event->order->User;

        if ($user) {
            // Send payment success notification
            $user->notify(new PaymentSuccessNotification($event->order, $event->payment));

            // Send invoice notification
            $user->notify(new InvoiceGeneratedNotification($event->order));

            // Send receipt email
            $user->notify(new ReceiptEmailedNotification($event->order, $event->payment));
        }
    }
}
