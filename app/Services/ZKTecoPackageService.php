<?php

namespace App\Services;

use App\ZKTeco\Lib\ZKTeco;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ZKTecoPackageService
{
    private $devices = [
        'IN' => [
            'ip' => '172.16.10.14',
            'port' => 4096,
            'type' => 'IN'
        ],
        'OUT' => [
            'ip' => '172.16.10.15',
            'port' => 4097,
            'type' => 'OUT'
        ]
    ];

    /**
     * Get monthly attendance data using the ZKTeco package
     */
    public function getMonthlyAttendanceData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return $this->getAttendanceDataForDateRange($startOfMonth, $endOfMonth);
    }

    /**
     * Get attendance data for a specific date range
     */
    public function getAttendanceDataForDateRange($startDate, $endDate)
    {
        $results = [
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'devices' => [],
            'total_records' => 0,
            'employees_found' => [],
            'raw_data' => []
        ];

        foreach ($this->devices as $deviceType => $device) {
            Log::info("Connecting to {$deviceType} device: {$device['ip']}:{$device['port']}");
            
            try {
                $deviceData = $this->extractFromDevice($device, $deviceType, $startDate, $endDate);
                $results['devices'][$deviceType] = $deviceData;
                
                if ($deviceData['success']) {
                    $results['total_records'] += $deviceData['records_count'];
                    $results['employees_found'] = array_unique(array_merge(
                        $results['employees_found'], 
                        $deviceData['employees']
                    ));
                    $results['raw_data'][$deviceType] = $deviceData['attendance_records'];
                }
                
            } catch (Exception $e) {
                $results['devices'][$deviceType] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'device_ip' => $device['ip'],
                    'device_type' => $deviceType
                ];
                Log::error("Failed to extract from {$deviceType}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Extract data from a specific device using the ZKTeco package
     */
    private function extractFromDevice($device, $deviceType, $startDate, $endDate)
    {
        $result = [
            'device_type' => $deviceType,
            'device_ip' => $device['ip'],
            'device_port' => $device['port'],
            'success' => false,
            'records_count' => 0,
            'employees' => [],
            'attendance_records' => [],
            'users_found' => [],
            'method' => 'ZKTeco Package (UDP)'
        ];

        try {
            // Create ZKTeco instance with correct port
            $zk = new ZKTeco($device['ip'], $device['port']);
            
            Log::info("Attempting connection to {$device['ip']}:{$device['port']}");
            
            // Connect to device
            $connected = $zk->connect();
            
            if (!$connected) {
                throw new Exception("Failed to connect to device");
            }

            Log::info("✓ Connected to {$deviceType} device successfully!");

            // STEP 1: Get users (read-only, safe operation)
            try {
                Log::info("Getting users from {$deviceType} device...");
                $users = $zk->getUser();
                
                if (!empty($users)) {
                    $result['users_found'] = $users;
                    $result['employees'] = array_column($users, 'userid');
                    Log::info("✓ Found " . count($users) . " users on {$deviceType} device");
                } else {
                    Log::info("No users found on {$deviceType} device");
                }
            } catch (Exception $e) {
                Log::warning("Could not get users from {$deviceType}: " . $e->getMessage());
            }

            // STEP 2: Get attendance data (read-only, safe operation)
            try {
                Log::info("Getting attendance data from {$deviceType} device...");
                
                // Use efficient method based on date range and device type
                if ($startDate->isToday() && $endDate->isToday()) {
                    // For today's data, use the efficient getTodaysRecords() method
                    Log::info("Using getTodaysRecords() for today's data...");
                    $attendance = \App\ZKTeco\Lib\ZKTeco::getTodaysRecords($zk);
                } else {
                    // For other date ranges, use getAttendance() with limit to avoid timeout
                    // Use smaller limit for OUT device to prevent timeout
                    $limit = ($deviceType === 'OUT') ? 500 : 1000;
                    Log::info("Using getAttendance() with limit {$limit} for date range...");
                    $attendance = $zk->getAttendance($limit);
                }
                
                if (!empty($attendance)) {
                    // Filter by date range
                    $filteredAttendance = $this->filterAttendanceByDateRange($attendance, $deviceType, $startDate, $endDate);
                    
                    $result['attendance_records'] = $filteredAttendance;
                    $result['records_count'] = count($filteredAttendance);
                    $result['success'] = true;
                    
                    Log::info("✓ Found " . count($attendance) . " total attendance records, " . count($filteredAttendance) . " in date range");
                } else {
                    Log::info("No attendance data found on {$deviceType} device");
                    $result['success'] = true; // Connection worked, just no data
                }
            } catch (Exception $e) {
                Log::warning("Could not get attendance from {$deviceType}: " . $e->getMessage());
            }

            // Always disconnect (safe operation)
            $zk->disconnect();
            Log::info("✓ Disconnected from {$deviceType} device");

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            Log::error("Device {$deviceType} extraction failed: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Filter attendance records by date range
     */
    private function filterAttendanceByDateRange($attendance, $deviceType, $startDate, $endDate)
    {
        $filtered = [];
        
        foreach ($attendance as $record) {
            try {
                // Parse timestamp from the record
                $punchTime = null;
                
                if (isset($record['timestamp'])) {
                    $punchTime = Carbon::parse($record['timestamp']);
                } elseif (isset($record['time'])) {
                    $punchTime = Carbon::parse($record['time']);
                }
                
                if ($punchTime && $punchTime->between($startDate, $endDate->endOfDay())) {
                    $filtered[] = [
                        'employee_id' => (string)($record['id'] ?? $record['userid'] ?? $record['uid']),
                        'device_ip' => $this->devices[$deviceType]['ip'],
                        'device_type' => $deviceType,
                        'punch_time' => $punchTime,
                        'verify_mode' => $record['type'] ?? 1,
                        'state' => $record['state'] ?? null,
                        'raw_record' => $record
                    ];
                }
                
            } catch (Exception $e) {
                Log::debug("Could not parse attendance record: " . json_encode($record));
            }
        }
        
        return $filtered;
    }

    /**
     * Save extracted data to database
     */
    public function saveToDatabase($extractedData)
    {
        $saved = [
            'employees' => 0,
            'attendance_records' => 0,
            'duplicates_skipped' => 0
        ];

        // Save employees
        foreach ($extractedData['employees_found'] as $employeeId) {
            $employee = Employee::firstOrCreate(
                ['punch_code_id' => $employeeId],
                ['name' => "Employee {$employeeId}", 'is_active' => true]
            );
            
            if ($employee->wasRecentlyCreated) {
                $saved['employees']++;
            }
        }

        // Save attendance records
        foreach ($extractedData['raw_data'] as $deviceType => $records) {
            foreach ($records as $record) {
                // Check for duplicates
                $exists = Attendance::where('punch_code_id', $record['employee_id'])
                    ->where('device_ip', $record['device_ip'])
                    ->where('punch_time', $record['punch_time'])
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'punch_code_id' => $record['employee_id'],
                        'device_ip' => $record['device_ip'],
                        'device_type' => $record['device_type'],
                        'punch_time' => $record['punch_time'],
                        'verify_mode' => $record['verify_mode'],
                        'is_processed' => false
                    ]);
                    $saved['attendance_records']++;
                } else {
                    $saved['duplicates_skipped']++;
                }
            }
        }

        return $saved;
    }

    /**
     * Test connection to both devices
     */
    public function testConnections()
    {
        $results = [];
        
        foreach ($this->devices as $deviceType => $device) {
            try {
                Log::info("Testing connection to {$deviceType} device: {$device['ip']}:{$device['port']}");
                
                $zk = new ZKTeco($device['ip'], $device['port']);
                $connected = $zk->connect();
                
                if ($connected) {
                    // Test getting device info (read-only)
                    $version = $zk->version();
                    $time = $zk->getTime();
                    
                    $zk->disconnect();
                    
                    $results[$deviceType] = [
                        'success' => true,
                        'message' => 'Connected successfully',
                        'version' => $version,
                        'device_time' => $time
                    ];
                    
                    Log::info("✓ {$deviceType} device connection successful");
                } else {
                    $results[$deviceType] = [
                        'success' => false,
                        'message' => 'Connection failed'
                    ];
                }
                
            } catch (Exception $e) {
                $results[$deviceType] = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
                Log::error("✗ {$deviceType} device connection failed: " . $e->getMessage());
            }
        }
        
        return $results;
    }

    /**
     * Fetch attendance data from ZKTeco devices
     */
    public function fetchAttendanceData($type = 'today', $limit = null)
    {
        // Increase execution time limit to prevent timeout
        set_time_limit(120); // 2 minutes
        
        try {
            if ($type === 'today') {
                $startDate = Carbon::today();
                $endDate = Carbon::today();
            } elseif ($type === 'recent') {
                // Get the most recent created_at timestamp from database
                $lastRecord = Attendance::orderBy('created_at', 'desc')->first();
                if ($lastRecord) {
                    $startDate = $lastRecord->created_at;
                    $endDate = Carbon::now();
                    Log::info("Fetching records newer than: " . $startDate->format('Y-m-d H:i:s'));
                } else {
                    // If no records exist, fetch last 7 days
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    Log::info("No existing records found, fetching last 7 days");
                }
            } else {
                $startDate = Carbon::now()->subDays(7);
                $endDate = Carbon::now();
            }

            $extractedData = $this->getAttendanceDataForDateRange($startDate, $endDate);
            $saved = $this->saveToDatabase($extractedData);

            return [
                'success' => true,
                'extracted_data' => $extractedData,
                'saved' => $saved
            ];
        } catch (Exception $e) {
            Log::error("Error extracting data from devices: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

