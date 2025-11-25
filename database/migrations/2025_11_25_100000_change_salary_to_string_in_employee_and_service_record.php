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
        // Employee Profile: salary
        if (Schema::hasColumn('employees', 'salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('salary')->nullable()->change();
            });
        }
        // Service Record: salary and appointment_salary
        if (Schema::hasColumn('service_records', 'salary')) {
            Schema::table('service_records', function (Blueprint $table) {
                $table->string('salary')->nullable()->change();
            });
        }
        if (Schema::hasColumn('service_records', 'appointment_salary')) {
            Schema::table('service_records', function (Blueprint $table) {
                $table->string('appointment_salary')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Employee Profile: salary
        if (Schema::hasColumn('employees', 'salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->decimal('salary', 10, 2)->nullable()->change();
            });
        }
        // Service Record: salary and appointment_salary
        if (Schema::hasColumn('service_records', 'salary')) {
            Schema::table('service_records', function (Blueprint $table) {
                $table->decimal('salary', 10, 2)->nullable()->change();
            });
        }
        if (Schema::hasColumn('service_records', 'appointment_salary')) {
            Schema::table('service_records', function (Blueprint $table) {
                $table->decimal('appointment_salary', 10, 2)->nullable()->change();
            });
        }
    }
};
