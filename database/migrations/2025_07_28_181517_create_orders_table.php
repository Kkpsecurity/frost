<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->bigInteger('user_id')->nullable(false);
            $table->smallInteger('course_id')->nullable(false);
            $table->smallInteger('payment_type_id')->default(1)->nullable(false);
            $table->decimal('course_price', 5, 2)->nullable(false);
            $table->integer('discount_code_id')->nullable();
            $table->decimal('total_price', 5, 2)->nullable(false);
            $table->timestampsTz(); // created_at and updated_at with timezone
            $table->timestampTz('completed_at')->nullable();
            $table->bigInteger('course_auth_id')->nullable();
            $table->timestampTz('refunded_at')->nullable();
            $table->bigInteger('refunded_by')->nullable();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('course_id');
            $table->index('payment_type_id');
            $table->index('discount_code_id');
            $table->index('course_auth_id');
            $table->index('completed_at');
            $table->index('refunded_at');
            $table->index('refunded_by');
            $table->index(['user_id', 'completed_at']);
            $table->index(['course_id', 'completed_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('restrict');
            // Uncomment these when the referenced tables are created:
            // $table->foreign('course_id')->references('id')->on('courses');
            // $table->foreign('discount_code_id')->references('id')->on('discount_codes');
            // $table->foreign('course_auth_id')->references('id')->on('course_auths');
            // $table->foreign('refunded_by')->references('id')->on('users');
        });

        // Set default values for timestamps to now()
        DB::statement('ALTER TABLE orders ALTER COLUMN created_at SET DEFAULT now()');
        DB::statement('ALTER TABLE orders ALTER COLUMN updated_at SET DEFAULT now()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
