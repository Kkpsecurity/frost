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
        Schema::create('payment_payflowpro', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable(false);
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->nullable(false);
            $table->decimal('total_price', 5, 2)->nullable(false);
            $table->timestampsTz(); // created_at and updated_at with timezone
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('refunded_at')->nullable();
            $table->bigInteger('refunded_by')->nullable();

            // Credit Card fields
            $table->timestampTz('cc_last_at')->nullable();
            $table->smallInteger('cc_last_result')->nullable();
            $table->string('cc_last_respmsg', 255)->nullable();
            $table->decimal('cc_amount', 10, 2)->nullable();
            $table->decimal('cc_fee', 10, 2)->nullable();
            $table->timestampTz('cc_transtime')->nullable();

            // PayPal fields
            $table->boolean('pp_is_sandbox')->default(false)->nullable(false);
            $table->string('pp_token_id', 36)->nullable();
            $table->string('pp_token', 32)->nullable();
            $table->bigInteger('pp_token_exp')->nullable();
            $table->smallInteger('pp_token_count')->default(0)->nullable(false);
            $table->string('pp_pnref', 64)->nullable();
            $table->string('pp_ppref', 64)->nullable();

            // JSON data fields
            $table->json('cc_last_data')->nullable();
            $table->json('cc_refund_data')->nullable();

            // Indexes for common queries
            $table->index('order_id');
            $table->index('uuid');
            $table->index('completed_at');
            $table->index('refunded_at');
            $table->index('pp_token_id');
            $table->index(['order_id', 'completed_at']);

            // Foreign key constraints (assuming orders and users tables exist)
            // Uncomment these if the referenced tables exist:
            // $table->foreign('order_id')->references('id')->on('orders');
            // $table->foreign('refunded_by')->references('id')->on('users');
        });

        // Set default values for timestamps to now()
        DB::statement('ALTER TABLE payment_payflowpro ALTER COLUMN created_at SET DEFAULT now()');
        DB::statement('ALTER TABLE payment_payflowpro ALTER COLUMN updated_at SET DEFAULT now()');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payflowpro');
    }
};
