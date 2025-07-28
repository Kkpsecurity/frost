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
        Schema::create('student_unit', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Foreign key relationships
            $table->unsignedBigInteger('course_auth_id')->notNull();
            $table->unsignedSmallInteger('course_unit_id')->notNull();
            $table->unsignedBigInteger('course_date_id')->notNull();
            $table->unsignedBigInteger('inst_unit_id')->notNull();

            // Timestamps with timezone (PostgreSQL timestamptz)
            $table->timestampTz('created_at')->notNull()->useCurrent();
            $table->timestampTz('updated_at')->notNull()->useCurrent();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('ejected_at')->nullable();

            // Additional fields
            $table->string('ejected_for', 255)->nullable();
            $table->json('verified')->nullable();
            $table->boolean('unit_completed')->notNull()->default(false);

            // Add indexes for foreign keys (foreign key constraints can be added later)
            $table->index('course_auth_id');
            $table->index('course_unit_id');
            $table->index('course_date_id');
            $table->index('inst_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_unit');
    }
};
