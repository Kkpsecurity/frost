<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Browser (Web Push) Notification Channel
 *
 * Stub channel registered via Notification::extend('browser', ...) in AppServiceProvider.
 * minishlink/web-push is installed and ready for a full implementation.
 *
 * TODO: implement web push delivery using WebPush + user push subscriptions table.
 */
class BrowserChannel
{
    /**
     * Send the given notification.
     *
     * The $notification may optionally implement toBrowser($notifiable)
     * to provide a custom payload. Until the full web push implementation
     * is in place this channel silently drops the notification.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        // No-op until web push subscriptions are implemented.
        // Log at debug level so we know it was attempted without spamming the log.
        Log::debug('BrowserChannel: push skipped (not yet implemented)', [
            'notifiable_id'   => $notifiable->id ?? null,
            'notification'    => get_class($notification),
        ]);
    }
}
