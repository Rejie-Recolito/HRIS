<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('type'); // 'vacation' or 'sick'
            $table->integer('amount');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // optional FK - employee table is named 'employee' in this app
            $table->foreign('employee_id')->references('id')->on('employee')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_credits');
    }
};
