<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentMethodRemoved;
use App\Notifications\Payment\PaymentMethodRemovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentMethodRemovedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentMethodRemoved $event): void
    {
        $event->user->notify(new PaymentMethodRemovedNotification($event->paymentMethod));
    }
}
