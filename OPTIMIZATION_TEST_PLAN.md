# 🧪 OPTIMIZATION SYSTEM - COMPREHENSIVE TEST PLAN

## Pre-Test Setup Checklist

### ✅ Database Requirements:
- [ ] `optimization_runs` table exists
- [ ] `optimization_teams` table exists
- [ ] `optimization_team_members` table exists
- [ ] `tasks` table has: `arrival_status`, `on_hold_reason`, `actual_duration`, `completed_at`
- [ ] `employees` table has: `has_driving_license`, `months_employed`
- [ ] `locations` table has: `base_cleaning_duration_minutes`

### ✅ Sample Data Requirements:
- [ ] At least 6 active employees (mix of drivers and non-drivers)
- [ ] At least 2 employees with `has_driving_license = 1`
- [ ] At least 2 contracted clients with locations
- [ ] No employees marked as day-off for test date

---

## PHASE 1: TASK CREATION & VALIDATION

### Test Case 1.1: Create Basic Task
**Steps:**
1. Login as Admin
2. Navigate to `/admin-tasks`
3. Select a client from dropdown
4. Select service date (e.g., tomorrow)
5. Select 1 cabin/location
6. Click "Create Task"

**Expected Results:**
- ✅ Task created with status = 'Pending'
- ✅ Task has `location_id` set
- ✅ Task has `estimated_duration_minutes` (from location base duration)
- ✅ Task has `scheduled_date` set correctly
- ✅ Task has `arrival_status = false` (default)

**SQL Verification:**
```sql
SELECT id, task_description, status, arrival_status,
       estimated_duration_minutes, scheduled_date
FROM tasks
ORDER BY id DESC LIMIT 1;
```

---

### Test Case 1.2: Create Task with Arrival Status (RULE 3)
**Steps:**
1. Create new task
2. **Check the "Guest Arriving" checkbox** (this sets `arrival_status = true`)
3. Submit

**Expected Results:**
- ✅ Task created with `arrival_status = 1`
- ✅ This task should be prioritized in optimization

**SQL Verification:**
```sql
SELECT id, task_description, arrival_status
FROM tasks
WHERE arrival_status = 1
ORDER BY id DESC LIMIT 1;
```

---

## PHASE 2: OPTIMIZATION TRIGGER

### Test Case 2.1: First Optimization (No Existing Schedule)
**Steps:**
1. Create 3-5 tasks for same service date
2. Click "Optimize & Assign" button

**Expected Behavior:**
- ✅ `OptimizationService::optimizeSchedule()` called
- ✅ Checks for existing saved schedule (should find none)
- ✅ Deletes any unsaved optimization runs for this date
- ✅ Proceeds with full optimization

**Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "optimization"
```

Look for:
```
Starting schedule optimization
service_date: [your date]
Deleted unsaved optimization runs
```

---

## PHASE 3: RULE-BASED PREPROCESSING (RULE 1)

### Test Case 3.1: Employee Allocation by Client
**Purpose:** Verify RULE 1 - Employees allocated fairly among clients

**Expected Behavior:**
- ✅ Tasks grouped by `client_id`
- ✅ Total workload calculated per client (sum of task durations)
- ✅ Employees distributed proportionally to workload
- ✅ Minimum 2 employees per client (for team formation)

**Check Logs:**
Look for:
```
Employee allocation by client
clients_count: X
total_employees: Y
```

**Manual Verification:**
```sql
-- Check tasks grouped by client
SELECT client_id, location_id, COUNT(*) as task_count,
       SUM(estimated_duration_minutes) as total_workload
