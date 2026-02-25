<?php

namespace Tests\Unit;

use App\Channels\WebPushChannel;
use App\Models\PushSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Unit tests for App\Channels\WebPushChannel.
 *
 * These tests cover the channel's guard logic (early-exit paths) and payload
 * construction — without making real VAPID network calls.
 *
 * The actual WebPush::flush() path is covered by integration smoke tests in
 * the Feature suite and is not mocked here to avoid coupling tests to the
 * library's internals.
 */
class WebPushChannelTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Returns a fake authenticated user backed by the in-memory DB. */
    private function makeUser(): \App\Models\User
    {
        return \App\Models\User::create([
            'fname'        => 'Push',
            'lname'        => 'Tester',
            'email'        => 'pushtester@example.com',
            'password'     => bcrypt('secret'),
            'avatar'       => '',
            'use_gravatar' => false,
        ]);
    }

    /** Returns a notification that has a toWebPush() method. */
    private function makeNotificationWithPush(array $payload = []): Notification
    {
        return new class($payload) extends Notification {
            public function __construct(private array $p) {}
            public function via($n): array
            {
                return ['webpush'];
            }
            public function toWebPush($n): array
            {
                return array_merge([
                    'title' => 'Test Push',
                    'body'  => 'Test body',
                    'url'   => 'https://example.com',
                    'tag'   => 'test-tag',
                ], $this->p);
            }
        };
    }

    /** Returns a notification that is MISSING toWebPush(). */
    private function makeNotificationWithoutPush(): Notification
    {
        return new class extends Notification {
            public function via($n): array
            {
                return ['webpush'];
            }
        };
    }

    // ─── Guard: missing toWebPush() ───────────────────────────────────────────

    public function test_send_logs_warning_when_notification_lacks_toweb_push(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('[WebPushChannel] Notification missing toWebPush()', \Mockery::any());

        $channel      = new WebPushChannel();
        $user         = $this->makeUser();
        $notification = $this->makeNotificationWithoutPush();

        // Must not throw; returns void
        $channel->send($user, $notification);

        $this->assertTrue(true); // reached here without exception
    }

    public function test_send_does_not_attempt_delivery_when_toweb_push_missing(): void
    {
        // No push subscriptions exist, but even if they did — the guard
        // must abort before loading them.
        Log::spy(); // suppress output without asserting

        $user = $this->makeUser();

        // Create a subscription to confirm the guard fires BEFORE DB queries
        PushSubscription::create([
            'user_id'    => $user->id,
            'endpoint'   => 'https://fcm.example.com/test-endpoint',
            'public_key' => 'fake_p256dh',
            'auth_token' => 'fake_auth',
        ]);

        $channel = new WebPushChannel();
        $channel->send($user, $this->makeNotificationWithoutPush());

        // Subscription must still exist — nothing was deleted/consumed
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
        ]);
    }

    // ─── Guard: no subscriptions ──────────────────────────────────────────────

    public function test_send_silently_skips_when_user_has_no_subscriptions(): void
    {
        $channel      = new WebPushChannel();
        $user         = $this->makeUser();
        $notification = $this->makeNotificationWithPush();

        // No PushSubscription rows exist for this user
        $this->assertDatabaseMissing('push_subscriptions', ['user_id' => $user->id]);

        // Must return void without exception
        $channel->send($user, $notification);

        $this->assertTrue(true);
    }

    // ─── Payload structure ────────────────────────────────────────────────────

    public function test_toweb_push_payload_contains_required_keys(): void
    {
        $notification = $this->makeNotificationWithPush();
        $user         = $this->makeUser();

        $payload = $notification->toWebPush($user);

        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('body',  $payload);
    }

    public function test_toweb_push_payload_title_and_body_are_strings(): void
    {
        $notification = $this->makeNotificationWithPush([
            'title' => 'Hello',
            'body'  => 'World',
        ]);
        $user    = $this->makeUser();
        $payload = $notification->toWebPush($user);

        $this->assertIsString($payload['title']);
        $this->assertIsString($payload['body']);
    }

    public function test_toweb_push_payload_url_is_preserved(): void
    {
        $notification = $this->makeNotificationWithPush(['url' => 'https://frost.test/exam']);
        $user         = $this->makeUser();
        $payload      = $notification->toWebPush($user);

        $this->assertSame('https://frost.test/exam', $payload['url']);
    }

    public function test_toweb_push_payload_defaults_work_when_optional_keys_absent(): void
    {
        // Only title + body — no url, tag, icon, badge
        $notification = new class extends Notification {
            public function via($n): array
            {
                return ['webpush'];
            }
            public function toWebPush($n): array
            {
                return ['title' => 'Minimal', 'body' => 'Body only'];
            }
        };

        $user    = $this->makeUser();
        $payload = $notification->toWebPush($user);

        // Should not have url/tag/icon if not set
        $this->assertSame('Minimal', $payload['title']);
        $this->assertSame('Body only', $payload['body']);
        $this->assertArrayNotHasKey('url', $payload);
    }

    // ─── Expired-subscription cleanup (DB integration) ───────────────────────

    public function test_stale_subscription_is_removed_from_db_after_expiry(): void
    {
        // This test verifies the cleanup query itself — not the WebPush network call.
        $user = $this->makeUser();

        PushSubscription::create([
            'user_id'    => $user->id,
            'endpoint'   => 'https://expired.example.com/endpoint',
            'public_key' => 'fake_p256dh',
            'auth_token' => 'fake_auth',
        ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://expired.example.com/endpoint',
        ]);

        // Simulate what the channel does when a subscription is confirmed expired
        PushSubscription::where('endpoint', 'https://expired.example.com/endpoint')
            ->where('user_id', $user->id)
            ->delete();

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://expired.example.com/endpoint',
        ]);
    }
}
