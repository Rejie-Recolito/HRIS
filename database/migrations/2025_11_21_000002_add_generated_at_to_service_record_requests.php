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
        Schema::table('service_record_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_record_requests', 'generated_at')) {
                $table->timestamp('generated_at')->nullable()->after('generated_pdf_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_record_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_record_requests', 'generated_at')) {
                $table->dropColumn('generated_at');
            }
        });
    }
};
