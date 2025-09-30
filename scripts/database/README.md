# Database Scripts

Scripts for database operations, synchronization, data management, and maintenance.

## Available Scripts

### Database Synchronization
- `sync-db-improved.sh` - Enhanced database synchronization
- `sync-db-in.sh` - Inbound database synchronization
- `validate-sync.*` - Validation scripts for sync operations

### Database Copying & Backup
- `copy_database.bat` - Windows batch database copy
- `copy_database.php` - PHP-based database copy utility
- `copy_database.ps1` - PowerShell database copy script

### Data Import & Management
- `insert_discount_codes.php` - Import discount code data
- `course_dates_*.sql` - Course date related SQL scripts
- `*.sql` - Various database schema and data scripts

### Validation & Verification
- `validate-and-sync.php` - Combined validation and sync
- Various SQL scripts for data integrity checks

## When to Use

- **Database Migrations:** When deploying schema changes
- **Data Imports:** When loading bulk data or seeding
- **Backup Operations:** Before major changes or deployments
- **Environment Sync:** Keeping dev/staging/prod in sync
- **Data Validation:** Ensuring data integrity and consistency

## Safety Guidelines

⚠️ **Important:** Always backup before running database modification scripts

1. **Test in development first**
2. **Review SQL scripts** before execution
3. **Backup production data** before major operations
4. **Validate results** after operations
5. **Monitor performance** during large operations

## Usage Patterns

### Development Workflow
```bash
# 1. Backup current database
php scripts/database/copy_database.php --backup

# 2. Sync from staging
bash scripts/database/sync-db-improved.sh

# 3. Validate sync
php scripts/database/validate-sync.php
```

### Production Deployment
```bash
# 1. Backup production
php scripts/database/copy_database.php --backup --env=production

# 2. Apply migrations
php artisan migrate

# 3. Validate deployment
php scripts/database/validate-and-sync.php --verify
```

---
*Category: Database | Last Updated: September 30, 2025*
