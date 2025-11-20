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
            $table->date('service_from')->nullable()->after('place_of_assignment');
            $table->date('service_to')->nullable()->after('service_from');
            $table->string('appointment_rank')->nullable()->after('service_to');
            $table->string('appointment_designation')->nullable()->after('appointment_rank');
            $table->string('appointment_status')->nullable()->after('appointment_designation');
            $table->decimal('appointment_monthly_base_pay', 12, 2)->nullable()->after('appointment_status');
            $table->string('station')->nullable()->after('appointment_monthly_base_pay');
            $table->string('place')->nullable()->after('station');
            $table->date('leave_without_pay_from')->nullable()->after('place');
            $table->date('leave_without_pay_to')->nullable()->after('leave_without_pay_from');
            $table->string('leave_without_pay')->nullable()->after('leave_without_pay_to');
            $table->date('separation_date')->nullable()->after('leave_without_pay');
            $table->string('separation_cause')->nullable()->after('separation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn([
                'service_from', 'service_to', 'appointment_rank', 'appointment_designation', 'appointment_status',
                'appointment_monthly_base_pay', 'station', 'place', 'leave_without_pay_from', 'leave_without_pay_to',
                'leave_without_pay', 'separation_date', 'separation_cause'
            ]);
        });
    }
};
