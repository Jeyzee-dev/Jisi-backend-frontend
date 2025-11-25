<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'decline_reason')) {
                $table->text('decline_reason')->nullable()->after('status')->comment('Reason for declining the appointment');
            }
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'decline_reason')) {
                $table->dropColumn('decline_reason');
            }
        });
    }
};
