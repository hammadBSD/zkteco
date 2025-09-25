<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'punch_code_id',
        'name',
        'email',
        'department',
        'position',
        'is_active',
        'device_ip',
        'device_type',
        'synced_with_website'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'synced_with_website' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'punch_code_id', 'punch_code_id');
    }
}
