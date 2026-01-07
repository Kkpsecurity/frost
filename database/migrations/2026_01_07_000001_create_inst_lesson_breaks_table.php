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
        Schema::create('inst_lesson_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inst_lesson_id')->nullable(false);
            $table->unsignedSmallInteger('break_number')->nullable(false); // 1..3
            $table->timestampTz('started_at')->useCurrent()->nullable(false);
            $table->unsignedBigInteger('started_by')->nullable(false);
            $table->timestampTz('ended_at')->nullable();
            $table->unsignedBigInteger('ended_by')->nullable();
            $table->integer('duration_seconds')->default(0)->nullable(false);

            $table->foreign('inst_lesson_id')
                ->references('id')
                ->on('inst_lesson')
                ->onDelete('cascade');

            $table->unique(['inst_lesson_id', 'break_number']);
            $table->index(['inst_lesson_id', 'ended_at']);
            $table->index(['inst_lesson_id', 'started_at']);
            $table->index('started_by');
            $table->index('ended_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inst_lesson_breaks');
    }
};
