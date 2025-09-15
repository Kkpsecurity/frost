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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('course_date_id')->unique(); // Prevents duplicates - idempotency
            $table->unsignedSmallInteger('course_unit_id');
            $table->string('title', 255);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->enum('modality', ['online', 'in_person'])->default('online');
            $table->string('location', 255)->nullable(); // physical location or "online"
            $table->enum('status', ['preparing', 'ready', 'live', 'completed', 'cancelled'])->default('preparing');
            
            // Meeting resources
            $table->string('meeting_url', 500)->nullable();
            $table->string('meeting_id', 100)->nullable();
            $table->json('meeting_config')->nullable(); // zoom settings, etc.
            $table->text('join_instructions')->nullable();
            
            // Capacity & policies
            $table->integer('capacity')->default(30);
            $table->enum('waitlist_policy', ['none', 'automatic', 'manual'])->default('none');
            $table->timestamp('late_join_cutoff')->nullable();
            
            // Audit fields
            $table->timestamp('classroom_created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->json('creation_metadata')->nullable(); // stores creation context
            
            $table->timestamps();
            
            // Indexes
            $table->index('course_date_id');
            $table->index('course_unit_id');
            $table->index('status');
            $table->index('starts_at');
            $table->index(['status', 'starts_at']); // for queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
