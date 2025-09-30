<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetUrl extends Model
{
    protected $fillable = [
        'name',
        'target_url',
    ];

    /**
     * Get target URL by name
     */
    public static function getUrlByName($name)
    {
        $targetUrl = self::where('name', $name)->first();
        return $targetUrl ? $targetUrl->target_url : null;
    }
}
