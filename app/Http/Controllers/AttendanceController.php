<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZKTecoPackageService;
use App\Services\ZKTecoHRSyncService;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\MonthlyAttendance;
use App\Models\CronLog;
use Illuminate\Support\Facades\Artisan;

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
    
    public function settings()
    {
        return view('settings.index');
    }
    
    public function syncWithWebsite()
    {
        try {
            // Run the sync command
            Artisan::call('zkteco:sync-to-hr', ['--type' => 'attendance']);
            $output = Artisan::output();
            
            // Parse the output to get meaningful information
            $lines = explode("\n", trim($output));
            $syncResult = [];
            
            foreach ($lines as $line) {
                if (strpos($line, '✅') !== false || strpos($line, '📊') !== false || strpos($line, '🎉') !== false) {
                    $syncResult[] = trim($line);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance sync completed successfully',
                'data' => [
                    'output' => $output,
                    'summary' => $syncResult
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing attendance: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function monthlyAttendance()
    {
        try {
            // Get monthly attendance data grouped by day
            $monthlyData = MonthlyAttendance::selectRaw('DATE(punch_time) as date, COUNT(*) as total_records')
                ->whereMonth('punch_time', now()->month)
                ->whereYear('punch_time', now()->year)
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
            
            // Get detailed records for the last 7 days
            $recentDays = MonthlyAttendance::selectRaw('DATE(punch_time) as date, punch_code, device_type, TIME(punch_time) as time, punch_type')
                ->where('punch_time', '>=', now()->subDays(7)->startOfDay())
                ->orderBy('punch_time', 'desc')
                ->get()
                ->groupBy('date');
            
            // Get total count for current month
            $totalThisMonth = MonthlyAttendance::whereMonth('punch_time', now()->month)
                ->whereYear('punch_time', now()->year)
                ->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Monthly attendance data retrieved successfully',
                'data' => [
                    'monthly_summary' => $monthlyData,
                    'recent_days' => $recentDays,
                    'total_this_month' => $totalThisMonth,
                    'current_month' => now()->format('F Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching monthly attendance: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function cronLogs()
    {
        try {
            // Get recent cron logs (last 50)
            $recentLogs = CronLog::recent(50)->get();
            
            // Get statistics
            $stats = [
                'total_runs' => CronLog::count(),
                'successful_runs' => CronLog::byStatus('success')->count(),
                'failed_runs' => CronLog::byStatus('failed')->count(),
                'running_jobs' => CronLog::byStatus('running')->count(),
                'today_runs' => CronLog::today()->count(),
                'last_24h_runs' => CronLog::last24Hours()->count(),
                'last_success' => CronLog::byStatus('success')->latest('completed_at')->first(),
                'last_failure' => CronLog::byStatus('failed')->latest('completed_at')->first(),
            ];
            
            // Get logs grouped by status for the last 7 days
            $weeklyStats = CronLog::where('started_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(started_at) as date, status, COUNT(*) as count')
                ->groupBy('date', 'status')
                ->orderBy('date', 'desc')
                ->get()
                ->groupBy('date');
            
            return response()->json([
                'success' => true,
                'message' => 'Cron logs retrieved successfully',
                'data' => [
                    'recent_logs' => $recentLogs,
                    'statistics' => $stats,
                    'weekly_stats' => $weeklyStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cron logs: ' . $e->getMessage()
            ], 500);
        }
    }
}
