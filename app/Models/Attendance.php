<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'punch_code_id',
        'device_ip',
        'device_type',
        'punch_time',
        'verify_mode',
        'is_processed'
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'is_processed' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'punch_code_id', 'punch_code_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('punch_time', today());
    }
}
