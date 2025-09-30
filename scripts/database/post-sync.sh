#!/bin/bash

# Post-Sync Operations for FROST Database
# Updates local-specific records after database sync

echo "🔄 Running post-sync operations..."

# Load configuration
SCRIPTDIR="$( dirname "$( realpath "${BASH_SOURCE[0]}" )" )"
source $SCRIPTDIR/xfer-lib.sh

# Update local-specific configuration
update_local_settings() {
    echo "   📝 Updating local settings..."
    
    # Update app URL to local development
    $PSQL_BIN -q $LOCAL_DB -c "UPDATE settings SET value = 'http://localhost:8000' WHERE key = 'app_url';"
    
    # Set debug mode to true for local development
    $PSQL_BIN -q $LOCAL_DB -c "UPDATE settings SET value = 'true' WHERE key = 'app_debug';"
    
    # Update mail settings for local testing
    $PSQL_BIN -q $LOCAL_DB -c "UPDATE settings SET value = 'log' WHERE key = 'mail_driver';"
    
    echo "   ✅ Local settings updated"
}

# Reset development users passwords (optional)
reset_dev_passwords() {
    echo "   🔑 Resetting development user passwords..."
    
    # Hash for 'password123' - change as needed
    local dev_password='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    
    # Update test users (adjust user IDs as needed)
    $PSQL_BIN -q $LOCAL_DB -c "UPDATE users SET password = '$dev_password' WHERE id IN (1, 2, 3);"
    
    echo "   ✅ Development passwords reset"
}

# Clear sensitive production data
clear_sensitive_data() {
    echo "   🧹 Clearing sensitive production data..."
    
    # Clear password reset tokens
    $PSQL_BIN -q $LOCAL_DB -c "TRUNCATE password_resets;"
    
    # Clear email verification tokens  
    $PSQL_BIN -q $LOCAL_DB -c "TRUNCATE email_verifications;"
    
    # Clear production API keys (if any)
    $PSQL_BIN -q $LOCAL_DB -c "UPDATE settings SET value = '' WHERE key LIKE '%api_key%';"
    
    echo "   ✅ Sensitive data cleared"
}

# Update file paths for local environment
update_file_paths() {
    echo "   📁 Updating file paths for local environment..."
    
    # Update storage paths if needed
    # This is environment-specific, adjust as needed
    
    echo "   ✅ File paths updated"
}

# Run all post-sync operations
main() {
    update_local_settings
    clear_sensitive_data
    
    # Optional operations (uncomment if needed)
    # reset_dev_passwords
    # update_file_paths
    
    echo "✅ Post-sync operations completed"
}

# Execute main function
main
