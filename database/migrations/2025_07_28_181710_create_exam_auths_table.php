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
        Schema::create('exam_auths', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->unsignedBigInteger('course_auth_id')->nullable(false);
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->nullable(false);
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('next_attempt_at')->nullable(false);
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('hidden_at')->nullable();
            $table->unsignedBigInteger('hidden_by')->nullable();
            $table->string('score')->nullable(); // varchar for flexible score formats
            $table->boolean('is_passed')->default(false)->nullable(false);
            $table->json('question_ids')->nullable(); // JSON indexed array
            $table->json('answers')->nullable(); // JSON hash
            $table->json('incorrect')->nullable(); // JSON hash

            // Indexes for common queries
            $table->index('course_auth_id');
            $table->index('uuid');
            $table->index('created_at');
            $table->index('expires_at');
            $table->index('next_attempt_at');
            $table->index('completed_at');
            $table->index('is_passed');
            $table->index('hidden_at');
            $table->index('hidden_by');
            $table->index(['course_auth_id', 'is_passed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_auths');
    }
};
