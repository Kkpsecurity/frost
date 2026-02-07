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
        Schema::table('self_study_lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('self_study_lessons', 'dnc_at')) {
                $table->timestampTz('dnc_at')
                    ->nullable()
                    ->after('completed_at')
                    ->comment('Did Not Complete - for failed self-study lessons');

                $table->index('dnc_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('self_study_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('self_study_lessons', 'dnc_at')) {
                $table->dropIndex(['dnc_at']);
                $table->dropColumn('dnc_at');
            }
        });
    }
};
