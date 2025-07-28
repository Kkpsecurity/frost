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
            $table->bigInteger('range_id')->nullable(false);
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->date('start_date')->nullable(false);
            $table->date('end_date')->nullable();
            $table->decimal('price', 5, 2)->nullable(false); // Price with 5 digits total, 2 decimal places
            $table->string('times', 64)->nullable(false); // Available times
            $table->boolean('appt_only')->default(false)->nullable(false); // Appointment only

            // Indexes for common queries
            $table->index('range_id');
            $table->index('is_active');
            $table->index('start_date');
            $table->index(['range_id', 'is_active']);
            $table->index(['range_id', 'start_date']);

            // Foreign key constraint
            $table->foreign('range_id')->references('id')->on('ranges')->onDelete('cascade');
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
