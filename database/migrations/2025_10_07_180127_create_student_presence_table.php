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
        Schema::create('student_presence', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('course_auth_id')->unsigned();
            $table->bigInteger('course_date_id')->unsigned()->nullable();
            $table->string('presence_type', 20)->default('online'); // 'online', 'offline'
            $table->timestamp('arrived_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('session_started_at')->nullable();
            $table->timestamp('session_ended_at')->nullable();
            $table->string('status', 20)->default('present'); // 'present', 'in_session', 'completed', 'left'
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_auth_id')->references('id')->on('course_auth')->onDelete('cascade');
            $table->foreign('course_date_id')->references('id')->on('course_date')->onDelete('set null');

            // Indexes for performance
            $table->index(['user_id', 'course_date_id'], 'idx_student_presence_user_date');
            $table->index('status', 'idx_student_presence_status');
            $table->index('presence_type', 'idx_student_presence_type');
            $table->index('arrived_at', 'idx_student_presence_arrived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_presence');
    }
};
