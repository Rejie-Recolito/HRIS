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
            $table->string('department')->nullable();
            $table->string('inCaseVacation')->nullable();
            $table->string('withinPhilippines')->nullable();
            $table->string('abroad')->nullable();
            $table->string('inCaseSick')->nullable();
            $table->string('inHospital')->nullable();
            $table->string('outPatient')->nullable();
            $table->string('inCaseSpecialLeaveBenefits')->nullable();
            $table->string('inCaseStudyLeave')->nullable();
            $table->string('commutation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            //
        });
    }
};
