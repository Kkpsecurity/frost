# Scripts Inventory & Organization

## 📁 Directory Structure

The scripts folder has been organized into logical categories for better maintainability and discovery.

### 🔍 `/analysis/` - System Analysis Tools
Scripts for analyzing different aspects of the Laravel application:

- `analyze_course_card_issues.php` - Analyzes course card display issues
- `analyze_course_issues.php` - General course-related problem analysis  
- `analyze_instructor_dashboard.php` - Instructor dashboard analysis
- `analyze_instunit.php` - Instructor unit analysis

**When to use:** When investigating issues, understanding system behavior, or generating reports.

### 🗄️ `/database/` - Database Management
Scripts for database operations, synchronization, and data management:

- `*.sql` files - Database schema and data scripts
- `copy_database.*` - Database copying utilities
- `sync-*.sh` - Database synchronization scripts
- `validate-sync.*` - Database validation scripts
- `insert_discount_codes.php` - Discount code data insertion
- `course_dates_*` - Course date related SQL scripts

**When to use:** For database migrations, data imports, backups, or synchronization between environments.

### 🚀 `/deployment/` - Deployment & Environment
Scripts for deploying and managing different environments:

- `deploy-*.ps1` - PowerShell deployment scripts
- `deploy-*.sh` - Bash deployment scripts  
- `deploy-config.conf` - Deployment configuration
- `deploy-launcher.bat` - Windows deployment launcher

**When to use:** When deploying to production, staging, or setting up new environments.

### 🧪 `/testing/` - Testing & Validation
Scripts for testing various components and functionality:

- `test_*.php` - Individual component tests
- `test_api.*` - API testing scripts
- `test_dashboard_*.php` - Dashboard functionality tests
- `test_course_*.php` - Course-related testing
- `test_user_*.php` - User functionality tests
- `phpinfo.php` - PHP configuration testing
- `simple_test*.php` - Basic functionality tests

**When to use:** During development, debugging, or validating functionality.

### 🐛 `/debugging/` - Debugging & Diagnostics  
Scripts for debugging and diagnosing issues:

- `debug_*.php` - Component-specific debugging scripts
- `checkRoutes.*` - Route validation scripts

**When to use:** When troubleshooting issues, investigating bugs, or understanding system behavior.

### 🔧 `/maintenance/` - System Maintenance
Scripts for maintaining and cleaning the system:

- `clear-cache.*` - Cache clearing utilities
- `flush-redis.sh` - Redis cache flushing
- `comprehensive_cleanup.php` - System cleanup
- `cleanup_duplicate_course_dates.php` - Data cleanup
- `*.sh` - Shell maintenance scripts
- `disable_livewire.php` - Feature toggle scripts

**When to use:** For regular maintenance, performance optimization, or system cleanup.

### 🎓 `/course-management/` - Course Administration
Scripts specifically for managing courses and related data:

- `add_course_dates.php` - Course date management
- `create_today_class*.php` - Daily course creation
- `fix_course_*.php` - Course-related fixes

**When to use:** For course setup, scheduling, or course-related maintenance.

### ⚙️ `/config/` - Configuration Management
Scripts for managing application configuration:

- `check_*.php` - Configuration validation scripts
- `fix_*.php` - Configuration fixing scripts
- `convert_settings_keys.php` - Settings migration
- `enable_*.php` - Feature enablement
- `verify_*.php` - Configuration verification
- `update_*.php` - Configuration updates
- `create-ssl-certs.ps1` - SSL certificate generation

**When to use:** When modifying settings, validating configuration, or setting up new features.

## 🎯 Quick Reference by Use Case

### Development Workflow
1. **Starting Development:** `maintenance/clear-cache.*`
2. **Testing Changes:** `testing/test_*.php`
3. **Debugging Issues:** `debugging/debug_*.php`
4. **Analyzing Problems:** `analysis/analyze_*.php`

### Production Deployment
1. **Pre-deployment:** `testing/` scripts for validation
2. **Deployment:** `deployment/deploy-*.ps1`
3. **Post-deployment:** `maintenance/` scripts for cleanup
4. **Verification:** `config/check_*.php`

### Database Operations
1. **Backup:** `database/copy_database.*`
2. **Sync:** `database/sync-*.sh`
3. **Import:** `database/*.sql`
4. **Validate:** `database/validate-sync.*`

### Course Management
1. **Setup:** `course-management/add_course_dates.php`
2. **Daily Operations:** `course-management/create_today_class*.php`
3. **Fixes:** `course-management/fix_course_*.php`

## 📋 Script Naming Conventions

- `test_*` - Testing scripts
- `debug_*` - Debugging scripts  
- `analyze_*` - Analysis scripts
- `check_*` - Validation scripts
- `fix_*` - Repair scripts
- `deploy_*` - Deployment scripts
- `sync_*` - Synchronization scripts
- `create_*` - Creation scripts
- `update_*` - Update scripts

## 🛠️ Usage Guidelines

### Before Running Scripts
1. **Read the script** - Understand what it does
2. **Check dependencies** - Ensure required tools are installed
3. **Test environment** - Run in development first
4. **Backup data** - For scripts that modify data

### Script Execution
- Run from Laravel project root: `php scripts/category/script_name.php`
- For PowerShell scripts: `.\scripts\category\script_name.ps1`
- For Bash scripts: `bash scripts/category/script_name.sh`

### Adding New Scripts
1. **Choose appropriate category** or create new one
2. **Follow naming conventions**
3. **Add documentation** in script comments
4. **Update this inventory** when adding new categories

## 🔄 Maintenance

This inventory should be updated when:
- New scripts are added
- Scripts are moved or renamed  
- New categories are created
- Usage patterns change

---

*Last updated: September 30, 2025*
*Total scripts organized: 150+*
