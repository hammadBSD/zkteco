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
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name')->default('fetch_and_sync_monthly');
            $table->string('month')->nullable(); // e.g., 2025-10
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->text('output')->nullable(); // Command output
            $table->text('error_message')->nullable(); // Error details if failed
            $table->json('summary')->nullable(); // JSON with records processed, synced, etc.
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'started_at']);
            $table->index(['job_name', 'started_at']);
            $table->index('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
    }
};