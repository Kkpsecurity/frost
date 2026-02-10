<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            \App\Listeners\SendEmailVerifiedNotification::class,
        ],

        // Payment Events
        \App\Events\Payment\PaymentCompleted::class => [
            \App\Listeners\Payment\SendPaymentSuccessNotifications::class,
        ],
        \App\Events\Payment\PaymentFailed::class => [
            \App\Listeners\Payment\SendPaymentFailedNotification::class,
        ],
        \App\Events\Payment\PaymentPending::class => [
            \App\Listeners\Payment\SendPaymentPendingNotification::class,
        ],
        \App\Events\Payment\PaymentMethodAdded::class => [
            \App\Listeners\Payment\SendPaymentMethodAddedNotification::class,
        ],
        \App\Events\Payment\PaymentMethodRemoved::class => [
            \App\Listeners\Payment\SendPaymentMethodRemovedNotification::class,
        ],
        \App\Events\Payment\RefundProcessed::class => [
            \App\Listeners\Payment\SendRefundProcessedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
