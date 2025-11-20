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
            // Add employee_id if it doesn't exist
            if (!Schema::hasColumn('service_records', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id');
                $table->foreign('employee_id')->references('id')->on('employee')->onDelete('cascade');
            }
            
            // Add new simplified columns
            if (!Schema::hasColumn('service_records', 'appointment_salary')) {
                $table->decimal('appointment_salary', 10, 2)->nullable()->after('appointment_status');
            }
            if (!Schema::hasColumn('service_records', 'station_place')) {
                $table->string('station_place')->nullable()->after('appointment_salary');
            }
            
            // Drop old columns that are no longer needed (safe drop)
            $columnsToDrop = [
                'name', 'age', 'date_of_birth', 'job_title', 'place_of_birth', 
                'office', 'status', 'date_of_service', 'place_of_assignment',
                'appointment_rank', 'appointment_monthly_base_pay', 'station', 
                'place', 'leave_of_absence', 'salary', 'request_status'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('service_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            if (Schema::hasColumn('service_records', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
            if (Schema::hasColumn('service_records', 'appointment_salary')) {
                $table->dropColumn('appointment_salary');
            }
            if (Schema::hasColumn('service_records', 'station_place')) {
                $table->dropColumn('station_place');
            }
        });
    }
};
