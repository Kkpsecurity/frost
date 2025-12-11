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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->unsignedSmallInteger('lesson_id')->nullable(false);
            $table->unsignedSmallInteger('eq_spec_id')->nullable();
            $table->smallInteger('correct')->nullable(false); // Which answer is correct (1-5)
            $table->text('question')->nullable(false);
            $table->text('answer_1')->nullable(false);
            $table->text('answer_2')->nullable(false);
            $table->text('answer_3')->nullable();
            $table->text('answer_4')->nullable();
            $table->text('answer_5')->nullable();
            $table->timestampTz('deact_at')->nullable(); // When question was deactivated
            $table->unsignedBigInteger('deact_by')->nullable(); // Who deactivated it

            // Indexes for common queries
            $table->index('lesson_id');
            $table->index('eq_spec_id');
            $table->index('deact_at');
            $table->index('deact_by');
            $table->index(['lesson_id', 'deact_at']); // Active questions for a lesson
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
