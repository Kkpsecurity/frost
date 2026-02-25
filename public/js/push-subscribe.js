/**
 * Frost Academy — Web Push Subscription Manager
 * -----------------------------------------------
 * Handles requesting browser push-notification permission, subscribing via
 * the VAPID public key, and posting the subscription to the Laravel backend.
 *
 * Assumes:
 *  - window.FrostWebPush.publicKey  (VAPID base64url public key from blade)
 *  - window.FrostWebPush.csrfToken  (Laravel CSRF token from blade)
 *  - window.FrostWebPush.subscribeUrl  = '/push/subscribe'
 *  - window.FrostWebPush.unsubscribeUrl = '/push/subscribe' (DELETE)
 *  - window.FrostWebPush.statusUrl  = '/push/subscription'
 *
 * Usage (in a blade view):
 *   <script>
 *     window.FrostWebPush = {
 *       publicKey:      '{{ config("webpush.vapid.public_key") }}',
 *       csrfToken:      '{{ csrf_token() }}',
 *       subscribeUrl:   '{{ route("push.subscribe") }}',
 *       unsubscribeUrl: '{{ route("push.unsubscribe") }}',
 *       statusUrl:      '{{ route("push.status") }}',
 *     };
 *   </script>
 *   <script src="/js/push-subscribe.js" defer></script>
 */

(function () {
    'use strict';

    const cfg = window.FrostWebPush || {};

    // ── Utility: convert base64url string to Uint8Array ──────────────────────
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw     = window.atob(base64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    // ── Register service worker ───────────────────────────────────────────────
    async function registerSW() {
        if (!('serviceWorker' in navigator)) {
            throw new Error('Service workers are not supported in this browser.');
        }
        const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        // Wait for it to be ready
        await navigator.serviceWorker.ready;
        return reg;
    }

    // ── Subscribe ─────────────────────────────────────────────────────────────
    async function subscribe(registration) {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            throw new Error('Notification permission was denied.');
        }

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly:      true,
            applicationServerKey: urlBase64ToUint8Array(cfg.publicKey),
        });

        const subJson = subscription.toJSON();

        const resp = await fetch(cfg.subscribeUrl, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  cfg.csrfToken,
                'Accept':        'application/json',
            },
            body: JSON.stringify({
                endpoint: subJson.endpoint,
                keys: {
                    p256dh: subJson.keys.p256dh,
                    auth:   subJson.keys.auth,
                },
            }),
        });

        if (!resp.ok) {
            const err = await resp.json().catch(() => ({}));
            throw new Error(err.message || 'Server rejected subscription.');
        }

        return subscription;
    }

    // ── Unsubscribe ───────────────────────────────────────────────────────────
    async function unsubscribe(registration) {
        const subscription = await registration.pushManager.getSubscription();
        if (!subscription) return;

        const endpoint = subscription.endpoint;

        await subscription.unsubscribe();

        await fetch(cfg.unsubscribeUrl, {
            method:  'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ endpoint }),
        });
    }

    // ── Public API ────────────────────────────────────────────────────────────
    window.FrostPush = {

        /**
         * Subscribe this device to push notifications.
         * Returns a promise that resolves with the PushSubscription.
         */
        async enable() {
            const reg = await registerSW();
            return subscribe(reg);
        },

        /**
         * Unsubscribe this device from push notifications.
         */
        async disable() {
            const reg = await registerSW();
            return unsubscribe(reg);
        },

        /**
         * Returns true if this device is currently subscribed.
         */
        async isSubscribed() {
            if (!('serviceWorker' in navigator)) return false;
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            return sub !== null;
        },

        /**
         * Bind a toggle button element to push enable/disable logic.
         * Expected: <button id="pushToggleBtn" data-subscribed="0|1">
         */
        async bindToggleButton(btnElement) {
            if (!btnElement) return;

            const updateBtn = (subscribed) => {
                btnElement.dataset.subscribed = subscribed ? '1' : '0';
                btnElement.innerHTML = subscribed
                    ? '<i class="fas fa-bell-slash me-2"></i>Disable Push Notifications'
                    : '<i class="fas fa-bell me-2"></i>Enable Push Notifications';
                btnElement.classList.toggle('btn-danger',  subscribed);
                btnElement.classList.toggle('btn-success', !subscribed);
            };

            // Set initial state
            const subscribed = await this.isSubscribed();
            updateBtn(subscribed);

            btnElement.addEventListener('click', async () => {
                btnElement.disabled = true;
                const spinner = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
                btnElement.innerHTML = spinner + 'Please wait…';

                try {
                    const currently = btnElement.dataset.subscribed === '1';
                    if (currently) {
                        await this.disable();
                        updateBtn(false);
                        showToast('Push notifications disabled for this device.', 'info');
                    } else {
                        await this.enable();
                        updateBtn(true);
                        showToast('Push notifications enabled! You will now receive alerts on this device.', 'success');
                    }
                } catch (err) {
                    console.error('[FrostPush]', err);
                    showToast('Could not update push notifications: ' + err.message, 'danger');
                    const was = btnElement.dataset.subscribed === '1';
                    updateBtn(was); // revert
                } finally {
                    btnElement.disabled = false;
                }
            });
        },
    };

    // ── Toast helper (AdminLTE / Bootstrap) ──────────────────────────────────
    function showToast(message, type) {
        // AdminLTE toastr if available
        if (window.toastr) {
            const methods = { success: 'success', danger: 'error', info: 'info', warning: 'warning' };
            window.toastr[methods[type] || 'info'](message);
            return;
        }
        // Fallback: Bootstrap alert above main content
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top:1rem;right:1rem;z-index:9999;min-width:300px;';
        alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // ── Auto-bind on DOMContentLoaded ─────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('frostPushToggleBtn');
        if (btn && window.FrostPush) {
            window.FrostPush.bindToggleButton(btn);
        }
    });

})();
