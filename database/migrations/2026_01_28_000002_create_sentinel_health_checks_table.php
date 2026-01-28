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
        Schema::create('sentinel_health_checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_type')->index();
            $table->enum('status', ['healthy', 'degraded', 'down'])->default('healthy')->index();
            $table->integer('response_time')->nullable()->comment('Response time in milliseconds');
            $table->json('details')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('created_at');
            $table->index(['check_type', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentinel_health_checks');
    }
};
