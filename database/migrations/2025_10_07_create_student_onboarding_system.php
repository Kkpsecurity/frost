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
        // Add onboarding tracking fields to student_unit table
        Schema::table('student_unit', function (Blueprint $table) {
            $table->timestamp('session_entered_at')->nullable()->after('created_at');
            $table->boolean('onboarding_completed')->default(false)->after('session_entered_at');
            $table->timestamp('agreement_accepted_at')->nullable()->after('onboarding_completed');
            $table->timestamp('rules_acknowledged_at')->nullable()->after('agreement_accepted_at');
            $table->timestamp('identity_verified_at')->nullable()->after('rules_acknowledged_at');
            
            // Add index for onboarding queries
            $table->index(['onboarding_completed', 'session_entered_at']);
        });

        // Create student_activity table for comprehensive audit tracking
        Schema::create('student_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_auth_id')->constrained('course_auth')->onDelete('cascade');
            $table->foreignId('student_unit_id')->constrained('student_unit')->onDelete('cascade');
            $table->foreignId('inst_unit_id')->nullable()->constrained('inst_unit')->onDelete('set null');
            $table->string('action', 255);
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index(['student_unit_id', 'action']);
            $table->index(['course_auth_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop student_activity table
        Schema::dropIfExists('student_activity');
        
        // Remove onboarding fields from student_unit table
        Schema::table('student_unit', function (Blueprint $table) {
            $table->dropIndex(['onboarding_completed', 'session_entered_at']);
            $table->dropColumn([
                'session_entered_at',
                'onboarding_completed', 
                'agreement_accepted_at',
                'rules_acknowledged_at',
                'identity_verified_at'
            ]);
        });
    }
};