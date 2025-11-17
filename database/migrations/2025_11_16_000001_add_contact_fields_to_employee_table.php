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
        Schema::table('employee', function (Blueprint $table) {
            if (!Schema::hasColumn('employee', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('place_of_birth');
            }
            if (!Schema::hasColumn('employee', 'email_address')) {
                $table->string('email_address')->nullable()->after('phone_number');
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
        Schema::table('employee', function (Blueprint $table) {
            if (Schema::hasColumn('employee', 'email_address')) {
                $table->dropColumn('email_address');
            }
            if (Schema::hasColumn('employee', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });
    }
};
