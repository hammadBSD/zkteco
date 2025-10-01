#!/bin/bash

# Monthly Attendance Fetch and Sync Script
# 
# This script will:
# 1. Fetch monthly attendance data from ZKTeco devices
# 2. Save the data to monthly_attendances table
# 3. Sync the newly created entries with the website
# 
# Usage: ./fetch_and_sync_monthly.sh [month]
# Example: ./fetch_and_sync_monthly.sh 2025-09
# 
# If no month is provided, it will use the current month

# Get month parameter or use current month
MONTH=${1:-$(date +%Y-%m)}

echo "🚀 Starting Monthly Attendance Fetch and Sync Process"
echo "📅 Target Month: $MONTH"
echo "=================================================="
echo ""

# Step 1: Fetch monthly attendance data
echo "📊 Step 1: Fetching monthly attendance data from ZKTeco devices..."
php artisan zkteco:fetch-monthly --month=$MONTH --save

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to fetch monthly attendance data"
    exit 1
fi

echo "✅ Monthly attendance data fetched and saved successfully!"
echo ""

# Step 2: Sync with website
echo "🔄 Step 2: Syncing monthly attendance data with website..."
php artisan zkteco:sync-to-hr --type=monthly-attendance --month=$MONTH

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to sync monthly attendance data with website"
    exit 1
fi

echo "✅ Monthly attendance data synced with website successfully!"
echo ""

# Step 3: Show sync status
echo "📈 Step 3: Getting sync status..."
php artisan zkteco:test-hr-api

echo ""
echo "=================================================="
echo "🎉 Monthly Attendance Fetch and Sync Process Completed Successfully!"
echo "📅 Month: $MONTH"
echo "✅ Data fetched from ZKTeco devices"
echo "✅ Data saved to monthly_attendances table"
echo "✅ Data synced with website"
echo "=================================================="
echo ""

echo "🏁 Process completed at $(date '+%Y-%m-%d %H:%M:%S')"
