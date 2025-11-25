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
        // Add soft delete column to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft delete column to appointments table
        if (Schema::hasTable('appointments') && !Schema::hasColumn('appointments', 'deleted_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add is_active column to users table if not exists (for deactivation)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop soft delete columns
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'deleted_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
