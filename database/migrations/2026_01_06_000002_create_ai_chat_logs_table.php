<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->timestampTz('created_at')->useCurrent()->nullable(false);

            $table->unsignedBigInteger('instructor_question_id')->nullable(false);
            $table->unsignedBigInteger('requested_by')->nullable();

            $table->text('prompt')->nullable(false);
            $table->jsonb('sources')->nullable();
            $table->jsonb('response')->nullable();
            $table->string('decision', 40)->nullable(false)->default('unknown'); // answered|refused|error

            $table->index('instructor_question_id');
            $table->index('requested_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_logs');
    }
};
