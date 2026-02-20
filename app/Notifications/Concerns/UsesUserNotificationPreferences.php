<?php

namespace App\Notifications\Concerns;

trait UsesUserNotificationPreferences
{
    /**
     * Filter a notification's default channels based on user preferences.
     *
     * Preference keys are stored in user_prefs as:
     * - notification_{notificationKey} (e.g. notification_payment.payment_success)
     * - notification_channel_{channel} (e.g. notification_channel_mail)
     */
    protected function preferredChannels(
        object $notifiable,
        string $notificationKey,
        array $defaultChannels,
        bool $userControllable = true
    ): array {
        $userPrefs = [];

        try {
            if (isset($notifiable->UserPrefs)) {
                $userPrefs = $notifiable->UserPrefs->pluck('pref_value', 'pref_name')->toArray();
            }
        } catch (\Throwable $e) {
            $userPrefs = [];
        }

        if ($userControllable) {
            $enabledPrefKey = str_starts_with($notificationKey, 'notification_')
                ? $notificationKey
                : 'notification_' . $notificationKey;
            if (($userPrefs[$enabledPrefKey] ?? '1') !== '1') {
                return [];
            }
        }

        $channels = [];
        foreach ($defaultChannels as $channel) {
            if (!$userControllable) {
                $channels[] = $channel;
                continue;
            }

            $channelPrefKey = 'notification_channel_' . $channel;
            if (($userPrefs[$channelPrefKey] ?? '1') === '1') {
                $channels[] = $channel;
            }
        }

        return array_values(array_unique($channels));
    }
}
