<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('self_study_lessons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_auth_id')->nullable(false);
            $table->smallInteger('lesson_id')->nullable(false);
            $table->timestampsTz(); // created_at and updated_at with timezone
            $table->timestampTz('agreed_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->integer('credit_minutes')->default(0)->nullable(false);
            $table->integer('seconds_viewed')->default(0)->nullable(false);

            // Indexes
            $table->index('course_auth_id');
            $table->index('lesson_id');

            // Foreign key constraints (assuming these reference other tables)
            // Uncomment these if the referenced tables exist:
            // $table->foreign('course_auth_id')->references('id')->on('course_auths');
            // $table->foreign('lesson_id')->references('id')->on('lessons');
        });

        // Set default values for timestamps to now()
        DB::statement('ALTER TABLE self_study_lessons ALTER COLUMN created_at SET DEFAULT now()');
        DB::statement('ALTER TABLE self_study_lessons ALTER COLUMN updated_at SET DEFAULT now()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_study_lessons');
    }
};
