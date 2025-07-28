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
        Schema::create('lessons', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->string('title', 64)->nullable(false);
            $table->smallInteger('credit_minutes')->nullable(false);
            $table->integer('video_seconds')->default(0)->nullable(false);

            // Indexes for common queries
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
