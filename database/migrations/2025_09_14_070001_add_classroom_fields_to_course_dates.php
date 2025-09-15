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
        Schema::table('course_dates', function (Blueprint $table) {
            $table->timestamp('classroom_created_at')->nullable()->after('ends_at');
            $table->json('classroom_metadata')->nullable()->after('classroom_created_at');
            
            // Add index for the auto-creation query
            $table->index(['starts_at', 'is_active', 'classroom_created_at'], 'idx_course_dates_auto_create');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_dates', function (Blueprint $table) {
            $table->dropIndex('idx_course_dates_auto_create');
            $table->dropColumn(['classroom_created_at', 'classroom_metadata']);
        });
    }
};