FROM tasks
WHERE scheduled_date = 'YOUR_TEST_DATE'
GROUP BY client_id, location_id;
```

**Expected:** If Client A has 120 minutes of work and Client B has 60 minutes, Client A should get ~2x more employees.

---

## PHASE 4: TEAM FORMATION (RULE 2)

### Test Case 4.1: Driver Constraint Validation
**Purpose:** Verify RULE 2 - Each team MUST have at least 1 driver

**Steps:**
1. Ensure you have at least 2 employees with `has_driving_license = 1`
2. Run optimization
3. Check created teams

**Expected Results:**
- ✅ Every `optimization_team` has at least 1 member where `employee.has_driving_license = 1`
- ✅ Team size: 2-3 members
- ✅ No team should have 0 drivers

**SQL Verification:**
```sql
-- Check teams and their driver count
SELECT
    ot.id as team_id,
    ot.team_index,
    ot.service_date,
    COUNT(otm.id) as total_members,
    SUM(CASE WHEN e.has_driving_license = 1 THEN 1 ELSE 0 END) as driver_count
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.service_date = 'YOUR_TEST_DATE'
GROUP BY ot.id;
```

**🚨 CRITICAL:** `driver_count` should be >= 1 for ALL teams!

**Check Logs:**
```
Team formed
team_index: 1
size: 2
has_driver: true  <-- MUST BE TRUE
member_ids: [1, 3]
```

---

### Test Case 4.2: Team Size Validation
**Expected:**
- ✅ All teams have 2-3 members
- ✅ No team has < 2 or > 3 members

**SQL:**
```sql
SELECT
    ot.id,
    COUNT(otm.id) as member_count
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
WHERE ot.service_date = 'YOUR_TEST_DATE'
GROUP BY ot.id
HAVING member_count NOT BETWEEN 2 AND 3;
```

**Expected:** Should return 0 rows (no invalid teams)

---

## PHASE 5: TASK PRIORITIZATION (RULE 3)

### Test Case 5.1: Arrival Status Priority
**Purpose:** Verify tasks with `arrival_status = 1` are assigned first

**Setup:**
1. Create 2 tasks for same client:
   - Task A: `arrival_status = 1` (guest arriving)
   - Task B: `arrival_status = 0` (regular)
2. Run optimization

**Expected:**
- ✅ Task A assigned before Task B
- ✅ Task A gets the best available team

**SQL Verification:**
```sql
SELECT
    t.id,
    t.task_description,
    t.arrival_status,
    t.assigned_team_id,
    ot.team_index,
    t.assigned_by_generation
FROM tasks t
LEFT JOIN optimization_teams ot ON t.assigned_team_id = ot.id
WHERE t.scheduled_date = 'YOUR_TEST_DATE'
ORDER BY t.arrival_status DESC, t.id;
```

**Check Logs:**
```
Prioritizing tasks by arrival status
arriving_tasks_count: 1
regular_tasks_count: 4
```

---

## PHASE 6: GENETIC ALGORITHM OPTIMIZATION

### Test Case 6.1: Fitness Calculation (RULE 5-7)
**Purpose:** Verify fitness penalties are applied correctly

**Expected Penalties:**
- ✅ **RULE 5:** Team exceeds 12 hours → Penalty = 100,000
- ✅ **RULE 6:** Task finishes after 3PM → Penalty = 50,000
- ✅ **RULE 7:** Unassigned tasks → Penalty = 10,000 per task

**Check Logs:**
```
Fitness calculated
makespan_hours: 8.5
deadline_violations: 0
unassigned_tasks: 0
fitness_score: 45.5  <-- Lower is better
```

**Manual Test:**
1. Create enough tasks so one team would exceed 12 hours
2. Run optimization
3. Check if penalty applied

**SQL:**
```sql
SELECT
    ot.id,
    SUM(t.estimated_duration_minutes) as total_minutes,
    SUM(t.estimated_duration_minutes) / 60.0 as total_hours
