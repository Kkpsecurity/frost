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
        Schema::create('user_prefs', function (Blueprint $table) {
            // Primary key columns (composite primary key)
            $table->bigInteger('user_id'); // Foreign key to users table
            $table->string('pref_name', 64); // Preference name - max 64 characters

            // Preference value
            $table->string('pref_value', 255); // Preference value - max 255 characters

            // Define composite primary key
            $table->primary(['user_id', 'pref_name']);

            // Foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_prefs');
    }
};
