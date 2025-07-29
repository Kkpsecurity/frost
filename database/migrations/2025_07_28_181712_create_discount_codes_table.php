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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->increments('id'); // integer auto-increment primary key
            $table->string('code', 32)->nullable(false);
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->timestampTz('expires_at')->nullable();
            $table->unsignedSmallInteger('course_id')->nullable();
            $table->decimal('set_price', 5, 2)->nullable(); // Fixed price discount
            $table->smallInteger('percent')->nullable(); // Percentage discount
            $table->integer('max_count')->nullable(); // Maximum usage count
            $table->string('client', 32)->nullable(); // Client/organization identifier
            $table->uuid('uuid')->nullable();

            // Indexes for common queries
            $table->unique('code'); // Discount codes should be unique
            $table->index('created_at');
            $table->index('expires_at');
            $table->index('course_id');
            $table->index('client');
            $table->index('uuid');
            $table->index(['code', 'expires_at']); // Active codes lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
