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
        Schema::create('zoom_creds', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('zoom_email', 255)->notNull();
            $table->text('zoom_password')->notNull();
            $table->text('zoom_passcode')->notNull();
            $table->string('zoom_status', 16)->notNull()->default('disabled');
            $table->string('pmi', 16)->notNull();
            $table->boolean('use_pmi')->notNull()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_creds');
    }
};
