<?php
/**
 * Generates a merge SQL file that adds Hostinger-unique data on top of local data.
 *
 * Strategy:
 * 1. The full local dump (opticrew_full_dump.sql) is imported FIRST on Hostinger.
 * 2. Then THIS generated file is imported to add back Hostinger-unique data with remapped IDs.
 */

$local = new mysqli('127.0.0.1', 'root', '', 'opticrew');
$host  = new mysqli('127.0.0.1', 'root', '', 'opticrew_hostinger');

if ($local->connect_error || $host->connect_error) {
    die("Connection failed: " . ($local->connect_error ?: $host->connect_error));
}

$local->set_charset('utf8mb4');
$host->set_charset('utf8mb4');

$output = [];
$output[] = "-- ============================================";
$output[] = "-- HOSTINGER MERGE SCRIPT (auto-generated)";
$output[] = "-- Import AFTER opticrew_full_dump.sql";
$output[] = "-- ============================================";
$output[] = "SET FOREIGN_KEY_CHECKS = 0;";
$output[] = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';";
$output[] = "";

// ---- Helper functions ----
function getMaxId($db, $table) {
    $r = $db->query("SELECT COALESCE(MAX(id), 0) as m FROM `$table`");
    return (int) $r->fetch_assoc()['m'];
}

function esc($db, $val) {
    if ($val === null) return 'NULL';
    return "'" . $db->real_escape_string($val) . "'";
}

function fetchAll($db, $sql) {
    $r = $db->query($sql);
    if (!$r) { echo "ERROR: $sql -> " . $db->error . "\n"; return []; }
    $rows = [];
    while ($row = $r->fetch_assoc()) $rows[] = $row;
    return $rows;
}

/** Get column names from the LOCAL (target) schema */
function getLocalCols($db, $table) {
    $r = $db->query("DESCRIBE `$table`");
    $cols = [];
    while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
    return $cols;
}

/**
 * Build an INSERT statement using only columns that exist in the local schema.
 * $overrides is an assoc array of col => value (already escaped or int).
 */
function buildInsert($localDb, $hostDb, $table, $row, $localCols, $overrides = []) {
    $vals = [];
    $usedCols = [];
    foreach ($localCols as $col) {
        if (array_key_exists($col, $overrides)) {
            $usedCols[] = $col;
            $vals[] = $overrides[$col];
        } elseif (array_key_exists($col, $row)) {
            $usedCols[] = $col;
            $vals[] = esc($localDb, $row[$col]);
        }
        // skip columns that exist in local but not in hostinger row
    }
    return "INSERT INTO `$table` (`" . implode("`,`", $usedCols) . "`) VALUES (" . implode(",", $vals) . ");";
}

// ---- Build ID mappings ----

// 1. USERS: Hostinger users >= 21 get new IDs starting at local_max + 1
$localMaxUser = getMaxId($local, 'users');
$hostUsers = fetchAll($host, "SELECT * FROM users WHERE id >= 21 ORDER BY id");
$userMap = [];
$nextUserId = $localMaxUser + 1;
foreach ($hostUsers as $u) {
    $userMap[(int)$u['id']] = $nextUserId++;
}
// Users 1-12, 19-20 match — map to themselves
foreach ([1,2,3,4,5,6,7,8,9,10,11,12,19,20] as $id) {
    $userMap[$id] = $id;
}

echo "User mapping (" . count($hostUsers) . " remapped, starting at " . ($localMaxUser + 1) . "):\n";
foreach ($userMap as $old => $new) {
    if ($old != $new) echo "  user $old -> $new\n";
}

// 2. EMPLOYEES
$localMaxEmp = getMaxId($local, 'employees');
$hostEmp12 = fetchAll($host, "SELECT * FROM employees WHERE id = 12");
$empMap = [];
for ($i = 1; $i <= 11; $i++) $empMap[$i] = $i;
if (!empty($hostEmp12)) {
    $empMap[12] = $localMaxEmp + 1;
    echo "Employee mapping: emp 12 -> " . ($localMaxEmp + 1) . "\n";
}

// 3. CLIENTS
$localMaxClient = getMaxId($local, 'clients');
$hostClients = fetchAll($host, "SELECT * FROM clients ORDER BY id");
$clientMap = [];
$nextClientId = $localMaxClient + 1;
foreach ($hostClients as $c) {
    $clientMap[(int)$c['id']] = $nextClientId++;
}
echo "Client mapping: " . count($clientMap) . " clients (starting at " . ($localMaxClient + 1) . ")\n";

