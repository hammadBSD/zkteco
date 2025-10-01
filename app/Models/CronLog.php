<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CronLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name',
        'month',
        'status',
        'started_at',
        'completed_at',
        'duration_seconds',
        'output',
        'error_message',
        'summary'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'summary' => 'array'
    ];

    /**
     * Get recent cron logs
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('started_at', 'desc')->limit($limit);
    }

    /**
     * Get logs by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get logs for a specific month
     */
    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Get today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    /**
     * Get logs from last 24 hours
     */
    public function scopeLast24Hours($query)
    {
        return $query->where('started_at', '>=', Carbon::now()->subHours(24));
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'success' => 'success',
            'failed' => 'danger',
            'running' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Check if job is currently running
     */
    public function isRunning()
    {
        return $this->status === 'running' && !$this->completed_at;
    }

    /**
     * Mark job as completed
     */
    public function markCompleted($status = 'success', $output = null, $summary = null, $errorMessage = null)
    {
        $this->update([
            'status' => $status,
            'completed_at' => now(),
            'duration_seconds' => $this->started_at->diffInSeconds(now()),
            'output' => $output,
            'summary' => $summary,
            'error_message' => $errorMessage
        ]);
    }
}