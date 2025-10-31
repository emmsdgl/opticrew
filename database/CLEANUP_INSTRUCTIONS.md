# Database Cleanup Instructions

## ⚠️ IMPORTANT: Read Before Executing

This cleanup will **permanently delete**:
- ✅ 22 tasks scheduled between Oct 26-30, 2025
- ✅ 4 external client accounts (users 13, 14, 15, 16)
- ✅ 4 client records (clients 1, 2, 3, 4)
- ✅ All related data (appointments, alerts, performance flags, etc.)

## 📋 Pre-Execution Checklist

### 1. **Create Database Backup (REQUIRED)**

```bash
# Open Command Prompt in C:\xampp\mysql\bin
cd C:\xampp\mysql\bin

# Create backup
mysqldump -u root opticrew > C:\xampp\htdocs\opticrew\database\backup_before_cleanup_2025-10-26.sql

# Verify backup was created
dir C:\xampp\htdocs\opticrew\database\backup_before_cleanup_2025-10-26.sql
```

### 2. **Review What Will Be Deleted**

**External Clients to be removed:**
| User ID | Email | Client ID | Name |
|---------|-------|-----------|------|
| 13 | emmausldigol@gmail.com | 1 | Emmaus Digol |
| 14 | mira@gmail.com | 2 | Miradel Leonardo |
| 15 | leira@gmail.com | 3 | Leira San Buenaventura |
| 16 | test@gmail.com | 4 | Miradel Leonardo |

**Tasks to be removed:**
- Task IDs: 4151 to 4176 (22 total tasks)
- Date range: Oct 26-30, 2025

## 🚀 Execution Methods

### Method 1: Via MySQL Command Line (RECOMMENDED)

```bash
# Open Command Prompt
cd C:\xampp\mysql\bin

# Execute cleanup script
mysql -u root opticrew < C:\xampp\htdocs\opticrew\database\cleanup_external_clients_and_tasks.sql
```

### Method 2: Via phpMyAdmin

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select `opticrew` database
3. Click **SQL** tab
4. Copy contents of `cleanup_external_clients_and_tasks.sql`
5. Paste and click **Go**

### Method 3: Manual Step-by-Step (SAFEST)

Execute queries one section at a time to monitor progress:

```sql
-- 1. Start transaction
START TRANSACTION;

-- 2. Delete tasks from Oct 26-30 (copy from script)
-- ... execute section 1 queries

-- 3. Delete external clients (copy from script)
-- ... execute section 2 queries

-- 4. Verify results
SELECT COUNT(*) FROM users WHERE role = 'external_client';
SELECT COUNT(*) FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30';

-- 5. If everything looks good, commit
COMMIT;

-- 6. If something is wrong, rollback
ROLLBACK;
```

## ✅ Post-Execution Verification

Run these queries to verify cleanup was successful:

```sql
-- Should return 0
SELECT COUNT(*) as external_clients FROM users WHERE role = 'external_client';

-- Should return 0
SELECT COUNT(*) as oct_tasks FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30';

-- Should return 0
SELECT COUNT(*) as deleted_clients FROM clients WHERE id IN (1, 2, 3, 4);

-- Should return 0
SELECT COUNT(*) as deleted_users FROM users WHERE id IN (13, 14, 15, 16);
```

## 🔄 Rollback (If Needed)

If something goes wrong and you need to restore:

```bash
# Stop MySQL
# Open Command Prompt
cd C:\xampp\mysql\bin

# Restore from backup
mysql -u root opticrew < C:\xampp\htdocs\opticrew\database\backup_before_cleanup_2025-10-26.sql
```

## 📊 Impact Summary

### Tables Affected:
- ✅ users (4 records deleted)
- ✅ clients (4 records deleted)
- ✅ tasks (22+ records deleted)
- ✅ client_appointments (all appointments for these clients)
- ✅ alerts (related to deleted tasks)
- ✅ performance_flags (related to deleted tasks)
- ✅ task_performance_histories (related to deleted tasks)
- ✅ invalid_tasks (related to deleted tasks)
- ✅ optimization_teams (teams assigned to deleted tasks)
- ✅ optimization_team_members (members of deleted teams)
- ⚠️ optimization_runs (triggered_by_task_id set to NULL)

### What's Preserved:
- ✅ All employee accounts
- ✅ All admin accounts
- ✅ Contracted clients (Kakslauttanen, Aikamatkat)
- ✅ All locations
- ✅ All company settings
- ✅ All holidays
- ✅ Tasks outside Oct 26-30 date range

## 🎯 After Cleanup

Once cleanup is complete, you can:
1. Fix the signup page for external clients
2. Test new client registrations
3. Verify external client workflow

## ⚠️ Final Warning

**This action is IRREVERSIBLE without a backup!**

✅ Make sure you have created a backup
✅ Review all queries before executing
✅ Consider testing on a development database first

---
*Created: 2025-10-26*
*Database: opticrew*
*Environment: Development*
