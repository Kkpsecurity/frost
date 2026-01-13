# Database Migration Plan - Old Live Data to New UI

**Date:** January 13, 2026  
**Status:** 🔴 CRITICAL - Missing Tables Detected  
**Database:** Production data from live server

---

## Current Status Analysis

### ✅ Existing Tables (40 total)
- Core tables present with production data
- **30,923 users** (12 instructors, 30,904 students)
- **13,784 course enrollments**
- **1,036 scheduled class dates**
- **58,619 validation records**

### ❌ Missing Critical Tables
1. **student_units** - Student daily attendance (was: `student_unit`)
2. **inst_units** - Instructor classroom sessions (was: `inst_unit`)
3. **student_activity** - NEW: Student activity tracking

### ❌ Missing Critical Columns
1. **users.avatar_url** - For displaying real avatars
2. **student_unit.onboarding_completed** - For onboarding flow
3. **student_unit.rules_accepted** - For onboarding flow
4. **student_unit.verified** - For ID verification (JSON field)

### ⚠️ Table Naming Issue
The old database uses **singular names**:
- `student_unit` (old) vs `student_units` (new code expects plural)
- `inst_unit` (old) vs `inst_units` (new code expects plural)

---

## Migration Strategy

### Phase 1: Table Name Standardization
**Priority:** 🔴 CRITICAL - Must be done first

The new UI code expects **plural** table names but the live database uses **singular**.

**Option A: Rename Tables (Recommended)**
```sql
ALTER TABLE student_unit RENAME TO student_units;
ALTER TABLE inst_unit RENAME TO inst_units;
```

**Option B: Update Laravel Models**
Set `protected $table = 'student_unit';` in models.

**Decision:** Option A (Rename) - Follows Laravel conventions

---

### Phase 2: Add Missing Columns to Existing Tables

#### 2.1 Add avatar_url to users table
```sql
ALTER TABLE users 
ADD COLUMN avatar_url VARCHAR(255) NULL;

-- Set default avatar for existing users
UPDATE users 
SET avatar_url = CONCAT('/storage/avatars/default-', 
    CASE 
        WHEN role_id = 5 THEN 'student'
        ELSE 'instructor'
    END, '.png')
WHERE avatar_url IS NULL;
```

#### 2.2 Add onboarding columns to student_unit (after rename)
```sql
ALTER TABLE student_units
ADD COLUMN onboarding_completed BOOLEAN DEFAULT FALSE,
ADD COLUMN rules_accepted BOOLEAN DEFAULT FALSE,
ADD COLUMN verified JSON NULL;

-- Mark old records as completed (backward compatibility)
UPDATE student_units 
SET onboarding_completed = TRUE,
    rules_accepted = TRUE
WHERE created_at < '2026-01-01';
```

---

### Phase 3: Create New Tables

#### 3.1 student_activity table
```bash
php artisan migrate --path=database/migrations/2026_01_12_000000_create_student_activity_table.php
```

This creates tracking for:
- Site entry/exit
- Classroom entry/exit
- Agreement/rules acceptance
- Tab visibility (away time)
- Button clicks

---

### Phase 4: Data Validation & Testing

#### 4.1 Verify Table Structure
```bash
php docs/scripts/database/analyze_structure.php
```

#### 4.2 Test Critical Paths
1. **Instructor Login** → Dashboard → Start Class
2. **Student Login** → Dashboard → Onboarding Flow
3. **Zoom Integration** → Start Screen Sharing
4. **Student Panel** → Display Avatars

#### 4.3 Sample Data Checks
```sql
-- Check student_units has data
SELECT COUNT(*) FROM student_units;

-- Check inst_units has data  
SELECT COUNT(*) FROM inst_units;

-- Check users have avatars
SELECT COUNT(*) FROM users WHERE avatar_url IS NOT NULL;

-- Check onboarding flags
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN onboarding_completed THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN rules_accepted THEN 1 ELSE 0 END) as rules_accepted
FROM student_units;
```

---

## Migration Execution Plan

### Pre-Migration Checklist
- [ ] Backup live database
- [ ] Document current table structure
- [ ] Test migration on local copy first
- [ ] Verify new UI code compatibility

### Migration Steps (Execute in Order)

#### Step 1: Backup Current Database
```bash
# PostgreSQL backup
pg_dump -h localhost -U postgres frost > frost_backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Step 2: Rename Tables to Plural
```sql
BEGIN;

ALTER TABLE student_unit RENAME TO student_units;
ALTER TABLE inst_unit RENAME TO inst_units;

