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
            // Status: pending → in_progress → verified → certified → completed
            // Add new status columns
            $table->timestamp('verified_at')->nullable()->after('request_status');
            $table->timestamp('certified_at')->nullable()->after('verified_at');
            $table->timestamp('completed_at')->nullable()->after('certified_at');
            
            // Store path to generated PDF for audit trail
            $table->string('generated_pdf_path')->nullable()->after('completed_at');
            
            // Track who certified the document
            $table->unsignedBigInteger('certified_by')->nullable()->after('generated_pdf_path');
            
            // Notes from HR admin during verification
            $table->text('verification_notes')->nullable()->after('certified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_record_requests', function (Blueprint $table) {
            $table->dropColumn([
                'verified_at',
                'certified_at',
                'completed_at',
                'generated_pdf_path',
                'certified_by',
                'verification_notes'
            ]);
        });
    }
};
