-- ============================================================================
-- SAFE DELETION SCRIPT FOR TODAY'S TASKS
-- ============================================================================
-- Date: 2025-10-29
-- Purpose: Delete all tasks scheduled for today (2025-10-29) and related data
--
-- Database: opticrew
-- Tables affected:
--   - tasks (6 tasks found for today)
--   - alerts (CASCADE DELETE - auto-deleted)
--   - invalid_tasks (CASCADE DELETE - auto-deleted)
--   - performance_flags (CASCADE DELETE - auto-deleted)
--   - task_performance_histories (CASCADE DELETE - auto-deleted)
--   - optimization_runs (SET NULL on triggered_by_task_id - stays but unlinked)
--
-- IMPORTANT: This script uses TRANSACTIONS for safety
-- ============================================================================

-- Start transaction for safe rollback
START TRANSACTION;

-- ============================================================================
-- STEP 1: SHOW WHAT WILL BE DELETED (FOR CONFIRMATION)
-- ============================================================================
SELECT '=== TASKS TO BE DELETED ===' as Info;
SELECT
    id,
    task_description,
    scheduled_date,
    status,
    assigned_team_id,
    optimization_run_id,
    created_at
FROM tasks
WHERE DATE(scheduled_date) = CURDATE()
AND deleted_at IS NULL;

-- Show counts of related data
SELECT '=== RELATED DATA COUNTS ===' as Info;
SELECT
    'Alerts' as Table_Name,
    COUNT(*) as Records_To_Delete
FROM alerts
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
)
UNION ALL
SELECT
    'Invalid Tasks',
    COUNT(*)
FROM invalid_tasks
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
)
UNION ALL
SELECT
    'Performance Flags',
    COUNT(*)
FROM performance_flags
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
)
UNION ALL
SELECT
    'Task Performance Histories',
    COUNT(*)
FROM task_performance_histories
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

-- ============================================================================
-- STEP 2: DELETE CHILD RECORDS MANUALLY (Even though CASCADE exists)
-- ============================================================================
-- This ensures you see what's being deleted and provides explicit control

-- Delete alerts related to today's tasks
DELETE FROM alerts
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

SELECT ROW_COUNT() as 'Alerts deleted';

-- Delete invalid_tasks related to today's tasks
DELETE FROM invalid_tasks
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

SELECT ROW_COUNT() as 'Invalid tasks deleted';

-- Delete performance_flags related to today's tasks
DELETE FROM performance_flags
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

SELECT ROW_COUNT() as 'Performance flags deleted';

-- Delete task_performance_histories related to today's tasks
DELETE FROM task_performance_histories
WHERE task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

SELECT ROW_COUNT() as 'Task performance histories deleted';

-- Update optimization_runs that reference these tasks (SET NULL)
UPDATE optimization_runs
SET triggered_by_task_id = NULL
WHERE triggered_by_task_id IN (
    SELECT id FROM tasks
    WHERE DATE(scheduled_date) = CURDATE()
    AND deleted_at IS NULL
);

SELECT ROW_COUNT() as 'Optimization runs unlinked';

-- ============================================================================
-- STEP 3: DELETE THE TASKS (HARD DELETE, not soft delete)
-- ============================================================================
DELETE FROM tasks
WHERE DATE(scheduled_date) = CURDATE()
AND deleted_at IS NULL;

SELECT ROW_COUNT() as 'Tasks deleted';

-- ============================================================================
-- STEP 4: VERIFY DELETION
-- ============================================================================
SELECT '=== VERIFICATION: Tasks remaining for today ===' as Info;
SELECT COUNT(*) as remaining_tasks_for_today
FROM tasks
WHERE DATE(scheduled_date) = CURDATE();

-- ============================================================================
-- STEP 5: COMMIT OR ROLLBACK
-- ============================================================================
-- REVIEW THE OUTPUT ABOVE CAREFULLY!
--
-- If everything looks correct:
--   COMMIT;
--
-- If you want to undo:
--   ROLLBACK;
--
-- By default, leaving this commented requires manual decision
-- Uncomment ONE of the lines below:

-- COMMIT;    -- Uncomment this to confirm deletion
-- ROLLBACK;  -- Uncomment this to cancel deletion

SELECT '=== TRANSACTION STATUS ===' as Info;
SELECT 'Transaction is still OPEN. You must manually COMMIT or ROLLBACK.' as Status;

-- ============================================================================
-- ALTERNATIVE: SOFT DELETE VERSION (If you prefer)
-- ============================================================================
-- If you want to soft delete (keep records but mark as deleted):
--
-- UPDATE tasks
-- SET deleted_at = NOW()
-- WHERE DATE(scheduled_date) = CURDATE()
-- AND deleted_at IS NULL;
--
-- This way you can recover the data later if needed.
-- ============================================================================
