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
        Schema::create('ranges', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->string('city', 255)->nullable(false);
            $table->string('address', 255)->nullable(false);
            $table->string('inst_name', 255)->nullable(false); // Instructor name
            $table->string('inst_email', 255)->nullable();
            $table->string('inst_phone', 16)->nullable();
            $table->decimal('price', 5, 2)->nullable(false); // Price with 5 digits total, 2 decimal places
            $table->string('times', 64)->default('...')->nullable(false); // Available times
            $table->boolean('appt_only')->default(false)->nullable(false); // Appointment only
            $table->text('range_html')->nullable(); // HTML description/details

            // Indexes for common queries
            $table->index('is_active');
            $table->index('city');
            $table->index(['is_active', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranges');
    }
};
