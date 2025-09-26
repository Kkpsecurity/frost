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
        // Drop the primary key constraint first
        Schema::table('orders', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Change the column type to integer
        DB::statement('ALTER TABLE orders ALTER COLUMN id TYPE integer USING id::integer');

        // Re-add the primary key constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->primary('id');
        });

        // Update the sequence if it exists
        DB::statement("SELECT setval(pg_get_serial_sequence('orders', 'id'), COALESCE(MAX(id), 1)) FROM orders");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the primary key constraint first
        Schema::table('orders', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Change the column type back to smallint (this may fail if there are values > 32767)
        DB::statement('ALTER TABLE orders ALTER COLUMN id TYPE smallint USING id::smallint');

        // Re-add the primary key constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->primary('id');
        });

        // Update the sequence if it exists
        DB::statement("SELECT setval(pg_get_serial_sequence('orders', 'id'), COALESCE(MAX(id), 1)) FROM orders");
    }
};