// 4. OPTIMIZATION_RUNS
$localMaxOptRun = getMaxId($local, 'optimization_runs');
$hostOptRuns = fetchAll($host, "SELECT * FROM optimization_runs ORDER BY id");
$optRunMap = [];
$nextOptRunId = $localMaxOptRun + 1;
foreach ($hostOptRuns as $r) {
    $optRunMap[(int)$r['id']] = $nextOptRunId++;
}

// 5. OPTIMIZATION_TEAMS
$localMaxOptTeam = getMaxId($local, 'optimization_teams');
$hostOptTeams = fetchAll($host, "SELECT * FROM optimization_teams ORDER BY id");
$optTeamMap = [];
$nextOptTeamId = $localMaxOptTeam + 1;
foreach ($hostOptTeams as $t) {
    $optTeamMap[(int)$t['id']] = $nextOptTeamId++;
}

// 6. TASKS
$localMaxTask = getMaxId($local, 'tasks');
$hostTasks = fetchAll($host, "SELECT * FROM tasks ORDER BY id");
$taskMap = [];
$nextTaskId = $localMaxTask + 1;
foreach ($hostTasks as $t) {
    $taskMap[(int)$t['id']] = $nextTaskId++;
}
echo "Task mapping: " . count($taskMap) . " tasks (starting at " . ($localMaxTask + 1) . ")\n";

// Helper to map a value through a mapping, returning the value itself or NULL
function mapVal($map, $val) {
    if ($val === null) return 'NULL';
    $intVal = (int)$val;
    return isset($map[$intVal]) ? $map[$intVal] : $intVal;
}

// ---- Generate INSERT statements ----

// === USERS ===
// Build sets of emails, google_ids, and usernames already in local to detect unique constraint conflicts
$localEmails = [];
$localGoogleIds = [];
$localUsernames = [];
$r = $local->query("SELECT email, google_id, username FROM users");
while ($row = $r->fetch_assoc()) {
    if ($row['email']) $localEmails[$row['email']] = true;
    if ($row['google_id']) $localGoogleIds[$row['google_id']] = true;
    if ($row['username']) $localUsernames[$row['username']] = true;
}

$userCols = getLocalCols($local, 'users');
$output[] = "-- ========== USERS (Hostinger signups) ==========";
foreach ($hostUsers as $u) {
    $overrides = ['id' => $userMap[(int)$u['id']]];
    // NULL out fields that conflict with unique constraints in local
    if (isset($u['google_id']) && $u['google_id'] !== null && isset($localGoogleIds[$u['google_id']])) {
        $overrides['google_id'] = 'NULL';
    }
    if (isset($u['email']) && isset($localEmails[$u['email']])) {
        // Append _dup to avoid email unique constraint conflict
        $overrides['email'] = esc($local, $u['email'] . '.dup');
    }
    if (isset($u['username']) && $u['username'] !== null && isset($localUsernames[$u['username']])) {
        $overrides['username'] = esc($local, $u['username'] . '_dup');
    }
    $output[] = buildInsert($local, $host, 'users', $u, $userCols, $overrides);
}
$output[] = "";

// === EMPLOYEES ===
$empCols = getLocalCols($local, 'employees');
$output[] = "-- ========== EMPLOYEES ==========";
foreach ($hostEmp12 as $e) {
    $overrides = [
        'id' => $empMap[12],
        'user_id' => mapVal($userMap, $e['user_id']),
    ];
    $output[] = buildInsert($local, $host, 'employees', $e, $empCols, $overrides);
}
$output[] = "";

// === CLIENTS ===
$clientCols = getLocalCols($local, 'clients');
$output[] = "-- ========== CLIENTS (personal clients from live site) ==========";
foreach ($hostClients as $c) {
    $overrides = [
        'id' => $clientMap[(int)$c['id']],
        'user_id' => mapVal($userMap, $c['user_id']),
    ];
    $output[] = buildInsert($local, $host, 'clients', $c, $clientCols, $overrides);
}
$output[] = "";

// === OPTIMIZATION_RUNS ===
$optRunCols = getLocalCols($local, 'optimization_runs');
$output[] = "-- ========== OPTIMIZATION RUNS ==========";
foreach ($hostOptRuns as $r) {
    $overrides = ['id' => $optRunMap[(int)$r['id']]];
    $output[] = buildInsert($local, $host, 'optimization_runs', $r, $optRunCols, $overrides);
}
$output[] = "";

