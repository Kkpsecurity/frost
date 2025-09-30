# Testing Scripts

Scripts for testing functionality, validating components, and ensuring system reliability.

## Available Scripts

### Component Testing
- `test_dashboard_*.php` - Dashboard functionality tests
- `test_course_*.php` - Course-related testing scripts
- `test_user_*.php` - User functionality validation
- `test_api.php` - API endpoint testing
- `test_permissions.php` - Permission system testing

### System Testing
- `test_db_*.php` - Database connectivity and functionality
- `test_redis.php` - Redis cache testing
- `test_settings.php` - Configuration testing
- `phpinfo.php` - PHP environment testing

### Integration Testing
- `test_helper_classes_integration.php` - Helper class integration tests
- `test_complete_*.php` - End-to-end system tests
- `simple_test*.php` - Basic functionality validation

### UI/Frontend Testing
- `test_api.html` - HTML-based API testing interface
- `test_blade_template.php` - Blade template rendering tests
- `test_ui_impact_demo.php` - UI change impact testing

## When to Use

### During Development
- **New Feature Development:** Test components as you build
- **Bug Fixes:** Validate fixes before deployment
- **Refactoring:** Ensure functionality remains intact
- **Integration:** Test component interactions

### Before Deployment
- **Pre-production Validation:** Run full test suite
- **Environment Verification:** Test in staging environment
- **Performance Baseline:** Establish performance metrics
- **Regression Testing:** Ensure no existing functionality broke

### Troubleshooting
- **Issue Investigation:** Isolate problematic components
- **System Validation:** Verify system health
- **Performance Analysis:** Identify bottlenecks
- **Configuration Verification:** Test settings and environment

## Test Categories

### 🟢 Safe Tests (Read-only)
- `test_db_connection.php` - Database connectivity
- `phpinfo.php` - PHP configuration
- `test_permissions.php` - Permission validation
- `test_api.php` - API endpoint validation

### 🟡 Moderate Tests (Limited writes)
- `test_dashboard_*.php` - Dashboard functionality
- `test_course_*.php` - Course operations
- `test_user_*.php` - User operations

### 🔴 Intensive Tests (System impact)
- `test_complete_*.php` - Full system tests
- `test_helper_classes_integration.php` - Integration tests

## Running Tests

### Individual Test
```bash
php scripts/testing/test_dashboard_system.php
```

### Test Suite (Example workflow)
```bash
# 1. Basic connectivity
php scripts/testing/test_db_connection.php

# 2. Core functionality  
php scripts/testing/test_permissions.php
php scripts/testing/test_api.php

# 3. Component tests
php scripts/testing/test_dashboard_system.php
php scripts/testing/test_course_auths.php

# 4. Integration tests
php scripts/testing/test_complete_dashboard_flow.php
```

## Test Output

Tests typically provide:
- ✅ **Pass/Fail Status** - Clear success/failure indication
- 📊 **Performance Metrics** - Execution time and resource usage
- 📋 **Detailed Results** - Specific component validation results
- 🐛 **Error Details** - Specific failure information for debugging
- 💡 **Recommendations** - Suggested fixes or improvements

---
*Category: Testing | Last Updated: September 30, 2025*