FROM optimization_teams ot
JOIN tasks t ON t.assigned_team_id = ot.id
WHERE ot.service_date = 'YOUR_TEST_DATE'
GROUP BY ot.id
HAVING total_hours > 12;
```

**Expected:** Should return 0 rows (optimizer should avoid 12+ hour schedules)

---

## PHASE 7: DATABASE PERSISTENCE

### Test Case 7.1: OptimizationRun Creation
**Expected:**
```sql
SELECT * FROM optimization_runs
WHERE service_date = 'YOUR_TEST_DATE'
ORDER BY id DESC LIMIT 1;
```

**Verify:**
- ✅ `service_date` matches
- ✅ `status = 'completed'`
- ✅ `total_tasks > 0`
- ✅ `total_teams > 0`
- ✅ `total_employees > 0`
- ✅ `final_fitness_score` is set
- ✅ `is_saved = 0` (initially unsaved)

---

### Test Case 7.2: OptimizationTeam Creation
**Expected:**
```sql
SELECT * FROM optimization_teams
WHERE service_date = 'YOUR_TEST_DATE';
```

**Verify:**
- ✅ Multiple teams created (1 team per driver typically)
- ✅ Each has `optimization_run_id` pointing to the run
- ✅ `team_index` starts from 1
- ✅ `service_date` matches

---

### Test Case 7.3: OptimizationTeamMember Creation
**Expected:**
```sql
SELECT
    ot.team_index,
    e.full_name,
    e.has_driving_license
FROM optimization_team_members otm
JOIN optimization_teams ot ON otm.optimization_team_id = ot.id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.service_date = 'YOUR_TEST_DATE'
ORDER BY ot.team_index, e.full_name;
```

**Verify:**
- ✅ Each team has members listed
- ✅ At least one member per team has `has_driving_license = 1`

---

### Test Case 7.4: Task Assignment
**Expected:**
```sql
SELECT
    t.id,
    t.task_description,
    t.status,
    t.assigned_team_id,
    ot.team_index
FROM tasks t
LEFT JOIN optimization_teams ot ON t.assigned_team_id = ot.id
WHERE t.scheduled_date = 'YOUR_TEST_DATE';
```

**Verify:**
- ✅ All tasks have `status = 'Scheduled'` (changed from 'Pending')
- ✅ All tasks have `assigned_team_id` set
- ✅ `assigned_team_id` points to valid `optimization_teams.id`

---

## PHASE 8: SAVE SCHEDULE (RULE 4 & 9)

### Test Case 8.1: Mark Schedule as Saved
**Steps:**
1. After optimization completes
2. Click "Save Schedule" button

**Expected:**
```sql
SELECT is_saved
FROM optimization_runs
WHERE service_date = 'YOUR_TEST_DATE'
ORDER BY id DESC LIMIT 1;
```

**Result:** `is_saved = 1`

---

## PHASE 9: REAL-TIME ADDITION (RULE 8)

### Test Case 9.1: Add Task to Today's Saved Schedule
**Setup:**
1. Create and optimize tasks for TODAY
2. Save the schedule (`is_saved = 1`)
3. Create a NEW task for TODAY
4. Click "Optimize & Assign"

**Expected Behavior:**
- ✅ Detects `isRealTimeAddition = true` (date is today)
- ✅ Finds existing saved schedule
- ✅ Calls `addTaskToExistingTeams()` instead of full optimization
- ✅ New task added to existing team (no team formation)

**Check Logs:**
```
Real-time addition detected - adding to existing teams
optimization_run_id: X
```

**SQL Verification:**
```sql
-- Count optimization runs for today
SELECT COUNT(*) as run_count
FROM optimization_runs
WHERE service_date = CURDATE();
```

**Expected:** Should still be 1 (no new run created)

---

### Test Case 9.2: Full Re-optimization for Unsaved Schedule
**Setup:**
1. Create tasks for TOMORROW
2. Optimize (don't save)
3. Create another task for TOMORROW
4. Optimize again

**Expected:**
- ✅ Old optimization run DELETED (unsaved)
- ✅ New optimization run created
- ✅ Full optimization performed

**Check Logs:**
```
Deleted unsaved optimization runs
count: 1
```

---

## PHASE 10: EMPLOYEE TASK VIEW

### Test Case 10.1: Employee Can See Tasks
**Steps:**
1. Login as an employee who was assigned to a team
2. Navigate to `/employee-tasks`

**Expected:**
- ✅ Employee sees tasks assigned to their team
- ✅ Tasks show correct status (Scheduled)
- ✅ Team members listed
- ✅ Start/Hold/Complete buttons visible

**SQL Verification:**
```sql
-- Check what tasks employee should see
SELECT
    t.id,
    t.task_description,
    t.status,
    ot.team_index
