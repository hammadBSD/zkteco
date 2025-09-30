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
        Schema::table('monthly_attendances', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate punch records
            // A combination of punch_code, device_ip, and punch_time should be unique
            $table->unique(['punch_code', 'device_ip', 'punch_time'], 'unique_monthly_punch_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_attendances', function (Blueprint $table) {
            $table->dropUnique('unique_monthly_punch_record');
        });
    }
};
