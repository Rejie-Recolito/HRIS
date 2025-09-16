<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->date('date_of_filing');
            $table->string('position');
            $table->decimal('salary', 10, 2);
            $table->string('type_of_leave');
            $table->string('others')->nullable();
            $table->integer('number_of_days');
            $table->string('inclusive_dates');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
