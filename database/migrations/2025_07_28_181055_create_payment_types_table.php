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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->boolean('is_active')->default(true)->nullable(false);
            $table->string('name')->nullable(false); // Payment type name (e.g., "Credit Card", "PayPal", etc.)
            $table->string('model_class')->nullable(false); // Laravel model class for this payment type
            $table->string('controller_class')->nullable(false); // Laravel controller class for processing

            // Indexes for common queries
            $table->index('is_active');
            $table->index('name');
        });

        // Insert default payment types
        DB::table('payment_types')->insert([
            [
                'id' => 1,
                'is_active' => true,
                'name' => 'Credit Card',
                'model_class' => 'App\\Models\\Payment\\CreditCardPayment',
                'controller_class' => 'App\\Http\\Controllers\\Payment\\CreditCardController'
            ],
            [
                'id' => 2,
                'is_active' => true,
                'name' => 'PayPal',
                'model_class' => 'App\\Models\\Payment\\PayPalPayment',
                'controller_class' => 'App\\Http\\Controllers\\Payment\\PayPalController'
            ],
            [
                'id' => 3,
                'is_active' => true,
                'name' => 'Bank Transfer',
                'model_class' => 'App\\Models\\Payment\\BankTransferPayment',
                'controller_class' => 'App\\Http\\Controllers\\Payment\\BankTransferController'
            ],
            [
                'id' => 4,
                'is_active' => false,
                'name' => 'Check',
                'model_class' => 'App\\Models\\Payment\\CheckPayment',
                'controller_class' => 'App\\Http\\Controllers\\Payment\\CheckController'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
