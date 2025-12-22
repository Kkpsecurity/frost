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
        Schema::table('student_lesson', function (Blueprint $table) {
            // Lesson failure tracking
            $table->timestamp('failed_at')->nullable()->after('completed_at');
            
            // Simple failure reason text
            $table->string('failure_reason')->nullable()->after('failed_at');
            
            // Index for queries
            $table->index(['student_unit_id', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_lesson', function (Blueprint $table) {
            $table->dropIndex(['student_unit_id', 'failed_at']);
            $table->dropColumn(['failed_at', 'failure_reason']);
        });
    }
};
