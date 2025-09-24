<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoEmployeeService;

class ShowEmployeeStatus extends Command
{
    protected $signature = 'zkteco:employee-status';
    protected $description = 'Show today\'s attendance status for all employees';

    public function handle()
    {
        $this->info('📊 Today\'s Employee Attendance Status');
        $this->info('=====================================');
        
        $service = new ZKTecoEmployeeService();
        $employees = $service->getAllEmployeesWithTodayStatus();
        
        if (empty($employees)) {
            $this->warn('No employees found. Run "php artisan zkteco:fetch-employees" first.');
            return;
        }
        
        $this->info("Found " . count($employees) . " employees\n");
        
        $tableData = [];
        foreach ($employees as $summary) {
            if ($summary) {
                $tableData[] = [
                    $summary['employee']->punch_code_id,
                    $summary['employee']->name,
                    $summary['check_in'] ? $summary['check_in']->punch_time->format('H:i:s') : 'N/A',
                    $summary['check_out'] ? $summary['check_out']->punch_time->format('H:i:s') : 'N/A',
                    $summary['status'],
                    $summary['total_records']
                ];
            }
        }
        
        $this->table(
            ['Punch Code', 'Name', 'Check In', 'Check Out', 'Status', 'Records'],
            $tableData
        );
        
        // Summary statistics
        $complete = collect($employees)->where('status', 'Complete')->count();
        $checkedIn = collect($employees)->where('status', 'Checked In')->count();
        $absent = collect($employees)->where('status', 'Absent')->count();
        
        $this->info("\n📈 Summary:");
        $this->line("✅ Complete (Check In + Check Out): {$complete}");
        $this->line("🟡 Checked In Only: {$checkedIn}");
        $this->line("❌ Absent: {$absent}");
    }
}
