<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action')->comment('e.g., create, update, delete, restore, approve, decline');
            $table->text('description');
            $table->string('model_type')->nullable()->comment('Model affected, e.g., Appointment, Service, User');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID of the model affected');
            $table->ipAddress('ip_address')->nullable();
            $table->longText('user_agent')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('action');
            $table->index('model_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
