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
        Schema::create('user_browsers', function (Blueprint $table) {
            // Primary key that's also a foreign key to users table
            $table->unsignedBigInteger('user_id')->primary();

            // Browser information
            $table->string('browser', 255)->notNull();

            // Only updated_at timestamp (no created_at based on model)
            $table->timestampTz('updated_at')->notNull()->useCurrent();

            // Foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_browsers');
    }
};
