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
        Schema::create('inst_lesson', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->unsignedBigInteger('inst_unit_id')->nullable(false);
            $table->unsignedSmallInteger('lesson_id')->nullable(false);
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->unsignedBigInteger('created_by')->nullable(false);
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->boolean('is_paused')->default(false)->nullable(false);

            // Indexes for common queries
            $table->index('inst_unit_id');
            $table->index('lesson_id');
            $table->index('created_by');
            $table->index('completed_by');
            $table->index('created_at');
            $table->index('is_paused');
            $table->index(['inst_unit_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inst_lesson');
    }
};
