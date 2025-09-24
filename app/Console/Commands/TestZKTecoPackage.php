<?php

namespace App\Console\Commands;

use App\Services\ZKTecoPackageService;
use Illuminate\Console\Command;
use Exception;

class TestZKTecoPackage extends Command
{
    protected $signature = 'zkteco:package-test {--month : Extract monthly data} {--save : Save to database}';
    protected $description = 'Test the ZKTeco package with your K77 devices (READ-ONLY)';

    public function handle()
    {
        $this->info('🔍 Testing ZKTeco Package with K77 Devices');
        $this->info('📡 Devices: 172.16.10.14:4096 (IN) and 172.16.10.15:4097 (OUT)');
        $this->info('⚠️  READ-ONLY MODE: No data will be modified on devices');
        $this->newLine();

        try {
            $service = new ZKTecoPackageService();
            
            // Test connections first
            $this->info('1️⃣ Testing device connections...');
            $connectionResults = $service->testConnections();
            
            foreach ($connectionResults as $deviceType => $result) {
                if ($result['success']) {
                    $this->info("   ✅ {$deviceType} Device: Connected");
                    if (isset($result['version'])) {
                        $this->info("      Version: " . ($result['version'] ?: 'Unknown'));
                    }
                    if (isset($result['device_time'])) {
                        $this->info("      Device Time: " . ($result['device_time'] ?: 'Unknown'));
                    }
                } else {
                    $this->error("   ❌ {$deviceType} Device: " . $result['message']);
                }
            }
            
            $this->newLine();
            
            // If any device connected successfully, try to extract data
            $anyConnected = collect($connectionResults)->contains('success', true);
            
            if ($anyConnected) {
                if ($this->option('month')) {
                    $this->info('2️⃣ Extracting MONTHLY attendance data...');
                    $extractedData = $service->getMonthlyAttendanceData();
                } else {
                    $this->info('2️⃣ Extracting recent attendance data...');
                    $startDate = now()->subDays(7);
                    $endDate = now();
                    $extractedData = $service->getAttendanceDataForDateRange($startDate, $endDate);
                }
                
                $this->displayExtractionResults($extractedData);
                
                // Save to database if requested
                if ($this->option('save') && $extractedData['total_records'] > 0) {
                    $this->info('3️⃣ Saving to database...');
                    $saved = $service->saveToDatabase($extractedData);
                    $this->displaySaveResults($saved);
                }
                
            } else {
                $this->error('❌ No devices could be connected. Check device status and network connectivity.');
                return 1;
            }
            
        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
        
        $this->info('✅ Test completed successfully!');
        return 0;
    }

    private function displayExtractionResults($data)
    {
        $this->info('📊 EXTRACTION RESULTS:');
        $this->info("   📅 Date Range: {$data['date_range']['start']} to {$data['date_range']['end']} ({$data['date_range']['days']} days)");
        $this->info("   📝 Total Records: {$data['total_records']}");
        $this->info("   👥 Employees Found: " . count($data['employees_found']));
        
        if (!empty($data['employees_found'])) {
            $this->info("   🆔 Employee IDs: " . implode(', ', array_slice($data['employees_found'], 0, 10)) . 
                       (count($data['employees_found']) > 10 ? '...' : ''));
        }
        
        $this->newLine();
        
        foreach ($data['devices'] as $deviceType => $deviceData) {
            $status = $deviceData['success'] ? '✅' : '❌';
            $this->info("{$status} {$deviceType} Device ({$deviceData['device_ip']}:{$deviceData['device_port']})");
            
            if ($deviceData['success']) {
                $this->info("   📊 Records: {$deviceData['records_count']}");
                $this->info("   👥 Users Found: " . count($deviceData['users_found']));
                $this->info("   🔧 Method: {$deviceData['method']}");
                
                // Show sample records
                if (!empty($deviceData['attendance_records'])) {
                    $this->info("   📋 Sample Records:");
                    foreach (array_slice($deviceData['attendance_records'], 0, 3) as $record) {
                        $this->info("      Employee {$record['employee_id']}: {$record['punch_time']->format('Y-m-d H:i:s')} ({$record['device_type']})");
                    }
                }
            } else {
                $this->error("   ❌ Error: " . ($deviceData['error'] ?? 'Unknown error'));
            }
            $this->newLine();
        }
    }

    private function displaySaveResults($saved)
    {
        $this->info('💾 SAVE RESULTS:');
        $this->info("   👥 New Employees: {$saved['employees']}");
        $this->info("   📝 New Attendance Records: {$saved['attendance_records']}");
        $this->info("   ⏭️ Duplicates Skipped: {$saved['duplicates_skipped']}");
        
        if ($saved['attendance_records'] > 0) {
            $this->info('🌐 View at: http://localhost:8000/attendance');
        }
    }
}
