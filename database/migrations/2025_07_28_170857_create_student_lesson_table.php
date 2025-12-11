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
        Schema::create('student_lesson', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Foreign key relationships
            $table->unsignedSmallInteger('lesson_id')->notNull();
            $table->unsignedBigInteger('student_unit_id')->notNull();
            $table->unsignedBigInteger('inst_lesson_id')->notNull();

            // Timestamps with timezone (PostgreSQL timestamptz)
            $table->timestampTz('created_at')->notNull()->useCurrent();
            $table->timestampTz('updated_at')->notNull()->useCurrent();
            $table->timestampTz('dnc_at')->nullable();
            $table->timestampTz('completed_at')->nullable();

            // Additional foreign key
            $table->unsignedBigInteger('completed_by')->nullable();

            // Add indexes for foreign keys (foreign key constraints can be added later)
            $table->index('lesson_id');
            $table->index('student_unit_id');
            $table->index('inst_lesson_id');
            $table->index('completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_lesson');
    }
};
