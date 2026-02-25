<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for the Web Push subscription endpoints.
 *
 * Routes under test:
 *   POST   /push/subscribe    → PushSubscriptionController@store
 *   DELETE /push/subscribe    → PushSubscriptionController@destroy
 *   GET    /push/subscription → PushSubscriptionController@status
 */
class PushSubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeUser(string $email = 'push@example.com'): User
    {
        return User::create([
            'fname'        => 'Push',
            'lname'        => 'User',
            'email'        => $email,
            'password'     => bcrypt('secret'),
            'avatar'       => '',
            'use_gravatar' => false,
        ]);
    }

    private function validPayload(string $endpoint = 'https://fcm.googleapis.com/fcm/send/test123'): array
    {
        return [
            'endpoint' => $endpoint,
            'keys'     => [
                'p256dh' => 'fake_p256dh_key_here',
                'auth'   => 'fake_auth_token',
            ],
        ];
    }

    // ─── Authentication guards ─────────────────────────────────────────────────

    public function test_unauthenticated_cannot_subscribe(): void
    {
        $response = $this->postJson('/push/subscribe', $this->validPayload());

        $response->assertStatus(401);
    }

    public function test_unauthenticated_cannot_unsubscribe(): void
    {
        $response = $this->deleteJson('/push/subscribe', ['endpoint' => 'https://example.com']);

        $response->assertStatus(401);
    }

    public function test_unauthenticated_cannot_check_status(): void
    {
        $response = $this->getJson('/push/subscription');

        $response->assertStatus(401);
    }

    // ─── POST /push/subscribe: validation ────────────────────────────────────

    public function test_store_requires_endpoint(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $payload = $this->validPayload();
        unset($payload['endpoint']);

        $response = $this->postJson('/push/subscribe', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['endpoint']);
    }

    public function test_store_requires_p256dh_key(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $payload = $this->validPayload();
        unset($payload['keys']['p256dh']);

        $response = $this->postJson('/push/subscribe', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['keys.p256dh']);
    }

    public function test_store_requires_auth_key(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $payload = $this->validPayload();
        unset($payload['keys']['auth']);

        $response = $this->postJson('/push/subscribe', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['keys.auth']);
    }

    // ─── POST /push/subscribe: success ───────────────────────────────────────

    public function test_store_creates_subscription_and_returns_201(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->postJson('/push/subscribe', $this->validPayload());

        $response->assertStatus(201)
            ->assertJson(['status' => 'subscribed']);
    }

    public function test_store_persists_subscription_in_database(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->postJson('/push/subscribe', $this->validPayload('https://fcm.example.com/ep1'));

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id'    => $user->id,
            'endpoint'   => 'https://fcm.example.com/ep1',
            'public_key' => 'fake_p256dh_key_here',
            'auth_token' => 'fake_auth_token',
        ]);
    }

    public function test_store_upserts_when_same_endpoint_posted_twice(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $endpoint = 'https://fcm.example.com/same-endpoint';

        $this->postJson('/push/subscribe', [
            'endpoint' => $endpoint,
            'keys'     => ['p256dh' => 'key_v1', 'auth' => 'auth_v1'],
        ]);

        $this->postJson('/push/subscribe', [
            'endpoint' => $endpoint,
            'keys'     => ['p256dh' => 'key_v2', 'auth' => 'auth_v2'],
        ]);

        // Only one record for this endpoint
        $this->assertSame(
            1,
            PushSubscription::where('user_id', $user->id)
                ->where('endpoint', $endpoint)
                ->count()
        );

        // Updated to the latest keys
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id'    => $user->id,
            'endpoint'   => $endpoint,
            'public_key' => 'key_v2',
            'auth_token' => 'auth_v2',
        ]);
    }

    public function test_store_allows_multiple_subscriptions_for_different_devices(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->postJson('/push/subscribe', $this->validPayload('https://fcm.example.com/device-1'));
        $this->postJson('/push/subscribe', $this->validPayload('https://fcm.example.com/device-2'));

        $count = PushSubscription::where('user_id', $user->id)->count();
        $this->assertSame(2, $count);
    }

    public function test_store_only_creates_subscription_for_current_user(): void
    {
        $userA = $this->makeUser('a@example.com');
        $userB = $this->makeUser('b@example.com');

        $this->actingAs($userA)
            ->postJson('/push/subscribe', $this->validPayload());

        $this->assertSame(0, PushSubscription::where('user_id', $userB->id)->count());
    }

    // ─── DELETE /push/subscribe ───────────────────────────────────────────────

    public function test_destroy_removes_subscription_and_returns_200(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $endpoint = 'https://fcm.example.com/to-remove';

        PushSubscription::create([
            'user_id'    => $user->id,
            'endpoint'   => $endpoint,
            'public_key' => 'pk',
            'auth_token' => 'at',
        ]);

        $response = $this->deleteJson('/push/subscribe', ['endpoint' => $endpoint]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'unsubscribed']);

        $this->assertDatabaseMissing('push_subscriptions', [
            'user_id'  => $user->id,
            'endpoint' => $endpoint,
        ]);
    }

    public function test_destroy_is_graceful_when_subscription_does_not_exist(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->deleteJson('/push/subscribe', [
            'endpoint' => 'https://does-not-exist.example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'unsubscribed']);
    }

    public function test_destroy_does_not_remove_other_users_subscription(): void
    {
        $userA = $this->makeUser('a@example.com');
        $userB = $this->makeUser('b@example.com');

        $endpoint = 'https://fcm.example.com/shared-endpoint';

        // Both users subscribed with the same endpoint value (edge case)
        PushSubscription::create(['user_id' => $userB->id, 'endpoint' => $endpoint, 'public_key' => 'pk', 'auth_token' => 'at']);

        $this->actingAs($userA)
            ->deleteJson('/push/subscribe', ['endpoint' => $endpoint]);

        // User B's record must survive
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id'  => $userB->id,
            'endpoint' => $endpoint,
        ]);
    }

    public function test_destroy_requires_endpoint(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->deleteJson('/push/subscribe', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['endpoint']);
    }

    // ─── GET /push/subscription (status) ─────────────────────────────────────

    public function test_status_returns_not_subscribed_when_no_subscriptions(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->getJson('/push/subscription');

        $response->assertStatus(200)
            ->assertJson([
                'subscribed' => false,
                'count'      => 0,
            ]);
    }

    public function test_status_returns_subscribed_when_subscription_exists(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        PushSubscription::create([
            'user_id'    => $user->id,
            'endpoint'   => 'https://fcm.example.com/active',
            'public_key' => 'pk',
            'auth_token' => 'at',
        ]);

        $response = $this->getJson('/push/subscription');

        $response->assertStatus(200)
            ->assertJson([
                'subscribed' => true,
                'count'      => 1,
            ]);
    }

    public function test_status_count_reflects_multiple_device_subscriptions(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        PushSubscription::create(['user_id' => $user->id, 'endpoint' => 'https://ep1.example.com', 'public_key' => 'pk1', 'auth_token' => 'at1']);
        PushSubscription::create(['user_id' => $user->id, 'endpoint' => 'https://ep2.example.com', 'public_key' => 'pk2', 'auth_token' => 'at2']);
        PushSubscription::create(['user_id' => $user->id, 'endpoint' => 'https://ep3.example.com', 'public_key' => 'pk3', 'auth_token' => 'at3']);

        $response = $this->getJson('/push/subscription');

        $response->assertStatus(200)
            ->assertJson(['count' => 3, 'subscribed' => true]);
    }

    public function test_status_includes_vapid_public_key_in_response(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->getJson('/push/subscription');

        $response->assertStatus(200)
            ->assertJsonStructure(['vapid_public']);

        // Key should not be empty
        $data = $response->json();
        $this->assertNotEmpty($data['vapid_public']);
    }

    public function test_status_only_counts_current_users_subscriptions(): void
    {
        $userA = $this->makeUser('a@example.com');
        $userB = $this->makeUser('b@example.com');

        // Create subscriptions for user B only
        PushSubscription::create(['user_id' => $userB->id, 'endpoint' => 'https://b-device.example.com', 'public_key' => 'pk', 'auth_token' => 'at']);

        // User A queries status — should see 0
        $response = $this->actingAs($userA)->getJson('/push/subscription');

        $response->assertJson(['subscribed' => false, 'count' => 0]);
    }

    // ─── Cross-user isolation ─────────────────────────────────────────────────

    public function test_each_user_has_isolated_subscription_scope(): void
    {
        $userA = $this->makeUser('a@example.com');
        $userB = $this->makeUser('b@example.com');

        $this->actingAs($userA)
            ->postJson('/push/subscribe', $this->validPayload('https://a.example.com/push'));

        $this->actingAs($userB)
            ->postJson('/push/subscribe', $this->validPayload('https://b.example.com/push'));

        $this->assertSame(1, PushSubscription::where('user_id', $userA->id)->count());
        $this->assertSame(1, PushSubscription::where('user_id', $userB->id)->count());
    }
}
