<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'staff', 'client'])->default('client');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->string('registration_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};