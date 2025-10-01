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

# For cron jobs, always use current month if no parameter provided
if [ -z "$1" ]; then
    MONTH=$(date +%Y-%m)
fi

# Create logs directory if it doesn't exist
mkdir -p logs

# Log file with timestamp
LOG_FILE="logs/fetch_and_sync_$(date +%Y%m%d).log"

# Start database logging
LOG_ID=$(php artisan cron:log start)

echo "🚀 Starting Monthly Attendance Fetch and Sync Process" | tee -a "$LOG_FILE"
echo "📅 Target Month: $MONTH" | tee -a "$LOG_FILE"
echo "🕐 Started at: $(date '+%Y-%m-%d %H:%M:%S')" | tee -a "$LOG_FILE"
echo "📊 Database Log ID: $LOG_ID" | tee -a "$LOG_FILE"
echo "==================================================" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

# Step 1: Fetch monthly attendance data
echo "📊 Step 1: Fetching monthly attendance data from ZKTeco devices..." | tee -a "$LOG_FILE"
php artisan zkteco:fetch-monthly --month=$MONTH --save 2>&1 | tee -a "$LOG_FILE"

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to fetch monthly attendance data" | tee -a "$LOG_FILE"
    php artisan cron:log complete --log-id=$LOG_ID --status=failed --error="Failed to fetch monthly attendance data"
    exit 1
fi

echo "✅ Monthly attendance data fetched and saved successfully!" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

# Step 2: Sync with website
echo "🔄 Step 2: Syncing monthly attendance data with website..." | tee -a "$LOG_FILE"
php artisan zkteco:sync-to-hr --type=monthly-attendance --month=$MONTH 2>&1 | tee -a "$LOG_FILE"

if [ $? -ne 0 ]; then
    echo "❌ Error: Failed to sync monthly attendance data with website" | tee -a "$LOG_FILE"
    php artisan cron:log complete --log-id=$LOG_ID --status=failed --error="Failed to sync monthly attendance data with website"
    exit 1
fi

echo "✅ Monthly attendance data synced with website successfully!" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

# Step 3: Show sync status
echo "📈 Step 3: Getting sync status..." | tee -a "$LOG_FILE"
php artisan zkteco:test-hr-api 2>&1 | tee -a "$LOG_FILE"

echo "" | tee -a "$LOG_FILE"
echo "==================================================" | tee -a "$LOG_FILE"
echo "🎉 Monthly Attendance Fetch and Sync Process Completed Successfully!" | tee -a "$LOG_FILE"
echo "📅 Month: $MONTH" | tee -a "$LOG_FILE"
echo "✅ Data fetched from ZKTeco devices" | tee -a "$LOG_FILE"
echo "✅ Data saved to monthly_attendances table" | tee -a "$LOG_FILE"
echo "✅ Data synced with website" | tee -a "$LOG_FILE"
echo "==================================================" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

echo "🏁 Process completed at $(date '+%Y-%m-%d %H:%M:%S')" | tee -a "$LOG_FILE"

# Complete database logging with success status
php artisan cron:log complete --log-id=$LOG_ID --status=success --output="Process completed successfully for month: $MONTH"
