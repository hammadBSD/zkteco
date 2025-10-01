<?php

/**
 * Monthly Attendance Fetch and Sync Script
 * 
 * This script will:
 * 1. Fetch monthly attendance data from ZKTeco devices
 * 2. Save the data to monthly_attendances table
 * 3. Sync the newly created entries with the website
 * 
 * Usage: php fetch_and_sync_monthly.php [month]
 * Example: php fetch_and_sync_monthly.php 2025-09
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Get month parameter from command line or use current month
$month = $argv[1] ?? date('Y-m');
$year = explode('-', $month)[0];
$monthNum = explode('-', $month)[1];

echo "🚀 Starting Monthly Attendance Fetch and Sync Process\n";
echo "📅 Target Month: {$month}\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Step 1: Fetch monthly attendance data
    echo "📊 Step 1: Fetching monthly attendance data from ZKTeco devices...\n";
    $fetchExitCode = $kernel->call('zkteco:fetch-monthly', [
        '--month' => $month,
        '--save' => true
    ]);
    
    if ($fetchExitCode !== 0) {
        echo "❌ Error: Failed to fetch monthly attendance data\n";
        exit(1);
    }
    
    echo "✅ Monthly attendance data fetched and saved successfully!\n\n";
    
    // Step 2: Sync with website
    echo "🔄 Step 2: Syncing monthly attendance data with website...\n";
    $syncExitCode = $kernel->call('zkteco:sync-to-hr', [
        '--type' => 'monthly-attendance',
        '--month' => $month
    ]);
    
    if ($syncExitCode !== 0) {
        echo "❌ Error: Failed to sync monthly attendance data with website\n";
        exit(1);
    }
    
    echo "✅ Monthly attendance data synced with website successfully!\n\n";
    
    // Step 3: Show summary
    echo "📈 Step 3: Getting sync status...\n";
    $statusExitCode = $kernel->call('zkteco:test-hr-api');
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🎉 Monthly Attendance Fetch and Sync Process Completed Successfully!\n";
    echo "📅 Month: {$month}\n";
    echo "✅ Data fetched from ZKTeco devices\n";
    echo "✅ Data saved to monthly_attendances table\n";
    echo "✅ Data synced with website\n";
    echo str_repeat("=", 50) . "\n";
    
} catch (Exception $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n🏁 Process completed at " . date('Y-m-d H:i:s') . "\n";
