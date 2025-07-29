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
        Schema::create('course_auths', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedSmallInteger('course_id')->nullable(false);
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('updated_at')->useCurrent()->nullable(false);
            $table->timestampTz('agreed_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->boolean('is_passed')->default(false)->nullable(false);
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->timestampTz('disabled_at')->nullable();
            $table->text('disabled_reason')->nullable();
            $table->timestampTz('submitted_at')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->string('dol_tracking', 32)->nullable(); // Department of Labor tracking
            $table->unsignedBigInteger('exam_admin_id')->nullable();
            $table->unsignedBigInteger('range_date_id')->nullable();
            $table->boolean('id_override')->default(false)->nullable(false);

            // Indexes for common queries
            $table->index('user_id');
            $table->index('course_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('completed_at');
            $table->index('is_passed');
            $table->index('start_date');
            $table->index('expire_date');
            $table->index('disabled_at');
            $table->index('submitted_by');
            $table->index('exam_admin_id');
            $table->index('range_date_id');
            $table->index(['user_id', 'course_id']); // User course enrollment lookup
            $table->index(['course_id', 'is_passed']); // Course completion stats
            $table->unique(['user_id', 'course_id']); // Prevent duplicate enrollments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_auths');
    }
};
