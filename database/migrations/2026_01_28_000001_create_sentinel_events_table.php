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
        Schema::create('sentinel_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index();
            $table->json('event_data');
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info')->index();
            $table->boolean('sent_to_n8n')->default(false)->index();
            $table->json('n8n_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index('created_at');
            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentinel_events');
    }
};
