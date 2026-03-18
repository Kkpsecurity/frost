<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('course_type', 20)->default('standard')->nullable(false)->after('is_active');
            $table->unsignedSmallInteger('renewal_cycle_months')->nullable()->after('course_type');
        });

        // Seed known course types
        // Florida G28 (course_id = 3) = g_class, 24-month renewal
        DB::table('courses')->where('id', 3)->update([
            'course_type'           => 'g_class',
            'renewal_cycle_months'  => 24,
        ]);

        // Florida D40 courses (IDs 1, 2) = d_class, 24-month renewal
        DB::table('courses')->whereIn('id', [1, 2])->update([
            'course_type'           => 'd_class',
            'renewal_cycle_months'  => 24,
        ]);
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_type', 'renewal_cycle_months']);
        });
    }
};
