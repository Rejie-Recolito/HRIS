<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionFieldsToLeaveApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->date('cert_as_of')->nullable()->after('status');
            $table->string('cert_vacation')->nullable()->after('cert_as_of');
            $table->string('cert_sick')->nullable()->after('cert_vacation');

            $table->string('recommendation')->nullable()->after('cert_sick');
            $table->text('recommendation_reason')->nullable()->after('recommendation');


            $table->string('approved_days_with_pay')->nullable()->after('recommendation_reason');
            $table->string('approved_days_without_pay')->nullable()->after('approved_days_with_pay');
            $table->string('approved_others')->nullable()->after('approved_days_without_pay');
            $table->text('disapproved_reason')->nullable()->after('approved_others');

            $table->string('authorized_officer')->nullable()->after('disapproved_reason');
            $table->date('action_date')->nullable()->after('authorized_officer');

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
                'cert_as_of',
                'cert_vacation',
                'cert_sick',
                'recommendation',
                'recommendation_reason',
                'total_earned',
                'less_this_application',
                'balance',
                'approved_days_with_pay',
                'approved_days_without_pay',
                'approved_others',
                'disapproved_reason',
                'authorized_officer',
                'action_date',
                'inclusive_from',
                'inclusive_to',
            ]);
        });
    }
}
