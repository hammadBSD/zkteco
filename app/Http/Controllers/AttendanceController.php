<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZKTecoPackageService;
use App\Models\Employee;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $todayAttendances = Attendance::today()->with('employee')->orderBy('punch_time', 'desc')->get();
        $totalEmployees = Employee::count();
        $todayCount = $todayAttendances->count();
        $totalRecords = Attendance::count();
        
        return view('attendance.index', compact('todayAttendances', 'totalEmployees', 'todayCount', 'totalRecords'));
    }
    
    public function fetchToday()
    {
        try {
            $service = new ZKTecoPackageService();
            $result = $service->fetchAttendanceData('today');
            
            return response()->json([
                'success' => true,
                'message' => 'Today\'s attendance data fetched successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function fetchLimited($limit = 10)
    {
        try {
            $service = new ZKTecoPackageService();
            $result = $service->fetchAttendanceData('recent', $limit);
            
            return response()->json([
                'success' => true,
                'message' => "Limited attendance data ({$limit} records) fetched successfully",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }
}
