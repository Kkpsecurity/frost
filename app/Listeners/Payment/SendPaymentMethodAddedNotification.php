<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentMethodAdded;
use App\Notifications\Payment\PaymentMethodAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentMethodAddedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentMethodAdded $event): void
    {
        $event->user->notify(new PaymentMethodAddedNotification($event->paymentMethod));
    }
}
