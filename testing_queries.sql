-- ================================================
-- OPTIMIZATION SYSTEM TESTING - SQL QUERIES
-- ================================================
-- Quick reference queries for testing and debugging
-- the optimization system workflow
-- ================================================

-- ================================================
-- SECTION 1: LATEST OPTIMIZATION RUN OVERVIEW
-- ================================================

-- Query 1.1: Latest optimization run summary
SELECT
    id AS run_id,
    service_date,
    total_tasks,
    total_employees_needed AS workforce_calculated,
    final_fitness_score,
    generations_run AS convergence_rate,
    TIMESTAMPDIFF(SECOND, created_at, updated_at) AS runtime_seconds,
    CASE
        WHEN is_saved = 1 THEN 'SAVED'
        ELSE 'UNSAVED'
    END AS schedule_status,
    created_at,
    updated_at
FROM optimization_runs
ORDER BY id DESC
LIMIT 1;

-- Query 1.2: Detailed metrics for latest run
SELECT
    or_run.id,
    or_run.service_date,
    or_run.total_tasks,
    or_run.total_employees_needed,
    or_run.final_fitness_score,
    or_run.generations_run,
    or_run.is_saved,
    COUNT(DISTINCT ot.id) AS teams_formed,
    COUNT(DISTINCT otm.employee_id) AS employees_used,
    COUNT(DISTINCT ott.task_id) AS tasks_assigned,
    SUM(t.duration) AS total_task_minutes,
    ROUND(SUM(t.duration) / 60, 2) AS total_task_hours
FROM optimization_runs or_run
LEFT JOIN optimization_teams ot ON or_run.id = ot.optimization_run_id
LEFT JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
LEFT JOIN optimization_team_tasks ott ON ot.id = ott.team_id
LEFT JOIN tasks t ON ott.task_id = t.id
WHERE or_run.id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY or_run.id;

-- ================================================
-- SECTION 2: TASK VERIFICATION
-- ================================================

-- Query 2.1: All tasks for specific date (REPLACE DATE)
SELECT
    t.id,
    CASE
        WHEN t.location_id IS NULL THEN 'EXTRA TASK'
        ELSE 'CABIN TASK'
    END AS task_type,
    COALESCE(c.company_name, cc.name, 'UNKNOWN') AS client,
    t.task_description,
    COALESCE(l.cabin_number, 'N/A') AS cabin,
    t.scheduled_date,
    t.scheduled_time,
    t.duration AS duration_minutes,
    ROUND(t.duration / 60, 2) AS duration_hours,
    t.estimated_duration_minutes,
    t.travel_time,
    CASE
        WHEN t.arrival_status = 1 THEN 'URGENT'
        ELSE 'Normal'
    END AS priority,
    t.status
FROM tasks t
LEFT JOIN locations l ON t.location_id = l.id
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON c.contracted_client_id = cc.id
WHERE t.scheduled_date = '2025-10-22'  -- CHANGE THIS DATE
ORDER BY t.arrival_status DESC, t.client_id, t.id;

-- Query 2.2: Extra tasks only (no location)
SELECT
    id,
    task_description,
    scheduled_date,
    duration AS duration_minutes,
    ROUND(duration / 60, 2) AS duration_hours,
    travel_time,
    latitude,
    longitude,
    status
FROM tasks
WHERE location_id IS NULL
  AND scheduled_date >= CURDATE()
ORDER BY scheduled_date DESC, id DESC;

-- Query 2.3: Tasks grouped by client
SELECT
    COALESCE(c.company_name, cc.name, 'UNKNOWN') AS client,
    t.scheduled_date,
    COUNT(*) AS task_count,
    SUM(t.duration) AS total_duration_minutes,
    ROUND(SUM(t.duration) / 60, 2) AS total_duration_hours,
    SUM(CASE WHEN t.arrival_status = 1 THEN 1 ELSE 0 END) AS urgent_tasks,
    SUM(CASE WHEN t.location_id IS NULL THEN 1 ELSE 0 END) AS extra_tasks
