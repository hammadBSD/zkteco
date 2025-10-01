#!/bin/bash

# ZKTeco Cron Job Management Script
# This script helps you manage the automatic sync cron job

SCRIPT_DIR="/Applications/MAMP/htdocs/zkteco"
CRON_COMMENT="# ZKTeco Monthly Attendance Sync"

show_status() {
    echo "📊 Current Cron Job Status:"
    echo "=========================="
    
    if crontab -l 2>/dev/null | grep -q "$CRON_COMMENT"; then
        echo "✅ Cron job is ACTIVE"
        echo "📅 Schedule: Every 10 minutes"
        echo "📝 Command: ./fetch_and_sync_monthly.sh"
        echo ""
        echo "📋 Current cron entry:"
        crontab -l | grep -A1 "$CRON_COMMENT"
    else
        echo "❌ Cron job is NOT ACTIVE"
    fi
    
    echo ""
    echo "📁 Log files in logs/ directory:"
    if [ -d "$SCRIPT_DIR/logs" ]; then
        ls -la "$SCRIPT_DIR/logs/" | grep fetch_and_sync
    else
        echo "   No logs directory found"
    fi
}

start_cron() {
    echo "🚀 Starting cron job..."
    
    # Create the cron entry
    (crontab -l 2>/dev/null; echo ""; echo "$CRON_COMMENT - Runs every 10 minutes"; echo "*/10 * * * * cd $SCRIPT_DIR && ./fetch_and_sync_monthly.sh >/dev/null 2>&1") | crontab -
    
    if [ $? -eq 0 ]; then
        echo "✅ Cron job started successfully!"
        echo "📅 Will run every 10 minutes automatically"
    else
        echo "❌ Failed to start cron job"
        exit 1
    fi
}

stop_cron() {
    echo "🛑 Stopping cron job..."
    
    # Remove the cron entry
    crontab -l 2>/dev/null | grep -v "$CRON_COMMENT" | grep -v "fetch_and_sync_monthly.sh" | crontab -
    
    if [ $? -eq 0 ]; then
        echo "✅ Cron job stopped successfully!"
    else
        echo "❌ Failed to stop cron job"
        exit 1
    fi
}

show_logs() {
    echo "📋 Recent log entries:"
    echo "======================"
    
    if [ -d "$SCRIPT_DIR/logs" ]; then
        LATEST_LOG=$(ls -t "$SCRIPT_DIR/logs"/fetch_and_sync_*.log 2>/dev/null | head -1)
        if [ -n "$LATEST_LOG" ]; then
            echo "📄 Latest log: $LATEST_LOG"
            echo ""
            tail -20 "$LATEST_LOG"
        else
            echo "   No log files found"
        fi
    else
        echo "   No logs directory found"
    fi
}

case "$1" in
    "start")
        start_cron
        ;;
    "stop")
        stop_cron
        ;;
    "status")
        show_status
        ;;
    "logs")
        show_logs
        ;;
    "restart")
        stop_cron
        sleep 1
        start_cron
        ;;
    *)
        echo "ZKTeco Cron Job Manager"
        echo "======================"
        echo ""
        echo "Usage: $0 {start|stop|status|logs|restart}"
        echo ""
        echo "Commands:"
        echo "  start   - Start the cron job (runs every 10 minutes)"
        echo "  stop    - Stop the cron job"
        echo "  status  - Show current cron job status"
        echo "  logs    - Show recent log entries"
        echo "  restart - Restart the cron job"
        echo ""
        show_status
        ;;
esac
