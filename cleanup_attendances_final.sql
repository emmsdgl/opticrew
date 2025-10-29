-- ========================================
-- ATTENDANCE CLEANUP SCRIPT - FINAL VERSION
-- Consolidates multiple daily attendance records into one per employee per day
-- ========================================

-- Step 1: Show current duplicate situation
SELECT 'BEFORE CLEANUP - Duplicates Found:' as status;
SELECT 
    employee_id,
    DATE(clock_in) as work_date,
    COUNT(*) as duplicate_count,
    SUM(total_minutes_worked) as total_minutes,
    CONCAT(FLOOR(SUM(total_minutes_worked) / 60), 'h ', MOD(SUM(total_minutes_worked), 60), 'm') as total_hours
FROM attendances
WHERE clock_in IS NOT NULL
GROUP BY employee_id, DATE(clock_in)
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC
LIMIT 10;

-- Step 2: Drop the unique constraint temporarily
ALTER TABLE attendances DROP INDEX unique_employee_clock_in;

-- Step 3: Update records we're keeping with consolidated data
UPDATE attendances a
INNER JOIN (
    SELECT 
        MIN(id) as keep_id,
        employee_id,
        DATE(clock_in) as work_date,
        MIN(clock_in) as earliest_clock_in,
        MAX(clock_out) as latest_clock_out,
        SUM(total_minutes_worked) as total_minutes
    FROM attendances
    WHERE clock_in IS NOT NULL
    GROUP BY employee_id, DATE(clock_in)
    HAVING COUNT(*) > 1
) consolidated ON a.id = consolidated.keep_id
SET 
    a.clock_in = consolidated.earliest_clock_in,
    a.clock_out = consolidated.latest_clock_out,
    a.total_minutes_worked = consolidated.total_minutes;

-- Step 4: Delete duplicate records (keep only the updated ones)
DELETE a FROM attendances a
INNER JOIN (
    SELECT 
        MIN(id) as keep_id,
        employee_id,
        DATE(clock_in) as work_date
    FROM attendances
    WHERE clock_in IS NOT NULL
    GROUP BY employee_id, DATE(clock_in)
    HAVING COUNT(*) > 1
) keep_these ON a.employee_id = keep_these.employee_id 
    AND DATE(a.clock_in) = keep_these.work_date
WHERE a.id != keep_these.keep_id;

-- Step 5: Add back the unique constraint
ALTER TABLE attendances 
ADD UNIQUE KEY unique_employee_clock_in (employee_id, clock_in);

-- Step 6: Show results
SELECT 'AFTER CLEANUP - Results:' as status;

SELECT COUNT(*) as total_records_remaining FROM attendances;

SELECT 
    employee_id,
    DATE(clock_in) as work_date,
    COUNT(*) as records_per_day
FROM attendances
WHERE clock_in IS NOT NULL
GROUP BY employee_id, DATE(clock_in)
HAVING COUNT(*) > 1
LIMIT 5;

SELECT 'If no duplicate days shown above, cleanup was successful!' as verification;
