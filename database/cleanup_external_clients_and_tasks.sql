-- =====================================================
-- DATABASE CLEANUP SCRIPT
-- Purpose: Remove external clients and tasks (Oct 26-30)
-- Created: 2025-10-26
-- =====================================================

-- IMPORTANT: Review this script before executing!
-- This will permanently delete data. Consider making a backup first.

START TRANSACTION;

-- =====================================================
-- SECTION 1: Delete Tasks from Oct 26-30, 2025
-- =====================================================

-- Step 1: Delete child records of tasks (Oct 26-30)
DELETE FROM alerts WHERE task_id IN (
    SELECT id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

DELETE FROM performance_flags WHERE task_id IN (
    SELECT id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

DELETE FROM task_performance_histories WHERE task_id IN (
    SELECT id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

DELETE FROM invalid_tasks WHERE task_id IN (
    SELECT id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

-- Step 2: Clear optimization_runs references to these tasks
UPDATE optimization_runs
SET triggered_by_task_id = NULL
WHERE triggered_by_task_id IN (
    SELECT id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

-- Step 3: Delete optimization team members for teams assigned to these tasks
DELETE FROM optimization_team_members WHERE optimization_team_id IN (
    SELECT assigned_team_id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

-- Step 4: Delete optimization teams assigned to these tasks
DELETE FROM optimization_teams WHERE id IN (
    SELECT assigned_team_id FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30'
);

-- Step 5: Delete the tasks themselves (Oct 26-30)
DELETE FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30';

-- =====================================================
-- SECTION 2: Delete External Client Accounts
-- =====================================================

-- External client IDs: 1, 2, 3, 4 (from users 13, 14, 15, 16)

-- Step 1: Delete client appointments
DELETE FROM client_appointments WHERE client_id IN (1, 2, 3, 4);

-- Step 2: Delete any remaining tasks linked to external clients
DELETE FROM alerts WHERE task_id IN (
    SELECT id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

DELETE FROM performance_flags WHERE task_id IN (
    SELECT id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

DELETE FROM task_performance_histories WHERE task_id IN (
    SELECT id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

DELETE FROM invalid_tasks WHERE task_id IN (
    SELECT id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

-- Clear optimization_runs references
UPDATE optimization_runs
SET triggered_by_task_id = NULL
WHERE triggered_by_task_id IN (
    SELECT id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

-- Delete optimization team members for these tasks
DELETE FROM optimization_team_members WHERE optimization_team_id IN (
    SELECT assigned_team_id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

-- Delete optimization teams for these tasks
DELETE FROM optimization_teams WHERE id IN (
    SELECT assigned_team_id FROM tasks WHERE client_id IN (1, 2, 3, 4)
);

-- Delete tasks linked to external clients
DELETE FROM tasks WHERE client_id IN (1, 2, 3, 4);

-- Step 3: Delete client records
DELETE FROM clients WHERE id IN (1, 2, 3, 4);

-- Step 4: Delete user accounts for external clients
DELETE FROM users WHERE id IN (13, 14, 15, 16);

-- =====================================================
-- VERIFICATION QUERIES (Check counts after deletion)
-- =====================================================

-- Check remaining external clients
SELECT COUNT(*) as remaining_external_clients FROM users WHERE role = 'external_client';

-- Check remaining tasks in Oct 26-30 range
SELECT COUNT(*) as remaining_oct_tasks FROM tasks WHERE scheduled_date BETWEEN '2025-10-26' AND '2025-10-30';

-- Check remaining client appointments
SELECT COUNT(*) as remaining_appointments FROM client_appointments WHERE client_id IN (1, 2, 3, 4);

COMMIT;

-- =====================================================
-- CLEANUP COMPLETE
-- =====================================================
-- Summary of what was deleted:
-- 1. All tasks scheduled between Oct 26-30, 2025
-- 2. All related records (alerts, performance flags, etc.)
-- 3. All external client accounts (users 13, 14, 15, 16)
-- 4. All client records (clients 1, 2, 3, 4)
-- 5. All appointments for these clients
-- =====================================================
