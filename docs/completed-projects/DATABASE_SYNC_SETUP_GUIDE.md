# FROST Database Sync Setup Guide
**Updated:** September 15, 2025  
**Purpose:** Complete guide for setting up secure database synchronization

---

## 🎯 OVERVIEW

The FROST database sync system safely copies live database data to your local development environment without interrupting the production database. This guide covers the updated, secure configuration.

---

## 📁 FILES CREATED/UPDATED

### ✅ **New/Updated Scripts:**

1. **`sync-db-improved.sh`** - Main sync script with safety features
2. **`xfer-lib.sh`** - Configuration library (Windows compatible)
3. **`post-sync.sh`** - Local environment updates after sync
4. **`flush-redis.sh`** - Cache clearing after sync
5. **`_createDB.sql`** - Database creation template
6. **`sync-database.bat`** - Windows batch wrapper

### ⚠️ **Existing Files:**
- **`sync-db-in.sh`** - Original script (keep as backup)
- **`copy_database.php`** - PHP alternative (already configured)
- **`database_config.env`** - Credential storage

---

## 🔧 CONFIGURATION STEPS

### **Step 1: Update Database Credentials**

Edit `scripts/xfer-lib.sh` with your specific settings:

```bash
# Database Configuration
REMOTE_HOST="develc.hq.cisadmin.com"        # Your live server
REMOTE_USER="frost"                         # SSH username
REMOTE_DB="frost-patch"                     # Source database name
LOCAL_DB="frost-devel"                      # Target database name

# Update PostgreSQL paths for your system
if [[ $IS_WINDOWS -eq 1 ]]; then
    POSTGRES_BIN="/c/laragon/bin/postgresql/postgresql-15.6/bin"
else
    POSTGRES_BIN="/usr/bin"
fi
```

### **Step 2: Configure SSH Access**

1. **Generate SSH Key** (if not exists):
   ```bash
   ssh-keygen -t rsa -b 4096 -f ~/.ssh/frost_rsa
   ```

2. **Copy public key to remote server**:
   ```bash
   ssh-copy-id -i ~/.ssh/frost_rsa.pub frost@develc.hq.cisadmin.com
   ```

3. **Update SSH key path in `xfer-lib.sh`**:
   ```bash
   SSH_USER_KEY="$HOME/.ssh/frost_rsa"
   ```

### **Step 3: Verify PostgreSQL Installation**

Ensure PostgreSQL client tools are installed and accessible:

```bash
# Check if tools exist
C:\laragon\bin\postgresql\postgresql-15.6\bin\psql.exe --version
C:\laragon\bin\postgresql\postgresql-15.6\bin\pg_dump.exe --version
```

If not found, update the `POSTGRES_BIN` path in `xfer-lib.sh`.

### **Step 4: Test Database Connections**

```bash
# Test local connection
psql -h localhost -U postgres -d frost-devel -c "SELECT 1;"

# Test remote connection (via SSH)
ssh -i ~/.ssh/frost_rsa frost@develc.hq.cisadmin.com "psql -d frost-patch -c 'SELECT 1;'"
```

---

## 🛡️ SAFETY FEATURES

### **Environment Protection:**
- ✅ Hostname checking prevents running on production
- ✅ Environment file validation (checks for `APP_ENV=local`)
- ✅ Interactive confirmations before destructive operations

### **Data Protection:**
- ✅ Automatic local database backup before sync
- ✅ Session table preservation (keeps you logged in)
- ✅ Sensitive data clearing (password tokens, API keys)

### **Error Handling:**
- ✅ Connection testing before operations
- ✅ Rollback capability with local backups
- ✅ Clear error messages and logging

---

## 🚀 USAGE EXAMPLES

### **Interactive Mode** (Recommended for first use):
```bash
# Windows
scripts\sync-database.bat

# Linux/Mac/Git Bash
scripts/sync-db-improved.sh
```

