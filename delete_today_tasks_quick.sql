-- ============================================================================
-- QUICK DELETE SCRIPT FOR TODAY'S TASKS (2025-10-29)
-- ============================================================================
-- This is the simplified version for quick execution
-- Use this if you're confident and want immediate deletion
-- ============================================================================

-- Delete today's tasks (2025-10-29)
-- Child records will be auto-deleted via CASCADE rules:
--   - alerts (CASCADE)
--   - invalid_tasks (CASCADE)
--   - performance_flags (CASCADE)
--   - task_performance_histories (CASCADE)
--
-- optimization_runs.triggered_by_task_id will be SET NULL (not deleted)

DELETE FROM tasks
WHERE DATE(scheduled_date) = '2025-10-29'
AND deleted_at IS NULL;

-- Show result
SELECT CONCAT('Deleted ', ROW_COUNT(), ' tasks for 2025-10-29') as Result;

-- Verify
SELECT COUNT(*) as remaining_tasks_for_today
FROM tasks
WHERE DATE(scheduled_date) = '2025-10-29';