// === OPTIMIZATION_TEAMS ===
$optTeamCols = getLocalCols($local, 'optimization_teams');
$output[] = "-- ========== OPTIMIZATION TEAMS ==========";
foreach ($hostOptTeams as $t) {
    $overrides = [
        'id' => $optTeamMap[(int)$t['id']],
        'optimization_run_id' => mapVal($optRunMap, $t['optimization_run_id']),
    ];
    $output[] = buildInsert($local, $host, 'optimization_teams', $t, $optTeamCols, $overrides);
}
$output[] = "";

// === OPTIMIZATION_TEAM_MEMBERS ===
$otmCols = getLocalCols($local, 'optimization_team_members');
$localMaxOTM = getMaxId($local, 'optimization_team_members');
$hostOTMs = fetchAll($host, "SELECT * FROM optimization_team_members ORDER BY id");
$nextOTMId = $localMaxOTM + 1;
$output[] = "-- ========== OPTIMIZATION TEAM MEMBERS ==========";
foreach ($hostOTMs as $m) {
    $overrides = [
        'id' => $nextOTMId++,
        'optimization_team_id' => mapVal($optTeamMap, $m['optimization_team_id']),
        'employee_id' => mapVal($empMap, $m['employee_id']),
    ];
    $output[] = buildInsert($local, $host, 'optimization_team_members', $m, $otmCols, $overrides);
}
$output[] = "";

// === OPTIMIZATION_GENERATIONS ===
$ogCols = getLocalCols($local, 'optimization_generations');
$localMaxOG = getMaxId($local, 'optimization_generations');
$hostOGs = fetchAll($host, "SELECT * FROM optimization_generations ORDER BY id");
$nextOGId = $localMaxOG + 1;
$output[] = "-- ========== OPTIMIZATION GENERATIONS ==========";
foreach ($hostOGs as $g) {
    $overrides = [
        'id' => $nextOGId++,
        'optimization_run_id' => mapVal($optRunMap, $g['optimization_run_id']),
    ];
    $output[] = buildInsert($local, $host, 'optimization_generations', $g, $ogCols, $overrides);
}
$output[] = "";

// === TASKS ===
$taskCols = getLocalCols($local, 'tasks');
$output[] = "-- ========== TASKS ==========";
foreach ($hostTasks as $t) {
    $overrides = [
        'id' => $taskMap[(int)$t['id']],
        'client_id' => mapVal($clientMap, $t['client_id']),
        'assigned_team_id' => mapVal($optTeamMap, $t['assigned_team_id']),
        'optimization_run_id' => mapVal($optRunMap, $t['optimization_run_id']),
        'completed_by' => mapVal($userMap, $t['completed_by']),
        'started_by' => mapVal($userMap, $t['started_by']),
        'approved_by' => mapVal($userMap, $t['approved_by']),
    ];
    $output[] = buildInsert($local, $host, 'tasks', $t, $taskCols, $overrides);
}
$output[] = "";

// === ATTENDANCES ===
$attCols = getLocalCols($local, 'attendances');
$localMaxAtt = getMaxId($local, 'attendances');
$hostAtts = fetchAll($host, "SELECT * FROM attendances ORDER BY id");
$nextAttId = $localMaxAtt + 1;
$output[] = "-- ========== ATTENDANCES ==========";
foreach ($hostAtts as $a) {
    $overrides = [
        'id' => $nextAttId++,
        'employee_id' => mapVal($empMap, $a['employee_id']),
    ];
    $output[] = buildInsert($local, $host, 'attendances', $a, $attCols, $overrides);
}
$output[] = "";

// === FEEDBACK ===
$fbCols = getLocalCols($local, 'feedback');
$localMaxFb = getMaxId($local, 'feedback');
$hostFbs = fetchAll($host, "SELECT * FROM feedback ORDER BY id");
$nextFbId = $localMaxFb + 1;
$output[] = "-- ========== FEEDBACK ==========";
foreach ($hostFbs as $f) {
    $overrides = [
        'id' => $nextFbId++,
        'task_id' => mapVal($taskMap, $f['task_id']),
        'employee_id' => mapVal($empMap, $f['employee_id'] ?? null),
        'client_id' => mapVal($clientMap, $f['client_id'] ?? null),
    ];
    // Handle appointment_id — remap if we have appointments
    if (isset($f['appointment_id']) && $f['appointment_id'] !== null) {
        $overrides['appointment_id'] = (int)$f['appointment_id']; // appointments keep original IDs (no conflict)
    }
    $output[] = buildInsert($local, $host, 'feedback', $f, $fbCols, $overrides);
}
$output[] = "";

