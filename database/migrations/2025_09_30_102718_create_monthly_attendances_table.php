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
        Schema::create('monthly_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('punch_code'); // ZKTeco device employee ID
            $table->string('device_ip'); // Which device recorded this
            $table->enum('device_type', ['IN', 'OUT']); // Entry or Exit device
            $table->datetime('punch_time'); // When the punch occurred
            $table->enum('punch_type', ['check_in', 'check_out', 'break_out', 'break_in'])->nullable();
            $table->integer('verify_mode')->nullable(); // Fingerprint, card, etc.
            $table->boolean('is_processed')->default(false); // For data processing status
            $table->boolean('synced_with_website')->nullable()->default(null); // Sync status with website
            $table->timestamps();
            
            $table->index(['punch_code', 'punch_time']);
            $table->index(['device_ip', 'punch_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendances');
    }
};
