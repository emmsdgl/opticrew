<?php
/**
 * OptiCrew Algorithm Comparison
 *
 * Compares: Rule-Based + Genetic Algorithm (Hybrid) vs Traditional Genetic Algorithm
 * For thesis defense demonstration.
 *
 * Reads DB credentials from Laravel's .env file automatically.
 */
session_start();

require_once __DIR__ . '/algorithms/RuleBasedPreprocessor.php';
require_once __DIR__ . '/algorithms/TraditionalGA.php';
require_once __DIR__ . '/algorithms/EnhancedHybridGA.php'; // This IS the Hybrid Algorithm (Rule-Based + Multi-Objective GA)

// ─── Database Connection (reads from Laravel .env) ───
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        // Search upward for .env (works on both local and Hostinger)
        $envPath = null;
        $dir = __DIR__;
        for ($i = 0; $i < 5; $i++) {
            $dir = dirname($dir);
            if (file_exists($dir . '/.env')) {
                $envPath = $dir . '/.env';
                break;
            }
        }

        $env = [];
        if ($envPath && file_exists($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                if (strpos($line, '=') === false) continue;
                [$key, $value] = explode('=', $line, 2);
                $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }

        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = $env['DB_PORT'] ?? '3306';
        $db   = $env['DB_DATABASE'] ?? 'opticrew';
        $user = $env['DB_USERNAME'] ?? 'root';
        $pass = $env['DB_PASSWORD'] ?? '';

        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

// ─── Data Loading ───
function loadEmployees($limit = null) {
    $db = getDB();
    $baseSql = "SELECT e.id, e.efficiency, e.has_driving_license, e.skills, e.is_active,
                       u.name as employee_name
                FROM employees e
                JOIN users u ON u.id = e.user_id
                WHERE e.is_active = 1 AND e.deleted_at IS NULL";

    if ($limit) {
        // Maintain realistic driver/non-driver ratio when limiting.
        // Without this, ORDER BY has_driving_license DESC + LIMIT picks
        // all drivers first, hiding the Traditional GA's team formation flaws.
        $totalDrivers = (int)$db->query("SELECT COUNT(*) FROM employees WHERE is_active = 1 AND deleted_at IS NULL AND has_driving_license = 1")->fetchColumn();
        $totalNonDrivers = (int)$db->query("SELECT COUNT(*) FROM employees WHERE is_active = 1 AND deleted_at IS NULL AND has_driving_license = 0")->fetchColumn();
        $total = $totalDrivers + $totalNonDrivers;

        if ($total > 0 && $limit < $total) {
            // Proportional selection — ensures non-drivers are represented
            $driverLimit = (int)round($limit * $totalDrivers / $total);
            $nonDriverLimit = $limit - $driverLimit;

            // Guarantee at least 2 non-drivers if available (realistic team composition)
            if ($nonDriverLimit < 2 && $totalNonDrivers >= 2) {
                $nonDriverLimit = 2;
                $driverLimit = $limit - $nonDriverLimit;
            }

            $drivers = $db->query($baseSql . " AND e.has_driving_license = 1 ORDER BY e.efficiency DESC LIMIT " . $driverLimit)->fetchAll();
            $nonDrivers = $db->query($baseSql . " AND e.has_driving_license = 0 ORDER BY e.efficiency DESC LIMIT " . $nonDriverLimit)->fetchAll();
            return array_merge($drivers, $nonDrivers);
        }
    }

    return $db->query($baseSql . " ORDER BY e.has_driving_license DESC, e.efficiency DESC" . ($limit ? " LIMIT " . (int)$limit : ""))->fetchAll();
}

function loadTasks($scheduledDate, $limit = null) {
    $db = getDB();
    // Join with locations to get contracted_client_id for proper grouping
    $sql = "SELECT t.id, t.location_id, t.client_id, t.task_description,
                   t.estimated_duration_minutes, t.duration, t.travel_time,
                   t.scheduled_date, t.arrival_status, t.required_skills,
                   t.status, l.contracted_client_id, l.location_name,
                   l.base_cleaning_duration_minutes
            FROM tasks t
            LEFT JOIN locations l ON l.id = t.location_id
            WHERE t.scheduled_date = ?
              AND t.deleted_at IS NULL
            ORDER BY t.arrival_status DESC, t.id ASC";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    $stmt = $db->prepare($sql);
    $stmt->execute([$scheduledDate]);
    $tasks = $stmt->fetchAll();

    // Ensure duration and client_id are set properly
    foreach ($tasks as &$task) {
        if (empty($task['duration']) && !empty($task['estimated_duration_minutes'])) {
            $task['duration'] = $task['estimated_duration_minutes'];
        }
        if (empty($task['duration']) && !empty($task['base_cleaning_duration_minutes'])) {
            $task['duration'] = $task['base_cleaning_duration_minutes'];
        }
        if (empty($task['duration'])) {
            $task['duration'] = 60; // fallback 60 min
        }
        if (empty($task['travel_time'])) {
            $task['travel_time'] = 0;
        }
        // CRITICAL: ManagerScheduleController stores contracted_client.id as client_id,
        // which collides with the clients table. Always use contracted_client_id from
        // the location join when available — it's the authoritative source.
        if (!empty($task['contracted_client_id'])) {
            $task['client_id'] = 'cc_' . $task['contracted_client_id'];
        } elseif (!empty($task['client_id'])) {
            $task['client_id'] = 'client_' . $task['client_id'];
        } else {
            $task['client_id'] = 'unassigned';
        }
    }
    return $tasks;
}

function loadClients() {
    $db = getDB();
    $clients = [];

    // Load personal/company clients (prefixed to avoid ID collision with contracted_clients)
    $personalClients = $db->query("SELECT id, COALESCE(
        NULLIF(CONCAT_WS(' ', first_name, last_name), ' '),
        company_name,
        CONCAT('Client #', id)
    ) as name FROM clients WHERE deleted_at IS NULL")->fetchAll();
    foreach ($personalClients as $c) {
        $clients[] = ['id' => 'client_' . $c['id'], 'name' => $c['name']];
    }

    // Load contracted clients (these are the main ones in OptiCrew)
    $contractedClients = $db->query("SELECT id, name FROM contracted_clients")->fetchAll();
    foreach ($contractedClients as $cc) {
        $clients[] = ['id' => 'cc_' . $cc['id'], 'name' => $cc['name']];
    }

    return $clients;
}

function loadAvailableDates() {
    $db = getDB();
    return $db->query("
        SELECT DISTINCT scheduled_date, COUNT(*) as task_count
        FROM tasks
        WHERE deleted_at IS NULL
        GROUP BY scheduled_date
        ORDER BY scheduled_date ASC
        LIMIT 200
    ")->fetchAll();
}

/**
 * Inject arrival_status = 1 on a number of tasks for the simulation.
 * If the existing tasks already contain arrivals, those are preserved and we
 * only fill in extras up to the requested target.
 *
 * @param array $tasks
 * @param int $arrivalCount  How many tasks should be marked as arrivals (capped at task count)
 * @return array
 */
function injectArrivals(array $tasks, int $arrivalCount): array {
    if (empty($tasks) || $arrivalCount <= 0) return $tasks;

    $arrivalCount = min($arrivalCount, count($tasks));

    // Count existing arrivals (from the database)
    $existingArrivalIndices = [];
    $nonArrivalIndices = [];
    foreach ($tasks as $i => $t) {
        if (!empty($t['arrival_status']) && (int)$t['arrival_status'] === 1) {
            $existingArrivalIndices[] = $i;
        } else {
            $nonArrivalIndices[] = $i;
        }
    }

    $needed = $arrivalCount - count($existingArrivalIndices);
    if ($needed <= 0) return $tasks; // Already enough arrivals

    // Randomly pick from non-arrivals (deterministic seed for reproducible runs)
    shuffle($nonArrivalIndices);
    $pick = array_slice($nonArrivalIndices, 0, $needed);
    foreach ($pick as $i) {
        $tasks[$i]['arrival_status'] = 1;
    }

    return $tasks;
}

// ─── Run Comparison ───
function runComparison($serviceDate, $employeeLimit, $taskLimit, $runs = 10, $arrivalCount = 0) {
    $employees = loadEmployees($employeeLimit);
    $tasks = loadTasks($serviceDate, $taskLimit);
    $allClients = loadClients();

    // Inject arrival tasks (for sequencing-fitness simulation)
    if ($arrivalCount > 0) {
        $tasks = injectArrivals($tasks, $arrivalCount);
    }

    if (empty($employees) || empty($tasks) || empty($allClients)) {
        return ['error' => 'Insufficient data. Employees: ' . count($employees) . ', Tasks: ' . count($tasks) . ', Clients: ' . count($allClients)];
    }

    // CRITICAL: Only pass clients that actually have tasks.
    // This prevents the Traditional GA from creating phantom teams for
    // irrelevant clients and getting artificially good workload balance.
    $taskClientIds = array_unique(array_column($tasks, 'client_id'));
    $clients = array_values(array_filter($allClients, function($c) use ($taskClientIds) {
        return in_array($c['id'], $taskClientIds);
    }));
    if (empty($clients)) {
        $clients = $allClients; // fallback
    }

    $gaConfig = [
        'population_size' => 50,
        'max_generations' => 100,
        'mutation_rate' => 0.1,
        'crossover_rate' => 0.8,
        'elite_percentage' => 0.1,
        'patience' => 15,
    ];

    $traditionalResults = [];
    $hybridResults = [];
    $traditionalValidation = null;
    $hybridValidation = null;
    $hybridExtendedReport = null;

    // Run Traditional GA (no preprocessing, baseline comparison)
    $traditionalTeams = [];
    for ($i = 0; $i < $runs; $i++) {
        $startTime = microtime(true);

        $traditionalGA = new TraditionalGA($gaConfig);
        $result = $traditionalGA->optimize($tasks, $employees, $clients);

        $elapsed = (microtime(true) - $startTime) * 1000;

        $traditionalResults[] = [
            'fitness' => $result['best_fitness'],
            'generations' => $result['generations'],
            'convergence' => $result['convergence_generation'],
            'time_ms' => $elapsed,
            'solution_time_ms' => $result['solution_time_ms'] ?? $elapsed,
        ];

        if ($i === 0 && !empty($result['best_schedule'])) {
            $traditionalTeams = $result['teams'] ?? [];
            $traditionalValidation = validateScheduleAccuracy(
                $result['best_schedule'], $tasks, $traditionalTeams, $employees
            );
        }
    }

    // Run Hybrid Algorithm (Rule-Based Preprocessing + Multi-Objective GA)
    $hybridTeams = [];
    for ($i = 0; $i < $runs; $i++) {
        $startTime = microtime(true);

        $preStart = microtime(true);
        $preprocessor = new RuleBasedPreprocessor();
        $preprocessed = $preprocessor->preprocess($tasks, $employees, $clients);
        $preElapsedMs = (microtime(true) - $preStart) * 1000;

        if (empty($preprocessed['valid_tasks'])) {
            $hybridResults[] = ['fitness' => 0, 'generations' => 0, 'time_ms' => 0, 'solution_time_ms' => 0];
            continue;
        }

        $hybridGA = new EnhancedHybridGA($gaConfig);
        $result = $hybridGA->optimize(
            $preprocessed['valid_tasks'],
            $preprocessed['employee_allocations'],
            $preprocessed['teams']
        );

        $elapsed = (microtime(true) - $startTime) * 1000;

        $hybridResults[] = [
            'fitness' => $result['best_fitness'],
            'generations' => $result['generations'],
            'convergence' => $result['convergence_generation'],
            'time_ms' => $elapsed,
            'solution_time_ms' => ($result['solution_time_ms'] ?? $elapsed) + $preElapsedMs,
        ];

        if ($i === 0 && !empty($result['best_schedule'])) {
            $hybridValidation = validateScheduleAccuracy(
                $result['best_schedule'], $tasks, $preprocessed['teams'], $employees
            );
            $hybridTeams = $preprocessed['teams'];
            $hybridExtendedReport = $result['extended_report'] ?? null;
        }
    }

    // Aggregate results
    return [
        'traditional' => aggregateResults($traditionalResults),
        'hybrid' => aggregateResults($hybridResults),
        'raw_traditional' => $traditionalResults,
        'raw_hybrid' => $hybridResults,
        'traditional_validation' => $traditionalValidation,
        'hybrid_validation' => $hybridValidation,
        'hybrid_extended_report' => $hybridExtendedReport,
        'meta' => [
            'employees_used' => count($employees),
            'tasks_used' => count($tasks),
            'clients_used' => count($clients),
            'service_date' => $serviceDate,
            'runs' => $runs,
        ],
    ];
}

function aggregateResults($results) {
    $fitnessValues = array_column($results, 'fitness');
    $generationValues = array_column($results, 'generations');
    $convergenceValues = array_filter(array_column($results, 'convergence'), fn($v) => $v !== null);
    $timeValues = array_column($results, 'time_ms');
    $solutionTimeValues = array_column($results, 'solution_time_ms');

    return [
        'avg_fitness' => count($fitnessValues) ? array_sum($fitnessValues) / count($fitnessValues) : 0,
        'best_fitness' => count($fitnessValues) ? max($fitnessValues) : 0,
        'avg_generations' => count($generationValues) ? array_sum($generationValues) / count($generationValues) : 0,
        'avg_convergence' => count($convergenceValues) ? array_sum($convergenceValues) / count($convergenceValues) : 0,
        'avg_time_ms' => count($timeValues) ? array_sum($timeValues) / count($timeValues) : 0,
        'avg_solution_time_ms' => count($solutionTimeValues) ? array_sum($solutionTimeValues) / count($solutionTimeValues) : 0,
        'std_dev_fitness' => stdDev($fitnessValues),
    ];
}

function stdDev($arr) {
    if (count($arr) < 2) return 0;
    $mean = array_sum($arr) / count($arr);
    $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $arr)) / count($arr);
    return sqrt($variance);
}