FROM tasks t
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON c.contracted_client_id = cc.id
WHERE t.scheduled_date = '2025-10-22'  -- CHANGE THIS DATE
GROUP BY client, t.scheduled_date
ORDER BY client;

-- ================================================
-- SECTION 3: TEAM FORMATION VERIFICATION
-- ================================================

-- Query 3.1: Teams with member count
SELECT
    ot.id AS team_id,
    ot.team_name,
    ot.optimization_run_id,
    COUNT(DISTINCT otm.id) AS team_size,
    GROUP_CONCAT(DISTINCT CONCAT(e.first_name, ' ', e.last_name) ORDER BY e.first_name SEPARATOR ', ') AS members,
    GROUP_CONCAT(DISTINCT e.role ORDER BY e.role SEPARATOR ', ') AS roles,
    SUM(CASE WHEN e.role = 'Driver' THEN 1 ELSE 0 END) AS has_driver
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name, ot.optimization_run_id
ORDER BY ot.team_name;

-- Query 3.2: RULE 2 Validation - Team size check (should be pairs first, then trios)
SELECT
    team_size,
    COUNT(*) AS teams_with_this_size,
    CASE
        WHEN team_size = 2 THEN 'VALID - Pair (RULE 2)'
        WHEN team_size = 3 THEN 'VALID - Trio (RULE 2 - Odd count fallback)'
        WHEN team_size = 1 THEN 'WARNING - Solo team (should be rare)'
        ELSE 'ERROR - Invalid team size'
    END AS validation_status
FROM (
    SELECT
        ot.id,
        COUNT(otm.id) AS team_size
    FROM optimization_teams ot
    JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
    WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
    GROUP BY ot.id
) AS team_sizes
GROUP BY team_size
ORDER BY team_size;

-- Query 3.3: RULE 2 Validation - Driver requirement
SELECT
    ot.team_name,
    COUNT(DISTINCT otm.employee_id) AS team_size,
    SUM(CASE WHEN e.role = 'Driver' THEN 1 ELSE 0 END) AS driver_count,
    CASE
        WHEN SUM(CASE WHEN e.role = 'Driver' THEN 1 ELSE 0 END) >= 1 THEN 'PASS - Has driver'
        ELSE 'FAIL - No driver (RULE 2 VIOLATION)'
    END AS driver_validation
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name
ORDER BY ot.team_name;

-- ================================================
-- SECTION 4: TASK ASSIGNMENTS TO TEAMS
-- ================================================

-- Query 4.1: Task assignments with client and priority info
SELECT
    ot.team_name,
    t.task_description,
    COALESCE(c.company_name, cc.name) AS client,
    CASE
        WHEN t.arrival_status = 1 THEN 'URGENT'
        ELSE 'Normal'
    END AS priority,
    t.scheduled_time,
    t.duration AS duration_minutes,
    ROUND(t.duration / 60, 2) AS duration_hours,
    t.travel_time
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON c.contracted_client_id = cc.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
ORDER BY ot.team_name, t.arrival_status DESC, t.scheduled_time;

-- Query 4.2: RULE 3 Validation - Arrival priority check
-- (Urgent tasks should be scheduled first for each team)
SELECT
    ot.team_name,
    GROUP_CONCAT(
        CASE WHEN t.arrival_status = 1 THEN 'URGENT' ELSE 'Normal' END
        ORDER BY t.scheduled_time
        SEPARATOR ' → '
    ) AS task_priority_sequence,
    CASE
        WHEN GROUP_CONCAT(
            CASE WHEN t.arrival_status = 1 THEN 'U' ELSE 'N' END
            ORDER BY t.scheduled_time
            SEPARATOR ''
        ) REGEXP '^U*N*$' THEN 'PASS - Urgent tasks first'
        ELSE 'FAIL - Priority violation (RULE 3)'
    END AS priority_validation
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name
ORDER BY ot.team_name;

