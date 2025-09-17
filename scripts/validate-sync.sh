#!/bin/bash

# Database Sync Validation and Testing Script
# Purpose: Validate connections, perform dry run, then sync if approved

# Source the configuration
source "$(dirname "$0")/xfer-lib.sh"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local color=$1
    local message=$2
    echo -e "${color}${message}${NC}"
}

# Function to print section headers
print_section() {
    echo ""
    print_status $BLUE "============================================"
    print_status $BLUE "$1"
    print_status $BLUE "============================================"
}

# Function to test database connection and get basic info
test_database_info() {
    local host=$1
    local db=$2
    local user=$3
    local password=$4
    local db_type=$5
    
    print_status $YELLOW "Testing $db_type database: $db on $host"
    
    # Set password environment variable
    export PGPASSWORD="$password"
    
    # Test basic connection
    if ! $PSQL_BIN -h $host -d $db -U $user -c "SELECT 1;" >/dev/null 2>&1; then
        print_status $RED "❌ Connection failed to $db_type database"
        return 1
    fi
    
    print_status $GREEN "✅ Connection successful to $db_type database"
    
    # Get database info
    echo "Database Information:"
    $PSQL_BIN -h $host -d $db -U $user -c "
        SELECT 
            current_database() as database_name,
            current_user as connected_user,
            version() as postgres_version;
    " 2>/dev/null
    
    # Get table count
    local table_count=$($PSQL_BIN -h $host -d $db -U $user -t -c "
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_type = 'BASE TABLE';
    " 2>/dev/null | tr -d ' ')
    
    echo "Tables in public schema: $table_count"
    
    # Get approximate record counts for key tables
    echo "Sample table record counts:"
    $PSQL_BIN -h $host -d $db -U $user -c "
        SELECT 
            schemaname,
            tablename,
            n_tup_ins as approx_rows
        FROM pg_stat_user_tables 
        WHERE schemaname = 'public'
        ORDER BY n_tup_ins DESC 
        LIMIT 10;
    " 2>/dev/null
    
    return 0
}

# Function to compare source and destination
compare_databases() {
    print_section "DATABASE COMPARISON"
    
    export PGPASSWORD="$PROD_PASSWORD"
    local prod_tables=$($PSQL_BIN -h $PROD_HOST -d $PROD_DB -U $PROD_USER -t -c "
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_type = 'BASE TABLE';
    " 2>/dev/null | tr -d ' ')
    
    export PGPASSWORD="$DEV_PASSWORD"
    local dev_tables=$($PSQL_BIN -h $DEV_HOST -d $DEV_DB -U $DEV_USER -t -c "
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = 'public' AND table_type = 'BASE TABLE';
    " 2>/dev/null | tr -d ' ')
    
    echo "Production tables: $prod_tables"
    echo "Development tables: $dev_tables"
    
    if [ "$prod_tables" -eq "$dev_tables" ]; then
        print_status $GREEN "✅ Table counts match"
    else
        print_status $YELLOW "⚠️  Table counts differ (this may be normal if schemas have diverged)"
    fi
}

# Function to perform dry run
perform_dry_run() {
    print_section "DRY RUN SIMULATION"
    
    print_status $YELLOW "Simulating sync from $PROD_DB to $DEV_DB..."
    
    # Check if sync script exists
    if [ ! -f "$(dirname "$0")/sync-db-improved.sh" ]; then
        print_status $RED "❌ sync-db-improved.sh not found"
        return 1
    fi
    
    echo "Dry run would:"
    echo "1. Create backup of $DEV_DB"
    echo "2. Drop and recreate $DEV_DB database"
    echo "3. Copy schema and data from $PROD_DB"
    echo "4. Update sequences and permissions"
    echo "5. Run post-sync cleanup"
    
    # Test that we can create a backup filename
    local backup_file="backup_${DEV_DB}_$(date +%Y%m%d_%H%M%S).sql"
    echo "Backup would be saved as: $backup_file"
    
    print_status $GREEN "✅ Dry run simulation complete"
    return 0
}

# Function to get user confirmation
get_confirmation() {
    local prompt=$1
    local response
    
    while true; do
        echo -n "$prompt (y/n): "
        read response
        case $response in
            [Yy]* ) return 0;;
            [Nn]* ) return 1;;
            * ) echo "Please answer yes (y) or no (n).";;
        esac
    done
}

# Main execution
main() {
    print_section "DATABASE SYNC VALIDATION"
    
    echo "This script will:"
    echo "1. Test connections to source and destination databases"
    echo "2. Compare database structures"
    echo "3. Perform a dry run simulation"
    echo "4. Optionally execute the actual sync"
    echo ""
    
    # Step 1: Test Source Database
    print_section "STEP 1: VALIDATE SOURCE DATABASE"
    if ! test_database_info "$PROD_HOST" "$PROD_DB" "$PROD_USER" "$PROD_PASSWORD" "PRODUCTION"; then
        print_status $RED "❌ Source database validation failed"
        exit 1
    fi
    
    # Step 2: Test Destination Database  
    print_section "STEP 2: VALIDATE DESTINATION DATABASE"
    if ! test_database_info "$DEV_HOST" "$DEV_DB" "$DEV_USER" "$DEV_PASSWORD" "DEVELOPMENT"; then
        print_status $RED "❌ Destination database validation failed"
        exit 1
    fi
    
    # Step 3: Compare databases
    compare_databases
    
    # Step 4: Dry run
    print_section "STEP 3: DRY RUN"
    if ! perform_dry_run; then
        print_status $RED "❌ Dry run failed"
        exit 1
    fi
    
    # Step 5: Confirmation for actual sync
    print_section "READY FOR SYNC"
    print_status $GREEN "✅ All validations passed!"
    
    echo ""
    print_status $YELLOW "WARNING: The sync will REPLACE all data in $DEV_DB with data from $PROD_DB"
    echo ""
    
    if get_confirmation "Do you want to proceed with the actual database sync?"; then
        print_section "EXECUTING SYNC"
        print_status $YELLOW "Starting database sync..."
        
        # Execute the actual sync
        bash "$(dirname "$0")/sync-db-improved.sh"
        
        if [ $? -eq 0 ]; then
            print_status $GREEN "✅ Database sync completed successfully!"
        else
            print_status $RED "❌ Database sync failed"
            exit 1
        fi
    else
        print_status $YELLOW "Sync cancelled by user"
        echo "You can run this script again when ready to sync."
    fi
}

# Run main function
main "$@"
