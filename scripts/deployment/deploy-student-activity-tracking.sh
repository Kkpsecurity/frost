#!/bin/bash
#
# Student Activity Tracking System - Deployment Script (Bash Version)
#
# Description: Deploys the Student Activity Tracking system from development to staging
# Author: FROST Development Team
# Version: 1.0
# Created: September 30, 2025
#

set -euo pipefail

# Configuration
SOURCE_PATH="//develc/webroot/frost-rclark"
STAGING_PATH="//atlas/webroot/frost-staging"
DRY_RUN=false
SKIP_BACKUP=false
FORCE=false
VERBOSE=false

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Logging
LOG_DIR="$(dirname "$0")/logs"
LOG_FILE="$LOG_DIR/deploy-$(date +%Y%m%d-%H%M%S).log"

# Create log directory
mkdir -p "$LOG_DIR"

# Logging function
log() {
    local level="$1"
    local message="$2"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')

    echo "[$timestamp] [$level] $message" >> "$LOG_FILE"

    case $level in
        "ERROR")
            echo -e "${RED}[$(date '+%H:%M:%S')] $message${NC}" >&2
            ;;
        "SUCCESS")
            echo -e "${GREEN}[$(date '+%H:%M:%S')] $message${NC}"
            ;;
        "WARN")
            echo -e "${YELLOW}[$(date '+%H:%M:%S')] $message${NC}"
            ;;
        "INFO")
            echo -e "[$(date '+%H:%M:%S')] $message"
            ;;
        "DEBUG")
            if [ "$VERBOSE" = true ]; then
                echo -e "${BLUE}[$(date '+%H:%M:%S')] $message${NC}"
            fi
            ;;
    esac
}

show_banner() {
    echo ""
    echo -e "${CYAN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║              FROST - Student Activity Tracking              ║${NC}"
    echo -e "${CYAN}║                    Deployment Script                        ║${NC}"
    echo -e "${CYAN}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

show_usage() {
    cat << EOF
Usage: $0 [OPTIONS]

Deploy Student Activity Tracking system to staging environment.

OPTIONS:
    -d, --dry-run       Preview deployment without making changes
    -s, --skip-backup   Skip creating backup of existing files
    -f, --force         Force deployment without confirmation
    -v, --verbose       Verbose output for debugging
    -h, --help          Show this help message

EXAMPLES:
    $0                  Deploy with backup and confirmation
    $0 --dry-run        Preview deployment
    $0 --force --skip-backup    Fast deployment without backup

EOF
}

parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            -d|--dry-run)
                DRY_RUN=true
                shift
                ;;
            -s|--skip-backup)
                SKIP_BACKUP=true
                shift
                ;;
            -f|--force)
                FORCE=true
                shift
                ;;
            -v|--verbose)
                VERBOSE=true
                shift
                ;;
            -h|--help)
                show_usage
                exit 0
                ;;
            *)
                echo "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
}

