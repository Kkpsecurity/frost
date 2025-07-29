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
        Schema::create('inst_unit', function (Blueprint $table) {
            $table->id(); // bigint auto-increment primary key
            $table->unsignedBigInteger('course_date_id')->nullable(false);
            $table->timestampTz('created_at')->useCurrent()->nullable(false);
            $table->unsignedBigInteger('created_by')->nullable(false);
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->unsignedBigInteger('assistant_id')->nullable();

            // Indexes for common queries (foreign keys will be added later)
            $table->index('course_date_id');
            $table->index('created_by');
            $table->index('completed_by');
            $table->index('assistant_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inst_unit');
    }
};
