<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('type'); // Added type field
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled', 'declined'])->default('pending');
            $table->text('purpose');
            $table->text('documents')->nullable();
            $table->text('notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};