check_prerequisites() {
    log "INFO" "Checking deployment prerequisites..."

    local issues=()

    # Check source path
    if [ ! -d "$SOURCE_PATH" ]; then
        issues+=("Source path does not exist: $SOURCE_PATH")
    fi

    # Check target path
    if [ ! -d "$STAGING_PATH" ]; then
        issues+=("Target path does not exist: $STAGING_PATH")
    fi

    # Check if Laravel application
    if [ ! -f "$SOURCE_PATH/artisan" ]; then
        issues+=("Not a Laravel application (artisan not found)")
    fi

    # Check if target is Laravel application
    if [ ! -f "$STAGING_PATH/artisan" ]; then
        issues+=("Target is not a Laravel application (artisan not found)")
    fi

    # Check required commands
    for cmd in php composer; do
        if ! command -v $cmd &> /dev/null; then
            issues+=("Required command not found: $cmd")
        fi
    done

    if [ ${#issues[@]} -gt 0 ]; then
        log "ERROR" "Prerequisites check failed:"
        for issue in "${issues[@]}"; do
            log "ERROR" "  - $issue"
        done
        return 1
    fi

    log "SUCCESS" "Prerequisites check passed"
    return 0
}

get_deployment_files() {
    cat << 'EOF'
database/migrations/2025_09_30_000000_create_student_activities_table.php|true|Database migration for student activities table
app/Models/StudentActivity.php|true|Eloquent model for student activities
app/Services/StudentActivityService.php|true|Service class for activity tracking business logic
app/Http/Middleware/TrackStudentActivity.php|true|Middleware for automatic activity tracking
app/Http/Controllers/Api/StudentActivityController.php|true|API controller for activity tracking endpoints
routes/api/student_activity_routes.php|true|API routes for student activity tracking
docs/architecture/student-activity-tracking.md|false|System architecture documentation
docs/deployment/student-activity-tracking-implementation.md|false|Implementation guide
docs/deployment/staging-deployment-guide.md|false|Staging deployment guide
KKP/scripts/deploy-student-activity-tracking.sh|false|Deployment script (this file)
EOF
}

create_backup_directory() {
    if [ "$SKIP_BACKUP" = true ]; then
        log "WARN" "Skipping backup creation (SkipBackup flag set)"
        echo ""
        return
    fi

    local backup_dir="$STAGING_PATH/backups/student-activity-tracking/$(date +%Y%m%d-%H%M%S)"

    if [ "$DRY_RUN" = false ]; then
        if mkdir -p "$backup_dir" 2>/dev/null; then
            log "SUCCESS" "Created backup directory: $backup_dir"
        else
            log "ERROR" "Failed to create backup directory: $backup_dir"
            echo ""
            return
        fi
    else
        log "DEBUG" "Would create backup directory: $backup_dir"
    fi

    echo "$backup_dir"
}

deploy_files() {
    local backup_dir="$1"
    local deployed=0
    local backed_up=0
    local skipped=0
    local errors=()

    log "INFO" "Starting file deployment..."
    log "INFO" "Source: $SOURCE_PATH"
    log "INFO" "Target: $STAGING_PATH"

    while IFS='|' read -r source_file critical description; do
        if [ -z "$source_file" ]; then continue; fi

        local source_path="$SOURCE_PATH/$source_file"
        local target_path="$STAGING_PATH/$source_file"
        local target_dir=$(dirname "$target_path")

        # Check if source file exists
        if [ ! -f "$source_path" ]; then
            if [ "$critical" = "true" ]; then
                local error="CRITICAL: Source file not found: $source_file"
                errors+=("$error")
                log "ERROR" "$error"
                continue
            else
                log "WARN" "Optional file not found, skipping: $source_file"
                ((skipped++))
                continue
            fi
        fi

        # Create target directory
        if [ ! -d "$target_dir" ]; then
            if [ "$DRY_RUN" = false ]; then
                mkdir -p "$target_dir"
            fi
            log "INFO" "Created directory: ${target_dir#$STAGING_PATH}"
        fi

        # Backup existing file
        if [ "$SKIP_BACKUP" = false ] && [ -f "$target_path" ] && [ -n "$backup_dir" ]; then
            local backup_file="$backup_dir/$source_file"
            local backup_file_dir=$(dirname "$backup_file")

            if [ "$DRY_RUN" = false ]; then
                mkdir -p "$backup_file_dir"
                cp "$target_path" "$backup_file"
            fi
            ((backed_up++))
            log "INFO" "Backed up: $source_file"
        fi

        # Deploy file
        if [ "$DRY_RUN" = false ]; then
            cp "$source_path" "$target_path"
        fi
        ((deployed++))

        local status
        if [ "$DRY_RUN" = true ]; then
            status="Would deploy"
        else
            status="Deployed"
        fi
        log "SUCCESS" "$status: $source_file"

        if [ "$VERBOSE" = true ]; then
            log "DEBUG" "  Description: $description"
        fi

    done < <(get_deployment_files)

    # Return results as space-separated values
    echo "$deployed $backed_up $skipped ${#errors[@]}"
    if [ ${#errors[@]} -gt 0 ]; then
        printf '%s\n' "${errors[@]}" >&2
    fi
}

show_post_deployment_tasks() {
    log "WARN" ""
    log "WARN" "Post-deployment configuration required:"

    log "INFO" "  ✓ Update app/Http/Kernel.php:"
    log "INFO" "    Add middleware to 'web' group"
    log "DEBUG" "    Add: \\App\\Http\\Middleware\\TrackStudentActivity::class"
    log "INFO" ""

    log "INFO" "  ✓ Update routes/api.php:"
    log "INFO" "    Include activity routes"
    log "DEBUG" "    Add: require __DIR__ . '/api/student_activity_routes.php';"
    log "INFO" ""

    log "INFO" "  ✓ Update app/Providers/AppServiceProvider.php:"
    log "INFO" "    Register service in register() method"
    log "DEBUG" "    Add: \$this->app->singleton(\\App\\Services\\StudentActivityService::class);"
    log "INFO" ""

    log "INFO" "Database Migration:"
    log "DEBUG" "  cd $STAGING_PATH"
    log "DEBUG" "  php artisan migrate"
    log "INFO" ""

    log "INFO" "Clear Caches:"
    log "DEBUG" "  php artisan route:clear"
    log "DEBUG" "  php artisan config:clear"
    log "DEBUG" "  php artisan cache:clear"
}

show_rollback_instructions() {
    local backup_dir="$1"

    if [ "$SKIP_BACKUP" = true ] || [ -z "$backup_dir" ]; then
        return
    fi

    log "WARN" ""
    log "WARN" "ROLLBACK INSTRUCTIONS:"
    log "INFO" "If you need to rollback this deployment:"
    log "DEBUG" "  1. Restore files: cp -r '$backup_dir'/* '$STAGING_PATH'/"
    log "DEBUG" "  2. Rollback migration: php artisan migrate:rollback --step=1"
}

confirm_deployment() {
    if [ "$FORCE" = true ]; then
        return 0
    fi

    echo ""
    echo -e "${YELLOW}Deployment Summary:${NC}"
    echo "  Environment: staging"
    echo "  Source: $SOURCE_PATH"
    echo "  Target: $STAGING_PATH"
    echo "  Backup: $([ "$SKIP_BACKUP" = true ] && echo "No" || echo "Yes")"
    echo "  Dry Run: $([ "$DRY_RUN" = true ] && echo "Yes" || echo "No")"
    echo ""

    read -p "Continue with deployment? (y/N): " -n 1 -r
    echo ""
    [[ $REPLY =~ ^[Yy]$ ]]
}

# Main execution
main() {
    show_banner

    log "INFO" "Starting Student Activity Tracking deployment"
    log "INFO" "Dry Run: $DRY_RUN"
    log "INFO" "Skip Backup: $SKIP_BACKUP"
    log "INFO" "Log file: $LOG_FILE"

    # Check prerequisites
    if ! check_prerequisites; then
        log "ERROR" "Deployment aborted due to failed prerequisites"
        exit 1
    fi

    # Get confirmation
    if ! confirm_deployment; then
        log "WARN" "Deployment cancelled by user"
        exit 0
    fi

    # Create backup directory
    backup_dir=$(create_backup_directory)

    # Deploy files
    results=$(deploy_files "$backup_dir")
    read -r deployed backed_up skipped error_count <<< "$results"

    # Show results
    log "INFO" ""
    log "INFO" "$(printf '=%.0s' {1..60})"
    log "INFO" "DEPLOYMENT SUMMARY"
    log "INFO" "$(printf '=%.0s' {1..60})"

    log "SUCCESS" "Files deployed: $deployed"
    if [ "$SKIP_BACKUP" = false ]; then
        log "INFO" "Files backed up: $backed_up"
    fi
    if [ "$skipped" -gt 0 ]; then
        log "WARN" "Files skipped: $skipped"
    fi

    if [ "$error_count" -gt 0 ]; then
        log "ERROR" "Errors encountered: $error_count"
        log "ERROR" "Deployment completed with errors!"
        exit 1
    else
        log "SUCCESS" "Deployment completed successfully!"
    fi

    if [ "$DRY_RUN" = false ]; then
        show_post_deployment_tasks
        show_rollback_instructions "$backup_dir"
    fi

    log "INFO" "Deployment log saved to: $LOG_FILE"
}

# Parse command line arguments
parse_arguments "$@"

# Run main function
main
