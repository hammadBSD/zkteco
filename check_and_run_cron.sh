#!/bin/bash

# Wake-up Catch-up Script
# This script checks if the cron hasn't run recently and runs it to catch up
# Can be triggered on system wake or run manually

BASE_DIR="/Applications/MAMP/htdocs/zkteco"
PHP_PATH="/usr/local/opt/php@8.2/bin/php"
cd "$BASE_DIR"

# Get the last cron log entry
LAST_RUN=$("$PHP_PATH" artisan tinker --execute="echo \App\Models\CronLog::latest('started_at')->value('started_at') ?? 'never';" 2>/dev/null | tail -1)

if [ "$LAST_RUN" = "never" ] || [ -z "$LAST_RUN" ]; then
    echo "No previous cron runs found. Running cron now..."
    ./fetch_and_sync_monthly.sh
    exit 0
fi

# Convert last run time to Unix timestamp
LAST_RUN_TS=$(date -j -f "%Y-%m-%d %H:%M:%S" "$LAST_RUN" +%s 2>/dev/null || date -j -f "%Y-%m-%dT%H:%M:%S" "$LAST_RUN" +%s 2>/dev/null || echo "0")

if [ "$LAST_RUN_TS" = "0" ]; then
    echo "Could not parse last run time. Running cron now..."
    ./fetch_and_sync_monthly.sh
    exit 0
fi

# Get current time as Unix timestamp
CURRENT_TS=$(date +%s)

# Calculate minutes since last run
MINUTES_SINCE_LAST_RUN=$(( (CURRENT_TS - LAST_RUN_TS) / 60 )) 

# If it's been more than 20 minutes since last run, run the cron
if [ $MINUTES_SINCE_LAST_RUN -gt 20 ]; then
    echo "Last cron run was $MINUTES_SINCE_LAST_RUN minutes ago (more than 20 minutes)."
    echo "Running cron to catch up..."
    ./fetch_and_sync_monthly.sh
else
    echo "Last cron run was $MINUTES_SINCE_LAST_RUN minutes ago. No catch-up needed."
fi



