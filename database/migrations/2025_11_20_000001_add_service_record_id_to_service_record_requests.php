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
        if (!Schema::hasTable('service_record_requests')) {
            return;
        }

        Schema::table('service_record_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_record_requests', 'service_record_id')) {
                $table->unsignedBigInteger('service_record_id')->nullable()->after('request_status');
                $table->foreign('service_record_id')->references('id')->on('service_records')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('service_record_requests')) {
            return;
        }

        Schema::table('service_record_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_record_requests', 'service_record_id')) {
                $table->dropForeign(['service_record_id']);
                $table->dropColumn('service_record_id');
            }
        });
    }
};
