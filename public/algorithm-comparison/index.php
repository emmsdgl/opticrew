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
require_once __DIR__ . '/algorithms/HybridGA.php';
require_once __DIR__ . '/algorithms/TraditionalGA.php';
require_once __DIR__ . '/algorithms/EnhancedHybridGA.php';

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

// ─── Run Comparison ───
function runComparison($serviceDate, $employeeLimit, $taskLimit, $runs = 10) {
    $employees = loadEmployees($employeeLimit);
    $tasks = loadTasks($serviceDate, $taskLimit);
    $allClients = loadClients();

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

    $hybridResults = [];
    $traditionalResults = [];
    $enhancedResults = [];
    $hybridValidation = null;
    $traditionalValidation = null;
    $enhancedValidation = null;

    // Run Hybrid (Rule-Based + GA with Elitism)
    $hybridTeams = [];
    for ($i = 0; $i < $runs; $i++) {
        $startTime = microtime(true);

        $preprocessor = new RuleBasedPreprocessor();
        $preprocessed = $preprocessor->preprocess($tasks, $employees, $clients);

        if (empty($preprocessed['valid_tasks'])) {
            $hybridResults[] = ['fitness' => 0, 'generations' => 0, 'time_ms' => 0];
            continue;
        }

        $hybridGA = new HybridGA($gaConfig);
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
        ];

        // Validate first run's best schedule for accuracy breakdown
        if ($i === 0 && !empty($result['best_schedule'])) {
            $hybridValidation = validateScheduleAccuracy(
                $result['best_schedule'], $tasks, $preprocessed['teams'], $employees
            );
            $hybridTeams = $preprocessed['teams'];
        }
    }

    // Run Traditional GA
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
        ];

        // Validate first run's best schedule
        if ($i === 0 && !empty($result['best_schedule'])) {
            $traditionalTeams = $result['teams'] ?? [];
            $traditionalValidation = validateScheduleAccuracy(
                $result['best_schedule'], $tasks, $traditionalTeams, $employees
            );
        }
    }

    // Run Enhanced Hybrid (Multi-Objective GA)
    $enhancedTeams = [];
    for ($i = 0; $i < $runs; $i++) {
        $startTime = microtime(true);

        $preprocessor = new RuleBasedPreprocessor();
        $preprocessed = $preprocessor->preprocess($tasks, $employees, $clients);

        if (empty($preprocessed['valid_tasks'])) {
            $enhancedResults[] = ['fitness' => 0, 'generations' => 0, 'time_ms' => 0];
            continue;
        }

        $enhancedGA = new EnhancedHybridGA($gaConfig);
        $result = $enhancedGA->optimize(
            $preprocessed['valid_tasks'],
            $preprocessed['employee_allocations'],
            $preprocessed['teams']
        );

        $elapsed = (microtime(true) - $startTime) * 1000;

        $enhancedResults[] = [
            'fitness' => $result['best_fitness'],
            'generations' => $result['generations'],
            'convergence' => $result['convergence_generation'],
            'time_ms' => $elapsed,
        ];

        if ($i === 0 && !empty($result['best_schedule'])) {
            $enhancedValidation = validateScheduleAccuracy(
                $result['best_schedule'], $tasks, $preprocessed['teams'], $employees
            );
            $enhancedTeams = $preprocessed['teams'];
        }
    }

    // Aggregate results
    return [
        'hybrid' => aggregateResults($hybridResults),
        'traditional' => aggregateResults($traditionalResults),
        'enhanced' => aggregateResults($enhancedResults),
        'raw_hybrid' => $hybridResults,
        'raw_traditional' => $traditionalResults,
        'raw_enhanced' => $enhancedResults,
        'hybrid_validation' => $hybridValidation,
        'traditional_validation' => $traditionalValidation,
        'enhanced_validation' => $enhancedValidation,
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

    return [
        'avg_fitness' => count($fitnessValues) ? array_sum($fitnessValues) / count($fitnessValues) : 0,
        'best_fitness' => count($fitnessValues) ? max($fitnessValues) : 0,
        'avg_generations' => count($generationValues) ? array_sum($generationValues) / count($generationValues) : 0,
        'avg_convergence' => count($convergenceValues) ? array_sum($convergenceValues) / count($convergenceValues) : 0,
        'avg_time_ms' => count($timeValues) ? array_sum($timeValues) / count($timeValues) : 0,
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

    try {
        $result = runComparison($serviceDate, $employeeLimit, $taskLimit, $runs);
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
        <p>Traditional GA vs Hybrid GA vs Enhanced Hybrid GA (Multi-Objective) &mdash; For thesis defense demonstration</p>
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
    const h = data.hybrid;
    const t = data.traditional;
    const e = data.enhanced;
    const meta = data.meta;

    // Determine winners (3-way)
    const fitValues = { hybrid: h.avg_fitness, traditional: t.avg_fitness, enhanced: e.avg_fitness };
    const convValues = { hybrid: h.avg_convergence, traditional: t.avg_convergence, enhanced: e.avg_convergence };
    const timeValues = { hybrid: h.avg_time_ms, traditional: t.avg_time_ms, enhanced: e.avg_time_ms };

    const fitWinner = Object.keys(fitValues).reduce((a, b) => fitValues[a] >= fitValues[b] ? a : b);
    const convWinner = Object.keys(convValues).reduce((a, b) => convValues[a] <= convValues[b] ? a : b);
    const timeWinner = Object.keys(timeValues).reduce((a, b) => timeValues[a] <= timeValues[b] ? a : b);

    let html = `
    <div class="results-section">
        <table class="comparison-table">
            <caption>Table ${tableNum}: ${label} Setup: Traditional GA vs Hybrid GA vs Enhanced Hybrid GA (Multi-Objective)</caption>
            <thead>
                <tr>
                    <th></th>
                    <th>No. of Employees</th>
                    <th>No. of Tasks</th>
                    <th>Fitness Rate</th>
                    <th>Convergence Rate</th>
                    <th>Run Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Traditional Genetic Algorithm</td>
                    <td>${meta.employees_used}</td>
                    <td>${meta.tasks_used}</td>
                    <td class="${fitWinner === 'traditional' ? 'winner' : 'loser'}">${t.avg_fitness.toFixed(4)}</td>
                    <td class="${convWinner === 'traditional' ? 'winner' : 'loser'}">${Math.round(t.avg_convergence)} generations</td>
                    <td class="${timeWinner === 'traditional' ? 'winner' : 'loser'}">${t.avg_time_ms.toFixed(2)} ms</td>
                </tr>
                <tr>
                    <td>Rule-Based + Genetic Algorithm (Hybrid)</td>
                    <td>${meta.employees_used}</td>
                    <td>${meta.tasks_used}</td>
                    <td class="${fitWinner === 'hybrid' ? 'winner' : 'loser'}">${h.avg_fitness.toFixed(4)}</td>
                    <td class="${convWinner === 'hybrid' ? 'winner' : 'loser'}">${Math.round(h.avg_convergence)} generations</td>
                    <td class="${timeWinner === 'hybrid' ? 'winner' : 'loser'}">${h.avg_time_ms.toFixed(2)} ms</td>
                </tr>
                <tr style="background: #f0fdf4;">
                    <td>Enhanced Hybrid GA (Multi-Objective)</td>
                    <td>${meta.employees_used}</td>
                    <td>${meta.tasks_used}</td>
                    <td class="${fitWinner === 'enhanced' ? 'winner' : 'loser'}">${e.avg_fitness.toFixed(4)}</td>
                    <td class="${convWinner === 'enhanced' ? 'winner' : 'loser'}">${Math.round(e.avg_convergence)} generations</td>
                    <td class="${timeWinner === 'enhanced' ? 'winner' : 'loser'}">${e.avg_time_ms.toFixed(2)} ms</td>
                </tr>
            </tbody>
        </table>
    </div>
    `;

    // Add Accuracy Validation Breakdown (3-way)
    if (data.hybrid_validation && data.traditional_validation && data.enhanced_validation) {
        html += renderAccuracyBreakdown3Way(data.hybrid_validation, data.traditional_validation, data.enhanced_validation, tableNum);
    } else if (data.hybrid_validation && data.traditional_validation) {
        html += renderAccuracyBreakdown3Way(data.hybrid_validation, data.traditional_validation, null, tableNum);
    }

    return html;
}

function renderAccuracyBreakdown3Way(hybridVal, traditionalVal, enhancedVal, tableNum) {
    const hTrueAcc = hybridVal.true_accuracy;
    const tTrueAcc = traditionalVal.true_accuracy;
    const eTrueAcc = enhancedVal ? enhancedVal.true_accuracy : 0;

    const accValues = { 'Traditional': tTrueAcc, 'Hybrid': hTrueAcc };
    if (enhancedVal) accValues['Enhanced Hybrid'] = eTrueAcc;
    const winner = Object.keys(accValues).reduce((a, b) => accValues[a] >= accValues[b] ? a : b);
    const winnerPct = accValues[winner];

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

    let gridStyle = enhancedVal ? 'grid-template-columns: 1fr 1fr 1fr;' : 'grid-template-columns: 1fr 1fr;';

    let html = `
    <div class="accuracy-section">
        <h3>Accuracy Validation Breakdown</h3>

        <div class="accuracy-explanation">
            <strong>Understanding the Difference:</strong><br>
            <span class="label-raw">RAW Accuracy</span> = Tasks scheduled (ignoring validity)<br>
            <span class="label-true">TRUE Accuracy</span> = Tasks scheduled WITH valid constraints (driver required, correct team size, client match)<br><br>
            <strong>Why This Matters:</strong> Traditional GA can assign tasks freely, achieving high numbers but creating impossible schedules.
            Both Hybrid algorithms use rule-based preprocessing to ensure valid, production-ready schedules.
        </div>

        <div class="accuracy-grid" style="${gridStyle}">
            ${renderAccuracyCard(traditionalVal, 'Traditional GA', 'traditional')}
            ${renderAccuracyCard(hybridVal, 'Hybrid GA', 'hybrid')}
            ${enhancedVal ? renderAccuracyCard(enhancedVal, 'Enhanced Hybrid GA', 'hybrid') : ''}
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
    let enhancedFitnessWins = 0;
    let avgTraditionalTrueAcc = 0;
    let avgHybridTrueAcc = 0;
    let avgEnhancedTrueAcc = 0;
    let avgTraditionalFitness = 0;
    let avgHybridFitness = 0;
    let avgEnhancedFitness = 0;
    let accCount = 0;

    allResults.forEach(r => {
        const tFit = r.data.traditional.avg_fitness;
        const hFit = r.data.hybrid.avg_fitness;
        const eFit = r.data.enhanced.avg_fitness;
        avgTraditionalFitness += tFit;
        avgHybridFitness += hFit;
        avgEnhancedFitness += eFit;

        const maxFit = Math.max(tFit, hFit, eFit);
        if (eFit === maxFit) enhancedFitnessWins++;
        else if (hFit === maxFit) hybridFitnessWins++;
        else traditionalFitnessWins++;

        if (r.data.hybrid_validation && r.data.traditional_validation && r.data.enhanced_validation) {
            avgHybridTrueAcc += r.data.hybrid_validation.true_accuracy;
            avgTraditionalTrueAcc += r.data.traditional_validation.true_accuracy;
            avgEnhancedTrueAcc += r.data.enhanced_validation.true_accuracy;
            accCount++;
        }
    });

    const total = allResults.length;
    avgTraditionalFitness /= total;
    avgHybridFitness /= total;
    avgEnhancedFitness /= total;
    if (accCount > 0) {
        avgTraditionalTrueAcc /= accCount;
        avgHybridTrueAcc /= accCount;
        avgEnhancedTrueAcc /= accCount;
    }

    return `
    <div class="results-section">
        <h2>Overall Summary</h2>
        <div class="summary-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="summary-card">
                <h4>Traditional GA</h4>
                <div class="value traditional-color">${traditionalFitnessWins} / ${total}</div>
                <div class="label">fitness wins &mdash; avg fitness: ${avgTraditionalFitness.toFixed(4)}</div>
                <div class="label" style="margin-top:6px;">avg TRUE accuracy: <strong style="color:#dc2626;">${avgTraditionalTrueAcc.toFixed(1)}%</strong></div>
            </div>
            <div class="summary-card">
                <h4>Hybrid GA (Current)</h4>
                <div class="value hybrid-color">${hybridFitnessWins} / ${total}</div>
                <div class="label">fitness wins &mdash; avg fitness: ${avgHybridFitness.toFixed(4)}</div>
                <div class="label" style="margin-top:6px;">avg TRUE accuracy: <strong style="color:#16a34a;">${avgHybridTrueAcc.toFixed(1)}%</strong></div>
            </div>
            <div class="summary-card" style="border: 2px solid #16a34a;">
                <h4>Enhanced Hybrid GA</h4>
                <div class="value" style="color:#059669;">${enhancedFitnessWins} / ${total}</div>
                <div class="label">fitness wins &mdash; avg fitness: ${avgEnhancedFitness.toFixed(4)}</div>
                <div class="label" style="margin-top:6px;">avg TRUE accuracy: <strong style="color:#16a34a;">${avgEnhancedTrueAcc.toFixed(1)}%</strong></div>
            </div>
        </div>
        <div class="info-banner">
            <strong>Key Findings:</strong><br>
            &bull; <strong class="traditional-color">Traditional GA</strong> achieves raw fitness but
            <strong style="color:#dc2626;">${(100 - avgTraditionalTrueAcc).toFixed(1)}% of assignments are invalid</strong>
            (no drivers, wrong clients).<br>
            &bull; <strong class="hybrid-color">Hybrid GA</strong> uses rule-based preprocessing for
            <strong style="color:#16a34a;">${avgHybridTrueAcc.toFixed(1)}% TRUE accuracy</strong>, but optimizes only workload balance (single objective).<br>
            &bull; <strong style="color:#059669;">Enhanced Hybrid GA</strong> adds 3 more objectives
            (task sequencing, makespan, idle time reduction) for a multi-objective fitness of
            <strong style="color:#059669;">${avgEnhancedFitness.toFixed(4)}</strong>
            &mdash; giving the GA a genuinely complex search space to optimize.
        </div>
    </div>
    `;
}
</script>
</body>
</html>
