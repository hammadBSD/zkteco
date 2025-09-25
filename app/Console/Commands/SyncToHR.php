<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoHRSyncService;

class SyncToHR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:sync-to-hr {--type=all : Type of data to sync (all, attendance, employees)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually sync ZKTeco data to HR system';

    protected $hrSyncService;

    public function __construct(ZKTecoHRSyncService $hrSyncService)
    {
        parent::__construct();
        $this->hrSyncService = $hrSyncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('🔄 Starting ZKTeco to HR sync...');
        $this->info("📋 Sync type: {$type}");
        
        try {
            switch ($type) {
                case 'attendance':
                    $this->info('📊 Syncing attendance data...');
                    $result = $this->hrSyncService->syncAttendanceToHR();
                    $this->displayResult('Attendance', $result);
                    break;
                    
                case 'employees':
                    $this->info('👥 Syncing employee data...');
                    $result = $this->hrSyncService->syncEmployeesToHR();
                    $this->displayResult('Employees', $result);
                    break;
                    
                case 'all':
                default:
                    $this->info('🔄 Syncing all data...');
                    $result = $this->hrSyncService->syncAllToHR();
                    
                    if (isset($result['employees'])) {
                        $this->displayResult('Employees', $result['employees']);
                    }
                    if (isset($result['attendance'])) {
                        $this->displayResult('Attendance', $result['attendance']);
                    }
                    break;
            }
            
            // Get sync status
            $this->info('📈 Getting HR sync status...');
            $status = $this->hrSyncService->getHRSyncStatus();
            if ($status['success']) {
                $this->info('✅ HR System Status:');
                $this->line("   Total Employees: {$status['data']['total_employees']}");
                $this->line("   Total Attendance Records: {$status['data']['total_attendance_records']}");
                $this->line("   Last Sync: {$status['data']['last_sync']}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
        }
        
        $this->info('🏁 Sync completed!');
    }
    
    private function displayResult($type, $result)
    {
        if ($result['success']) {
            $this->info("✅ {$type} sync successful!");
            if (isset($result['data'])) {
                foreach ($result['data'] as $key => $value) {
                    $this->line("   {$key}: {$value}");
                }
            }
        } else {
            $this->error("❌ {$type} sync failed: " . ($result['message'] ?? 'Unknown error'));
        }
    }
}