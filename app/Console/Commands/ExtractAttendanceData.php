<?php

namespace App\Console\Commands;

use App\Services\ZKTecoDataExtractor;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExtractAttendanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:extract {--save : Save data to database} {--show-raw : Show raw data} {--yesterday-only : Only extract yesterday} {--today-only : Only extract today} {--month : Extract entire current month} {--start= : Custom start date (Y-m-d)} {--end= : Custom end date (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract actual employee attendance data from ZKTeco devices for yesterday and today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $extractor = new ZKTecoDataExtractor();
            
            // Determine what data to extract based on options
            if ($this->option('month')) {
                $this->info('🔍 Extracting attendance data from ZKTeco K77 devices...');
                $this->info('📅 Date range: Entire Current Month');
                $this->newLine();
                $this->info('📡 Connecting to devices and extracting monthly data...');
                $extractedData = $extractor->getCurrentMonthData();
                
            } elseif ($this->option('start') && $this->option('end')) {
                $startDate = Carbon::parse($this->option('start'));
                $endDate = Carbon::parse($this->option('end'));
                $this->info('🔍 Extracting attendance data from ZKTeco K77 devices...');
                $this->info("📅 Date range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
                $this->newLine();
                $this->info('📡 Connecting to devices and extracting custom range data...');
                $extractedData = $extractor->getAttendanceDataForDateRange($startDate, $endDate);
                
            } else {
                $this->info('🔍 Extracting attendance data from ZKTeco K77 devices...');
                $this->info('📅 Date range: Yesterday and Today');
                $this->newLine();
                $this->info('📡 Connecting to devices and extracting data...');
                $extractedData = $extractor->getTodayAndYesterdayData();
            }
            
            // Show summary
            $summary = $extractor->getSummary($extractedData);
            $this->displaySummary($summary);
            
            // Show detailed results
            $this->displayDetailedResults($extractedData);
            
            // Show raw data if requested
            if ($this->option('show-raw')) {
                $this->displayRawData($extractedData);
            }
            
            // Save to database if requested
            if ($this->option('save')) {
                $this->info('💾 Saving data to database...');
                $saved = $extractor->saveToDatabase($extractedData);
                $this->displaySaveResults($saved);
            } else {
                $this->warn('💡 Use --save flag to save this data to the database');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Extraction failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function displaySummary($summary)
    {
        $this->info('📊 EXTRACTION SUMMARY');
        $this->info('═══════════════════════');
        
        if (isset($summary['date_range']['yesterday'])) {
            // Old format for yesterday/today
            $this->info("📅 Date Range: {$summary['date_range']['yesterday']} to {$summary['date_range']['today']}");
        } else {
            // New format for custom ranges
            $this->info("📅 Date Range: {$summary['date_range']['start']} to {$summary['date_range']['end']} ({$summary['date_range']['days']} days)");
        }
        
        $this->info("📝 Total Records: {$summary['total_records']}");
        $this->info("👥 Unique Employees: {$summary['unique_employees']}");
        $this->info("🖥️  Devices Successful: {$summary['devices_successful']}/{$summary['devices_total']}");
        $this->newLine();
    }

    private function displayDetailedResults($extractedData)
    {
        $this->info('🔍 DEVICE DETAILS');
        $this->info('═══════════════════');
        
        foreach ($extractedData['devices'] as $deviceType => $deviceData) {
            $status = $deviceData['success'] ? '✅' : '❌';
            $this->info("{$status} {$deviceType} Device ({$deviceData['device_ip']})");
            
            if ($deviceData['success']) {
                $this->info("   📊 Records Found: {$deviceData['records_count']}");
                $this->info("   👥 Employees: " . count($deviceData['employees']));
                $this->info("   🔧 Method: {$deviceData['method']}");
                
                if (!empty($deviceData['employees'])) {
                    $this->info("   🆔 Employee IDs: " . implode(', ', $deviceData['employees']));
                }
            } else {
                $this->error("   ❌ Errors: " . implode(', ', $deviceData['errors']));
            }
            $this->newLine();
        }
    }

    private function displayRawData($extractedData)
    {
        $this->info('📄 RAW ATTENDANCE DATA');
        $this->info('═══════════════════════');
        
        foreach ($extractedData['raw_data'] as $deviceType => $records) {
            if (empty($records)) continue;
            
            $this->info("📱 {$deviceType} Device Records:");
            
            $tableData = [];
            foreach (array_slice($records, 0, 20) as $record) { // Show first 20 records
                $tableData[] = [
                    $record['employee_id'],
                    $record['punch_time']->format('Y-m-d H:i:s'),
                    $record['device_type'],
                    $record['verify_mode'] ?? 'N/A',
                    $record['parsed_format'] ?? 'unknown'
                ];
            }
            
            $this->table(
                ['Employee ID', 'Punch Time', 'Device', 'Verify Mode', 'Format'],
                $tableData
            );
            
            if (count($records) > 20) {
                $this->info("... and " . (count($records) - 20) . " more records");
            }
            $this->newLine();
        }
    }

    private function displaySaveResults($saved)
    {
        $this->info('💾 SAVE RESULTS');
        $this->info('═══════════════');
        $this->info("👥 New Employees: {$saved['employees']}");
        $this->info("📝 New Attendance Records: {$saved['attendance_records']}");
        $this->info("⏭️  Duplicates Skipped: {$saved['duplicates_skipped']}");
        $this->newLine();
        
        if ($saved['attendance_records'] > 0) {
            $this->info('✅ Data successfully saved to database!');
            $this->info('🌐 View at: http://localhost:8000/attendance');
        }
    }
}
