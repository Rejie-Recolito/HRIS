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
        // Use change() to alter existing columns to nullable. Requires doctrine/dbal.
        Schema::table('service_records', function (Blueprint $table) {
            if (Schema::hasColumn('service_records', 'age')) {
                $table->integer('age')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'job_title')) {
                $table->string('job_title')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'place_of_birth')) {
                $table->string('place_of_birth')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'office')) {
                $table->string('office')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'status')) {
                $table->string('status')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'date_of_service')) {
                $table->date('date_of_service')->nullable()->change();
            }
            if (Schema::hasColumn('service_records', 'place_of_assignment')) {
                $table->string('place_of_assignment')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            if (Schema::hasColumn('service_records', 'age')) {
                $table->integer('age')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'job_title')) {
                $table->string('job_title')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'place_of_birth')) {
                $table->string('place_of_birth')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'office')) {
                $table->string('office')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'status')) {
                $table->string('status')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'date_of_service')) {
                $table->date('date_of_service')->nullable(false)->change();
            }
            if (Schema::hasColumn('service_records', 'place_of_assignment')) {
                $table->string('place_of_assignment')->nullable(false)->change();
            }
        });
    }
};
