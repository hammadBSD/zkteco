<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoPackageService;
use App\Models\Attendance;

class FetchTodayAttendance extends Command
{
    protected $signature = 'zkteco:fetch-today';
    protected $description = 'Fetch and save today\'s attendance data from ZKTeco devices';

    public function handle()
    {
        $this->info('🚀 Fetching today\'s attendance data from ZKTeco devices...');
        
        $devices = [
            'IN' => ['ip' => '172.16.10.14', 'port' => 4096],
            'OUT' => ['ip' => '172.16.10.15', 'port' => 4097]
        ];
        
        $totalSaved = 0;
        
        foreach ($devices as $deviceType => $device) {
            $this->info("📡 Fetching from {$deviceType} device...");
            
            try {
                $zk = new \App\ZKTeco\Lib\ZKTeco($device['ip'], $device['port']);
                
                if ($zk->connect()) {
                    $this->info("✅ Connected to {$deviceType} device");
                    
                    // Get today's records
                    $todayRecords = \App\ZKTeco\Lib\ZKTeco::getTodaysRecords($zk);
                    $this->info("📊 Found " . count($todayRecords) . " records for today");
                    
                    $saved = 0;
                    foreach ($todayRecords as $record) {
                        // Check for duplicates
                        $exists = Attendance::where('punch_code_id', $record['id'])
                            ->where('device_ip', $device['ip'])
                            ->where('punch_time', $record['timestamp'])
                            ->exists();
                        
                        if (!$exists) {
                            Attendance::create([
                                'punch_code_id' => $record['id'],
                                'device_ip' => $device['ip'],
                                'device_type' => $deviceType,
                                'punch_time' => $record['timestamp'],
                                'verify_mode' => $record['type'] ?? 1,
                                'is_processed' => false
                            ]);
                            $saved++;
                        }
                    }
                    
                    $this->info("💾 Saved {$saved} new {$deviceType} records");
                    $totalSaved += $saved;
                    
                    $zk->disconnect();
                } else {
                    $this->error("❌ Failed to connect to {$deviceType} device");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error with {$deviceType} device: " . $e->getMessage());
            }
        }
        
        $this->info("🎉 Total saved: {$totalSaved} attendance records");
        
        // Show summary
        $todayCount = Attendance::today()->count();
        $this->info("📊 Total today's records in database: {$todayCount}");
    }
}
