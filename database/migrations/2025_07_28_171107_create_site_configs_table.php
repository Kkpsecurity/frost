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
        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->string('cast_to', 50);
            $table->string('config_name', 255);
            $table->text('config_value');
            $table->timestamps();

            // Add indexes for better performance
            $table->index('config_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_configs');
    }
};
