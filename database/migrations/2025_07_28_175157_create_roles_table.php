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
        Schema::create('roles', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('name', 16)->nullable(false);
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'sys_admin'],
            ['id' => 2, 'name' => 'admin'],
            ['id' => 3, 'name' => 'instructor'],
            ['id' => 4, 'name' => 'support'],
            ['id' => 5, 'name' => 'student'],
            ['id' => 6, 'name' => 'guest'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
