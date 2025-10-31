<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeparateVacationSickCreditsToLeaveApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            // Vacation-specific credits
            $table->integer('vacation_total_earned')->nullable()->after('total_earned');
            $table->integer('vacation_less_this_application')->nullable()->after('vacation_total_earned');
            $table->integer('vacation_balance')->nullable()->after('vacation_less_this_application');

            // Sick-specific credits
            $table->integer('sick_total_earned')->nullable()->after('vacation_balance');
            $table->integer('sick_less_this_application')->nullable()->after('sick_total_earned');
            $table->integer('sick_balance')->nullable()->after('sick_less_this_application');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn([
                'vacation_total_earned',
                'vacation_less_this_application',
                'vacation_balance',
                'sick_total_earned',
                'sick_less_this_application',
                'sick_balance',
            ]);
        });
    }
}
