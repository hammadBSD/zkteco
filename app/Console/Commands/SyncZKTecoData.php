<?php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use Illuminate\Console\Command;
use Exception;

class SyncZKTecoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:sync {--employees : Sync employee data} {--attendance : Sync attendance data} {--all : Sync both employees and attendance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from ZKTeco attendance devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $zkService = new ZKTecoService();
        
        $syncEmployees = $this->option('employees') || $this->option('all');
        $syncAttendance = $this->option('attendance') || $this->option('all');
        
        // If no specific option is provided, sync both
        if (!$syncEmployees && !$syncAttendance) {
            $syncEmployees = true;
            $syncAttendance = true;
        }

        try {
            if ($syncEmployees) {
                $this->info('Syncing employee data...');
                $employeeCount = $zkService->syncEmployeeData();
                $this->info("✓ Synced {$employeeCount} employees");
            }

            if ($syncAttendance) {
                $this->info('Syncing attendance data...');
                $attendanceCount = $zkService->syncAttendanceData();
                $this->info("✓ Synced {$attendanceCount} attendance records");
            }

            $this->info('✓ ZKTeco data sync completed successfully!');
            
        } catch (Exception $e) {
            $this->error('✗ Sync failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