-- Query 4.3: Team workload summary
SELECT
    ot.team_name,
    COUNT(DISTINCT ott.task_id) AS tasks_assigned,
    SUM(t.duration + t.travel_time) AS total_minutes,
    ROUND(SUM(t.duration + t.travel_time) / 60, 2) AS total_hours,
    CASE
        WHEN SUM(t.duration + t.travel_time) / 60 <= 12 THEN 'PASS - Within 12hr limit (RULE 7)'
        ELSE 'FAIL - Exceeds 12hr limit (RULE 7 VIOLATION)'
    END AS workload_validation
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name
ORDER BY ot.team_name;

-- Query 4.4: RULE 7 Validation - Check for 12-hour violations
SELECT
    team_name,
    total_hours,
    CASE
        WHEN total_hours > 12 THEN CONCAT('VIOLATION: ', ROUND(total_hours - 12, 2), ' hours over limit')
        ELSE 'OK'
    END AS rule_7_status
FROM (
    SELECT
        ot.team_name,
        ROUND(SUM(t.duration + t.travel_time) / 60, 2) AS total_hours
    FROM optimization_teams ot
    JOIN optimization_team_tasks ott ON ot.id = ott.team_id
    JOIN tasks t ON ott.task_id = t.id
    WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
    GROUP BY ot.id, ot.team_name
) AS workloads
WHERE total_hours > 12;

-- ================================================
-- SECTION 5: WORKFORCE CALCULATION VERIFICATION
-- ================================================

-- Query 5.1: Manual workforce calculation - Step by step
-- Run this for a specific service date to verify workforce methodology

-- Step (a) & (b): Calculate total required work hours
SELECT
    scheduled_date,
    COUNT(*) AS task_count,
    SUM(duration) AS total_duration_minutes,
    SUM(travel_time) AS total_travel_minutes,
    SUM(duration + travel_time) AS total_work_minutes,
    ROUND(SUM(duration + travel_time) / 60, 2) AS T_req_hours
FROM tasks
WHERE scheduled_date = '2025-10-22'  -- CHANGE THIS DATE
GROUP BY scheduled_date;

-- Step (c), (d), (e): Compare with optimization run calculation
SELECT
    'Database Calculation' AS source,
    total_employees_needed AS N_final
FROM optimization_runs
WHERE service_date = '2025-10-22'  -- CHANGE THIS DATE
ORDER BY id DESC
LIMIT 1;

-- Query 5.2: Workforce calculation with config values
-- (Requires knowing config values - update these)
SET @H_avail = 8.0;    -- Available hours per day
SET @R = 0.85;         -- Utilization rate (85%)
SET @service_date = '2025-10-22';

SELECT
    scheduled_date,
    COUNT(*) AS task_count,
    ROUND(SUM(duration + travel_time) / 60, 2) AS T_req_hours,
    CEILING(SUM(duration + travel_time) / 60 / (@H_avail * @R)) AS N_base_calculated,
    (SELECT total_employees_needed FROM optimization_runs WHERE service_date = @service_date ORDER BY id DESC LIMIT 1) AS N_final_database,
    CASE
        WHEN CEILING(SUM(duration + travel_time) / 60 / (@H_avail * @R)) =
             (SELECT total_employees_needed FROM optimization_runs WHERE service_date = @service_date ORDER BY id DESC LIMIT 1)
        THEN 'MATCH - Calculation correct'
        ELSE 'MISMATCH - Check calculation'
    END AS validation_status
FROM tasks
WHERE scheduled_date = @service_date
GROUP BY scheduled_date;

-- ================================================
-- SECTION 6: ANALYTICS VERIFICATION
-- ================================================

