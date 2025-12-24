<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds session management, pause tracking, progress tracking,
     * quota tracking, and failed lesson recovery fields to
     * self_study_lessons table.
     */
    public function up(): void
    {
        Schema::table('self_study_lessons', function (Blueprint $table) {
            // Session management
            $table->string('session_id', 36)->nullable()->after('lesson_id')
                  ->comment('UUID for session tracking and local storage linkage');
            $table->integer('session_duration_minutes')->nullable()->after('session_id')
                  ->comment('Total session time: lesson length + buffer');
            $table->timestampTz('session_expires_at')->nullable()->after('session_duration_minutes')
                  ->comment('When session automatically expires');
            
            // Pause tracking
            $table->integer('total_pause_minutes_allowed')->nullable()->after('session_expires_at')
                  ->comment('Total pause time allowed (calculated by algorithm)');
            $table->integer('total_pause_minutes_used')->default(0)->after('total_pause_minutes_allowed')
                  ->comment('Pause time consumed so far');
            $table->jsonb('pause_intervals')->nullable()->after('total_pause_minutes_used')
                  ->comment('Distributed pause intervals [5, 15, 10]');
            
            // Progress tracking
            $table->integer('video_duration_seconds')->nullable()->after('pause_intervals')
                  ->comment('Total video length in seconds');
            $table->integer('playback_progress_seconds')->default(0)->after('video_duration_seconds')
                  ->comment('Current playback position (for resume)');
            $table->decimal('completion_percentage', 5, 2)->default(0)->after('playback_progress_seconds')
                  ->comment('Percentage of video watched');
            
            // Quota tracking
            $table->integer('quota_consumed_minutes')->nullable()->after('credit_minutes')
                  ->comment('Quota deducted (rounded to standard increment)');
            $table->string('quota_status', 20)->default('pending')->after('quota_consumed_minutes')
                  ->comment('pending/consumed/refunded');
            
            // Failed lesson recovery
            $table->unsignedBigInteger('original_student_lesson_id')->nullable()->after('lesson_id')
                  ->comment('Links to failed online lesson if this is a redo');
            $table->boolean('is_redo')->default(false)->after('original_student_lesson_id')
                  ->comment('True if this is a redo of failed online lesson');
            $table->boolean('redo_passed')->default(false)->after('is_redo')
                  ->comment('True if redo was completed successfully');
            
            // Indexes for performance
            $table->index('session_id');
            $table->index(['course_auth_id', 'lesson_id']);
            $table->index('is_redo');
            
            // Foreign key constraint
            $table->foreign('original_student_lesson_id')
                  ->references('id')
                  ->on('student_lesson')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('self_study_lessons', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['original_student_lesson_id']);
            
            // Drop indexes
            $table->dropIndex(['session_id']);
            $table->dropIndex(['course_auth_id', 'lesson_id']);
            $table->dropIndex(['is_redo']);
            
            // Drop columns
            $table->dropColumn([
                'session_id',
                'session_duration_minutes',
                'session_expires_at',
                'total_pause_minutes_allowed',
                'total_pause_minutes_used',
                'pause_intervals',
                'video_duration_seconds',
                'playback_progress_seconds',
                'completion_percentage',
                'quota_consumed_minutes',
                'quota_status',
                'original_student_lesson_id',
                'is_redo',
                'redo_passed',
            ]);
        });
    }
};
