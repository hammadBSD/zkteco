<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyAttendance extends Model
{
    protected $table = 'monthly_attendances';

    protected $fillable = [
        'punch_code',
        'device_ip',
        'device_type',
        'punch_time',
        'punch_type',
        'verify_mode',
        'is_processed',
        'synced_with_website',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'is_processed' => 'boolean',
        'synced_with_website' => 'boolean',
    ];

    /**
     * Scope for a specific month
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('punch_time', $year)
                    ->whereMonth('punch_time', $month);
    }

    /**
     * Scope for current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('punch_time', Carbon::now()->year)
                    ->whereMonth('punch_time', Carbon::now()->month);
    }
}