// === CLIENT_APPOINTMENTS ===
$apptCols = getLocalCols($local, 'client_appointments');
$output[] = "-- ========== CLIENT APPOINTMENTS ==========";
$hostAppts = fetchAll($host, "SELECT * FROM client_appointments ORDER BY id");
foreach ($hostAppts as $a) {
    $overrides = [
        'client_id' => mapVal($clientMap, $a['client_id']),
    ];
    $output[] = buildInsert($local, $host, 'client_appointments', $a, $apptCols, $overrides);
}
$output[] = "";

// === DAY_OFFS ===
$doCols = getLocalCols($local, 'day_offs');
$output[] = "-- ========== DAY OFFS ==========";
$hostDayOffs = fetchAll($host, "SELECT * FROM day_offs ORDER BY id");
foreach ($hostDayOffs as $d) {
    $overrides = [
        'employee_id' => mapVal($empMap, $d['employee_id']),
    ];
    $output[] = buildInsert($local, $host, 'day_offs', $d, $doCols, $overrides);
}
$output[] = "";

// === EMPLOYEE_REQUESTS ===
$erCols = getLocalCols($local, 'employee_requests');
$output[] = "-- ========== EMPLOYEE REQUESTS ==========";
$hostReqs = fetchAll($host, "SELECT * FROM employee_requests ORDER BY id");
foreach ($hostReqs as $r) {
    $overrides = [
        'employee_id' => mapVal($empMap, $r['employee_id']),
    ];
    $output[] = buildInsert($local, $host, 'employee_requests', $r, $erCols, $overrides);
}
$output[] = "";

// === NOTIFICATIONS ===
$notifCols = getLocalCols($local, 'notifications');
$localMaxNotif = getMaxId($local, 'notifications');
$hostNotifs = fetchAll($host, "SELECT * FROM notifications ORDER BY id");
$nextNotifId = $localMaxNotif + 1;
$output[] = "-- ========== NOTIFICATIONS ==========";
foreach ($hostNotifs as $n) {
    $overrides = [
        'id' => $nextNotifId++,
        'user_id' => mapVal($userMap, $n['user_id']),
    ];
    $output[] = buildInsert($local, $host, 'notifications', $n, $notifCols, $overrides);
}
$output[] = "";

// === TASK_CHECKLIST_COMPLETIONS ===
$tccCols = getLocalCols($local, 'task_checklist_completions');
$localMaxTCC = getMaxId($local, 'task_checklist_completions');
$hostTCCs = fetchAll($host, "SELECT * FROM task_checklist_completions ORDER BY id");
$nextTCCId = $localMaxTCC + 1;
$output[] = "-- ========== TASK CHECKLIST COMPLETIONS ==========";
foreach ($hostTCCs as $tc) {
    $overrides = [
        'id' => $nextTCCId++,
        'task_id' => mapVal($taskMap, $tc['task_id']),
    ];
    $output[] = buildInsert($local, $host, 'task_checklist_completions', $tc, $tccCols, $overrides);
}
$output[] = "";

// === USER_ACTIVITY_LOGS ===
$ualCols = getLocalCols($local, 'user_activity_logs');
$localMaxUAL = getMaxId($local, 'user_activity_logs');
$hostUALs = fetchAll($host, "SELECT * FROM user_activity_logs ORDER BY id");
$nextUALId = $localMaxUAL + 1;
$output[] = "-- ========== USER ACTIVITY LOGS ==========";
foreach ($hostUALs as $l) {
    $overrides = [
        'id' => $nextUALId++,
        'user_id' => mapVal($userMap, $l['user_id']),
    ];
    $output[] = buildInsert($local, $host, 'user_activity_logs', $l, $ualCols, $overrides);
}
$output[] = "";

$output[] = "SET FOREIGN_KEY_CHECKS = 1;";
$output[] = "-- ========== DONE ==========";

$sql = implode("\n", $output);
file_put_contents(__DIR__ . '/opticrew_hostinger_merge.sql', $sql);

echo "\nGenerated: opticrew_hostinger_merge.sql (" . number_format(strlen($sql)) . " bytes, " . count($output) . " lines)\n";
echo "Done!\n";

$local->close();
$host->close();