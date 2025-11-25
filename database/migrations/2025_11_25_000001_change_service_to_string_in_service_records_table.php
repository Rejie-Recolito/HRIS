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
            $table->string('service_to_tmp')->nullable()->after('service_from');
        });
        // Copy data from old service_to to service_to_tmp
        \DB::statement("UPDATE service_records SET service_to_tmp = service_to");
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn('service_to');
        });
        Schema::table('service_records', function (Blueprint $table) {
            $table->string('service_to')->nullable()->after('service_from');
        });
        // Copy data back
        \DB::statement("UPDATE service_records SET service_to = service_to_tmp");
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn('service_to_tmp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->date('service_to_tmp')->nullable()->after('service_from');
        });
        // Copy data from string to date where possible
        \DB::statement("UPDATE service_records SET service_to_tmp = NULL");
        \DB::statement("UPDATE service_records SET service_to_tmp = service_to WHERE service_to IS NOT NULL AND service_to != 'Present'");
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn('service_to');
        });
        Schema::table('service_records', function (Blueprint $table) {
            $table->date('service_to')->nullable()->after('service_from');
        });
        \DB::statement("UPDATE service_records SET service_to = service_to_tmp");
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn('service_to_tmp');
        });
    }
};
