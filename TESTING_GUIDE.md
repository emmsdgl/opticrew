# Optimization System Testing Guide - Option A

## Overview
This guide provides comprehensive test scenarios for the optimization system with focus on:
1. End-to-end workflow testing
2. Multi-client workforce calculation
3. Extra tasks without main form requirement
4. Save Schedule functionality
5. Analytics dashboard verification

---

## Test Scenario 1: Single Client with Multiple Cabins

### Objective
Test basic optimization with various service types and cabin types for one client.

### Test Data
**Client**: Kakslauttanen
**Service Date**: 2025-10-22
**Rate Type**: Hourly Rate (€25/hr)

**Cabins to Add**:
1. Cabin A01 - Deep Cleaning - Standard Cabin
2. Cabin A02 - Standard Cleaning - Deluxe Cabin
3. Cabin A03 - Express Cleaning - Standard Cabin
4. Cabin A04 - Deep Cleaning - Suite
5. Cabin A05 - Standard Cleaning - Standard Cabin

### Expected Results
- ✅ 5 tasks created successfully
- ✅ Workforce calculation executes (Step a-e methodology)
- ✅ Teams formed following RULE 2 (pairs first, then trios if odd count)
- ✅ Optimization runs and creates OptimizationRun record
- ✅ Analytics shows Fitness Rate, Convergence Rate, Runtime
- ✅ "Save Schedule" button appears enabled

### Verification Steps

#### Step 1: Create Tasks
1. Navigate to Admin Tasks page
2. Click on date: October 22, 2025
3. Select client: Kakslauttanen
4. Select Rate Type: Hourly Rate
5. Add each cabin using the form
6. Click "Create Tasks" button

#### Step 2: Check Database - Tasks Created
```sql
-- View created tasks
SELECT
    id,
    location_id,
    client_id,
    task_description,
    scheduled_date,
    duration,
    estimated_duration_minutes,
    arrival_status,
    status
FROM tasks
WHERE scheduled_date = '2025-10-22'
ORDER BY id DESC
LIMIT 5;
```

**Expected Output**:
- 5 rows returned
- `client_id` = 1 (Kakslauttanen)
- `duration` and `estimated_duration_minutes` populated based on service type
- `status` = 'Pending'
- `arrival_status` varies by cabin (some true, some false based on locations table)

#### Step 3: Check Database - Optimization Run Created
```sql
-- View latest optimization run
SELECT
    id,
    service_date,
    total_tasks,
    total_employees_needed,
    final_fitness_score,
    generations_run,
    is_saved,
    created_at,
    updated_at
FROM optimization_runs
ORDER BY id DESC
LIMIT 1;
```

**Expected Output**:
- `service_date` = '2025-10-22'
- `total_tasks` = 5
- `total_employees_needed` = calculated by workforce methodology (likely 2-3 employees)
- `final_fitness_score` = 1.0 (optimal) or close to 1.0
- `generations_run` = number between 1-100
- `is_saved` = 0 (unsaved initially)

#### Step 4: Check Database - Teams Formed
```sql
-- View teams created for this optimization run
SELECT
    ot.id AS team_id,
    ot.team_name,
    ot.optimization_run_id,
    COUNT(otm.id) AS team_size,
    GROUP_CONCAT(e.first_name SEPARATOR ', ') AS team_members
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY ot.id, ot.team_name, ot.optimization_run_id;
```

**Expected Output**:
- For 5 tasks with calculated employees:
  - If 4 employees: 2 teams of 2 members each
  - If 5 employees: 2 teams of 2, 1 team of 1 (trio fallback if needed)
- Each team has `team_name` like 'Team 1', 'Team 2', etc.

#### Step 5: Check Database - Task Assignments
```sql
-- View task assignments to teams
SELECT
    ott.team_id,
    ot.team_name,
    t.task_description,
    t.scheduled_time,
    t.duration,
    t.arrival_status
FROM optimization_team_tasks ott
JOIN optimization_teams ot ON ott.team_id = ot.id
JOIN tasks t ON ott.task_id = t.id
WHERE ot.optimization_run_id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
ORDER BY ot.team_name, t.arrival_status DESC, t.scheduled_time;
```

**Expected Output**:
- All 5 tasks assigned to teams
- Tasks with `arrival_status = true` appear first (RULE 3: priority scheduling)
- Tasks distributed across teams
- No team exceeds 12-hour work limit (RULE 7)