-- Query 6.1: Data for Analytics dashboard (last 10 runs)
SELECT
    id,
    service_date,
    DATE_FORMAT(service_date, '%b %d, %Y') AS formatted_date,
    is_saved,
    CASE WHEN is_saved = 1 THEN 'Saved' ELSE 'Unsaved' END AS status_badge,
    ROUND(final_fitness_score, 4) AS fitness_rate,
    CASE WHEN final_fitness_score >= 0.999 THEN 'Optimal' ELSE 'Sub-optimal' END AS fitness_status,
    generations_run AS convergence_rate,
    TIMESTAMPDIFF(SECOND, created_at, updated_at) AS runtime_seconds,
    CASE
        WHEN TIMESTAMPDIFF(SECOND, created_at, updated_at) < 60
            THEN CONCAT(ROUND(TIMESTAMPDIFF(SECOND, created_at, updated_at), 2), 's')
        WHEN TIMESTAMPDIFF(SECOND, created_at, updated_at) < 3600
            THEN CONCAT(
                FLOOR(TIMESTAMPDIFF(SECOND, created_at, updated_at) / 60), 'm ',
                MOD(TIMESTAMPDIFF(SECOND, created_at, updated_at), 60), 's'
            )
        ELSE CONCAT(
            FLOOR(TIMESTAMPDIFF(SECOND, created_at, updated_at) / 3600), 'h ',
            FLOOR(MOD(TIMESTAMPDIFF(SECOND, created_at, updated_at), 3600) / 60), 'm'
        )
    END AS runtime_formatted,
    total_tasks,
    total_employees_needed,
    (SELECT COUNT(*) FROM optimization_teams WHERE optimization_run_id = optimization_runs.id) AS teams_formed,
    DATE_FORMAT(created_at, '%b %d, %Y %H:%i:%s') AS created_at_formatted
FROM optimization_runs
ORDER BY created_at DESC
LIMIT 10;

-- Query 6.2: Fitness Rate analysis
SELECT
    CASE
        WHEN final_fitness_score >= 0.999 THEN 'Optimal (1.0)'
        WHEN final_fitness_score >= 0.9 THEN 'Good (0.9-0.999)'
        WHEN final_fitness_score >= 0.8 THEN 'Fair (0.8-0.899)'
        ELSE 'Poor (<0.8)'
    END AS fitness_category,
    COUNT(*) AS run_count,
    ROUND(AVG(generations_run), 2) AS avg_generations,
    ROUND(AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)), 2) AS avg_runtime_seconds
FROM optimization_runs
GROUP BY fitness_category
ORDER BY MIN(final_fitness_score) DESC;

-- Query 6.3: Convergence Rate analysis
SELECT
    CASE
        WHEN generations_run < 20 THEN 'Very Fast (<20 gen)'
        WHEN generations_run < 50 THEN 'Fast (20-49 gen)'
        WHEN generations_run < 80 THEN 'Normal (50-79 gen)'
        ELSE 'Slow (80+ gen)'
    END AS convergence_category,
    COUNT(*) AS run_count,
    ROUND(AVG(final_fitness_score), 4) AS avg_fitness,
    MIN(generations_run) AS min_generations,
    MAX(generations_run) AS max_generations
FROM optimization_runs
GROUP BY convergence_category
ORDER BY MIN(generations_run);

-- ================================================
-- SECTION 7: MULTI-CLIENT TESTING
-- ================================================

-- Query 7.1: Tasks from multiple clients for same date
SELECT
    t.scheduled_date,
    COALESCE(c.company_name, cc.name) AS client,
    COUNT(*) AS task_count,
    SUM(t.duration) AS total_duration_minutes,
    ROUND(SUM(t.duration) / 60, 2) AS total_duration_hours
FROM tasks t
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON c.contracted_client_id = cc.id
WHERE t.scheduled_date = '2025-10-23'  -- CHANGE THIS DATE
GROUP BY t.scheduled_date, client
ORDER BY client;

