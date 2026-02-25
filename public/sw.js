/**
 * Frost Academy — Web Push Service Worker
 * ----------------------------------------
 * Handles push events and notification clicks when the browser tab is closed.
 *
 * Installed by push-subscribe.js via:
 *   navigator.serviceWorker.register('/sw.js')
 *
 * Version: 1.0.0
 */

const CACHE_NAME = 'frost-sw-v1';
const NOTIFICATION_ICON = '/images/frost-icon-192.png';
const NOTIFICATION_BADGE = '/images/frost-badge-72.png';

// ─── Push event ──────────────────────────────────────────────────────────────

self.addEventListener('push', function (event) {
    let data = {};

    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = {
            title: 'Frost Academy',
            body:  event.data ? event.data.text() : 'You have a new notification.',
        };
    }

    const title   = data.title  || 'Frost Academy';
    const options = {
        body:    data.body   || '',
        icon:    data.icon   || NOTIFICATION_ICON,
        badge:   data.badge  || NOTIFICATION_BADGE,
        tag:     data.tag    || 'frost-notification',
        data: {
            url:    data.url   || '/',
            ...( data.data || {} ),
        },
        requireInteraction: false,
        renotify: true,
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ─── Notification click ───────────────────────────────────────────────────────

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
            // If a Frost tab is already open, focus it and navigate
            for (const client of windowClients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.focus();
                    return client.navigate(targetUrl);
                }
            }
            // Otherwise open a new tab
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// ─── Push subscription change (auto-renewal) ─────────────────────────────────

self.addEventListener('pushsubscriptionchange', function (event) {
    event.waitUntil(
        self.registration.pushManager.subscribe(event.oldSubscription.options).then(function (subscription) {
            // Re-register with the server
            return fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '',    // CSRF not required for service worker re-register (use signed URL if needed)
                },
                body: JSON.stringify(subscription),
            });
        })
    );
});