### **Dry Run Mode** (See what would happen):
```bash
scripts/sync-db-improved.sh --dry-run
```

### **Automated Mode** (No prompts):
```bash
scripts/sync-db-improved.sh --force --all
```

### **Selective Operations**:
```bash
# Backup remote but don't purge job tables
scripts/sync-db-improved.sh --backup --no-purge

# Quick sync without remote backup
scripts/sync-db-improved.sh --no-backup --purge
```

---

## 📊 WHAT HAPPENS DURING SYNC

### **Pre-Sync Operations:**
1. 🔍 Environment validation
2. 🔗 Connection testing
3. 📊 Database size reporting
4. 💾 Local database backup
5. 💾 Session table backup

### **Main Sync Process:**
1. 📤 Remote database dump (optional)
2. 📥 Download dump file
3. 🏗️ Recreate local database
4. 📥 Import remote data
5. 🧹 Clean job tables (optional)

### **Post-Sync Operations:**
1. 🔄 Restore local sessions
2. ⚙️ Update local settings
3. 🧹 Clear sensitive data
4. 🗑️ Clear caches
5. ✅ Verify completion

---

## 🔍 TROUBLESHOOTING

### **Common Issues:**

#### **"PostgreSQL not found"**
- Update `POSTGRES_BIN` path in `xfer-lib.sh`
- Verify PostgreSQL is installed: `psql --version`

#### **"SSH connection failed"**
- Check SSH key exists: `ls ~/.ssh/frost_rsa`
- Test SSH connection: `ssh -i ~/.ssh/frost_rsa frost@develc.hq.cisadmin.com`
- Verify key is authorized on remote server

#### **"Database connection failed"**
- Check local PostgreSQL is running
- Verify database exists: `psql -l | grep frost-devel`
- Check credentials in `database_config.env`

#### **"Permission denied"**
- Make scripts executable: `chmod +x scripts/*.sh`
- Check file permissions: `ls -la scripts/`

### **Recovery:**

#### **If sync fails mid-process:**
1. Check backup files in `scripts/backups/`
2. Restore from backup:
   ```bash
   psql -h localhost -d frost-devel -f scripts/backups/frost-devel_backup_YYYYMMDD_HHMMSS.sql
   ```

#### **If sessions are lost:**
1. Check session backup: `scripts/_sessions.sql`
2. Restore sessions:
   ```bash
   psql -h localhost -d frost-devel -f scripts/_sessions.sql
   ```

---

## 🔒 SECURITY CONSIDERATIONS

### **Credential Management:**
- ❌ Never commit SSH private keys to version control
- ❌ Never commit database passwords in plain text
- ✅ Use environment variables or encrypted storage
- ✅ Regularly rotate SSH keys and passwords

### **Network Security:**
- ✅ Use SSH key authentication (not passwords)
- ✅ Limit SSH access to specific IPs if possible
- ✅ Use VPN for additional security layer

### **Data Protection:**
- ✅ Sync only to local development environments
- ✅ Clear sensitive production data post-sync
- ✅ Encrypt local backups if storing long-term

---

## 📋 MAINTENANCE

### **Regular Tasks:**
- 🔄 Update SSH keys monthly
- 🧹 Clean old backup files weekly
- 🔍 Review sync logs for errors
- 📊 Monitor database sizes for growth

### **Updates:**
- Keep PostgreSQL client tools updated
- Review and update post-sync operations
- Test sync process after server changes

---

## ✅ VERIFICATION CHECKLIST

Before first use, verify:

- [ ] SSH key configured and tested
- [ ] PostgreSQL client tools installed
- [ ] Database connections working
- [ ] Scripts have execute permissions
- [ ] Backup directory exists and writable
- [ ] Local environment properly configured
- [ ] Test with `--dry-run` mode first

---

**Ready to sync!** Start with `--dry-run` mode to verify everything works correctly.
