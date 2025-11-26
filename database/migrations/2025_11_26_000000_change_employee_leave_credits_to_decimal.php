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
        Schema::table('employee', function (Blueprint $table) {
            // change integer columns to decimal with 2 fractional digits
            $table->decimal('vacation_leave_credits', 8, 2)->default(0)->change();
            $table->decimal('sick_leave_credits', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->integer('vacation_leave_credits')->default(0)->change();
            $table->integer('sick_leave_credits')->default(0)->change();
        });
    }
};
