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
        Schema::create('course_unit_lessons', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->unsignedSmallInteger('course_unit_id')->nullable(false);
            $table->unsignedSmallInteger('lesson_id')->nullable(false);
            $table->smallInteger('progress_minutes')->nullable(false);
            $table->smallInteger('instr_seconds')->nullable(false);
            $table->smallInteger('ordering')->default(1)->nullable(false);

            // Indexes for common queries
            $table->index('course_unit_id');
            $table->index('lesson_id');
            $table->index('ordering');
            $table->index(['course_unit_id', 'ordering']); // Order lessons within a unit
            $table->unique(['course_unit_id', 'lesson_id']); // Prevent duplicate lesson assignments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_unit_lessons');
    }
};
