<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZKTecoHRSyncService;
use App\Models\TargetUrl;

class TestHRApiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkteco:test-hr-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to HR API with authentication';

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
        $this->info('🔍 Testing HR API Connection...');
        
        try {
            // Test basic connectivity to all URLs
            $this->info('📡 Testing basic connectivity...');
            $hrApiUrls = $this->getHRApiUrls();
            
            $this->line("   Found " . count($hrApiUrls) . " HR API URLs:");
            foreach ($hrApiUrls as $i => $url) {
                $isHttps = strpos($url, 'https://') === 0;
                $type = $isHttps ? 'LIVE' : 'LOCAL';
                $this->line("   " . ($i + 1) . ". {$url} ({$type})");
            }
            
            $allSuccessful = true;
            
            // Test ping endpoint for each URL
            foreach ($hrApiUrls as $i => $hrApiUrl) {
                $isHttps = strpos($hrApiUrl, 'https://') === 0;
                $type = $isHttps ? 'LIVE' : 'LOCAL';
                
                $this->info("🏓 Testing ping endpoint for {$type} ({$hrApiUrl})...");
                $pingResponse = \Illuminate\Support\Facades\Http::get($hrApiUrl . '/ping');
                
                if ($pingResponse->successful()) {
                    $this->info("✅ Ping successful for {$type}!");
                    $this->line('   Response: ' . $pingResponse->body());
                } else {
                    $this->error("❌ Ping failed for {$type}!");
                    $this->error('   Status: ' . $pingResponse->status());
                    $this->error('   Response: ' . $pingResponse->body());
                    $allSuccessful = false;
                }
            }
            
            if (!$allSuccessful) {
                $this->error('❌ Some URLs failed ping test. Stopping here.');
                return;
            }
            
            // Test sync status endpoint with authentication (using first URL)
            $this->info('📡 Testing sync status endpoint...');
            $status = $this->hrSyncService->getHRSyncStatus();
            
            if (isset($status['success']) && $status['success']) {
                $this->info('✅ HR API Connection Successful!');
                $this->line('📊 HR System Status:');
                if (isset($status['data'])) {
                    $this->line("   Total Employees: {$status['data']['total_employees']}");
                    $this->line("   Total Attendance Records: {$status['data']['total_attendance_records']}");
                    $this->line("   Last Sync: {$status['data']['last_sync']}");
                    $this->line("   Status: {$status['data']['status']}");
                } else {
                    $this->line('   Response: ' . json_encode($status));
                }
            } else {
                $this->error('❌ HR API Connection Failed!');
                $this->error('Error: ' . ($status['message'] ?? 'Unknown error'));
                $this->error('Full response: ' . json_encode($status));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Connection test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
        
        $this->info('🏁 Test completed!');
    }

    /**
     * Get HR API URLs from database or fallback to config
     */
    private function getHRApiUrls()
    {
        // Try to get URLs from database first
        $targetUrls = TargetUrl::where('name', 'hcm_api')->get();
        
        if ($targetUrls->isNotEmpty()) {
            return $targetUrls->pluck('target_url')->toArray();
        }
        
        // Fallback to config if not found in database
        return [config('zkteco.hr_api_url', 'http://hcm.local/api')];
    }

    /**
     * Get single HR API URL (for backward compatibility)
     */
    private function getHRApiUrl()
    {
        $urls = $this->getHRApiUrls();
        return $urls[0]; // Return first URL for backward compatibility
    }
}