<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronLog;
use Carbon\Carbon;

class LogCronExecution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:log {action} {--log-id=} {--status=} {--output=} {--summary=} {--error=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log cron job execution to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $logId = $this->option('log-id');
        
        switch ($action) {
            case 'start':
                return $this->startLog();
            case 'complete':
                return $this->completeLog($logId);
            default:
                $this->error('Invalid action. Use "start" or "complete"');
                return 1;
        }
    }

    private function startLog()
    {
        $log = CronLog::create([
            'job_name' => 'fetch_and_sync_monthly',
            'month' => date('Y-m'),
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->info($log->id);
        return 0;
    }

    private function completeLog($logId)
    {
        if (!$logId) {
            $this->error('Log ID is required for completion');
            return 1;
        }

        $log = CronLog::find($logId);
        if (!$log) {
            $this->error('Log entry not found');
            return 1;
        }

        $status = $this->option('status') ?? 'success';
        $output = $this->option('output');
        $summary = $this->option('summary');
        $error = $this->option('error');

        // Parse summary if provided as JSON string
        if ($summary) {
            $summary = json_decode($summary, true);
        }

        $log->markCompleted($status, $output, $summary, $error);

        $this->info("Log completed: {$log->id}");
        return 0;
    }
}