<?php

namespace Tests\Unit;

use App\Models\PushSubscription;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit tests for App\Models\PushSubscription.
 *
 * These tests do NOT touch the database — they only verify model metadata
 * (fillable, hidden, table name).  DB-level behaviour is covered in the
 * Feature suite (PushSubscriptionControllerTest).
 */
class PushSubscriptionModelTest extends TestCase
{
    private function makeModel(): PushSubscription
    {
        return new PushSubscription();
    }

    // ── Table name ────────────────────────────────────────────────────────────

    public function test_table_is_push_subscriptions(): void
    {
        $this->assertSame('push_subscriptions', $this->makeModel()->getTable());
    }

    // ── Fillable ──────────────────────────────────────────────────────────────

    public function test_user_id_is_fillable(): void
    {
        $this->assertContains('user_id', $this->makeModel()->getFillable());
    }

    public function test_endpoint_is_fillable(): void
    {
        $this->assertContains('endpoint', $this->makeModel()->getFillable());
    }

    public function test_public_key_is_fillable(): void
    {
        $this->assertContains('public_key', $this->makeModel()->getFillable());
    }

    public function test_auth_token_is_fillable(): void
    {
        $this->assertContains('auth_token', $this->makeModel()->getFillable());
    }

    public function test_user_agent_is_fillable(): void
    {
        $this->assertContains('user_agent', $this->makeModel()->getFillable());
    }

    public function test_fillable_has_exactly_five_fields(): void
    {
        $this->assertCount(5, $this->makeModel()->getFillable());
    }

    // ── Hidden (security — keys must not leak in JSON responses) ─────────────

    public function test_public_key_is_hidden(): void
    {
        $this->assertContains('public_key', $this->makeModel()->getHidden());
    }

    public function test_auth_token_is_hidden(): void
    {
        $this->assertContains('auth_token', $this->makeModel()->getHidden());
    }

    public function test_endpoint_is_not_hidden(): void
    {
        // Endpoint is safe to expose (it's public) — we need it in the UI
        $this->assertNotContains('endpoint', $this->makeModel()->getHidden());
    }

    // ── Timestamps ────────────────────────────────────────────────────────────

    public function test_timestamps_are_enabled(): void
    {
        $this->assertTrue($this->makeModel()->usesTimestamps());
    }
}
