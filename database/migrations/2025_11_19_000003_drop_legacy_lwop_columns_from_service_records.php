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
            if (Schema::hasColumn('service_records', 'leave_without_pay')) {
                $table->dropColumn('leave_without_pay');
            }
            if (Schema::hasColumn('service_records', 'leave_without_pay_from')) {
                $table->dropColumn('leave_without_pay_from');
            }
            if (Schema::hasColumn('service_records', 'leave_without_pay_to')) {
                $table->dropColumn('leave_without_pay_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            if (!Schema::hasColumn('service_records', 'leave_without_pay')) {
                $table->string('leave_without_pay')->nullable()->after('leave_without_pay_to');
            }
            if (!Schema::hasColumn('service_records', 'leave_without_pay_from')) {
                $table->date('leave_without_pay_from')->nullable()->after('place');
            }
            if (!Schema::hasColumn('service_records', 'leave_without_pay_to')) {
                $table->date('leave_without_pay_to')->nullable()->after('leave_without_pay_from');
            }
        });
    }
};
