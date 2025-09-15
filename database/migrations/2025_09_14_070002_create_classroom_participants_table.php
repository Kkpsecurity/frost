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
        Schema::create('classroom_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedInteger('user_id'); // student
            $table->enum('role', ['student', 'instructor', 'assistant'])->default('student');
            $table->enum('status', ['enrolled', 'present', 'absent', 'late', 'excused'])->default('enrolled');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->json('metadata')->nullable(); // attendance notes, special accommodations, etc.
            
            $table->timestamps();
            
            // Prevent duplicate enrollments
            $table->unique(['classroom_id', 'user_id'], 'unique_classroom_participant');
            
            // Indexes
            $table->index('classroom_id');
            $table->index('user_id');
            $table->index(['classroom_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_participants');
    }
};
