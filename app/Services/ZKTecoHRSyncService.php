<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\MonthlyAttendance;
use App\Models\TargetUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ZKTecoHRSyncService
{
    private $hrApiUrl;
    private $apiKey;

    public function __construct()
    {
        // Configure your HR website URL and API key
        $this->hrApiUrl = $this->getHRApiUrl();
        $this->apiKey = config('zkteco.hr_api_key', 'zkteco-secure-api-key-2024');
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

    /**
     * Sync data to multiple URLs
     */
    private function syncToMultipleUrls($endpoint, $payload, $isLiveUrl = false)
    {
        $urls = $this->getHRApiUrls();
        $results = [];
        
        foreach ($urls as $url) {
            $isHttps = strpos($url, 'https://') === 0;
            
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post($url . $endpoint, $payload);

                $results[$url] = [
                    'success' => $response->successful(),
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'is_https' => $isHttps
                ];

                Log::info("ZKTeco HR Sync: Synced to {$url}", $results[$url]);

            } catch (\Exception $e) {
                $results[$url] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'is_https' => $isHttps
                ];
                
                Log::error("ZKTeco HR Sync: Failed to sync to {$url}", ['error' => $e->getMessage()]);
            }
        }
        
        return $results;
    }

    /**
     * Sync attendance data to HR system
     */
    public function syncAttendanceToHR($attendanceRecords = null)
    {
        try {
            // If no records provided, get only unsynced records
            if (!$attendanceRecords) {
                $attendanceRecords = Attendance::with('employee')
                    ->whereNull('synced_with_website')
                    ->get();
            }

            if ($attendanceRecords->isEmpty()) {
                Log::info('ZKTeco HR Sync: No attendance records to sync');
                return ['success' => true, 'message' => 'No records to sync'];
            }

            // Format data for HR API
            $formattedRecords = $attendanceRecords->map(function ($record) {
                return [
                    'punch_code_id' => $record->punch_code_id,
                    'device_ip' => $record->device_ip,
                    'device_type' => $record->device_type,
                    'punch_time' => $record->punch_time->toISOString(),
                    'verify_mode' => $record->verify_mode,
                    'is_processed' => $record->is_processed
                ];
            })->toArray();

            $payload = [
                'attendance_records' => $formattedRecords,
                'sync_timestamp' => Carbon::now()->toISOString(),
                'source' => 'ZKTeco-Local'
            ];

            // Sync to all URLs
            $results = $this->syncToMultipleUrls('/zkteco/sync-attendance', $payload);

            // Mark records as synced based on URL type
            $recordIds = $attendanceRecords->pluck('id');
            
            foreach ($results as $url => $result) {
                if ($result['success'] && $result['is_https']) {
                    // HTTPS URL - mark as synced to live website
                    Attendance::whereIn('id', $recordIds)->update(['synced_with_website' => true]);
                    Log::info("ZKTeco HR Sync: Attendance synced to live website ({$url})");
                } elseif ($result['success'] && !$result['is_https']) {
                    // HTTP URL - mark as synced to regular website
                    Attendance::whereIn('id', $recordIds)->update(['synced_with_website' => true]);
                    Log::info("ZKTeco HR Sync: Attendance synced to regular website ({$url})");
                }
            }

            $successCount = collect($results)->where('success', true)->count();
            $totalCount = count($results);

            return [
                'success' => $successCount > 0,
                'message' => "Synced to {$successCount}/{$totalCount} URLs",
                'results' => $results
            ];

        } catch (\Exception $e) {
            Log::error('ZKTeco HR Sync: Error syncing attendance: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Sync employee data to HR system
     */
    public function syncEmployeesToHR($employees = null)
    {
        try {
            // If no employees provided, get only unsynced employees
            if (!$employees) {
                $employees = Employee::whereNull('synced_with_website')->get();
            }

            if ($employees->isEmpty()) {
                Log::info('ZKTeco HR Sync: No employee records to sync');
                return ['success' => true, 'message' => 'No employees to sync'];
            }

            // Format data for HR API
            $formattedEmployees = $employees->map(function ($employee) {
                return [
                    'punch_code_id' => $employee->punch_code_id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'department' => $employee->department,
                    'position' => $employee->position,
                    'device_ip' => $employee->device_ip,
                    'device_type' => $employee->device_type,
                    'is_active' => $employee->is_active
                ];
            })->toArray();

            $payload = [
                'employees' => $formattedEmployees,
                'sync_timestamp' => Carbon::now()->toISOString(),
                'source' => 'ZKTeco-Local'
            ];

            // Send to HR API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->hrApiUrl . '/zkteco/sync-employees', $payload);

            if ($response->successful()) {
                $result = $response->json();
                // Mark employees as synced
                Employee::whereIn('id', $employees->pluck('id'))->update(['synced_with_website' => true]);
                Log::info('ZKTeco HR Sync: Employees synced successfully', $result);
                return $result;
            } else {
                Log::error('ZKTeco HR Sync: Failed to sync employees', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['success' => false, 'message' => 'Failed to sync employees'];
            }

        } catch (\Exception $e) {
            Log::error('ZKTeco HR Sync: Error syncing employees: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Sync monthly attendance data to HR system
     */
    public function syncMonthlyAttendanceToHR($month = null)
    {
        try {
            // Parse month parameter
            if ($month) {
                $monthParts = explode('-', $month);
                if (count($monthParts) !== 2) {
                    return ['success' => false, 'message' => 'Invalid month format. Use YYYY-MM'];
                }
                
                $year = (int) $monthParts[0];
                $monthNum = (int) $monthParts[1];
                
                // Validate month
                if ($monthNum < 1 || $monthNum > 12) {
                    return ['success' => false, 'message' => 'Invalid month. Must be between 01-12'];
                }
                
                // Get monthly attendance records for the specified month that are not synced to either website
                $monthlyRecords = MonthlyAttendance::whereYear('punch_time', $year)
                    ->whereMonth('punch_time', $monthNum)
                    ->where(function($query) {
                        $query->where(function($q) {
                            $q->whereNull('synced_with_website')
                              ->orWhere('synced_with_website', false);
                        })->orWhere(function($q) {
                            $q->whereNull('synced_with_live_website')
                              ->orWhere('synced_with_live_website', false);
                        });
                    })
                    ->get();
            } else {
                // If no month specified, get all unsynced monthly records
                $monthlyRecords = MonthlyAttendance::where(function($query) {
                    $query->where(function($q) {
                        $q->whereNull('synced_with_website')
                          ->orWhere('synced_with_website', false);
                    })->orWhere(function($q) {
                        $q->whereNull('synced_with_live_website')
                          ->orWhere('synced_with_live_website', false);
                    });
                })->get();
            }

            if ($monthlyRecords->isEmpty()) {
                Log::info('ZKTeco HR Sync: No monthly attendance records to sync');
                return [
                    'success' => true, 
                    'message' => 'No monthly attendance records to sync',
                    'data' => [
                        'records_synced' => 0,
                        'month' => $month ?? 'all'
                    ]
                ];
            }

            // Format data for HR API
            $formattedRecords = $monthlyRecords->map(function ($record) {
                return [
                    'punch_code' => $record->punch_code,
                    'device_ip' => $record->device_ip,
                    'device_type' => $record->device_type,
                    'punch_time' => $record->punch_time->toISOString(),
                    'punch_type' => $record->punch_type,
                    'verify_mode' => $record->verify_mode,
                    'is_processed' => $record->is_processed
                ];
            })->toArray();

            $payload = [
                'monthly_attendance_records' => $formattedRecords,
                'sync_timestamp' => Carbon::now()->toISOString(),
                'source' => 'ZKTeco-Monthly',
                'month' => $month
            ];

            // Send to HR API in batches to avoid timeout
            $batchSize = 100; // Process 100 records at a time
            $totalRecords = count($formattedRecords);
            $totalBatches = ceil($totalRecords / $batchSize);
            $totalSynced = 0;
            $urls = $this->getHRApiUrls();
            
            Log::info("ZKTeco HR Sync: Processing {$totalRecords} monthly attendance records in {$totalBatches} batches to " . count($urls) . " URLs");
            
            // Process each batch
            for ($i = 0; $i < $totalBatches; $i++) {
                $offset = $i * $batchSize;
                $batch = array_slice($formattedRecords, $offset, $batchSize);
                
                $batchPayload = [
                    'monthly_attendance_records' => $batch,
                    'sync_timestamp' => Carbon::now()->toISOString(),
                    'source' => 'ZKTeco-Monthly',
                    'month' => $month,
                    'batch_info' => [
                        'current_batch' => $i + 1,
                        'total_batches' => $totalBatches,
                        'records_in_batch' => count($batch)
                    ]
                ];
                
                // Sync batch to all URLs
                $batchResults = [];
                foreach ($urls as $url) {
                    $isHttps = strpos($url, 'https://') === 0;
                    
                    try {
                        $response = Http::timeout(60)->withHeaders([
                            'Authorization' => 'Bearer ' . $this->apiKey,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ])->post($url . '/zkteco/sync-monthly-attendance', $batchPayload);
                        
                        $batchResults[$url] = [
                            'success' => $response->successful(),
                            'status' => $response->status(),
                            'response' => $response->body(),
                            'is_https' => $isHttps
                        ];
                        
                        if ($response->successful()) {
                            $result = $response->json();
                            $batchSynced = $result['data']['saved_records'] ?? count($batch);
                            $totalSynced += $batchSynced;
                            
                            Log::info("ZKTeco HR Sync: Batch " . ($i + 1) . "/{$totalBatches} synced to {$url} - {$batchSynced} records");
                        } else {
                            Log::error("ZKTeco HR Sync: Batch " . ($i + 1) . "/{$totalBatches} failed for {$url}", [
                                'status' => $response->status(),
                                'response' => $response->body()
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        $batchResults[$url] = [
                            'success' => false,
                            'error' => $e->getMessage(),
                            'is_https' => $isHttps
                        ];
                        
                        Log::error("ZKTeco HR Sync: Batch " . ($i + 1) . "/{$totalBatches} exception for {$url}: " . $e->getMessage());
                    }
                }
                
                // Mark records as synced based on URL type
                $batchRecordIds = array_slice($monthlyRecords->pluck('id')->toArray(), $offset, $batchSize);
                
                foreach ($batchResults as $url => $result) {
                    if ($result['success'] && $result['is_https']) {
                        // HTTPS URL - mark as synced to live website
                        MonthlyAttendance::whereIn('id', $batchRecordIds)->update(['synced_with_live_website' => true]);
                        Log::info("ZKTeco HR Sync: Monthly attendance batch marked as synced to live website ({$url})");
                    } elseif ($result['success'] && !$result['is_https']) {
                        // HTTP URL - mark as synced to regular website
                        MonthlyAttendance::whereIn('id', $batchRecordIds)->update(['synced_with_website' => true]);
                        Log::info("ZKTeco HR Sync: Monthly attendance batch marked as synced to regular website ({$url})");
                    }
                }
            }

            Log::info('ZKTeco HR Sync: Monthly attendance synced successfully - Total synced: ' . $totalSynced);
            
            return [
                'success' => true,
                'message' => "Monthly attendance synced successfully for {$month}",
                'data' => [
                    'records_synced' => $totalSynced,
                    'total_batches' => $totalBatches,
                    'month' => $month
                ]
            ];

        } catch (\Exception $e) {
            Log::error('ZKTeco HR Sync: Error syncing monthly attendance: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get sync status from HR system
     */
    public function getHRSyncStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json'
            ])->get($this->hrApiUrl . '/zkteco/sync-status');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('ZKTeco HR Sync: Failed to get sync status', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['success' => false, 'message' => 'Failed to get sync status'];
            }

        } catch (\Exception $e) {
            Log::error('ZKTeco HR Sync: Error getting sync status: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Sync all data (employees + attendance) to HR system
     */
    public function syncAllToHR()
    {
        $results = [];
        
        // Sync employees first
        $employeeResult = $this->syncEmployeesToHR();
        $results['employees'] = $employeeResult;
        
        // Then sync attendance
        $attendanceResult = $this->syncAttendanceToHR();
        $results['attendance'] = $attendanceResult;
        
        return $results;
    }
}
