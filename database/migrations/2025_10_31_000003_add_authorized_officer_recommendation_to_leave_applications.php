<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_applications', 'authorized_officer_recommendation')) {
                $table->string('authorized_officer_recommendation')->nullable()->after('authorized_officer');
            }
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
            if (Schema::hasColumn('leave_applications', 'authorized_officer_recommendation')) {
                $table->dropColumn('authorized_officer_recommendation');
            }
        });
    }
};