-- Query 7.2: Verify teams work across multiple clients
SELECT
    ot.team_name,
    GROUP_CONCAT(DISTINCT COALESCE(c.company_name, cc.name) ORDER BY COALESCE(c.company_name, cc.name) SEPARATOR ', ') AS clients_served,
    COUNT(DISTINCT t.client_id) AS client_count,
    COUNT(ott.task_id) AS task_count
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON c.contracted_client_id = cc.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name
ORDER BY ot.team_name;

-- ================================================
-- SECTION 8: EXTRA TASKS TESTING
-- ================================================

-- Query 8.1: Extra tasks duration verification
SELECT
    id,
    task_description,
    scheduled_date,
    duration AS duration_minutes,
    ROUND(duration / 60, 2) AS duration_hours,
    -- Reverse calculate the price estimate
    ROUND(duration / 60 * 25, 2) AS estimated_price_eur,
    CASE
        WHEN duration = 30 THEN 'MIN (30 min cap)'
        WHEN duration = 480 THEN 'MAX (8 hr cap)'
        ELSE 'CALCULATED'
    END AS duration_source,
    status
FROM tasks
WHERE location_id IS NULL
  AND scheduled_date >= CURDATE()
ORDER BY scheduled_date DESC, id DESC;

-- Query 8.2: Extra tasks vs cabin tasks comparison
SELECT
    scheduled_date,
    CASE WHEN location_id IS NULL THEN 'Extra Task' ELSE 'Cabin Task' END AS task_type,
    COUNT(*) AS count,
    ROUND(AVG(duration), 2) AS avg_duration_minutes,
    ROUND(MIN(duration), 2) AS min_duration,
    ROUND(MAX(duration), 2) AS max_duration
FROM tasks
WHERE scheduled_date >= CURDATE()
GROUP BY scheduled_date, task_type
ORDER BY scheduled_date DESC, task_type;

-- ================================================
-- SECTION 9: SAVE SCHEDULE TESTING
-- ================================================

-- Query 9.1: Check saved/unsaved schedules
SELECT
    id,
    service_date,
    CASE
        WHEN is_saved = 1 THEN 'SAVED'
        ELSE 'UNSAVED'
    END AS schedule_status,
    final_fitness_score,
    generations_run,
    created_at,
    updated_at,
    TIMESTAMPDIFF(SECOND, created_at, updated_at) AS time_to_save_seconds
FROM optimization_runs
ORDER BY created_at DESC
LIMIT 10;

-- Query 9.2: Find unsaved schedules
SELECT
    id,
    service_date,
    total_tasks,
    final_fitness_score,
    created_at
FROM optimization_runs
WHERE is_saved = 0
ORDER BY service_date DESC;

-- Query 9.3: Update specific schedule to saved (USE WITH CAUTION)
-- UPDATE optimization_runs
-- SET is_saved = 1, updated_at = NOW()
-- WHERE id = ?;  -- Replace ? with actual ID

-- ================================================
-- SECTION 10: CLEANUP QUERIES (USE WITH CAUTION)
-- ================================================

-- Query 10.1: Delete optimization data for specific date
-- UNCOMMMENT AND USE CAREFULLY - THIS DELETES DATA!
/*
SET FOREIGN_KEY_CHECKS = 0;

-- Delete team-task assignments
DELETE ott FROM optimization_team_tasks ott
JOIN optimization_teams ot ON ott.team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';  -- CHANGE DATE

-- Delete team members
DELETE otm FROM optimization_team_members otm
JOIN optimization_teams ot ON otm.optimization_team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';  -- CHANGE DATE

-- Delete teams
DELETE ot FROM optimization_teams ot
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';  -- CHANGE DATE

-- Delete optimization run
DELETE FROM optimization_runs
WHERE service_date = '2025-10-22';  -- CHANGE DATE

-- Delete tasks
DELETE FROM tasks
WHERE scheduled_date = '2025-10-22';  -- CHANGE DATE

SET FOREIGN_KEY_CHECKS = 1;
*/

