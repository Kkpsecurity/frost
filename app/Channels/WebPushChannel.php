<?php

namespace App\Channels;

use App\Models\PushSubscription;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;

/**
 * Laravel notification channel that delivers Web Push (VAPID) notifications.
 *
 * Notifications must implement toWebPush(object $notifiable): array
 * Expected payload keys:
 *   title   (string, required)
 *   body    (string, required)
 *   url     (string, optional – clicked action URL)
 *   icon    (string, optional – absolute URL or path from public/)
 *   badge   (string, optional)
 *   tag     (string, optional – groups/replaces notifications by tag)
 *   data    (array,  optional – arbitrary JSON passed to service worker)
 *
 * Usage: return ['database', 'mail', 'webpush'] in via()
 */
class WebPushChannel
{
    /**
     * Send the given notification via Web Push.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Apply OPENSSL_CONF fix for Windows environments (no-op on Linux)
        $opensslConf = config('webpush.openssl_conf');
        if ($opensslConf && file_exists($opensslConf)) {
            putenv("OPENSSL_CONF={$opensslConf}");
        }

        // Ensure the notification provides a toWebPush() method
        if (! method_exists($notification, 'toWebPush')) {
            Log::warning('[WebPushChannel] Notification missing toWebPush()', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Load subscriptions for this user
        /** @var \App\Models\User $notifiable */
        $subscriptions = PushSubscription::where('user_id', $notifiable->id)->get();

        if ($subscriptions->isEmpty()) {
            return; // User has no push subscription — silently skip
        }

        // Build payload
        $payload = $notification->toWebPush($notifiable);
        $title   = $payload['title'] ?? 'New Notification';
        $body    = $payload['body']  ?? '';

        $fullPayload = json_encode([
            'title'   => $title,
            'body'    => $body,
            'url'     => $payload['url']   ?? null,
            'icon'    => $payload['icon']  ?? null,
            'badge'   => $payload['badge'] ?? null,
            'tag'     => $payload['tag']   ?? null,
            'data'    => $payload['data']  ?? [],
        ]);

        // Initialise WebPush with VAPID auth
        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => config('webpush.vapid.subject'),
                    'publicKey'  => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ]);

            $webPush->setReuseVAPIDHeaders(true);
        } catch (\Throwable $e) {
            Log::error('[WebPushChannel] Failed to initialise WebPush', [
                'error' => $e->getMessage(),
            ]);
            return;
        }

        // Queue all subscription sends
        $staleIds = [];
        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'publicKey'       => $sub->public_key,
                    'authToken'       => $sub->auth_token,
                    'contentEncoding' => 'aesgcm',
                ]);

                $webPush->queueNotification($subscription, $fullPayload);
            } catch (\Throwable $e) {
                Log::warning('[WebPushChannel] Failed to queue subscription', [
                    'subscription_id' => $sub->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        // Flush and handle reports
        try {
            /** @var MessageSentReport $report */
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getEndpoint();

                if ($report->isSubscriptionExpired()) {
                    // Remove expired subscriptions
                    $staleIds[] = PushSubscription::where('endpoint', $endpoint)
                        ->where('user_id', $notifiable->id)
                        ->value('id');
                } elseif (! $report->isSuccess()) {
                    Log::warning('[WebPushChannel] Push delivery failed', [
                        'endpoint' => substr($endpoint, 0, 80) . '...',
                        'reason'   => $report->getReason(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('[WebPushChannel] flush() exception', ['error' => $e->getMessage()]);
        }

        // Clean up expired subscriptions
        if (! empty($staleIds)) {
            PushSubscription::whereIn('id', array_filter($staleIds))->delete();
        }
    }
}
