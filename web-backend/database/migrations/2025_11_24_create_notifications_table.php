<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'appointment_approved', 'appointment_declined', 'new_message', 'status_change', etc.
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // icon type for frontend
            $table->string('color')->nullable(); // notification color: success, warning, info, error
            $table->foreignId('related_id')->nullable(); // appointment_id, message_id, etc.
            $table->string('related_type')->nullable(); // appointment, message, etc.
            $table->json('data')->nullable(); // additional data
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(true);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        // Notification preferences table
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('email_appointment_approved')->default(true);
            $table->boolean('email_appointment_declined')->default(true);
            $table->boolean('email_appointment_reminder')->default(true);
            $table->boolean('email_new_message')->default(true);
            $table->boolean('email_status_change')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->boolean('in_app_appointment_updates')->default(true);
            $table->boolean('in_app_messages')->default(true);
            $table->boolean('in_app_reminders')->default(true);
            $table->json('quiet_hours')->nullable(); // { "enabled": true, "start": "22:00", "end": "08:00" }
            $table->timestamps();
        });

        // Notification history (archive)
        Schema::create('notification_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('delivery_method'); // email, in_app, sms, etc.
            $table->string('status'); // sent, delivered, failed, bounced
            $table->json('delivery_data')->nullable();
            $table->text('delivery_error')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_history');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};