-- Query 10.2: Delete UNSAVED optimization runs only
-- UNCOMMMENT AND USE CAREFULLY!
/*
SET FOREIGN_KEY_CHECKS = 0;

DELETE ott FROM optimization_team_tasks ott
JOIN optimization_teams ot ON ott.team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.is_saved = 0;

DELETE otm FROM optimization_team_members otm
JOIN optimization_teams ot ON otm.optimization_team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.is_saved = 0;

DELETE ot FROM optimization_teams ot
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.is_saved = 0;

DELETE FROM optimization_runs WHERE is_saved = 0;

SET FOREIGN_KEY_CHECKS = 1;
*/

-- ================================================
-- SECTION 11: EMPLOYEE AVAILABILITY
-- ================================================

-- Query 11.1: Active employees available for optimization
SELECT
    id,
    CONCAT(first_name, ' ', last_name) AS full_name,
    role,
    CASE WHEN is_active = 1 THEN 'Active' ELSE 'Inactive' END AS status
FROM employees
WHERE is_active = 1
ORDER BY role DESC, first_name;

-- Query 11.2: Employee role distribution
SELECT
    role,
    COUNT(*) AS employee_count,
    CASE
        WHEN role = 'Driver' THEN 'Can lead teams (RULE 2)'
        ELSE 'Team member'
    END AS team_role
FROM employees
WHERE is_active = 1
GROUP BY role
ORDER BY employee_count DESC;

-- ================================================
-- SECTION 12: COMPREHENSIVE VALIDATION REPORT
-- ================================================

-- Query 12: Complete validation report for latest optimization run
SELECT
    'OPTIMIZATION RUN' AS check_category,
    CONCAT('Run ID: ', or_run.id) AS check_item,
    CONCAT(
        'Date: ', or_run.service_date,
        ' | Tasks: ', or_run.total_tasks,
        ' | Workforce: ', or_run.total_employees_needed,
        ' | Fitness: ', ROUND(or_run.final_fitness_score, 4),
        ' | Generations: ', or_run.generations_run,
        ' | Status: ', CASE WHEN or_run.is_saved = 1 THEN 'SAVED' ELSE 'UNSAVED' END
    ) AS result
FROM optimization_runs or_run
WHERE or_run.id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)

UNION ALL

SELECT
    'TEAM FORMATION' AS check_category,
    'Total teams formed' AS check_item,
    CONCAT(COUNT(DISTINCT ot.id), ' teams') AS result
FROM optimization_teams ot
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)

UNION ALL

SELECT
    'TEAM SIZE DISTRIBUTION' AS check_category,
    CONCAT('Teams of ', team_size, ' members') AS check_item,
    CONCAT(COUNT(*), ' teams') AS result
FROM (
    SELECT ot.id, COUNT(otm.id) AS team_size
    FROM optimization_teams ot
    JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
    WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
    GROUP BY ot.id
) AS sizes
GROUP BY team_size

UNION ALL

SELECT
    'RULE 2 - DRIVER CHECK' AS check_category,
    ot.team_name AS check_item,
    CASE
        WHEN SUM(CASE WHEN e.role = 'Driver' THEN 1 ELSE 0 END) >= 1
        THEN 'PASS - Has driver'
        ELSE 'FAIL - No driver'
    END AS result
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name

UNION ALL

SELECT
    'RULE 7 - WORKLOAD CHECK' AS check_category,
    ot.team_name AS check_item,
    CONCAT(
        ROUND(SUM(t.duration + t.travel_time) / 60, 2), ' hours - ',
        CASE
            WHEN SUM(t.duration + t.travel_time) / 60 <= 12 THEN 'PASS'
            ELSE 'FAIL - Exceeds 12hr'
        END
    ) AS result
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name

ORDER BY check_category, check_item;

-- ================================================
-- END OF TESTING QUERIES
-- ================================================
-- Remember to replace placeholder dates (2025-10-22, etc.)
-- with your actual test dates before running queries
-- ================================================