-- Verify
SELECT table_name FROM information_schema.tables 
WHERE table_name IN ('student_units', 'inst_units');

COMMIT;
```

#### Step 3: Add Missing Columns
```sql
BEGIN;

-- Add avatar_url to users
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255);

-- Add onboarding columns to student_units
ALTER TABLE student_units 
ADD COLUMN IF NOT EXISTS onboarding_completed BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS rules_accepted BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS verified JSON;

-- Mark historical records as completed
UPDATE student_units 
SET onboarding_completed = TRUE,
    rules_accepted = TRUE
WHERE created_at < '2026-01-01' AND onboarding_completed = FALSE;

COMMIT;
```

#### Step 4: Run Laravel Migrations for New Tables
```bash
php artisan migrate --path=database/migrations/2026_01_12_000000_create_student_activity_table.php
```

#### Step 5: Verify Structure
```bash
php docs/scripts/database/analyze_structure.php
```

Expected output:
```
✅ users (30923 records) - has avatar_url
✅ course_auths (13784 records) - has agreed_at
✅ student_units - has onboarding_completed, rules_accepted, verified
✅ inst_units - exists
✅ student_activity - exists (new table)
```

#### Step 6: Test Application
1. Instructor login and classroom start
2. Student login and onboarding flow
3. Zoom screen sharing
4. Student avatars display

---

## Rollback Plan

If migration fails:

```sql
BEGIN;

-- Rename tables back
ALTER TABLE student_units RENAME TO student_unit;
ALTER TABLE inst_units RENAME TO inst_unit;

-- Drop new columns
ALTER TABLE users DROP COLUMN IF EXISTS avatar_url;
ALTER TABLE student_unit DROP COLUMN IF EXISTS onboarding_completed;
ALTER TABLE student_unit DROP COLUMN IF EXISTS rules_accepted;
ALTER TABLE student_unit DROP COLUMN IF EXISTS verified;

-- Drop new tables
DROP TABLE IF EXISTS student_activity;

COMMIT;
```

Then restore from backup:
```bash
psql -h localhost -U postgres frost < frost_backup_YYYYMMDD_HHMMSS.sql
```

---

## Post-Migration Tasks

### 1. Update Existing Records
- Set default avatars for all users
- Mark pre-2026 student_units as onboarding complete
- Verify zoom_creds are all disabled

### 2. Test Scenarios
- [ ] Instructor starts new class
- [ ] Student completes onboarding
- [ ] Zoom screen sharing works
- [ ] Student avatars display
- [ ] Activity tracking records events

### 3. Monitor for Issues
- Check Laravel logs for errors
- Monitor database queries for performance
- Test with real instructor/student accounts

---

## Migration Scripts

### Create Migration Script
```bash
php docs/scripts/database/create_migration_script.php
```

This will generate a complete SQL migration file ready to execute.

### Test Migration (Dry Run)
```bash
php docs/scripts/database/test_migration.php --dry-run
```

### Execute Migration
```bash
php docs/scripts/database/execute_migration.php
```

---

## Risk Assessment

### 🔴 High Risk
- Table rename might break existing queries
- Data loss if rollback needed
- Downtime during migration

### 🟡 Medium Risk
- Performance impact from new columns
- NULL values in new columns
- Backward compatibility issues

### 🟢 Low Risk
- New tables (no existing data affected)
- Adding columns (old code ignores them)

---

## Timeline Estimate

- **Backup:** 5 minutes
- **Table Rename:** 2 minutes
- **Add Columns:** 5 minutes
- **Run Migrations:** 2 minutes
- **Testing:** 15 minutes
- **Total:** ~30 minutes

---

## Success Criteria

✅ All critical tables exist with correct names
✅ All new columns present and populated
✅ Instructor can start classroom session
✅ Student can complete onboarding
✅ Zoom integration works
✅ Student avatars display
✅ No errors in Laravel logs

---

## Contact Points

**Database Admin:** [Contact Info]  
**Developer:** [Contact Info]  
**Testing:** [Contact Info]

---

## Next Steps

1. ✅ **DONE:** Analyze current database structure
2. ⏳ **TODO:** Create and test migration SQL script
3. ⏳ **TODO:** Execute migration on development copy
4. ⏳ **TODO:** Test all features on dev
5. ⏳ **TODO:** Execute on production with backup
6. ⏳ **TODO:** Verify and monitor

---

**Status:** READY FOR SCRIPT GENERATION  
**Last Updated:** January 13, 2026
