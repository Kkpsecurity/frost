<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates student_video_quota table for tracking video quota allocation,
     * usage, and refunds for self-study lessons.
     */
    public function up(): void
    {
        Schema::create('student_video_quota', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to users table
            $table->unsignedBigInteger('user_id')->unique();
            
            // Quota tracking (in hours, 2 decimal places)
            $table->decimal('total_hours', 5, 2)->default(10.00)->comment('Total video quota allocated');
            $table->decimal('used_hours', 5, 2)->default(0.00)->comment('Hours consumed from quota');
            $table->decimal('refunded_hours', 5, 2)->default(0.00)->comment('Hours refunded due to online lesson pass');
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes for performance
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_video_quota');
    }
};
