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
        Schema::create('exams', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->string('admin_title', 32)->nullable(false);
            $table->smallInteger('num_questions')->nullable(false);
            $table->smallInteger('num_to_pass')->nullable(false);
            $table->integer('policy_expire_seconds')->default(7200)->nullable(false); // 2 hours
            $table->integer('policy_wait_seconds')->default(86400)->nullable(false); // 24 hours
            $table->smallInteger('policy_attempts')->default(2)->nullable(false);

            // Indexes for common queries
            $table->index('admin_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
