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
        Schema::create('course_units', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->unsignedSmallInteger('course_id')->nullable(false);
            $table->string('title', 64)->nullable();
            $table->string('admin_title', 64)->nullable();
            $table->smallInteger('ordering')->default(1)->nullable(false);

            // Indexes for common queries
            $table->index('course_id');
            $table->index('ordering');
            $table->index(['course_id', 'ordering']); // Order units within a course
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_units');
    }
};
