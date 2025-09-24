<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoPackageService;
use App\Models\Employee;
use App\Models\Attendance;

class TestAttendanceFetch extends Command
{
    protected $signature = 'zkteco:test-attendance';
    protected $description = 'Test fetching today\'s attendance data from ZKTeco devices';

    public function handle()
    {
        $this->info('🚀 Testing ZKTeco Attendance Fetch...');
        
        $service = new ZKTecoPackageService();
        
        // Test device configuration
        $devices = [
            'IN' => ['ip' => '172.16.10.14', 'port' => 4096],
            'OUT' => ['ip' => '172.16.10.15', 'port' => 4097]
        ];
        
        foreach ($devices as $deviceType => $device) {
            $this->info("📡 Testing {$deviceType} device ({$device['ip']}:{$device['port']})...");
            
            try {
                $zk = new \App\ZKTeco\Lib\ZKTeco($device['ip'], $device['port']);
                
                if ($zk->connect()) {
                    $this->info("✅ Connected to {$deviceType} device");
                    
                    // Test today's records
                    $todayRecords = \App\ZKTeco\Lib\ZKTeco::getTodaysRecords($zk);
                    $this->info("📊 Found " . count($todayRecords) . " records for today");
                    
                    if (!empty($todayRecords)) {
                        $this->info("📋 Sample record:");
                        $sample = reset($todayRecords);
                        $this->line("   Employee ID: {$sample['id']}");
                        $this->line("   Timestamp: {$sample['timestamp']}");
                        $this->line("   Type: {$sample['type']}");
                        $this->line("   State: {$sample['state']}");
                    }
                    
                    // Test limited records (last 5)
                    $this->info("📊 Testing limited records (last 5)...");
                    $limitedRecords = $zk->getAttendance(5);
                    $this->info("📊 Found " . count($limitedRecords) . " limited records");
                    
                    if (!empty($limitedRecords)) {
                        $this->info("📋 Sample limited record:");
                        $sample = reset($limitedRecords);
                        $this->line("   Employee ID: {$sample['id']}");
                        $this->line("   Timestamp: {$sample['timestamp']}");
                        $this->line("   Type: {$sample['type']}");
                    }
                    
                    $zk->disconnect();
                } else {
                    $this->error("❌ Failed to connect to {$deviceType} device");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error with {$deviceType} device: " . $e->getMessage());
            }
        }
        
        $this->info('🎉 Test completed!');
    }
}
