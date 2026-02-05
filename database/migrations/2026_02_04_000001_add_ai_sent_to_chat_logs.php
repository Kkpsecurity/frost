<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add ai_sent column to track AI-generated instructor messages
     */
    public function up(): void
    {
        Schema::table('chat_logs', function (Blueprint $table) {
            $table->boolean('ai_sent')->default(false)->after('body');
            $table->index('ai_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_logs', function (Blueprint $table) {
            $table->dropIndex(['chat_logs_ai_sent_index']);
            $table->dropColumn('ai_sent');
        });
    }
};
