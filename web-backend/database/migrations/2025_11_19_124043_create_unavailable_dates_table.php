<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unavailable_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->boolean('all_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unavailable_dates');
    }
};