#### Step 6: Verify Analytics Dashboard
1. Navigate to Admin → Analytics
2. Check "Latest Optimization Run" card

**Expected Display**:
- **Service Date**: Oct 22, 2025
- **Status**: Unsaved (yellow badge)
- **Fitness Rate**: 1.0000 or close (with green checkmark if 1.0)
- **Convergence Rate**: Number of generations (e.g., 15, 42, etc.)
- **Runtime**: Time in seconds/minutes (e.g., "2.45s" or "1m 15s")
- **Tasks processed**: 5
- **Teams formed**: 2-3 teams

3. Check "Optimization History" table
- Latest run appears at top of table
- All metrics visible in table row

#### Step 7: Test "Save Schedule" Button
1. Return to Admin Tasks page
2. Verify "Save Schedule" button is enabled (green, not grayed out)
3. Click "Save Schedule" button
4. Confirm success message appears

#### Step 8: Verify Schedule Saved
```sql
-- Check is_saved flag updated
SELECT id, service_date, is_saved, updated_at
FROM optimization_runs
ORDER BY id DESC
LIMIT 1;
```

**Expected Output**:
- `is_saved` = 1
- `updated_at` timestamp reflects recent update

#### Step 9: Re-check Analytics Dashboard
1. Refresh Analytics page
2. Latest run should now show **Status**: Saved (green badge)

---

## Test Scenario 2: Multiple Clients - Workforce Calculation

### Objective
Test workforce calculation when tasks come from different clients.

### Test Data
**Service Date**: 2025-10-23

**Client 1 - Kakslauttanen**:
1. Cabin K01 - Deep Cleaning - Standard Cabin
2. Cabin K02 - Standard Cleaning - Deluxe Cabin

**Client 2 - Aikamatkat**:
1. Cabin A01 - Express Cleaning - Standard Cabin
2. Cabin A02 - Deep Cleaning - Suite

**Client 3 - Walk-in Customer**:
- Name: "Test Hotel Oy"
- Location: "Test Address, Saariselkä"
- Cabins:
  1. Cabin T01 - Standard Cleaning - Standard Cabin

### Expected Results
- ✅ Tasks from all 3 clients created
- ✅ Workforce calculation aggregates all tasks for the service date
- ✅ Teams formed can include employees working across different clients
- ✅ Optimization creates single OptimizationRun for the date
- ✅ Analytics shows combined metrics

### Verification Steps

#### Step 1: Create Kakslauttanen Tasks
1. Navigate to Admin Tasks → October 23, 2025
2. Select Kakslauttanen → Add 2 cabins → Create Tasks

#### Step 2: Create Aikamatkat Tasks
1. Same date (October 23, 2025)
2. Select Aikamatkat → Add 2 cabins → Create Tasks

#### Step 3: Create Walk-in Customer Tasks
1. Same date (October 23, 2025)
2. Select "Walk-in" → Fill customer details → Add 1 cabin → Create Tasks

#### Step 4: Check Workforce Calculation
```sql
-- View all tasks for the service date
SELECT
    t.id,
    COALESCE(c.company_name, cc.name) AS client,
    t.task_description,
    t.duration,
    t.estimated_duration_minutes,
    t.travel_time
FROM tasks t
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON t.client_id = cc.id
WHERE t.scheduled_date = '2025-10-23'
ORDER BY t.client_id, t.id;
```

**Expected Output**:
- 5 tasks total from 3 different clients
- Each task has proper duration and travel_time

#### Step 5: Verify Workforce Methodology Application
```sql
-- Check optimization run workforce calculation
SELECT
    id,
    service_date,
    total_tasks,
    total_employees_needed,
    final_fitness_score
FROM optimization_runs
WHERE service_date = '2025-10-23'
ORDER BY id DESC
LIMIT 1;
```

**Manual Calculation to Verify**:

**Step (a): Di = Ai / Si**
- Calculate cleaning duration per task based on area and speed
- Use default: Si = 10 m²/hour from config

**Step (b): T_req = Σ(Di + Li)**
- Sum all task durations + travel times
- Example: If 5 tasks @ avg 90min each + 30min travel = (90+30) × 5 = 600 minutes = 10 hours

