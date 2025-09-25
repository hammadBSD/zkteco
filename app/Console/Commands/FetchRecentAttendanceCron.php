<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoPackageService;
use App\Services\ZKTecoHRSyncService;
use App\Models\Attendance;
use Carbon\Carbon;

class FetchRecentAttendanceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:fetch-recent-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch recent attendance records from ZKTeco devices (Cron job - runs every 7 minutes)';

    protected $zktecoService;
    protected $hrSyncService;

    public function __construct()
    {
        parent::__construct();
        $this->zktecoService = new ZKTecoPackageService();
        $this->hrSyncService = new ZKTecoHRSyncService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting ZKTeco Recent Attendance Cron Job...');
        $this->info('⏰ Time: ' . Carbon::now()->format('Y-m-d H:i:s'));
        
        try {
            // Get current record count before fetching
            $beforeCount = Attendance::count();
            $this->info("📊 Current attendance records in database: {$beforeCount}");
            
            // Get the most recent created_at timestamp from database
            $lastRecord = Attendance::orderBy('created_at', 'desc')->first();
            if ($lastRecord) {
                $startDate = $lastRecord->created_at;
                $endDate = Carbon::now();
                $this->info("Fetching records newer than: " . $startDate->format('Y-m-d H:i:s'));
            } else {
                // If no records exist, fetch last 7 days
                $startDate = Carbon::now()->subDays(7);
                $endDate = Carbon::now();
                $this->info("No existing records found, fetching last 7 days");
            }

            // Fetch recent attendance data using the same method as frontend
            $extractedData = $this->zktecoService->getAttendanceDataForDateRange($startDate, $endDate);
            $saved = $this->zktecoService->saveToDatabase($extractedData);
            
            $result = [
                'success' => true,
                'extracted_data' => $extractedData,
                'saved' => $saved
            ];
            
            if ($result['success']) {
                $saved = $result['saved'];
                $afterCount = Attendance::count();
                $newRecords = $afterCount - $beforeCount;
                
                $this->info('✅ Cron job completed successfully!');
                $this->info("📈 New records fetched: {$newRecords}");
                $this->info("💾 Total records in database: {$afterCount}");
                
                if (isset($saved['attendance_records'])) {
                    $this->info("🆕 New attendance records saved: {$saved['attendance_records']}");
                }
                if (isset($saved['duplicates_skipped'])) {
                    $this->info("⏭️ Duplicates skipped: {$saved['duplicates_skipped']}");
                }
                
                // Log success
                \Log::info("ZKTeco Cron Job Success: Fetched {$newRecords} new records");
                
                // Sync to HR system if enabled
                if (config('zkteco.sync_enabled', true)) {
                    $this->info('🔄 Syncing data to HR system...');
                    $syncResult = $this->hrSyncService->syncAllToHR();
                    
                    if (isset($syncResult['attendance']['success']) && $syncResult['attendance']['success']) {
                        $this->info('✅ HR sync completed successfully');
                        \Log::info("ZKTeco HR Sync Success: " . json_encode($syncResult));
                    } else {
                        $this->warn('⚠️ HR sync failed or disabled');
                        \Log::warning("ZKTeco HR Sync Failed: " . json_encode($syncResult));
                    }
                } else {
                    $this->info('ℹ️ HR sync is disabled');
                }
                
            } else {
                $this->error('❌ Cron job failed!');
                $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
                
                // Log error
                \Log::error("ZKTeco Cron Job Failed: " . ($result['error'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Cron job failed with exception: ' . $e->getMessage());
            \Log::error("ZKTeco Cron Job Exception: " . $e->getMessage());
        }
        
        $this->info('🏁 Cron job finished at: ' . Carbon::now()->format('Y-m-d H:i:s'));
    }
}