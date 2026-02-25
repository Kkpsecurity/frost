<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the push_subscriptions table.
 *
 * Stores Web Push (VAPID) subscription objects per-user.
 * One user may have many subscriptions (multiple devices/browsers).
 *
 * Columns:
 *   endpoint    – The push service URL (unique per subscription)
 *   public_key  – Client-generated key used in payload encryption (p256dh)
 *   auth_token  – 16-byte auth secret for encryption (base64url)
 *   user_agent  – Optional, for display in the UI
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('endpoint');
            $table->string('public_key', 512)->nullable();
            $table->string('auth_token', 512)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // A user can only have one subscription per endpoint
            $table->unique(['user_id', 'endpoint'], 'push_subscriptions_user_endpoint_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
