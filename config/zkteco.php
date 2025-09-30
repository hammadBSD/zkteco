<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZKTeco HR Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for syncing data with HR system
    |
    */

    'hr_api_url' => env('ZKTECO_HR_API_URL', 'http://hcm.local/api'),
    'hr_api_key' => env('ZKTECO_HR_API_KEY', 'zkteco-secure-api-key-2024'),
    
    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    */
    
    'sync_enabled' => env('ZKTECO_SYNC_ENABLED', true),
    'sync_interval' => env('ZKTECO_SYNC_INTERVAL', 5), // minutes
    'batch_size' => env('ZKTECO_BATCH_SIZE', 100),
];