**Step (c): N_base = Ceiling(T_req / (H_avail × R))**
- H_avail = 8 hours (from config)
- R = 0.85 (85% utilization)
- N_base = Ceiling(10 / (8 × 0.85)) = Ceiling(10 / 6.8) = Ceiling(1.47) = **2 employees**

**Step (d): N_cost-max** (if budget limit set)
- Usually null for testing, skip this

**Step (e): N_final = Maximum(N_base, Minimum(N_set, N_cost-max))**
- N_final = max(2, min(available_employees, unlimited)) = **2 employees**

**Compare with database value**: `total_employees_needed` should match calculated N_final

#### Step 6: Verify Team Formation Across Clients
```sql
-- View teams and their assigned tasks with client info
SELECT
    ot.team_name,
    t.task_description,
    COALESCE(c.company_name, cc.name) AS client,
    t.arrival_status,
    t.scheduled_time
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
LEFT JOIN clients c ON t.client_id = c.id
LEFT JOIN contracted_clients cc ON t.client_id = cc.id
WHERE ot.optimization_run_id = (
    SELECT id FROM optimization_runs
    WHERE service_date = '2025-10-23'
    ORDER BY id DESC LIMIT 1
)
ORDER BY ot.team_name, t.arrival_status DESC, t.scheduled_time;
```

**Expected Output**:
- Teams have tasks from multiple clients mixed together
- Arrival priority tasks (arrival_status = true) scheduled first for each team
- No team exceeds 12-hour limit

---

## Test Scenario 3: Extra Tasks Only (No Main Form)

### Objective
Test creating only extra tasks without selecting any cabins.

### Test Data
**Client**: Kakslauttanen
**Service Date**: 2025-10-24
**Rate Type**: Hourly Rate (€25/hr)

**Extra Tasks Only**:
1. Window Cleaning - €150
2. Deep Carpet Cleaning - €200
3. Exterior Maintenance - €100

### Expected Results
- ✅ Form validation accepts submission without cabinsList
- ✅ 3 extra tasks created with `location_id = null`
- ✅ Duration estimated from price (€25 = ~1 hour)
  - Window Cleaning: €150 → ~6 hours → 360 minutes
  - Deep Carpet Cleaning: €200 → ~8 hours → 480 minutes (capped)
  - Exterior Maintenance: €100 → ~4 hours → 240 minutes
- ✅ Tasks included in optimization
- ✅ Teams assigned to extra tasks

### Verification Steps

#### Step 1: Create Extra Tasks Only
1. Navigate to Admin Tasks → October 24, 2025
2. Select Kakslauttanen
3. **Do NOT add any cabins**
4. Add extra tasks:
   - Type: "Window Cleaning", Price: €150
   - Type: "Deep Carpet Cleaning", Price: €200
   - Type: "Exterior Maintenance", Price: €100
5. Click "Create Tasks"

#### Step 2: Verify Form Validation
- Should NOT show error "You must select at least one cabin"
- Should show success message with 3 tasks created

#### Step 3: Check Extra Tasks in Database
```sql
-- View extra tasks created
SELECT
    id,
    location_id,
    client_id,
    task_description,
    scheduled_date,
    duration,
    estimated_duration_minutes,
    travel_time,
    arrival_status,
    latitude,
    longitude
FROM tasks
WHERE scheduled_date = '2025-10-24'
  AND location_id IS NULL
ORDER BY id DESC;
```

**Expected Output**:
- 3 tasks returned
- `location_id` = NULL for all
- `client_id` = 1 (Kakslauttanen)
- `task_description` matches entered types
- `duration` calculated from price:
  - Window Cleaning: 360 minutes (€150 / 25 × 60)
  - Deep Carpet Cleaning: 480 minutes (capped at 8 hours)
  - Exterior Maintenance: 240 minutes (€100 / 25 × 60)
- `estimated_duration_minutes` matches `duration`
- `travel_time` = 30 minutes (default)
- `arrival_status` = false (default for extra tasks)
- `latitude` and `longitude` = Kakslauttanen coordinates (68.33470361, 27.33426652)

#### Step 4: Verify Optimization Includes Extra Tasks
```sql
-- Check optimization run
SELECT
    id,
    service_date,
    total_tasks,
    total_employees_needed,
    final_fitness_score
FROM optimization_runs
WHERE service_date = '2025-10-24'
ORDER BY id DESC
LIMIT 1;
```

