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
        Schema::create('courses', function (Blueprint $table) {
            $table->smallIncrements('id'); // smallint auto-increment primary key
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->unsignedSmallInteger('exam_id')->nullable(false);
            $table->unsignedSmallInteger('eq_spec_id')->nullable(false);
            $table->string('title', 64)->nullable(false);
            $table->string('title_long')->nullable(); // varchar without length limit
            $table->decimal('price', 5, 2)->nullable(false);
            $table->integer('total_minutes')->nullable(false);
            $table->smallInteger('policy_expire_days')->default(180)->nullable(false);
            $table->json('dates_template')->nullable();
            $table->unsignedSmallInteger('zoom_creds_id')->default(2)->nullable(false);
            $table->boolean('needs_range')->default(false)->nullable(false);

            // Indexes for common queries
            $table->index('is_active');
            $table->index('exam_id');
            $table->index('eq_spec_id');
            $table->index('title');
            $table->index('zoom_creds_id');
            $table->index('needs_range');
            $table->index(['is_active', 'needs_range']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
