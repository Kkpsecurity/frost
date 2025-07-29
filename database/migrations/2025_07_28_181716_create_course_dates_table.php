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
        Schema::create('course_dates', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->unsignedSmallInteger('course_unit_id')->nullable(false);
            $table->timestampTz('starts_at')->nullable(false);
            $table->timestampTz('ends_at')->nullable(false);

            // Indexes for common queries
            $table->index('is_active');
            $table->index('course_unit_id');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index(['course_unit_id', 'starts_at']);
            $table->index(['is_active', 'starts_at']); // Active sessions lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_dates');
    }
};
