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
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('updated_at')->useCurrent()->nullable(false);
            $table->timestampTz('hidden_at')->nullable();
            $table->unsignedBigInteger('course_date_id')->nullable(false);
            $table->unsignedBigInteger('inst_id')->nullable(); // instructor user id
            $table->unsignedBigInteger('student_id')->nullable(); // student user id
            $table->text('body')->nullable(false);

            // Indexes for common queries
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('hidden_at');
            $table->index('course_date_id');
            $table->index('inst_id');
            $table->index('student_id');
            $table->index(['course_date_id', 'created_at']); // Chat history for a session
            $table->index(['course_date_id', 'hidden_at']); // Visible chats for a session
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
