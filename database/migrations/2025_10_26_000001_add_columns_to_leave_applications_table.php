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
        Schema::table('leave_applications', function (Blueprint $table) {
            // Add missing columns referenced by the model/controller
            $table->string('department')->nullable()->after('inclusive_dates');
            $table->string('commutation')->nullable()->after('department');
            $table->string('inCaseVacation')->nullable()->after('commutation');
            $table->string('inCaseSick')->nullable()->after('inCaseVacation');
            $table->string('inHospital')->nullable()->after('inCaseSick');
            $table->string('outPatient')->nullable()->after('inHospital');
            $table->string('inCaseSpecialLeaveBenefits')->nullable()->after('outPatient');
            $table->string('inCaseStudyLeave')->nullable()->after('inCaseSpecialLeaveBenefits');
            $table->string('withinPhilippines')->nullable()->after('inCaseStudyLeave');
            $table->string('abroad')->nullable()->after('withinPhilippines');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn([
                'department',
                'commutation',
                'inCaseVacation',
                'inCaseSick',
                'inHospital',
                'outPatient',
                'inCaseSpecialLeaveBenefits',
                'inCaseStudyLeave',
                'withinPhilippines',
                'abroad',
            ]);
        });
    }
};
