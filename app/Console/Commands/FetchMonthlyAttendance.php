<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyAttendance;
use App\Services\ZKTecoPackageService;
use Carbon\Carbon;

class FetchMonthlyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:fetch-monthly {--month= : Specific month (YYYY-MM format)} {--save : Save data to monthly_attendances table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch monthly attendance data from ZKTeco devices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('📅 Fetching Monthly Attendance Data...');
            
            // Determine the month to fetch
            $month = $this->option('month');
            if ($month) {
                $this->info("🎯 Target Month: {$month}");
                $targetDate = Carbon::createFromFormat('Y-m', $month);
            } else {
                $targetDate = Carbon::now();
                $this->info("🎯 Target Month: {$targetDate->format('Y-m')} (Current Month)");
            }
            
            $year = $targetDate->year;
            $monthNum = $targetDate->month;
            
            $this->info("📊 Fetching data for {$targetDate->format('F Y')}...");
            
            // Get monthly data using ZKTecoPackageService
            $monthlyData = $this->getMonthlyDataUsingService($targetDate);
            
            if (empty($monthlyData)) {
                $this->warn('⚠️  No monthly data found from devices.');
                return;
            }
            
            $this->info("📈 Found " . count($monthlyData) . " monthly attendance records");
            
            // Display the data
            $this->displayMonthlyData($monthlyData);
            
            // Save to database if requested
            if ($this->option('save')) {
                $this->saveMonthlyData($monthlyData, $year, $monthNum);
            } else {
                $this->info('💡 Use --save option to store data in monthly_attendances table');
            }
            
            $this->info('✅ Monthly attendance fetch completed!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error fetching monthly attendance: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
    
    /**
     * Display monthly data in a formatted table
     */
    private function displayMonthlyData($monthlyData)
    {
        $headers = ['Punch Code', 'Device IP', 'Device Type', 'Punch Time', 'Verify Mode'];
        $rows = [];
        
        foreach ($monthlyData as $record) {
            $rows[] = [
                $record['punch_code'] ?? 'N/A',
                $record['device_ip'] ?? 'N/A',
                $record['device_type'] ?? 'N/A',
                $record['punch_time'] ?? 'N/A',
                $record['verify_mode'] ?? 'N/A',
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    /**
     * Save monthly data to the monthly_attendances table
     */
    private function saveMonthlyData($monthlyData, $year, $month)
    {
        $this->info('💾 Saving monthly data to database...');
        
        $savedCount = 0;
        $duplicateCount = 0;
        
        foreach ($monthlyData as $record) {
            try {
                $monthlyAttendance = MonthlyAttendance::updateOrCreate(
                    [
                        'punch_code' => $record['punch_code'],
                        'device_ip' => $record['device_ip'],
                        'punch_time' => Carbon::parse($record['punch_time']),
                    ],
                    [
                        'device_type' => $record['device_type'],
                        'punch_type' => $record['punch_type'] ?? null,
                        'verify_mode' => $record['verify_mode'] ?? null,
                        'is_processed' => false,
                        'synced_with_website' => false,
                    ]
                );
                
                // Check if it was newly created or updated
                if ($monthlyAttendance->wasRecentlyCreated) {
                    $savedCount++;
                } else {
                    $duplicateCount++;
                }
            } catch (\Exception $e) {
                // Handle any unique constraint violations gracefully
                $duplicateCount++;
            }
        }
        
        $this->info("✅ Saved {$savedCount} new records");
        if ($duplicateCount > 0) {
            $this->info("🔄 Skipped {$duplicateCount} duplicate records");
        }
        
        // Show current month's total
        $totalForMonth = MonthlyAttendance::forMonth($year, $month)->count();
        $this->info("📊 Total records for {$year}-{$month}: {$totalForMonth}");
    }
    
    /**
     * Get monthly data using ZKTecoPackageService with date range
     */
    private function getMonthlyDataUsingService(Carbon $targetDate)
    {
        $this->info("🔄 Using ZKTecoPackageService for efficient data fetching...");
        
        // Set date range: from start of month to current date
        $startDate = $targetDate->copy()->startOfMonth();
        $endDate = Carbon::now();
        
        $this->info("📅 Date Range: {$startDate->format('Y-m-d H:i:s')} to {$endDate->format('Y-m-d H:i:s')}");
        
        try {
            $service = new ZKTecoPackageService();
            
            // Use the service's method to get attendance data for the date range
            $attendanceData = $service->getAttendanceDataForDateRange($startDate, $endDate);
            
            $this->info("📊 Service returned total records: " . $attendanceData['total_records']);
            
            // Convert the service data to our expected format
            $monthlyData = [];
            
            // Extract records from raw_data
            if (isset($attendanceData['raw_data']) && is_array($attendanceData['raw_data'])) {
                foreach ($attendanceData['raw_data'] as $deviceType => $deviceRecords) {
                    $this->info("📱 Processing {$deviceType} device records: " . count($deviceRecords));
                    
                    foreach ($deviceRecords as $record) {
                        $monthlyData[] = [
                            'punch_code' => $record['employee_id'],
                            'device_ip' => $record['device_ip'],
                            'device_type' => $record['device_type'],
                            'punch_time' => $record['punch_time']->format('Y-m-d H:i:s'),
                            'punch_type' => $this->getPunchTypeFromState($record['state']),
                            'verify_mode' => $record['verify_mode'],
                        ];
                    }
                }
            }
            
            $this->info("✅ Converted to " . count($monthlyData) . " monthly records");
            
            return $monthlyData;
            
        } catch (\Exception $e) {
            $this->error("❌ Error using ZKTecoPackageService: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Convert attendance state to punch type
     */
    private function getPunchTypeFromState($state)
    {
        switch ($state) {
            case 1:
                return 'check_in';
            case 2:
                return 'check_out';
            case 3:
                return 'break_out';
            case 4:
                return 'break_in';
            default:
                return null;
        }
    }
}
