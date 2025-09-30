<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
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
     * Get HR API URL from database or fallback to config
     */
    private function getHRApiUrl()
    {
        // Try to get URL from database first
        $targetUrl = TargetUrl::getUrlByName('hcm_api');
        
        if ($targetUrl) {
            return $targetUrl;
        }
        
        // Fallback to config if not found in database
        return config('zkteco.hr_api_url', 'http://hcm.local/api');
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

            // Send to HR API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->hrApiUrl . '/zkteco/sync-attendance', $payload);

            if ($response->successful()) {
                $result = $response->json();
                // Mark attendance records as synced
                Attendance::whereIn('id', $attendanceRecords->pluck('id'))->update(['synced_with_website' => true]);
                Log::info('ZKTeco HR Sync: Attendance synced successfully', $result);
                return $result;
            } else {
                Log::error('ZKTeco HR Sync: Failed to sync attendance', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['success' => false, 'message' => 'Failed to sync attendance'];
            }

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