FROM tasks t
JOIN optimization_teams ot ON t.assigned_team_id = ot.id
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
WHERE otm.employee_id = YOUR_EMPLOYEE_ID
  AND t.scheduled_date >= CURDATE()
ORDER BY t.scheduled_date;
```

---

## CRITICAL BUGS TO WATCH FOR

### 🚨 Bug 1: Teams Without Drivers
**Symptom:** Team created with no driver
**SQL Check:**
```sql
SELECT ot.id, COUNT(otm.id) as members,
       SUM(CASE WHEN e.has_driving_license = 1 THEN 1 ELSE 0 END) as drivers
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
GROUP BY ot.id
HAVING drivers = 0;
```

**Expected:** 0 rows

---

### 🚨 Bug 2: Tasks Not Assigned
**Symptom:** Tasks remain in "Pending" after optimization
**SQL Check:**
```sql
SELECT COUNT(*) FROM tasks
WHERE scheduled_date = 'YOUR_TEST_DATE'
  AND status = 'Pending';
```

**Expected:** 0 (all should be "Scheduled")

---

### 🚨 Bug 3: Arrival Status Not Prioritized
**Symptom:** Regular tasks assigned before arriving guest tasks
**SQL Check:**
```sql
SELECT id, arrival_status, assigned_team_id, assigned_by_generation
FROM tasks
WHERE scheduled_date = 'YOUR_TEST_DATE'
ORDER BY assigned_by_generation;
```

**Expected:** `arrival_status = 1` tasks should have lower `assigned_by_generation` values

---

## TESTING SEQUENCE (Follow This Order)

1. ✅ **Verify Database Schema** (run migrations)
2. ✅ **Create Sample Data** (employees with drivers)
3. ✅ **Test Single Task Creation** (verify task fields)
4. ✅ **Test Optimization Trigger** (check logs)
5. ✅ **Verify Team Formation** (SQL check driver constraint)
6. ✅ **Verify Task Assignment** (all tasks scheduled)
7. ✅ **Verify Arrival Priority** (RULE 3)
8. ✅ **Test Save Schedule** (is_saved flag)
9. ✅ **Test Real-Time Addition** (today vs future)
10. ✅ **Test Employee View** (see assigned tasks)

---

## QUICK VERIFICATION SCRIPT

Run this after optimization to check everything:

```sql
-- 1. Check optimization run was created
SELECT * FROM optimization_runs WHERE service_date = 'YOUR_TEST_DATE';

-- 2. Check teams have drivers
SELECT
    ot.id, ot.team_index,
    COUNT(otm.id) as members,
    SUM(CASE WHEN e.has_driving_license = 1 THEN 1 ELSE 0 END) as drivers
FROM optimization_teams ot
JOIN optimization_team_members otm ON ot.id = otm.optimization_team_id
JOIN employees e ON otm.employee_id = e.id
WHERE ot.service_date = 'YOUR_TEST_DATE'
GROUP BY ot.id;

-- 3. Check all tasks were assigned
SELECT status, COUNT(*)
FROM tasks
WHERE scheduled_date = 'YOUR_TEST_DATE'
GROUP BY status;

-- 4. Check arrival priority
SELECT id, arrival_status, assigned_by_generation
FROM tasks
WHERE scheduled_date = 'YOUR_TEST_DATE'
ORDER BY arrival_status DESC, assigned_by_generation;
```

---

## NEXT STEPS AFTER TESTING

If all tests pass:
- [ ] Fix Hold Task (add Resume button)
- [ ] Test employee task actions (Start/Hold/Complete)
- [ ] Test admin alert notifications
- [ ] Run nightly reconciliation job
- [ ] Database cleanup (remove old tables)

If tests fail:
- [ ] Document which phase failed
- [ ] Check logs for error messages
- [ ] Verify SQL queries manually
- [ ] Debug specific service/controller
