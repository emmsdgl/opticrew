-- ========================================
-- ATTENDANCE CLEANUP SCRIPT V2
-- Handles unique constraint properly
-- ========================================

-- Step 1: Drop the unique constraint temporarily
ALTER TABLE attendances DROP INDEX unique_employee_clock_in;

-- Step 2: Delete duplicate records, keeping only the one with earliest ID per day
DELETE a1 FROM attendances a1
INNER JOIN (
    SELECT 
        employee_id,
        DATE(clock_in) as work_date,
        MIN(id) as keep_id
    FROM attendances
    WHERE clock_in IS NOT NULL
    GROUP BY employee_id, DATE(clock_in)
    HAVING COUNT(*) > 1
) a2 ON a1.employee_id = a2.employee_id 
    AND DATE(a1.clock_in) = a2.work_date
    AND a1.id != a2.keep_id;

-- Step 3: Update remaining records with consolidated times
UPDATE attendances a
INNER JOIN (
    SELECT 
        MIN(id) as keep_id,
        employee_id,
        DATE(clock_in) as work_date,
        MIN(clock_in) as earliest_clock_in,
        MAX(clock_out) as latest_clock_out,
        SUM(total_minutes_worked) as total_minutes
    FROM (
        -- Get ALL records for days that HAD duplicates (before deletion)
        SELECT a1.* FROM attendances a1
        INNER JOIN (
            SELECT employee_id, DATE(clock_in) as work_date
            FROM attendances
            WHERE clock_in IS NOT NULL
            GROUP BY employee_id, DATE(clock_in)
        ) a2 ON a1.employee_id = a2.employee_id 
            AND DATE(a1.clock_in) = a2.work_date
    ) as all_day_records
    GROUP BY employee_id, work_date
) consolidated ON a.id = consolidated.keep_id
SET 
    a.clock_in = consolidated.earliest_clock_in,
    a.clock_out = consolidated.latest_clock_out,
    a.total_minutes_worked = consolidated.total_minutes;

-- Step 4: Add a better unique constraint (one record per employee per DAY)
-- Note: We can't add a unique constraint on DATE(clock_in) directly in MySQL,
-- so we'll add a unique index on (employee_id, clock_in) which enforces uniqueness at the timestamp level
ALTER TABLE attendances 
ADD UNIQUE KEY unique_employee_date (employee_id, clock_in);

-- Step 5: Show results
SELECT 'Cleanup Complete!' as status;

SELECT 
    COUNT(*) as total_records_after_cleanup
FROM attendances;

SELECT 
    employee_id,
    DATE(clock_in) as work_date,
    COUNT(*) as records_per_day
FROM attendances
GROUP BY employee_id, DATE(clock_in)
HAVING COUNT(*) > 1
LIMIT 10;

SELECT 'If no results above, cleanup was successful!' as verification;
