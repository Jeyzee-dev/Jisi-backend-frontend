<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Documents table for storing notary documents
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('file_path'); // storage path
            $table->string('document_type'); // ID proof, power of attorney, contract, etc.
            $table->text('description')->nullable();
            $table->string('status')->default('uploaded'); // uploaded, signed, archived, deleted
            $table->json('metadata')->nullable(); // page count, scanned status, etc.
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'appointment_id']);
            $table->index(['document_type', 'status']);
        });

        // Document versions for audit trail
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('filename');
            $table->string('file_path');
            $table->integer('version_number');
            $table->string('change_type'); // upload, modification, signature, scan
            $table->text('change_description')->nullable();
            $table->json('change_metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['document_id', 'version_number']);
        });

        // Audit logs for security and compliance
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // create, update, delete, view, download, login, logout, etc.
            $table->string('entity_type'); // User, Appointment, Document, Message, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('status')->default('success'); // success, failed, unauthorized
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'entity_type']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