**Expected Output**:
- `total_tasks` = 3
- `total_employees_needed` calculated based on extra task durations
- With 360 + 480 + 240 = 1080 minutes = 18 hours total work
- Plus travel: 18 + 1.5 hours = 19.5 hours
- N_base = Ceiling(19.5 / (8 × 0.85)) = Ceiling(2.87) = **3 employees**

#### Step 5: Verify Team Assignments
```sql
-- View team assignments for extra tasks
SELECT
    ot.team_name,
    t.task_description,
    t.duration,
    t.location_id,
    COUNT(*) OVER (PARTITION BY ot.id) as tasks_per_team
FROM optimization_teams ot
JOIN optimization_team_tasks ott ON ot.id = ott.team_id
JOIN tasks t ON ott.task_id = t.id
WHERE ot.optimization_run_id = (
    SELECT id FROM optimization_runs
    WHERE service_date = '2025-10-24'
    ORDER BY id DESC LIMIT 1
)
ORDER BY ot.team_name;
```

**Expected Output**:
- All 3 extra tasks assigned to teams
- Tasks distributed to avoid 12-hour violations

---

## Test Scenario 4: Combined Cabins + Extra Tasks

### Objective
Test creating both cabin tasks and extra tasks in single submission.

### Test Data
**Client**: Aikamatkat
**Service Date**: 2025-10-25
**Rate Type**: Hourly Rate (€25/hr)

**Cabins**:
1. Cabin B01 - Standard Cleaning - Standard Cabin
2. Cabin B02 - Deep Cleaning - Deluxe Cabin

**Extra Tasks**:
1. Laundry Service - €75
2. Kitchen Deep Clean - €125

### Expected Results
- ✅ 4 tasks created total (2 cabin + 2 extra)
- ✅ Cabin tasks have location_id populated
- ✅ Extra tasks have location_id = NULL
- ✅ All tasks included in optimization
- ✅ Workforce calculation includes both types

### Verification Steps

#### Step 1: Create Combined Tasks
1. Admin Tasks → October 25, 2025
2. Select Aikamatkat
3. Add 2 cabins
4. Add 2 extra tasks
5. Create Tasks

#### Step 2: Verify Mixed Task Types
```sql
-- View all tasks for the date
SELECT
    id,
    task_description,
    location_id,
    CASE
        WHEN location_id IS NULL THEN 'Extra Task'
        ELSE 'Cabin Task'
    END AS task_type,
    duration,
    estimated_duration_minutes
FROM tasks
WHERE scheduled_date = '2025-10-25'
ORDER BY location_id NULLS LAST, id;
```

**Expected Output**:
- 4 tasks total
- 2 with location_id (cabin tasks)
- 2 with location_id = NULL (extra tasks)
- Extra tasks have durations:
  - Laundry Service: 180 minutes (€75 / 25 × 60 = 3 hours)
  - Kitchen Deep Clean: 300 minutes (€125 / 25 × 60 = 5 hours)

---

## Test Scenario 5: Save Schedule and Verify State

### Objective
Test the complete save schedule workflow and state management.

### Verification Steps

#### Step 1: Create New Schedule
1. Create tasks for any date (e.g., 2025-10-26)
2. Wait for optimization to complete
3. Verify "Save Schedule" button appears enabled

#### Step 2: Initial State Check
```sql
-- Check optimization run before saving
SELECT id, service_date, is_saved
FROM optimization_runs
WHERE service_date = '2025-10-26'
ORDER BY id DESC
LIMIT 1;
```

**Expected**: `is_saved = 0`

#### Step 3: Click Save Schedule
1. Click "Save Schedule" button
2. Verify success message appears

#### Step 4: Verify Saved State
```sql
-- Check after saving
SELECT id, service_date, is_saved, updated_at
FROM optimization_runs
WHERE service_date = '2025-10-26'
ORDER BY id DESC
LIMIT 1;
```

**Expected**: `is_saved = 1`

#### Step 5: Try Saving Again
1. Refresh page
2. Button should show "No Unsaved Schedule" and be disabled
3. If clicking, should get message: "This schedule is already saved"

---

## Troubleshooting Common Issues

### Issue: "You must select at least one cabin or add at least one extra task"
**Cause**: Both cabinsList and extraTasks are empty
**Fix**: Add at least one cabin OR one extra task

