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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // bigint primary key with auto increment

            // User status and role
            $table->boolean('is_active')->default(true);
            $table->smallInteger('role_id')->default(5); // Foreign key to roles table

            // User name fields
            $table->string('lname', 255); // Last name - required
            $table->string('fname', 255); // First name - required

            // Authentication fields
            $table->string('email', 255)->unique(); // Email - required and unique
            $table->string('password', 100)->nullable(); // Password - nullable
            $table->string('remember_token', 100)->nullable(); // Remember token - nullable

            // Timestamps with timezone (PostgreSQL timestamptz)
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->timestampTz('email_verified_at')->nullable();

            // Profile fields
            $table->string('avatar', 255)->nullable(); // Avatar path - nullable
            $table->boolean('use_gravatar')->default(false); // Use Gravatar - default false

            // Student information as JSON
            $table->json('student_info')->nullable(); // JSON field for student data

            // Email preferences
            $table->boolean('email_opt_in')->default(false); // Email opt-in - default false

            // Foreign key constraint (uncomment if roles table exists)
            // $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
