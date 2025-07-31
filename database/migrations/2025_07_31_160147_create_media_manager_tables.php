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
        // Media Manager Files table
        Schema::create('media_manager_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('disk');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->json('metadata')->nullable();
            $table->string('collection')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['disk', 'collection']);
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Media Manager Permissions table
        Schema::create('media_manager_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('disk');
            $table->json('permissions'); // ['view', 'upload', 'delete', 'move', 'archive']
            $table->timestamps();

            $table->unique(['role', 'disk']);
        });

        // Media Manager Audit Logs table
        Schema::create('media_manager_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->string('disk');
            $table->string('file_path');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'action']);
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_manager_audit_logs');
        Schema::dropIfExists('media_manager_permissions');
        Schema::dropIfExists('media_manager_files');
    }
};
