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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->decimal('salary', 10, 2);
            $table->date('date_of_birth');
            $table->string('job_title');
            $table->string('place_of_birth');
            $table->string('office');
            $table->string('status');
            $table->date('date_of_service');
            $table->string('place_of_assignment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};