// ─── TRUE Accuracy Validation ───
// Validates assignments against OptiCrew business rules:
// Rule 1: Each team must have at least 1 driver
// Rule 2: Team size must be 2-3 members
// Rule 3: Tasks must be assigned to teams serving the correct client
// Rule 4: Team workload must not exceed max work hours (12h = 720 min)
function validateScheduleAccuracy($schedule, $tasks, $teams, $employees, $maxWorkHours = 720) {
    $taskMap = [];
    foreach ($tasks as $task) {
        $taskMap[$task['id']] = $task;
    }

    // Use composite key (team_id + client_id) because the preprocessor
    // duplicates each physical team for every client with the SAME team_id.
    // Using team_id alone causes the last client to overwrite earlier ones,
    // resulting in false "Client mismatch" violations.
    $teamMap = [];
    foreach ($teams as $team) {
        $key = $team['team_id'] . '_' . ($team['client_id'] ?? '');
        $teamMap[$key] = $team;
    }
    // Also keep a simple team_id map for driver/size checks when no client_id in assignment
    $teamByIdMap = [];
    foreach ($teams as $team) {
        $teamByIdMap[$team['team_id']] = $team;
    }

    $teamWorkloads = [];
    $results = [];
    $validCount = 0;
    $invalidCount = 0;
    $violations = [];

    foreach ($schedule as $assignment) {
        $taskId = $assignment['task_id'];
        $teamId = $assignment['team_id'];
        $assignmentClientId = $assignment['client_id'] ?? null;
        $task = $taskMap[$taskId] ?? null;

        // Look up team using composite key first, fall back to simple team_id
        $teamKey = $teamId . '_' . ($assignmentClientId ?? ($task['client_id'] ?? ''));
        $team = $teamMap[$teamKey] ?? $teamByIdMap[$teamId] ?? null;

        if (!$task) {
            $invalidCount++;
            $results[] = ['task_id' => $taskId, 'valid' => false, 'reason' => 'Task not found'];
            continue;
        }

        $taskViolations = [];

        // Rule 1: Team must have at least 1 driver
        if ($team) {
            $hasDriver = false;
            foreach ($team['members'] as $member) {
                if (($member['has_driving_license'] ?? 0) == 1) {
                    $hasDriver = true;
                    break;
                }
            }
            if (!$hasDriver) {
                $taskViolations[] = 'No driver in team';
            }

            // Rule 2: Team size must be 2-3
            $teamSize = count($team['members']);
            if ($teamSize < 2 || $teamSize > 3) {
                $taskViolations[] = "Invalid team size ($teamSize)";
            }

            // Rule 3: Client matching
            if (isset($task['client_id']) && isset($team['client_id'])) {
                if ($task['client_id'] != $team['client_id']) {
                    $taskViolations[] = 'Client mismatch';
                }
            }
        } else {
            $taskViolations[] = 'Team not found';
        }

        // Rule 4: Workload check (accumulate)
        $duration = ($task['duration'] ?? 60) + ($task['travel_time'] ?? 0);
        if (!isset($teamWorkloads[$teamId])) $teamWorkloads[$teamId] = 0;
        $teamWorkloads[$teamId] += $duration;

        if (empty($taskViolations)) {
            $validCount++;
            $results[] = ['task_id' => $taskId, 'valid' => true, 'reason' => null];
        } else {
            $invalidCount++;
            $results[] = ['task_id' => $taskId, 'valid' => false, 'reason' => implode(', ', $taskViolations)];
            foreach ($taskViolations as $v) {
                $violations[$v] = ($violations[$v] ?? 0) + 1;
            }
        }
    }

    // Second pass: mark workload violations
    $overworkedTeams = 0;
    foreach ($teamWorkloads as $teamId => $workload) {
        if ($workload > $maxWorkHours) {
            $overworkedTeams++;
        }
    }

    $totalTasks = count($tasks);
    $scheduledCount = count($schedule);
    $unscheduledCount = $totalTasks - $scheduledCount;

    return [
        'total_tasks' => $totalTasks,
        'scheduled' => $scheduledCount,
        'unscheduled' => $unscheduledCount,
        'valid' => $validCount,
        'invalid' => $invalidCount,
        'raw_accuracy' => $totalTasks > 0 ? round(($scheduledCount / $totalTasks) * 100, 2) : 0,
        'true_accuracy' => $totalTasks > 0 ? round(($validCount / $totalTasks) * 100, 2) : 0,
        'violations' => $violations,
        'overworked_teams' => $overworkedTeams,
        'assignment_details' => $results,
    ];
}

