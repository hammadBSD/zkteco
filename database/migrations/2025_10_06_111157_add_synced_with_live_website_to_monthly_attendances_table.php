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
            $table->boolean('synced_with_live_website')->default(false)->after('synced_with_website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_attendances', function (Blueprint $table) {
            $table->dropColumn('synced_with_live_website');
        });
    }
};