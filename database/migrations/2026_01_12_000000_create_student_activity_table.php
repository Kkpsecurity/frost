<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates student_activity table for tracking all student actions and site interactions
     * Used for: audit trail, compliance, analytics, support troubleshooting
     */
    public function up(): void
    {
        Schema::create('student_activity', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Student Reference
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Optional Context References
            $table->unsignedBigInteger('course_auth_id')->nullable();
            $table->unsignedBigInteger('course_date_id')->nullable();
            $table->unsignedBigInteger('student_unit_id')->nullable();
            $table->unsignedBigInteger('inst_unit_id')->nullable();

            $table->foreign('course_auth_id')->references('id')->on('course_auths')->onDelete('set null');
            $table->foreign('course_date_id')->references('id')->on('course_dates')->onDelete('set null');
            $table->foreign('student_unit_id')->references('id')->on('student_units')->onDelete('set null');
            $table->foreign('inst_unit_id')->references('id')->on('inst_units')->onDelete('set null');

            // Activity Classification
            $table->string('category', 50)->index(); // entry, navigation, interaction, agreement, system
            $table->string('activity_type', 100)->index(); // Specific action type
            $table->text('description')->nullable(); // Human-readable description

            // Activity Data
            $table->json('data')->nullable(); // Flexible storage for event-specific data
            $table->json('metadata')->nullable(); // System metadata (IP, user agent, etc.)

            // Session Context
            $table->string('session_id', 100)->nullable()->index(); // Browser session ID
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('url', 500)->nullable(); // Page/endpoint accessed

            // Tab Visibility Tracking
            $table->timestamp('started_at')->nullable(); // When activity started
            $table->timestamp('ended_at')->nullable(); // When activity ended
            $table->integer('duration_seconds')->nullable(); // Duration for timed activities

            // Timestamps
            $table->timestamps(); // created_at, updated_at

            // Indexes for Performance
            $table->index(['user_id', 'created_at']);
            $table->index(['category', 'activity_type']);
            $table->index(['session_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activity');
    }
};
