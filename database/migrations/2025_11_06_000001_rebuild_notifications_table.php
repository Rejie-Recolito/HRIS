<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            // If table doesn't exist, the original migration (now corrected) will handle creation.
            return;
        }

        // Create a temporary new notifications table with the correct schema
        Schema::create('notifications_new', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Copy rows from old notifications table into the new one
        $rows = DB::table('notifications')->get();
        foreach ($rows as $row) {
            // Try to convert existing data to JSON; if it's already JSON string, keep it
            $data = $row->data;
            // If data is text, attempt to decode then re-encode so it becomes valid JSON
            $decoded = null;
            try {
                $decoded = json_decode($data, true);
            } catch (\Throwable $e) {
                $decoded = null;
            }

            $payload = $decoded !== null && $decoded !== false ? $decoded : ['message' => $data];

            DB::table('notifications_new')->insert([
                'id' => (string) Str::uuid(),
                'type' => $row->type,
                'notifiable_id' => (string) $row->notifiable_id,
                'notifiable_type' => $row->notifiable_type,
                'data' => json_encode($payload),
                'read_at' => $row->read_at,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        // Drop old table and rename new table
        Schema::drop('notifications');
        Schema::rename('notifications_new', 'notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::drop('notifications');
        }
        // There's no automatic rollback of original schema; leave as dropped.
    }
};
