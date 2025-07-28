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
        Schema::create('inst_licenses', function (Blueprint $table) {
            $table->increments('id'); // integer auto-increment primary key
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->string('license', 16)->nullable(false);
            $table->date('expires_at')->nullable(false);

            // Indexes for common queries
            $table->index('user_id');
            $table->index('license');
            $table->index('expires_at');
            $table->index(['user_id', 'license']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inst_licenses');
    }
};
