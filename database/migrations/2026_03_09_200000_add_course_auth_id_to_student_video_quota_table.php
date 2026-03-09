<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add course_auth_id to student_video_quota so quota is tracked
     * per enrollment rather than globally per user.
     *
     * The existing unique constraint on user_id is dropped and replaced
     * with a composite unique on (user_id, course_auth_id).
     *
     * Existing rows will have course_auth_id = NULL (nullable column),
     * which is harmless — new lookups always include course_auth_id.
     */
    public function up(): void
    {
        Schema::table('student_video_quota', function (Blueprint $table) {
            // Add per-enrollment column — nullable so existing rows aren't broken
            $table->unsignedBigInteger('course_auth_id')->nullable()->after('user_id');

            // Drop the old per-user unique constraint
            $table->dropUnique(['user_id']);

            // One quota record per (user × enrollment)
            $table->unique(['user_id', 'course_auth_id']);
            $table->index('course_auth_id');

            $table->foreign('course_auth_id')
                ->references('id')
                ->on('course_auths')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('student_video_quota', function (Blueprint $table) {
            $table->dropForeign(['course_auth_id']);
            $table->dropIndex(['course_auth_id']);
            $table->dropUnique(['user_id', 'course_auth_id']);
            $table->dropColumn('course_auth_id');
            $table->unique('user_id');
        });
    }
};
