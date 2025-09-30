#!/bin/bash

# FROST Database Sync Script - Windows Compatible
# Safely syncs live database to local development environment
# 
# Usage: ./sync-db-improved.sh [options]
# Options:
#   --dry-run, -d     Show what would be done without executing
#   --force, -f       Skip confirmation prompts
#   --backup, -b      Force create remote backup
#   --no-backup       Skip remote backup
#   --purge, -p       Force purge job tables
#   --no-purge        Skip purging job tables
#   --help, -h        Show this help message

set -e

# Script configuration
SCRIPTDIR="$( dirname "$( realpath "${BASH_SOURCE[0]}" )" )"
source "$SCRIPTDIR/xfer-lib.sh"

# Default options
DRY_RUN=false
FORCE_MODE=false
DO_BACKUP_REMOTE=""
DO_PURGE_TABLES=""

# Parse command line arguments
parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            --dry-run|-d)
                DRY_RUN=true
                shift
                ;;
            --force|-f)
                FORCE_MODE=true
                shift
                ;;
            --backup|-b|+br)
                DO_BACKUP_REMOTE=1
                shift
                ;;
            --no-backup|-nb)
                DO_BACKUP_REMOTE=0
                shift
                ;;
            --purge|-p|+pg)
                DO_PURGE_TABLES=1
                shift
                ;;
            --no-purge|-np)
                DO_PURGE_TABLES=0
                shift
                ;;
            --all|-a)
                DO_BACKUP_REMOTE=1
                DO_PURGE_TABLES=1
                shift
                ;;
            --help|-h)
                show_help
                exit 0
                ;;
            *)
                echo "Unknown option: $1"
                echo "Use --help for usage information"
                exit 1
                ;;
        esac
    done
}

# Show help message
show_help() {
    cat << EOF
FROST Database Sync Script

DESCRIPTION:
    Safely syncs the live database to your local development environment.
    Includes safety checks, backups, and environment validation.

USAGE:
    $0 [OPTIONS]

OPTIONS:
    --dry-run, -d     Show what would be done without executing
    --force, -f       Skip confirmation prompts (use with caution)
    --backup, -b      Force create remote backup
    --no-backup       Skip remote backup
    --purge, -p       Force purge job tables
    --no-purge        Skip purging job tables
    --all, -a         Enable backup and purge options
    --help, -h        Show this help message

EXAMPLES:
    $0                          # Interactive mode
    $0 --dry-run                # See what would happen
    $0 --force --all            # Automated sync with all options
    $0 --backup --no-purge      # Backup remote but don't purge tables

SAFETY FEATURES:
    - Environment validation (prevents running on production)
    - Local database backup before sync
    - Connection testing before operations
    - Session table preservation
    - Post-sync local configuration updates

EOF
}

# Main script header
show_header() {
    cat << EOF

🚀 FROST Database Sync Script
============================
$(date)

Configuration:
- Source: $REMOTE_DB @ $REMOTE_HOST
- Target: $LOCAL_DB @ $LOCAL_HOST
- Mode: $([ "$DRY_RUN" = true ] && echo "DRY RUN" || echo "LIVE")

EOF
}

# Interactive prompts (unless force mode)
prompt_user() {
    local message=$1
    local default=${2:-"n"}
    
    if [[ "$FORCE_MODE" = true ]]; then
        echo "$message (auto-answered: $default due to --force)"
        [[ "$default" = "y" ]] && return 0 || return 1
    fi
    
    while true; do
        read -p "$message [y/n] " -r answer
        case ${answer,,} in
            y|yes) return 0 ;;
            n|no) return 1 ;;
            *) echo "Please answer y or n" ;;
        esac
    done
}

# Remote backup function
backup_remote() {
    local dumpfile="$SCRIPTDIR/_${REMOTE_DB}.sql"
    
    echo "🔄 Backing up remote database..."
    
    if [[ "$DRY_RUN" = true ]]; then
        echo "   🔍 DRY RUN: Would backup $REMOTE_DB to $dumpfile"
        return 0
    fi
    
    # Create file with proper permissions
    if [[ ! -e "$dumpfile" ]]; then
        touch "$dumpfile"
        chmod 0664 "$dumpfile"
    fi
    
    # Backup remote database
    echo "   📤 Creating remote dump..."
    if $SSH_BIN -i "$SSH_USER_KEY" $REMOTE_USER@$REMOTE_HOST "pg_dump $REMOTE_DB > $REMOTE_HOME/$REMOTE_DB.sql"; then
        echo "   ✅ Remote dump created"
    else
        echo "   ❌ Failed to create remote dump"
        exit 1
    fi
    
    # Copy to local
    echo "   📥 Copying to local system..."
    if $SCP_BIN -i "$SSH_USER_KEY" $REMOTE_USER@$REMOTE_HOST:$REMOTE_HOME/$REMOTE_DB.sql "$dumpfile"; then
        echo "   ✅ Remote backup completed: $(basename "$dumpfile")"
    else
        echo "   ❌ Failed to copy remote backup"
        exit 1
    fi
}

