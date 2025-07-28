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
        Schema::create('exam_question_spec', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->string('name', 16)->nullable(false);

            // Indexes for common queries
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_question_spec');
    }
};
