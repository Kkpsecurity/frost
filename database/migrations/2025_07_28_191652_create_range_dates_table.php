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
        Schema::create('range_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('range_id')->constrained('ranges')->onDelete('cascade');
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->date('start_date')->nullable(false);
            $table->date('end_date')->nullable(); // Nullable for single-day events
            $table->decimal('price', 6, 2)->nullable(false); // Price with 6 digits total, 2 decimal places
            $table->string('times', 128)->default('')->nullable(false); // Available times/schedule
            $table->boolean('appt_only')->default(false)->nullable(false); // Appointment only

            // Indexes for common queries
            $table->index('range_id');
            $table->index('is_active');
            $table->index('start_date');
            $table->index(['range_id', 'is_active']);
            $table->index(['is_active', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('range_dates');
    }
};