// ─── Handle AJAX Request ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run') {
    header('Content-Type: application/json');
    set_time_limit(300);

    $serviceDate = $_POST['service_date'] ?? date('Y-m-d', strtotime('+1 day'));
    $employeeLimit = (int)($_POST['employee_limit'] ?? 10);
    $taskLimit = (int)($_POST['task_limit'] ?? 10);
    $runs = min((int)($_POST['runs'] ?? 10), 20);
    // Number of arrival tasks to inject (per day) — capped at the task limit
    $arrivalCount = max(0, min((int)($_POST['arrivals'] ?? 0), $taskLimit));

    try {
        $result = runComparison($serviceDate, $employeeLimit, $taskLimit, $runs, $arrivalCount);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ─── Load page data ───
$availableDates = [];
$totalEmployees = 0;
$totalTasks = 0;
try {
    $availableDates = loadAvailableDates();
    $totalEmployees = getDB()->query("SELECT COUNT(*) FROM employees WHERE is_active = 1 AND deleted_at IS NULL")->fetchColumn();
} catch (Exception $e) {
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algorithm Comparison - OptiCrew (Local Testing)</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        /* Header */
        .header {
            background: linear-gradient(135deg, #071957, #1e3a8a);
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .header h1 { font-size: 28px; margin-bottom: 6px; }
        .header p { color: #93c5fd; font-size: 14px; }
        .header .badge {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 12px;
            vertical-align: middle;
        }

        /* Setup Cards */
        .setups-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .setup-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            transition: border-color 0.2s;
        }
        .setup-card.active { border-color: #3b82f6; }
        .setup-card h3 { font-size: 16px; color: #475569; margin-bottom: 12px; }
        .setup-card label { display: block; font-size: 13px; color: #64748b; margin-bottom: 4px; font-weight: 500; }
        .setup-card select, .setup-card input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 10px;
            background: #f8fafc;
        }
        .setup-card select:focus, .setup-card input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Buttons */
        .btn-run {
            background: #071957;
            color: white;
            padding: 12px 32px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-run:hover { background: #1e3a8a; }
        .btn-run:disabled { background: #94a3b8; cursor: not-allowed; }
        .btn-row { text-align: center; margin-bottom: 24px; }

        /* Results */
        .results-section { margin-bottom: 32px; }
        .results-section h2 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Comparison Table (matches document format) */
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .comparison-table caption {
            text-align: left;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 14px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-style: italic;
        }
        .comparison-table th {
            background: #f1f5f9;
            padding: 10px 16px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .comparison-table td {
            padding: 12px 16px;
            text-align: center;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .comparison-table tr:last-child td { border-bottom: none; }
        .comparison-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: #334155;
        }
        .comparison-table .winner { color: #16a34a; font-weight: 700; }
        .comparison-table .loser { color: #64748b; }

        /* Status */
        .status-bar {
            background: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            display: none;
        }
        .status-bar.show { display: block; }
        .status-bar .progress-text {
            font-size: 14px;
            color: #475569;
            margin-bottom: 8px;
        }
        .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            border-radius: 3px;
            transition: width 0.3s;
            width: 0%;
        }

        /* Info banner */
        .info-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #1e40af;
        }

        /* Error */
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 16px;
            color: #dc2626;
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* Summary card */
        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px; }
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .summary-card h4 { font-size: 14px; color: #64748b; margin-bottom: 8px; }
        .summary-card .value { font-size: 28px; font-weight: 700; }
        .summary-card .label { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .hybrid-color { color: #1d4ed8; }
        .traditional-color { color: #9333ea; }

        /* Accuracy Validation Section */
        .accuracy-section {
            background: white;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .accuracy-section h3 {
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 16px;
            color: #1e293b;
        }
        .accuracy-explanation {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }
        .accuracy-explanation .label-raw { color: #f97316; font-weight: 700; }
        .accuracy-explanation .label-true { color: #16a34a; font-weight: 700; }

        .accuracy-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .accuracy-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
        }
        .accuracy-card.traditional { border-color: #e9d5ff; }
        .accuracy-card.hybrid { border-color: #bfdbfe; }
        .accuracy-card h4 {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .accuracy-card.traditional h4 { color: #7c3aed; }
        .accuracy-card.hybrid h4 { color: #1d4ed8; }

        .task-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 16px;
            justify-content: center;
        }
        .task-icon {
            width: 28px;
            height: 28px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .task-icon.valid { background: #dcfce7; color: #16a34a; }
        .task-icon.invalid { background: #fee2e2; color: #dc2626; }
        .task-icon.unscheduled { background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; }

        .accuracy-stats {
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            font-size: 13px;
        }
        .accuracy-stats .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }
        .accuracy-stats .stat-row .stat-label {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .accuracy-stats .stat-icon {
            width: 18px;
            height: 18px;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        .accuracy-stats .stat-icon.valid { background: #dcfce7; color: #16a34a; }
        .accuracy-stats .stat-icon.invalid { background: #fee2e2; color: #dc2626; }
        .accuracy-stats .stat-icon.unscheduled { background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; }
        .accuracy-stats .stat-value { font-weight: 700; }
        .accuracy-stats .stat-value.green { color: #16a34a; }
        .accuracy-stats .stat-value.red { color: #dc2626; }
        .accuracy-stats .stat-value.gray { color: #94a3b8; }

        .accuracy-rates {
            border-top: 2px solid #e2e8f0;
            margin-top: 10px;
            padding-top: 10px;
        }
        .accuracy-rates .rate-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
            font-weight: 600;
        }
        .accuracy-rates .rate-value { font-weight: 700; }
        .accuracy-rates .rate-value.green { color: #16a34a; }
        .accuracy-rates .rate-value.orange { color: #f97316; }

        .violations-list {
            margin-top: 10px;
            font-size: 12px;
            color: #64748b;
        }
        .violations-list .violation {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .accuracy-winner {
            text-align: center;
            margin-top: 16px;
            padding: 10px 20px;
            background: #071957;
            color: white;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .setups-grid { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
            .accuracy-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>Algorithm Comparison <span class="badge">LOCAL ONLY</span></h1>
        <p>Traditional Genetic Algorithm vs Hybrid Algorithm (Rule-Based + Multi-Objective GA) &mdash; For thesis defense demonstration</p>
    </div>

    <?php if (isset($dbError)): ?>
        <div class="error-box">Database Error: <?= htmlspecialchars($dbError) ?></div>
    <?php endif; ?>

    <div class="info-banner">
        <strong>Database:</strong> Connected to <code>opticrew</code> &mdash;
        <strong><?= $totalEmployees ?></strong> active employees available.
        <?php if (empty($availableDates)): ?>
            <br><strong>No scheduled tasks found.</strong> Create tasks with future dates first (see test case guide below).
        <?php else: ?>
            <strong><?= count($availableDates) ?></strong> dates with pending/scheduled tasks.
        <?php endif; ?>
    </div>

    <!-- 3 Setup Configurations -->
    <div class="setups-grid">
        <?php
        $setups = [
            ['label' => 'First Setup', 'employees' => 10, 'tasks' => 10, 'table' => 16],
            ['label' => 'Second Setup', 'employees' => 20, 'tasks' => 15, 'table' => 17],
            ['label' => 'Third Setup', 'employees' => 20, 'tasks' => 35, 'table' => 18],
        ];
        foreach ($setups as $i => $setup):
        ?>
        <div class="setup-card" id="setup-<?= $i ?>">
            <h3>Table <?= $setup['table'] ?>: <?= $setup['label'] ?></h3>
            <label>Service Date</label>
            <select name="date_<?= $i ?>" class="setup-date">
                <?php if (empty($availableDates)): ?>
                    <option value="<?= date('Y-m-d', strtotime('+1 day')) ?>"><?= date('Y-m-d', strtotime('+1 day')) ?> (no tasks yet)</option>
                <?php else: ?>
                    <?php foreach ($availableDates as $d): ?>
                        <option value="<?= $d['scheduled_date'] ?>"><?= $d['scheduled_date'] ?> (<?= $d['task_count'] ?> tasks)</option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label>No. of Employees</label>
            <input type="number" class="setup-employees" value="<?= $setup['employees'] ?>" min="2" max="<?= $totalEmployees ?>">

            <label>No. of Tasks</label>
            <input type="number" class="setup-tasks" value="<?= $setup['tasks'] ?>" min="2" max="100">

            <label>Arrivals per Day</label>
            <input type="number" class="setup-arrivals" value="0" min="0" max="100" title="Number of tasks per day to mark as arrivals (sequencing test)">

            <label>Runs per Algorithm</label>
            <input type="number" class="setup-runs" value="10" min="1" max="20">
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Run Button -->
    <div class="btn-row">
        <button class="btn-run" id="btnRunAll" onclick="runAllSetups()">Run All 3 Setups</button>
    </div>

    <!-- Progress -->
    <div class="status-bar" id="statusBar">
        <div class="progress-text" id="progressText">Preparing...</div>
        <div class="progress-bar"><div class="fill" id="progressFill"></div></div>
    </div>

    <!-- Results Container -->
    <div id="resultsContainer"></div>
</div>

<script>
async function runAllSetups() {
    const btn = document.getElementById('btnRunAll');
    const statusBar = document.getElementById('statusBar');
    const progressText = document.getElementById('progressText');
    const progressFill = document.getElementById('progressFill');
    const resultsContainer = document.getElementById('resultsContainer');

    btn.disabled = true;
    statusBar.classList.add('show');
    resultsContainer.innerHTML = '';

    const setups = [];
    for (let i = 0; i < 3; i++) {
        const card = document.getElementById('setup-' + i);
        setups.push({
            index: i,
            date: card.querySelector('.setup-date').value,
            employees: card.querySelector('.setup-employees').value,
            tasks: card.querySelector('.setup-tasks').value,
            arrivals: card.querySelector('.setup-arrivals').value,
            runs: card.querySelector('.setup-runs').value,
        });
    }

    const allResults = [];

    for (let s = 0; s < setups.length; s++) {
        const setup = setups[s];
        const tableNum = [16, 17, 18][s];
        const label = ['First', 'Second', 'Third'][s];

        progressText.textContent = `Running Setup ${s + 1}/3: ${setup.employees} employees, ${setup.tasks} tasks...`;
        progressFill.style.width = ((s) / 3 * 100) + '%';

        // Mark active card
        document.querySelectorAll('.setup-card').forEach(c => c.classList.remove('active'));
        document.getElementById('setup-' + s).classList.add('active');

        try {
            const formData = new FormData();
            formData.append('action', 'run');
            formData.append('service_date', setup.date);
            formData.append('employee_limit', setup.employees);
            formData.append('task_limit', setup.tasks);
            formData.append('arrivals', setup.arrivals);
            formData.append('runs', setup.runs);

            const response = await fetch('', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.error) {
                resultsContainer.innerHTML += `
                    <div class="error-box">Setup ${s+1} Error: ${data.error}</div>
                `;
                continue;
            }

            allResults.push({ setup, data, tableNum, label });

            // Render table
            resultsContainer.innerHTML += renderComparisonTable(data, tableNum, label, setup);

        } catch (err) {
            resultsContainer.innerHTML += `
                <div class="error-box">Setup ${s+1} Error: ${err.message}</div>
            `;
        }
    }

    // Render summary
    if (allResults.length > 0) {
        resultsContainer.innerHTML += renderSummary(allResults);
    }

    progressFill.style.width = '100%';
    progressText.textContent = 'All setups complete!';
    document.querySelectorAll('.setup-card').forEach(c => c.classList.remove('active'));
    btn.disabled = false;

    setTimeout(() => { statusBar.classList.remove('show'); }, 2000);
}

function renderComparisonTable(data, tableNum, label, setup) {
    const t = data.traditional;
    const h = data.hybrid;
    const meta = data.meta;

    // Determine winners (2-way)
    const fitWinner = h.avg_fitness >= t.avg_fitness ? 'hybrid' : 'traditional';
    const convWinner = h.avg_convergence <= t.avg_convergence ? 'hybrid' : 'traditional';
    const solveWinner = (h.avg_solution_time_ms || h.avg_time_ms) <= (t.avg_solution_time_ms || t.avg_time_ms) ? 'hybrid' : 'traditional';
    const timeWinner = h.avg_time_ms <= t.avg_time_ms ? 'hybrid' : 'traditional';

    let html = `
    <div class="results-section">
        <table class="comparison-table">
            <caption>Table ${tableNum}: ${label} Setup — Traditional GA vs Hybrid Algorithm</caption>
            <thead>
                <tr>
                    <th></th>
                    <th>No. of Employees</th>
                    <th>No. of Tasks</th>
                    <th>Fitness Rate</th>
                    <th>Convergence / Total Gens</th>
                    <th title="Time spent only on the GA loop (finding the solution), excluding report rendering">Solve Time</th>
                    <th title="Total wall-clock time including extended report building">Total Run Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Traditional Genetic Algorithm</td>
                    <td>${meta.employees_used}</td>
                    <td>${meta.tasks_used}</td>
                    <td class="${fitWinner === 'traditional' ? 'winner' : 'loser'}">${t.avg_fitness.toFixed(4)}</td>
                    <td class="${convWinner === 'traditional' ? 'winner' : 'loser'}">${Math.round(t.avg_convergence)} / ${Math.round(t.avg_generations || 0)} gens</td>
                    <td class="${solveWinner === 'traditional' ? 'winner' : 'loser'}">${(t.avg_solution_time_ms || t.avg_time_ms).toFixed(2)} ms</td>
                    <td class="${timeWinner === 'traditional' ? 'winner' : 'loser'}">${t.avg_time_ms.toFixed(2)} ms</td>
                </tr>
                <tr style="background: #f0fdf4;">
                    <td>Hybrid Algorithm (Rule-Based + Multi-Objective GA)</td>
                    <td>${meta.employees_used}</td>
                    <td>${meta.tasks_used}</td>
                    <td class="${fitWinner === 'hybrid' ? 'winner' : 'loser'}">${h.avg_fitness.toFixed(4)}</td>
                    <td class="${convWinner === 'hybrid' ? 'winner' : 'loser'}">${Math.round(h.avg_convergence)} / ${Math.round(h.avg_generations || 0)} gens</td>
                    <td class="${solveWinner === 'hybrid' ? 'winner' : 'loser'}">${(h.avg_solution_time_ms || h.avg_time_ms).toFixed(2)} ms</td>
                    <td class="${timeWinner === 'hybrid' ? 'winner' : 'loser'}">${h.avg_time_ms.toFixed(2)} ms</td>
                </tr>
            </tbody>
        </table>
    </div>
    `;

    // Add Accuracy Validation Breakdown (2-way)
    if (data.hybrid_validation && data.traditional_validation) {
        html += renderAccuracyBreakdown2Way(data.hybrid_validation, data.traditional_validation, tableNum);
    }

    // Hybrid extended report (timetable, makespan, subtasks, employee performance)
    if (data.hybrid_extended_report) {
        html += renderHybridExtendedReport(data.hybrid_extended_report);
    }

    return html;
}

function fmtMin(min) {
    if (min == null || isNaN(min)) return '-';
    const h = Math.floor(min / 60);
    const m = min % 60;
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}

function renderMakespanTimetable(timetable, borderColor, bgColor) {
    if (!timetable || timetable.length === 0) {
        return `<div style="font-size:11px; color:#94a3b8; padding:8px;">(no data)</div>`;
    }

    let html = '';
    timetable.forEach(team => {
        html += `<div style="border:1px solid ${borderColor}; border-radius:6px; padding:8px; margin-bottom:6px; background:${bgColor};">
            <div style="font-size:11px; font-weight:600; color:#0f172a; margin-bottom:4px;">
                Team #${team.team_id} &middot; eff ${team.efficiency} &middot; finish ${team.team_finish_label}
            </div>
            <table style="width:100%; font-size:10px; border-collapse:collapse;">
                <thead>
                    <tr style="background:rgba(0,0,0,0.04);">
                        <th style="padding:3px 4px; text-align:left;">#</th>
                        <th style="padding:3px 4px; text-align:left;">Task</th>
                        <th style="padding:3px 4px; text-align:right;">Min</th>
                        <th style="padding:3px 4px; text-align:center;">Start</th>
                        <th style="padding:3px 4px; text-align:center;">End</th>
                    </tr>
                </thead>
                <tbody>`;
        team.tasks.forEach(t => {
            const arrival = t.arrival_status ? '🛬 ' : '';
            html += `<tr>
                <td style="padding:3px 4px;">${t.sequence}</td>
                <td style="padding:3px 4px;">${arrival}#${t.task_id}</td>
                <td style="padding:3px 4px; text-align:right;">${t.effective_duration}</td>
                <td style="padding:3px 4px; text-align:center;">${t.start_label}</td>
                <td style="padding:3px 4px; text-align:center;">${t.end_label}</td>
            </tr>`;
        });
        html += `</tbody></table></div>`;
    });
    return html;
}

function renderHybridExtendedReport(rep) {
    if (!rep || !rep.timetable) return '';

    let html = `
    <div class="extended-report">
        <h3 style="margin-top: 24px; color: #071957; font-size: 18px;">Hybrid Algorithm — Extended Report</h3>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 14px;">
            Timetable, makespan comparison, subtask simulation, per-employee efficiency, and
            per-generation fitness data for the best schedule from the first run.
        </p>

        <div style="background:#fff7ed; border:1px solid #fdba74; border-radius:8px; padding:10px 14px; margin-bottom:14px;">
            <strong style="color:#9a3412; font-size:12px;">⚙ Run summary:</strong>
            <span style="font-size:12px; color:#0f172a;">
                Population size: <strong>${rep.population_size}</strong> &middot;
                Total generations run: <strong>${rep.total_generations_run}</strong> &middot;
                Best fitness reached at generation: <strong>${rep.convergence_generation ?? '—'}</strong>
            </span>
        </div>
    `;

    // ── Section 0: Fitness per generation (population evolution) ──
    if (rep.fitness_per_generation && rep.fitness_per_generation.length > 0) {
        html += `<div class="extended-section">
            <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">0. Fitness Per Generation (Population Evolution)</h4>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                Each row is one generation. <strong>Best</strong> = highest-scoring individual,
                <strong>Avg</strong> = mean of the population, <strong>Worst</strong> = lowest-scoring.
                Click "▶ show all" to see every individual's fitness in that generation.
            </p>
            <table style="width:100%; font-size:11px; border-collapse:collapse; border:1px solid #e2e8f0;">
                <thead>
                    <tr style="background:#e2e8f0;">
                        <th style="padding:6px; text-align:left;">Gen</th>
                        <th style="padding:6px; text-align:right;">Best</th>
                        <th style="padding:6px; text-align:right;">Average</th>
                        <th style="padding:6px; text-align:right;">Worst</th>
                        <th style="padding:6px; text-align:right;">Population</th>
                        <th style="padding:6px; text-align:left;">All Individuals</th>
                    </tr>
                </thead>
                <tbody>`;
        rep.fitness_per_generation.forEach((g, idx) => {
            const isBest = (g.generation === rep.convergence_generation);
            const rowBg = isBest ? 'background:#dcfce7;' : '';
            const detailsId = 'gen-detail-' + idx;
            html += `<tr style="${rowBg}">
                <td style="padding:5px 6px; border-top:1px solid #f1f5f9; font-weight:${isBest ? '700' : '400'};">
                    ${g.generation}${isBest ? ' ⭐' : ''}
                </td>
                <td style="padding:5px 6px; text-align:right; border-top:1px solid #f1f5f9; font-weight:600;">${Number(g.best).toFixed(4)}</td>
                <td style="padding:5px 6px; text-align:right; border-top:1px solid #f1f5f9;">${Number(g.average).toFixed(4)}</td>
                <td style="padding:5px 6px; text-align:right; border-top:1px solid #f1f5f9; color:#94a3b8;">${Number(g.worst).toFixed(4)}</td>
                <td style="padding:5px 6px; text-align:right; border-top:1px solid #f1f5f9;">${g.population_size}</td>
                <td style="padding:5px 6px; border-top:1px solid #f1f5f9;">
                    <details>
                        <summary style="cursor:pointer; color:#3b82f6; font-size:10px;">▶ show all ${g.population_size}</summary>
                        <div style="max-width:600px; font-family:monospace; font-size:10px; color:#475569; margin-top:4px; line-height:1.4;">
                            ${(g.individual_fitnesses || []).map((f, i) => {
                                const dot = i === 0 ? '🥇' : (i === 1 ? '🥈' : (i === 2 ? '🥉' : '•'));
                                return `${dot} ${Number(f).toFixed(4)}`;
                            }).join(' &nbsp; ')}
                        </div>
                    </details>
                </td>
            </tr>`;
        });
        html += `</tbody></table>
        <p style="font-size:10px; color:#94a3b8; margin-top:6px;">
            ⭐ = generation where the global best fitness was discovered.
            🥇🥈🥉 = top 3 individuals in that generation.
        </p>
        </div>`;
    }

    // ── Section 1: Timetable per team ──
    html += `<div class="extended-section">
        <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">1. Task Sequencing &amp; Timetable</h4>
        <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
            Service start: <strong>${fmtMin(rep.service_start_minutes)}</strong>.
            Each team's tasks are ordered, and start/end times are derived from team efficiency × task duration.
        </p>`;

    rep.timetable.forEach(team => {
        html += `<div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px; margin-bottom:10px; background:#f8fafc;">
            <div style="font-size:12px; font-weight:600; color:#0f172a; margin-bottom:6px;">
                Team #${team.team_id} &middot; Efficiency: ${team.efficiency} &middot; Finish: ${team.team_finish_label}
            </div>
            <table style="width:100%; font-size:11px; border-collapse:collapse;">
                <thead>
                    <tr style="background:#e2e8f0;">
                        <th style="padding:4px 6px; text-align:left;">#</th>
                        <th style="padding:4px 6px; text-align:left;">Task</th>
                        <th style="padding:4px 6px; text-align:center;">Arrival</th>
                        <th style="padding:4px 6px; text-align:right;">Base (min)</th>
                        <th style="padding:4px 6px; text-align:right;">Effective</th>
                        <th style="padding:4px 6px; text-align:center;">Start</th>
                        <th style="padding:4px 6px; text-align:center;">End</th>
                    </tr>
                </thead>
                <tbody>`;
        team.tasks.forEach(t => {
            html += `<tr>
                <td style="padding:4px 6px;">${t.sequence}</td>
                <td style="padding:4px 6px;">#${t.task_id}</td>
                <td style="padding:4px 6px; text-align:center;">${t.arrival_status ? '🛬' : '—'}</td>
                <td style="padding:4px 6px; text-align:right;">${t.base_duration}</td>
                <td style="padding:4px 6px; text-align:right;">${t.effective_duration}</td>
                <td style="padding:4px 6px; text-align:center;">${t.start_label}</td>
                <td style="padding:4px 6px; text-align:center;">${t.end_label}</td>
            </tr>`;
        });
        html += `</tbody></table></div>`;
    });
    html += `</div>`;

    // ── Section 1b: Per-client breakdown (timetable grouped by client + makespan per client) ──
    if (rep.client_breakdown && rep.client_breakdown.length > 0) {
        html += `<div class="extended-section">
            <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">1b. Per-Client Breakdown (timetable + makespan score)</h4>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                Same teams grouped under their client. Each client has its own makespan
                (when its last team finishes) and a corresponding makespan score.
            </p>`;

        rep.client_breakdown.forEach(client => {
            const scoreColor = client.makespan_score >= 0.8 ? '#16a34a'
                               : (client.makespan_score >= 0.5 ? '#eab308' : '#dc2626');
            html += `<div style="border:1px solid #cbd5e1; border-radius:10px; padding:12px; margin-bottom:14px; background:#fff;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid #e2e8f0;">
                    <div>
                        <div style="font-size:13px; font-weight:700; color:#0f172a;">${client.client_name}</div>
                        <div style="font-size:11px; color:#64748b;">
                            ${client.team_count} team(s) &middot; ${client.task_count} task(s)
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:11px; color:#64748b;">Client Makespan</div>
                        <div style="font-size:18px; font-weight:700; color:#0f172a;">${client.makespan_label}</div>
                        <div style="font-size:11px; color:${scoreColor}; font-weight:600;">
                            Score: ${client.makespan_score}
                        </div>
                    </div>
                </div>`;

            client.teams.forEach(team => {
                html += `<div style="border:1px solid #e2e8f0; border-radius:6px; padding:8px; margin-bottom:6px; background:#f8fafc;">
                    <div style="font-size:11px; font-weight:600; color:#0f172a; margin-bottom:4px;">
                        Team #${team.team_id} &middot; eff ${team.efficiency} &middot; finish ${team.team_finish_label}
                    </div>
                    <table style="width:100%; font-size:10px; border-collapse:collapse;">
                        <thead>
                            <tr style="background:rgba(0,0,0,0.04);">
                                <th style="padding:3px 4px; text-align:left;">#</th>
                                <th style="padding:3px 4px; text-align:left;">Task</th>
                                <th style="padding:3px 4px; text-align:center;">Arrival</th>
                                <th style="padding:3px 4px; text-align:right;">Eff. Dur</th>
                                <th style="padding:3px 4px; text-align:center;">Start</th>
                                <th style="padding:3px 4px; text-align:center;">End</th>
                            </tr>
                        </thead>
                        <tbody>`;
                team.tasks.forEach(t => {
                    html += `<tr>
                        <td style="padding:3px 4px;">${t.sequence}</td>
                        <td style="padding:3px 4px;">#${t.task_id}</td>
                        <td style="padding:3px 4px; text-align:center;">${t.arrival_status ? '🛬' : '—'}</td>
                        <td style="padding:3px 4px; text-align:right;">${t.effective_duration}</td>
                        <td style="padding:3px 4px; text-align:center;">${t.start_label}</td>
                        <td style="padding:3px 4px; text-align:center;">${t.end_label}</td>
                    </tr>`;
                });
                html += `</tbody></table></div>`;
            });

            html += `</div>`;
        });

        html += `</div>`;
    }

    // ── Section 2: Makespan comparison (numbers + sequence side-by-side) ──
    const cmp = rep.comparison_long_makespan || {};
    html += `<div class="extended-section">
        <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">2. Makespan Optimization</h4>
        <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
            "Makespan" is when the LAST team finishes. Lower = better. We compare the GA's optimized
            sequence to a deliberately worse alternate sequence.
        </p>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div style="border:2px solid #16a34a; border-radius:8px; padding:12px; background:#f0fdf4;">
                <div style="font-size:12px; font-weight:600; color:#15803d;">✅ Optimized (GA)</div>
                <div style="font-size:24px; font-weight:700; margin-top:4px;">${fmtMin(rep.makespan_minutes)}</div>
                <div style="font-size:11px; color:#475569;">Total: ${rep.makespan_minutes} min</div>
                <div style="font-size:11px; color:#475569;">Score: <strong>${rep.makespan_score}</strong></div>
            </div>
            <div style="border:2px solid #dc2626; border-radius:8px; padding:12px; background:#fef2f2;">
                <div style="font-size:12px; font-weight:600; color:#b91c1c;">⚠️ Long-Makespan Alt</div>
                <div style="font-size:24px; font-weight:700; margin-top:4px;">${fmtMin(cmp.makespan_minutes)}</div>
                <div style="font-size:11px; color:#475569;">Total: ${cmp.makespan_minutes} min</div>
                <div style="font-size:11px; color:#475569;">Score: <strong>${cmp.makespan_score}</strong></div>
            </div>
        </div>
        <div style="margin-top:8px; font-size:12px; color:#0f172a;">
            <strong>GA improvement:</strong> saved <strong>${cmp.improvement_minutes} min</strong>
            (<strong>${cmp.improvement_percent}%</strong> shorter than the alternative)
        </div>

        <!-- Side-by-side timetable comparison -->
        <div style="margin-top:14px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div>
                <div style="font-size:12px; font-weight:600; color:#15803d; margin-bottom:6px;">
                    ✅ Optimized Sequence
                </div>
                ${renderMakespanTimetable(rep.timetable, '#16a34a', '#f0fdf4')}
            </div>
            <div>
                <div style="font-size:12px; font-weight:600; color:#b91c1c; margin-bottom:6px;">
                    ⚠️ Long-Makespan Alternative
                </div>
                ${renderMakespanTimetable(cmp.timetable, '#dc2626', '#fef2f2')}
            </div>
        </div>
    </div>`;

    // ── Section 3: Subtask simulation ──
    if (rep.subtask_simulation && rep.subtask_simulation.length > 0) {
        html += `<div class="extended-section">
            <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">3. Subtask Simulation (per task checklist)</h4>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                Each task has up to 4 subtasks randomly assigned to team members. Used to compute
                the per-employee performance ratio below.
            </p>`;
        rep.subtask_simulation.slice(0, 6).forEach(taskSim => { // limit display to first 6
            html += `<div style="border:1px solid #e2e8f0; border-radius:6px; padding:8px; margin-bottom:8px; background:#fff;">
                <div style="font-size:11px; font-weight:600; color:#1e293b;">
                    Task #${taskSim.task_id} (Team #${taskSim.team_id}) &middot;
                    ${taskSim.task_start_label}–${taskSim.task_end_label} &middot;
                    estimated ${taskSim.estimated_duration} min
                </div>
                <ul style="margin:4px 0 0 16px; font-size:11px; color:#475569; list-style:disc;">`;
            taskSim.subtasks.forEach(st => {
                html += `<li>${st.name} → <strong>${st.completed_by_name}</strong>
                    (${st.start_label}–${st.end_label})</li>`;
            });
            html += `</ul></div>`;
        });
        if (rep.subtask_simulation.length > 6) {
            html += `<p style="font-size:11px; color:#94a3b8; margin-top:4px;">
                (Showing first 6 of ${rep.subtask_simulation.length} tasks)
            </p>`;
        }
        html += `</div>`;
    }

    // ── Section 4: Per-employee performance & efficiency ──
    if (rep.employee_performance && rep.employee_performance.length > 0) {
        html += `<div class="extended-section">
            <h4 style="margin: 14px 0 8px; font-size: 14px; color: #1e293b;">4. Per-Employee Efficiency</h4>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                Computed from the subtask simulation above using:<br>
                <code>contribution = subtasks_done / total_subtasks</code><br>
                <code>expected_time = task_duration × contribution</code><br>
                <code>performance_ratio = expected / actual (capped at 1.0)</code><br>
                <code>new_efficiency = (old × 0.7) + (ratio × 0.3)</code>, clamped [0.5, 1.0]
            </p>
            <table style="width:100%; font-size:11px; border-collapse:collapse;">
                <thead>
                    <tr style="background:#e2e8f0;">
                        <th style="padding:6px; text-align:left;">Employee</th>
                        <th style="padding:6px; text-align:right;">Team</th>
                        <th style="padding:6px; text-align:right;">Tasks Touched</th>
                        <th style="padding:6px; text-align:right;">Starting Eff</th>
                        <th style="padding:6px; text-align:right;">Final Eff</th>
                    </tr>
                </thead>
                <tbody>`;
        rep.employee_performance.forEach(emp => {
            const eff = emp.current_efficiency;
            const color = eff >= 0.95 ? '#16a34a' : (eff >= 0.8 ? '#eab308' : '#dc2626');
            html += `<tr>
                <td style="padding:6px;">${emp.name}</td>
                <td style="padding:6px; text-align:right;">#${emp.team_id}</td>
                <td style="padding:6px; text-align:right;">${emp.tasks_touched}</td>
                <td style="padding:6px; text-align:right;">${emp.starting_efficiency}</td>
                <td style="padding:6px; text-align:right; font-weight:700; color:${color};">${eff}</td>
            </tr>`;
        });
        html += `</tbody></table>
        </div>`;
    }

    html += `</div>`;
    return html;
}

function renderAccuracyBreakdown2Way(hybridVal, traditionalVal, tableNum) {
    const hTrueAcc = hybridVal.true_accuracy;
    const tTrueAcc = traditionalVal.true_accuracy;
    const winner = hTrueAcc >= tTrueAcc ? 'Hybrid Algorithm' : 'Traditional GA';
    const winnerPct = Math.max(hTrueAcc, tTrueAcc);

    function renderAccuracyCard(val, title, cssClass) {
        return `
            <div class="accuracy-card ${cssClass}">
                <h4>${title}</h4>
                <div class="task-grid">
                    ${renderTaskIcons(val)}
                </div>
                <div class="accuracy-stats">
                    <div class="stat-row">
                        <span class="stat-label">Total Tasks:</span>
                        <span class="stat-value">${val.total_tasks}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><span class="stat-icon valid">&#10003;</span> Valid:</span>
                        <span class="stat-value green">${val.valid}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><span class="stat-icon invalid">&#10007;</span> Invalid:</span>
                        <span class="stat-value red">${val.invalid}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><span class="stat-icon unscheduled">&#9675;</span> Unscheduled:</span>
                        <span class="stat-value gray">${val.unscheduled}</span>
                    </div>
                    <div class="accuracy-rates">
                        <div class="rate-row">
                            <span>RAW Accuracy:</span>
                            <span class="rate-value orange">${val.raw_accuracy.toFixed(2)}%</span>
                        </div>
                        <div class="rate-row">
                            <span>TRUE Accuracy:</span>
                            <span class="rate-value green">${val.true_accuracy.toFixed(2)}%</span>
                        </div>
                    </div>
                    ${renderViolations(val.violations)}
                </div>
            </div>
        `;
    }

    let html = `
    <div class="accuracy-section">
        <h3>Accuracy Validation Breakdown</h3>

        <div class="accuracy-explanation">
            <strong>Understanding the Difference:</strong><br>
            <span class="label-raw">RAW Accuracy</span> = Tasks scheduled (ignoring validity)<br>
            <span class="label-true">TRUE Accuracy</span> = Tasks scheduled WITH valid constraints (driver required, correct team size, client match)<br><br>
            <strong>Why This Matters:</strong> Traditional GA can assign tasks freely, achieving high numbers but creating impossible schedules
            (teams without drivers, wrong clients). The Hybrid Algorithm uses rule-based preprocessing to ensure every assignment is valid and production-ready.
        </div>

        <div class="accuracy-grid" style="grid-template-columns: 1fr 1fr;">
            ${renderAccuracyCard(traditionalVal, 'Traditional GA', 'traditional')}
            ${renderAccuracyCard(hybridVal, 'Hybrid Algorithm', 'hybrid')}
        </div>

        <div class="accuracy-winner">
            ${winner} Wins with ${winnerPct.toFixed(2)}% TRUE Accuracy
        </div>
    </div>
    `;

    return html;
}

function renderTaskIcons(validation) {
    let icons = '';

    // Valid assignments (green checkmarks)
    for (let i = 0; i < validation.valid; i++) {
        icons += '<span class="task-icon valid">&#10003;</span>';
    }

    // Invalid assignments (red X)
    for (let i = 0; i < validation.invalid; i++) {
        icons += '<span class="task-icon invalid">&#10007;</span>';
    }

    // Unscheduled (gray circles)
    for (let i = 0; i < validation.unscheduled; i++) {
        icons += '<span class="task-icon unscheduled">&#9675;</span>';
    }

    return icons;
}

function renderViolations(violations) {
    if (!violations || Object.keys(violations).length === 0) return '';

    let html = '<div class="violations-list"><strong style="font-size:11px;color:#94a3b8;">Violations breakdown:</strong>';
    for (const [rule, count] of Object.entries(violations)) {
        html += `<div class="violation"><span>${rule}</span><span style="color:#dc2626;font-weight:600;">${count}</span></div>`;
    }
    html += '</div>';
    return html;
}

function renderSummary(allResults) {
    let traditionalFitnessWins = 0;
    let hybridFitnessWins = 0;
    let avgTraditionalTrueAcc = 0;
    let avgHybridTrueAcc = 0;
    let avgTraditionalFitness = 0;
    let avgHybridFitness = 0;
    let accCount = 0;

    allResults.forEach(r => {
        const tFit = r.data.traditional.avg_fitness;
        const hFit = r.data.hybrid.avg_fitness;
        avgTraditionalFitness += tFit;
        avgHybridFitness += hFit;

        if (hFit >= tFit) hybridFitnessWins++;
        else traditionalFitnessWins++;

        if (r.data.hybrid_validation && r.data.traditional_validation) {
            avgHybridTrueAcc += r.data.hybrid_validation.true_accuracy;
            avgTraditionalTrueAcc += r.data.traditional_validation.true_accuracy;
            accCount++;
        }
    });

    const total = allResults.length;
    avgTraditionalFitness /= total;
    avgHybridFitness /= total;
    if (accCount > 0) {
        avgTraditionalTrueAcc /= accCount;
        avgHybridTrueAcc /= accCount;
    }

    return `
    <div class="results-section">
        <h2>Overall Summary</h2>
        <div class="summary-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="summary-card">
                <h4>Traditional GA</h4>
                <div class="value traditional-color">${traditionalFitnessWins} / ${total}</div>
                <div class="label">fitness wins &mdash; avg fitness: ${avgTraditionalFitness.toFixed(4)}</div>
                <div class="label" style="margin-top:6px;">avg TRUE accuracy: <strong style="color:#dc2626;">${avgTraditionalTrueAcc.toFixed(1)}%</strong></div>
            </div>
            <div class="summary-card" style="border: 2px solid #16a34a;">
                <h4>Hybrid Algorithm</h4>
                <div class="value" style="color:#059669;">${hybridFitnessWins} / ${total}</div>
                <div class="label">fitness wins &mdash; avg fitness: ${avgHybridFitness.toFixed(4)}</div>
                <div class="label" style="margin-top:6px;">avg TRUE accuracy: <strong style="color:#16a34a;">${avgHybridTrueAcc.toFixed(1)}%</strong></div>
            </div>
        </div>
        <div class="info-banner">
            <strong>Conclusion:</strong>
            The <strong class="traditional-color">Traditional GA</strong> achieves raw fitness but
            <strong style="color:#dc2626;">${(100 - avgTraditionalTrueAcc).toFixed(1)}% of its assignments are invalid</strong>
            (teams without drivers, wrong client matches, invalid team sizes).
            The <strong style="color:#059669;">Hybrid Algorithm</strong> combines rule-based preprocessing
            with a multi-objective Genetic Algorithm that optimizes workload balance, task sequencing (arrivals first),
            and makespan (minimize when the last team finishes) — achieving
            <strong style="color:#16a34a;">${avgHybridTrueAcc.toFixed(1)}% TRUE accuracy</strong>
            with an average fitness of <strong style="color:#059669;">${avgHybridFitness.toFixed(4)}</strong>.
            Every scheduled task is valid and production-ready.
        </div>
    </div>
    `;
}
</script>
</body>
</html>
