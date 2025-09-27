<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->string('status')->default('Submitted')->after('inclusive_dates');
        });
    }

    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
