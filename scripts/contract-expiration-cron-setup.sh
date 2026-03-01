#!/bin/bash

# contract-expiration-cron-setup.sh
# 
# Script pour configurer automatiquement les tâches cron pour les alertes d'expiration de contrats
#
# Usage: bash contract-expiration-cron-setup.sh /path/to/project
#

set -e

if [ -z "$1" ]; then
    echo "Usage: bash contract-expiration-cron-setup.sh /path/to/project"
    echo ""
    echo "Example:"
    echo "  bash contract-expiration-cron-setup.sh /var/www/sport_insightt"
    exit 1
fi

PROJECT_PATH="$1"
LOG_PATH="/var/log/sport-insightt"
LOG_FILE="$LOG_PATH/contract-expiration.log"
CRON_COMMENT="# Contract Expiration Alerts - Sport Insight"

# Verify project path
if [ ! -f "$PROJECT_PATH/bin/console" ]; then
    echo "ERROR: Project path is invalid. bin/console not found in: $PROJECT_PATH"
    exit 1
fi

echo "===================================="
echo "Contract Expiration Cron Setup"
echo "===================================="
echo ""
echo "Project Path: $PROJECT_PATH"
echo "Log File: $LOG_FILE"
echo ""

# Create log directory
if [ ! -d "$LOG_PATH" ]; then
    echo "Creating log directory: $LOG_PATH"
    sudo mkdir -p "$LOG_PATH"
    sudo chmod 755 "$LOG_PATH"
fi

# Create log file
if [ ! -f "$LOG_FILE" ]; then
    echo "Creating log file: $LOG_FILE"
    sudo touch "$LOG_FILE"
    sudo chmod 666 "$LOG_FILE"
fi

# Backup current crontab
CRONTAB_BACKUP="/tmp/crontab.backup.$(date +%s)"
echo "Backing up current crontab to: $CRONTAB_BACKUP"
crontab -l > "$CRONTAB_BACKUP" 2>/dev/null || echo "# No existing crontab"

# Remove existing contract expiration cron entries (if any)
TEMP_CRON=$(mktemp)
crontab -l 2>/dev/null | grep -v "app:contract:expiration" > "$TEMP_CRON" || true

# Add new cron entries
echo ""
echo "Adding cron entries..."
echo ""

# Entry 1: Check expired contracts daily at 08:00
echo "1) Daily at 08:00 - Check for expired contracts"
echo "0 8 * * * cd $PROJECT_PATH && php bin/console app:contract:expiration >> $LOG_FILE 2>&1" >> "$TEMP_CRON"
echo "   0 8 * * * cd $PROJECT_PATH && php bin/console app:contract:expiration"
echo ""

# Entry 2: Check expiring-soon contracts daily at 09:00 (7-day warning)
echo "2) Daily at 09:00 - Check for contracts expiring within 7 days"
echo "0 9 * * * cd $PROJECT_PATH && php bin/console app:contract:expiration --days-ahead=7 >> $LOG_FILE 2>&1" >> "$TEMP_CRON"
echo "   0 9 * * * cd $PROJECT_PATH && php bin/console app:contract:expiration --days-ahead=7"
echo ""

# Install new crontab
crontab "$TEMP_CRON"
rm "$TEMP_CRON"

echo "===================================="
echo "✅ Cron job setup completed!"
echo "===================================="
echo ""
echo "Summary:"
echo "  📅 Expired contracts: Daily at 08:00"
echo "  📅 Expiring soon (7 days): Daily at 09:00"
echo "  📁 Logs: $LOG_FILE"
echo ""
echo "To verify cron is installed:"
echo "  crontab -l | grep app:contract:expiration"
echo ""
echo "To view logs in real-time:"
echo "  tail -f $LOG_FILE"
echo ""
echo "To restore original crontab if needed:"
echo "  crontab $CRONTAB_BACKUP"
echo ""
