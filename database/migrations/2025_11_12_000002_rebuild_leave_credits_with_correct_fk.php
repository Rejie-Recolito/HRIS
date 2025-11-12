<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Rebuild the leave_credits table to ensure the foreign key points to
        // the actual 'employee' table (this project uses singular 'employee').
        Schema::disableForeignKeyConstraints();

        // Create a temporary table with the corrected foreign key
        Schema::create('leave_credits_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('type');
            $table->integer('amount');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            // Correct FK to singular 'employee' table
            $table->foreign('employee_id')->references('id')->on('employee')->onDelete('cascade');
        });

        // Copy data from old table if it exists
        if (Schema::hasTable('leave_credits')) {
            $rows = DB::table('leave_credits')->get();
            if ($rows->isNotEmpty()) {
                $insert = $rows->map(function ($r) {
                    $arr = (array) $r;
                    // Ensure only expected keys are present
                    return [
                        'id' => $arr['id'] ?? null,
                        'employee_id' => $arr['employee_id'] ?? null,
                        'type' => $arr['type'] ?? null,
                        'amount' => $arr['amount'] ?? null,
                        'assigned_by' => $arr['assigned_by'] ?? null,
                        'notes' => $arr['notes'] ?? null,
                        'created_at' => $arr['created_at'] ?? now(),
                        'updated_at' => $arr['updated_at'] ?? now(),
                        'source_type' => $arr['source_type'] ?? null,
                        'source_id' => $arr['source_id'] ?? null,
                    ];
                })->toArray();

                // Insert preserving IDs where possible (sqlite allows explicit id inserts)
                DB::table('leave_credits_new')->insert($insert);
            }

            // Drop the old table and rename the new one
            Schema::dropIfExists('leave_credits');
        }

        Schema::rename('leave_credits_new', 'leave_credits');
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        // No-op: leave_credits will remain referencing the correct table.
        // If necessary, you can implement a revert to the previous state here.
    }
};
