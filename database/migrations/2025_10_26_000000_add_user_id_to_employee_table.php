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
            // Add nullable user_id column. Do not force a foreign key on SQLite to avoid ALTER TABLE limitations.
            $table->unsignedBigInteger('user_id')->nullable();

            // Only add FK constraint when not using SQLite.
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }

            $table->dropColumn('user_id');
        });
    }
};