# Purge job tables
purge_tables() {
    echo "🧹 Purging job tables..."
    
    local tables=("failed_jobs" "jobs" "job_batches")
    
    for table in "${tables[@]}"; do
        echo "   🗑️  Cleaning table: $table"
        
        if [[ "$DRY_RUN" = true ]]; then
            echo "      🔍 DRY RUN: Would run TRUNCATE $table RESTART IDENTITY;"
        else
            echo "TRUNCATE $table RESTART IDENTITY;" | $PSQL_BIN -q $LOCAL_DB
        fi
    done
    
    echo "   ✅ Job tables purged"
}

# Backup and restore sessions
manage_sessions() {
    local action=$1
    local sessionfile="$SCRIPTDIR/_sessions.sql"
    
    if [[ "$action" = "backup" ]]; then
        echo "💾 Backing up local sessions..."
        
        if [[ "$DRY_RUN" = true ]]; then
            echo "   🔍 DRY RUN: Would backup sessions table to $sessionfile"
        else
            $PGDUMP_BIN -at sessions $LOCAL_DB | sed '/^SE/d' > "$sessionfile"
            echo "   ✅ Sessions backed up"
        fi
        
    elif [[ "$action" = "restore" ]]; then
        echo "🔄 Restoring local sessions..."
        
        if [[ "$DRY_RUN" = true ]]; then
            echo "   🔍 DRY RUN: Would restore sessions from $sessionfile"
        else
            echo 'TRUNCATE sessions;' | $PSQL_BIN -q $LOCAL_DB
            $PSQL_BIN -qf "$sessionfile" $LOCAL_DB
            echo "   ✅ Sessions restored"
        fi
    fi
}

# Main sync process
perform_sync() {
    local dumpfile="$SCRIPTDIR/_${REMOTE_DB}.sql"
    
    echo "🔄 Loading database on local server..."
    
    if [[ "$DRY_RUN" = true ]]; then
        echo "   🔍 DRY RUN: Would recreate database $LOCAL_DB"
        echo "   🔍 DRY RUN: Would load data from $dumpfile"
        return 0
    fi
    
    # Recreate database
    echo "   🏗️  Recreating database $LOCAL_DB..."
    sed "s/DBNAME/$LOCAL_DB/g" "$SCRIPTDIR/_createDB.sql" | $PSQL_BIN -q template1
    
    # Load data
    echo "   📥 Loading data..."
    if $PSQL_BIN -qf "$dumpfile" $LOCAL_DB; then
        echo "   ✅ Database loaded successfully"
    else
        echo "   ❌ Failed to load database"
        exit 1
    fi
}

# Main execution flow
main() {
    # Parse arguments
    parse_arguments "$@"
    
    # Show header
    show_header
    
    # Safety checks
    check_environment
    validate_prerequisites
    
    # Connection tests
    test_database_connection $REMOTE_HOST $REMOTE_DB $REMOTE_USER
    test_database_connection $LOCAL_HOST $LOCAL_DB $LOCAL_USER
    
    # Show transfer summary
    display_transfer_summary
    
    # Confirm operation
    if [[ "$DRY_RUN" = false ]] && [[ "$FORCE_MODE" = false ]]; then
        echo "⚠️  WARNING: This operation will:"
        echo "   • Replace all data in $LOCAL_DB"
        echo "   • Cannot be undone!"
        echo
        
        if ! prompt_user "Continue with database sync?"; then
            echo "Operation cancelled."
            exit 0
        fi
    fi
    
    # Create local backup
    if [[ "$DRY_RUN" = false ]]; then
        backup_local_database
    fi
    
    # Backup sessions
    manage_sessions backup
    
    # Backup remote (if requested)
    if [[ -z "$DO_BACKUP_REMOTE" ]]; then
        if prompt_user "Backup remote database?" "n"; then
            DO_BACKUP_REMOTE=1
        fi
    fi
    
    if [[ "$DO_BACKUP_REMOTE" = "1" ]]; then
        backup_remote
    fi
    
    # Perform sync
    perform_sync
    
    # Purge tables (if requested)
    if [[ -z "$DO_PURGE_TABLES" ]]; then
        if prompt_user "Purge job tables?" "y"; then
            DO_PURGE_TABLES=1
        fi
    fi
    
    if [[ "$DO_PURGE_TABLES" = "1" ]]; then
        purge_tables
    fi
    
    # Restore sessions
    manage_sessions restore
    
    # Post-sync operations
    if [[ "$DRY_RUN" = false ]]; then
        echo "🔧 Running post-sync operations..."
        source "$SCRIPTDIR/post-sync.sh"
        
        echo "🧹 Flushing cache..."
        source "$SCRIPTDIR/flush-redis.sh"
    else
        echo "🔍 DRY RUN: Would run post-sync operations"
        echo "🔍 DRY RUN: Would flush cache"
    fi
    
    # Success message
    echo
    if [[ "$DRY_RUN" = true ]]; then
        echo "🔍 DRY RUN COMPLETED - No actual changes were made"
        echo "   Remove --dry-run flag to perform actual sync"
    else
        echo "✅ DATABASE SYNC COMPLETED SUCCESSFULLY!"
        echo "   Local database $LOCAL_DB is now synchronized with $REMOTE_DB"
    fi
    echo
}

# Execute main function with all arguments
main "$@"
