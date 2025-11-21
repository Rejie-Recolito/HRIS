<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dtr_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->nullable()->constrained('dtr_uploads')->nullOnDelete();
            $table->dateTime('occurred_at')->nullable();
            $table->string('employee')->nullable();
            $table->string('time_in')->nullable();
            $table->string('time_out')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dtr_entries');
    }
};
