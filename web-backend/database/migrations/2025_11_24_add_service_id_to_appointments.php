<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Add service_id foreign key to link appointments to services table
            $table->foreignId('service_id')->nullable()->after('type')->constrained('services')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeignKey(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
