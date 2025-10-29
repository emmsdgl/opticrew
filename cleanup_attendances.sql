-- ========================================
-- ATTENDANCE CLEANUP SCRIPT
-- Consolidates multiple daily records into one per employee per day
-- ========================================

-- Step 1: Create a temporary table with consolidated data
CREATE TEMPORARY TABLE temp_consolidated_attendances AS
SELECT 
    MIN(id) as keep_id,
    employee_id,
    DATE(clock_in) as work_date,
    MIN(clock_in) as earliest_clock_in,
    MAX(clock_out) as latest_clock_out,
    SUM(total_minutes_worked) as total_minutes,
    -- Keep first record's location data (or use MIN/MAX if needed)
    (SELECT clock_in_latitude FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_in LIMIT 1) as clock_in_latitude,
    (SELECT clock_in_longitude FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_in LIMIT 1) as clock_in_longitude,
    (SELECT clock_out_latitude FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_out DESC LIMIT 1) as clock_out_latitude,
    (SELECT clock_out_longitude FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_out DESC LIMIT 1) as clock_out_longitude,
    (SELECT clock_in_distance FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_in LIMIT 1) as clock_in_distance,
    (SELECT clock_out_distance FROM attendances a 
     WHERE a.employee_id = attendances.employee_id 
     AND DATE(a.clock_in) = DATE(attendances.clock_in)
     ORDER BY clock_out DESC LIMIT 1) as clock_out_distance
FROM attendances
WHERE clock_in IS NOT NULL
GROUP BY employee_id, DATE(clock_in)
HAVING COUNT(*) > 1;  -- Only process days with duplicates

-- Step 2: Show what will be consolidated (for review)
SELECT 
    employee_id,
    work_date,
    earliest_clock_in,
    latest_clock_out,
    total_minutes,
    CONCAT(FLOOR(total_minutes / 60), 'h ', MOD(total_minutes, 60), 'm') as hours_worked
FROM temp_consolidated_attendances
ORDER BY employee_id, work_date
LIMIT 20;

-- Step 3: Update the records we're keeping with consolidated data
UPDATE attendances a
INNER JOIN temp_consolidated_attendances t ON a.id = t.keep_id
SET 
    a.clock_in = t.earliest_clock_in,
    a.clock_out = t.latest_clock_out,
    a.total_minutes_worked = t.total_minutes,
    a.clock_in_latitude = t.clock_in_latitude,
    a.clock_in_longitude = t.clock_in_longitude,
    a.clock_out_latitude = t.clock_out_latitude,
    a.clock_out_longitude = t.clock_out_longitude,
    a.clock_in_distance = t.clock_in_distance,
    a.clock_out_distance = t.clock_out_distance;

-- Step 4: Delete duplicate records (keep only the ones we updated)
DELETE a FROM attendances a
INNER JOIN temp_consolidated_attendances t 
    ON a.employee_id = t.employee_id 
    AND DATE(a.clock_in) = t.work_date
WHERE a.id != t.keep_id;

-- Step 5: Show results
SELECT 
    'Cleanup Complete!' as status,
    COUNT(*) as remaining_records
FROM attendances;

-- Drop temporary table
DROP TEMPORARY TABLE IF EXISTS temp_consolidated_attendances;
