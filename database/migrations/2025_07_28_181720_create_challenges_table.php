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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->unsignedBigInteger('student_lesson_id')->nullable(false);
            $table->boolean('is_final')->default(false)->nullable(false);
            $table->boolean('is_eol')->default(false)->nullable(false); // end of lesson
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('updated_at')->useCurrent()->nullable(false);
            $table->timestampTz('expires_at')->nullable(false);
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('failed_at')->nullable();

            // Indexes for common queries
            $table->index('student_lesson_id');
            $table->index('is_final');
            $table->index('is_eol');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('expires_at');
            $table->index('completed_at');
            $table->index('failed_at');
            $table->index(['student_lesson_id', 'is_final']); // Final challenges for a lesson
            $table->index(['expires_at', 'completed_at']); // Active/pending challenges
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
