<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_questions', function (Blueprint $table) {
            $table->id();
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('updated_at')->useCurrent()->nullable(false);

            $table->unsignedBigInteger('course_date_id')->nullable(false);
            $table->unsignedBigInteger('student_id')->nullable(false);

            $table->string('topic', 80)->nullable(false);
            $table->string('urgency', 20)->nullable(false); // normal|urgent
            $table->text('question')->nullable(false);

            $table->string('status', 40)->nullable(false)->default('received');
            $table->timestampTz('held_at')->nullable();

            $table->unsignedBigInteger('answered_by')->nullable();
            $table->timestampTz('answered_at')->nullable();
            $table->string('answer_visibility', 20)->nullable(); // private|public
            $table->text('answer_text')->nullable();

            $table->string('ai_status', 40)->nullable(); // queued|ready|refused|error
            $table->timestampTz('ai_generated_at')->nullable();
            $table->string('ai_confidence', 20)->nullable(); // high|med|low
            $table->jsonb('ai_sources')->nullable();
            $table->text('ai_answer_instructor')->nullable();
            $table->text('ai_answer_student')->nullable();

            $table->index('course_date_id');
            $table->index('student_id');
            $table->index(['course_date_id', 'created_at']);
            $table->index(['course_date_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_questions');
    }
};
