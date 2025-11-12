<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('notes');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
        });
    }

    public function down()
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