### Issue: No optimization run created
**Cause**: No employees available or database connection issue
**Check**:
```sql
SELECT id, first_name, last_name, role FROM employees WHERE is_active = 1;
```
Ensure at least 2 employees exist.

### Issue: Fitness Rate not reaching 1.0
**Cause**: Conflicts in schedule (overlapping assignments, rule violations)
**Check**:
```sql
-- View task timing conflicts
SELECT
    t1.id AS task1_id,
    t1.task_description AS task1,
    t2.id AS task2_id,
    t2.task_description AS task2,
    t1.scheduled_time,
    t1.duration
FROM tasks t1
JOIN tasks t2 ON t1.scheduled_date = t2.scheduled_date
WHERE t1.id < t2.id
  AND t1.scheduled_time = t2.scheduled_time;
```

### Issue: Extra task duration shows unexpected value
**Cause**: Price-based estimation formula
**Formula**: `duration = max(30, min(480, (price / 25) * 60))`
**Range**: Always between 30 minutes and 8 hours (480 minutes)

---

## SQL Quick Reference for Testing

### View All Recent Optimization Runs
```sql
SELECT
    id,
    service_date,
    total_tasks,
    total_employees_needed,
    final_fitness_score,
    generations_run,
    is_saved,
    created_at
FROM optimization_runs
ORDER BY created_at DESC
LIMIT 10;
```

### View Latest Run Complete Details
```sql
SELECT
    or_run.id,
    or_run.service_date,
    or_run.total_tasks,
    or_run.total_employees_needed,
    or_run.final_fitness_score,
    or_run.generations_run,
    COUNT(DISTINCT ot.id) AS teams_formed,
    COUNT(DISTINCT otm.employee_id) AS employees_used,
    COUNT(DISTINCT ott.task_id) AS tasks_assigned
FROM optimization_runs or_run
LEFT JOIN optimization_teams ot ON or_run.id = ot.optimization_run_id
LEFT JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
LEFT JOIN optimization_team_tasks ott ON ot.id = ott.team_id
WHERE or_run.id = (SELECT id FROM optimization_runs ORDER BY id DESC LIMIT 1)
GROUP BY or_run.id;
```

### Delete Test Data (Use with Caution)
```sql
-- Delete optimization data for specific date
SET FOREIGN_KEY_CHECKS = 0;

DELETE ott FROM optimization_team_tasks ott
JOIN optimization_teams ot ON ott.team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';

DELETE otm FROM optimization_team_members otm
JOIN optimization_teams ot ON otm.optimization_team_id = ot.id
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';

DELETE ot FROM optimization_teams ot
JOIN optimization_runs or_run ON ot.optimization_run_id = or_run.id
WHERE or_run.service_date = '2025-10-22';

DELETE FROM optimization_runs WHERE service_date = '2025-10-22';
DELETE FROM tasks WHERE scheduled_date = '2025-10-22';

SET FOREIGN_KEY_CHECKS = 1;
```

---

## Test Completion Checklist

### Scenario 1: Single Client
- [ ] Tasks created successfully
- [ ] Optimization run created
- [ ] Teams formed with correct size (pairs/trios)
- [ ] Analytics shows all metrics
- [ ] Save Schedule button works
- [ ] is_saved flag updates

### Scenario 2: Multiple Clients
- [ ] Tasks from 3 clients created
- [ ] Workforce calculation aggregates correctly
- [ ] Teams include mixed client tasks
- [ ] Arrival priority respected

### Scenario 3: Extra Tasks Only
- [ ] Form accepts no cabins
- [ ] 3 extra tasks created
- [ ] Durations calculated from price
- [ ] location_id is NULL
- [ ] Optimization includes extra tasks

### Scenario 4: Combined Tasks
- [ ] Both cabin and extra tasks created
- [ ] Mixed in same optimization run
- [ ] Correct location_id values

### Scenario 5: Save Schedule
- [ ] Button state management works
- [ ] Database flag updates
- [ ] Analytics reflects saved state
- [ ] Cannot save twice

---

## Support and Debugging

If any test fails, provide the following information:
1. Which scenario failed (1-5)
2. Which verification step failed
3. Error message (if any)
4. SQL query results showing unexpected data
5. Browser console errors (F12 → Console tab)

This will help identify and fix issues quickly.
