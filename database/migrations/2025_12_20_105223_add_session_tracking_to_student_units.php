<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            // Heartbeat tracking (detect disconnects)
            $table->timestamp('last_heartbeat_at')->nullable()->after('created_at');
            
            // Session expiration (created_at + 12 hours)
            $table->timestamp('session_expires_at')->nullable()->after('last_heartbeat_at');
            
            // Intentional leave tracking
            $table->timestamp('left_at')->nullable()->after('completed_at');
            
            // Indexes for performance
            $table->index('last_heartbeat_at');
            $table->index('session_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_unit', function (Blueprint $table) {
            $table->dropIndex(['last_heartbeat_at']);
            $table->dropIndex(['session_expires_at']);
            $table->dropColumn(['last_heartbeat_at', 'session_expires_at', 'left_at']);
        });
    }
};
