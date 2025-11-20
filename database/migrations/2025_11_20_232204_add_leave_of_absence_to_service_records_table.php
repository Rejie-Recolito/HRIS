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
        Schema::table('service_records', function (Blueprint $table) {
            if (!Schema::hasColumn('service_records', 'leave_of_absence')) {
                $table->string('leave_of_absence')->nullable()->after('station_place');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            if (Schema::hasColumn('service_records', 'leave_of_absence')) {
                $table->dropColumn('leave_of_absence');
            }
        });
    }
};
