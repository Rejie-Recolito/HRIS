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
        // NOTE: changing existing column nullability requires doctrine/dbal package.
        Schema::table('employee', function (Blueprint $table) {
            if (Schema::hasColumn('employee', 'phone_number')) {
                // make phone_number NOT NULL
                $table->string('phone_number')->nullable(false)->change();
            }
            if (Schema::hasColumn('employee', 'email_address')) {
                // make email_address NOT NULL
                $table->string('email_address')->nullable(false)->change();
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
                $table->string('email_address')->nullable()->change();
            }
            if (Schema::hasColumn('employee', 'phone_number')) {
                $table->string('phone_number')->nullable()->change();
            }
        });
    }
};
