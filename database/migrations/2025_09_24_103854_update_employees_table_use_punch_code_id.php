<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'punch_code_id');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'punch_code_id');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('punch_code_id', 'employee_id');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('punch_code_id', 'employee_id');
        });
    }
};