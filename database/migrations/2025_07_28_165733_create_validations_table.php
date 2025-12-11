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
        Schema::create('validations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->notNull();
            $table->bigInteger('course_auth_id')->nullable();
            $table->bigInteger('student_unit_id')->nullable();
            $table->smallInteger('status')->notNull()->default(0);
            $table->string('id_type', 64)->nullable();
            $table->text('reject_reason')->nullable();

            // Add indexes for foreign keys
            $table->index('course_auth_id');
            $table->index('student_unit_id');
        });

        // Add UUID default after table creation for PostgreSQL compatibility
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE validations ALTER COLUMN uuid SET DEFAULT uuid_generate_v4()');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
