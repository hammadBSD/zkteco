<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

class ZKTecoEmployeeService
{
    public function fetchAllEmployees()
    {
        $devices = [
            'IN' => ['ip' => '172.16.10.14', 'port' => 4096],
            'OUT' => ['ip' => '172.16.10.15', 'port' => 4097]
        ];
        
        $allEmployees = [];
        $savedCount = 0;
        
        foreach ($devices as $deviceType => $device) {
            try {
                $zk = new \App\ZKTeco\Lib\ZKTeco($device['ip'], $device['port']);
                
                if ($zk->connect()) {
                    Log::info("Fetching employees from {$deviceType} device...");
                    $users = $zk->getUsers();
                    Log::info("Users data: " . json_encode($users));
                    
                           if (!empty($users)) {
                               foreach ($users as $userid => $user) {
                                   $punchCodeId = $user['userid']; // Use 'userid' field (punch code) instead of 'uid' (device ID)
                                   $name = $user['name'] ?? "Employee {$punchCodeId}";
                            
                            // Check if employee already exists
                            $existingEmployee = Employee::where('punch_code_id', $punchCodeId)->first();
                            
                            if (!$existingEmployee) {
                                Employee::create([
                                    'punch_code_id' => $punchCodeId,
                                    'name' => $name,
                                    'is_active' => true
                                ]);
                                $savedCount++;
                                Log::info("Created new employee: {$name} (ID: {$punchCodeId})");
                            } else {
                                // Update existing employee info
                                $existingEmployee->update([
                                    'name' => $name,
                                ]);
                                Log::info("Updated employee: {$name} (ID: {$punchCodeId})");
                            }
                            
                            $allEmployees[] = [
                                'punch_code_id' => $punchCodeId,
                                'name' => $name,
                                'device_type' => $deviceType,
                                'device_ip' => $device['ip']
                            ];
                        }
                    }
                    
                    $zk->disconnect();
                } else {
                    Log::error("Failed to connect to {$deviceType} device");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching employees from {$deviceType} device: " . $e->getMessage());
            }
        }
        
        return [
            'success' => true,
            'employees_fetched' => count($allEmployees),
            'employees_saved' => $savedCount,
            'employees' => $allEmployees
        ];
    }
    
    public function getEmployeeAttendanceSummary($punchCodeId)
    {
        $employee = Employee::where('punch_code_id', $punchCodeId)->first();
        
        if (!$employee) {
            return null;
        }
        
        $todayAttendances = Attendance::where('punch_code_id', $punchCodeId)
            ->whereDate('punch_time', today())
            ->orderBy('punch_time')
            ->get();
        
        $checkIn = $todayAttendances->where('device_type', 'IN')->first();
        $checkOut = $todayAttendances->where('device_type', 'OUT')->first();
        
        return [
            'employee' => $employee,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_records' => $todayAttendances->count(),
            'status' => $this->getAttendanceStatus($checkIn, $checkOut)
        ];
    }
    
    private function getAttendanceStatus($checkIn, $checkOut)
    {
        if ($checkIn && $checkOut) {
            return 'Complete';
        } elseif ($checkIn && !$checkOut) {
            return 'Checked In';
        } elseif (!$checkIn && $checkOut) {
            return 'Checked Out (No Check In)';
        } else {
            return 'Absent';
        }
    }
    
    public function getAllEmployeesWithTodayStatus()
    {
        $employees = Employee::all();
        $result = [];
        
        foreach ($employees as $employee) {
            $summary = $this->getEmployeeAttendanceSummary($employee->punch_code_id);
            $result[] = $summary;
        }
        
        return $result;
    }
}
