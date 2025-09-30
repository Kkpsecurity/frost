# Database Transfer# Production Database (source for sync - live production data)
PROD_HOST="develc.hq.cisadmin.com"
PROD_DB="frost"
PROD_USER="frost"
PROD_PASSWORD="kj,L@-N%AyAFWxda"

# Development Database (target for sync - your local development from .env)
DEV_HOST="develc.hq.cisadmin.com"
DEV_DB="frost-devel"
DEV_USER="frost"
DEV_PASSWORD="kj,L@-N%AyAFWxda"

# Sync Direction: frost (PRODUCTION) → frost-devel (YOUR LOCAL DEV)
# This matches your .env file: DB_DATABASE=frost-develLibrary
# Windows-compatible configuration for FROST database sync

# Environment Detection
if [[ "$OS" == "Windows_NT" ]]; then
    IS_WINDOWS=1
    POSTGRES_BIN="/c/laragon/bin/postgresql/postgresql-15.6/bin"
else
    IS_WINDOWS=0
    POSTGRES_BIN="/usr/bin"
fi

# Database Configuration (from .env file)
REMOTE_HOST="develc.hq.cisadmin.com"
REMOTE_USER="frost"
REMOTE_DB="frost-patch"  # Source database (production/staging)
REMOTE_HOME="/home/frost"

# Local database (matches .env DB_* settings)
# Production Database (source for sync)
PROD_HOST="develc.hq.cisadmin.com"
PROD_DB="frost-patch"
PROD_USER="frost"
PROD_PASSWORD="kj,L@-N%AyAFWxda"

# Development Database (target for sync - your local development)
DEV_HOST="develc.hq.cisadmin.com"
DEV_DB="frost-devel"
DEV_USER="frost"
DEV_PASSWORD="kj,L@-N%AyAFWxda"

# Note: Both databases are on the same remote server
# Production DB: frost (live data)
# Development DB: frost-devel (your development copy)

# Security - SSH Configuration (update paths for your environment)
if [[ $IS_WINDOWS -eq 1 ]]; then
    SSH_USER_KEY="$HOME/.ssh/frost_rsa"
    SSH_BIN="ssh"
    SCP_BIN="scp"
else
    SSH_USER_KEY="$HOME/.ssh/frost_rsa"
    SSH_BIN="/usr/bin/ssh"
    SCP_BIN="/usr/bin/scp"
fi

# PostgreSQL Commands
if [[ $IS_WINDOWS -eq 1 ]]; then
    PSQL_BIN="$POSTGRES_BIN/psql.exe"
    PGDUMP_BIN="$POSTGRES_BIN/pg_dump.exe"
    CREATEDB_BIN="$POSTGRES_BIN/createdb.exe"
    DROPDB_BIN="$POSTGRES_BIN/dropdb.exe"
else
    PSQL_BIN="$POSTGRES_BIN/psql"
    PGDUMP_BIN="$POSTGRES_BIN/pg_dump"
    CREATEDB_BIN="$POSTGRES_BIN/createdb"
    DROPDB_BIN="$POSTGRES_BIN/dropdb"
fi

# Environment Safety Check
check_environment() {
    echo "🔍 Environment Safety Check..."
    
    # Prevent running on production
    if [[ "$HOSTNAME" == *"prod"* ]] || [[ "$HOSTNAME" == *"live"* ]]; then
        echo "❌ ERROR: Cannot run sync on production environment!"
        echo "   Current hostname: $HOSTNAME"
        exit 1
    fi
    
    # Check if we're in development
    if [[ ! -f ".env" ]] || [[ ! $(grep "APP_ENV=local" .env) ]]; then
        echo "⚠️  WARNING: This doesn't appear to be a local development environment"
        echo "   Please verify you're running this on your local development machine"
        read -p "Continue anyway? [y/N] " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    echo "✅ Environment check passed"
}

# Database Connection Test
test_database_connection() {
    local host=$1
    local db=$2
    local user=$3
    local password=$4
    
    echo "🔍 Testing database connection to $db on $host..."
    
    # Set password environment variable
    export PGPASSWORD="$password"
    
    if [[ $host == "localhost" ]]; then
        # Local connection (not used in this setup - both are remote)
        if $PSQL_BIN -h $host -d $db -U $user -c "SELECT 1;" >/dev/null 2>&1; then
            echo "✅ Local database connection successful"
            return 0
        else
            echo "❌ Local database connection failed"
            return 1
        fi
    else
        # Direct database connection (both source and target are on same remote host)
        if $PSQL_BIN -h $host -d $db -U $user -c "SELECT 1;" >/dev/null 2>&1; then
            echo "✅ Database connection successful ($db on $host)"
            return 0
        else
            echo "❌ Database connection failed ($db on $host)"
            return 1
        fi
    fi
}

# Validate Prerequisites
validate_prerequisites() {
    echo "🔍 Validating prerequisites..."
    
    # Check if PostgreSQL binaries exist
    if [[ ! -x "$PSQL_BIN" ]]; then
        echo "❌ PostgreSQL psql not found at: $PSQL_BIN"
        echo "   Please update POSTGRES_BIN in the configuration"
        exit 1
    fi
    
    if [[ ! -x "$PGDUMP_BIN" ]]; then
        echo "❌ PostgreSQL pg_dump not found at: $PGDUMP_BIN"
        echo "   Please update POSTGRES_BIN in the configuration"
        exit 1
    fi
    
    # Check SSH key if doing remote sync
    if [[ ! -f "$SSH_USER_KEY" ]]; then
        echo "❌ SSH key not found at: $SSH_USER_KEY"
        echo "   Please ensure SSH key is properly configured"
        exit 1
    fi
    
    echo "✅ Prerequisites validated"
}

# Backup Local Database
backup_local_database() {
    local backup_file="${LOCAL_DB}_backup_$(date +%Y%m%d_%H%M%S).sql"
    local backup_path="$(dirname "$0")/backups/$backup_file"
    
    echo "🔄 Creating local database backup..."
    
    # Create backup directory
    mkdir -p "$(dirname "$backup_path")"
    
    # Create backup
    if $PGDUMP_BIN -h $LOCAL_HOST -d $LOCAL_DB -f "$backup_path"; then
        echo "✅ Local backup created: $backup_file"
        echo "   Path: $backup_path"
    else
        echo "❌ Failed to create local backup"
        exit 1
    fi
}

# Get Database Size (for safety checks)
get_database_size() {
    local host=$1
    local db=$2
    
    if [[ $host == "localhost" ]]; then
        $PSQL_BIN -h $host -d $db -t -c "SELECT pg_size_pretty(pg_database_size('$db'));"
    else
        $SSH_BIN -i $SSH_USER_KEY $REMOTE_USER@$host "psql -d $db -t -c \"SELECT pg_size_pretty(pg_database_size('$db'));\""
    fi
}

# Display Transfer Summary
display_transfer_summary() {
    echo
    echo "📊 TRANSFER SUMMARY"
    echo "==================="
    echo "Source: $REMOTE_DB on $REMOTE_HOST"
    echo "Target: $LOCAL_DB on $LOCAL_HOST"
    echo "Remote DB Size: $(get_database_size $REMOTE_HOST $REMOTE_DB | xargs)"
    echo "Local DB Size (before): $(get_database_size $LOCAL_HOST $LOCAL_DB | xargs)"
    echo
}
