<?php
/**
 * Seeder: create two scheduled_date buckets — one with 15 tasks, one with 20 tasks.
 *
 * Strategy: clone the column shape from a real existing task row so every NOT NULL
 * column is satisfied without us having to hard-code the schema. Only the fields
 * we care about (scheduled_date, location_id, client_id, status, duration, etc.)
 * are overwritten.
 *
 * Run from browser:  http://localhost/opticrew/public/algorithm-comparison/seed_dates.php
 * Idempotent: if a target date already has >= the requested count, it skips.
 */

header('Content-Type: text/plain');

// ─── Minimal DB connection (reads Laravel .env) ───
function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $envPath = null;
    $dir = __DIR__;
    for ($i = 0; $i < 5; $i++) {
        $dir = dirname($dir);
        if (file_exists($dir . '/.env')) { $envPath = $dir . '/.env'; break; }
    }
    $env = [];
    if ($envPath) {
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }
    }
    $pdo = new PDO(
        "mysql:host=" . ($env['DB_HOST'] ?? '127.0.0.1') .
        ";port=" . ($env['DB_PORT'] ?? '3306') .
        ";dbname=" . ($env['DB_DATABASE'] ?? 'opticrew') . ";charset=utf8mb4",
        $env['DB_USERNAME'] ?? 'root',
        $env['DB_PASSWORD'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    return $pdo;
}

$db = getDB();

$date15 = '2025-07-01';
$date20 = '2025-07-02';

echo "Target dates:\n";
echo "  15 tasks → $date15\n";
echo "  20 tasks → $date20\n\n";

// ─── Grab a template task (most recent non-deleted) ───
$template = $db->query("SELECT * FROM tasks WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 1")->fetch();
if (!$template) {
    exit("ERROR: No existing task to use as a template. Create at least one task first.\n");
}

// Drop columns we don't want to copy verbatim
unset($template['id'], $template['created_at'], $template['updated_at'], $template['deleted_at']);

// ─── Pull a pool of valid locations (with their client_id) to spread tasks across ───
$locations = $db->query("
    SELECT id, contracted_client_id
    FROM locations
    WHERE deleted_at IS NULL AND contracted_client_id IS NOT NULL
")->fetchAll();

if (empty($locations)) {
    exit("ERROR: No locations with contracted_client_id found.\n");
}

// ─── Build INSERT statement dynamically from template columns ───
$columns = array_keys($template);
$placeholders = array_map(fn($c) => ":$c", $columns);
$sql = "INSERT INTO tasks (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
$insert = $db->prepare($sql);

function seedDate(PDOStatement $insert, array $template, array $locations, string $date, int $count): int {
    $inserted = 0;
    for ($i = 1; $i <= $count; $i++) {
        $loc = $locations[array_rand($locations)];
        $row = $template;
        $row['scheduled_date'] = $date;
        $row['location_id']    = $loc['id'];
        $row['client_id']      = $loc['contracted_client_id'];
        $row['status']         = 'Pending';
        $row['task_description'] = "Seeded task #{$i} for {$date}";
        if (array_key_exists('arrival_status', $row))      $row['arrival_status'] = 0;
        if (array_key_exists('assigned_team_id', $row))    $row['assigned_team_id'] = null;
        if (array_key_exists('started_at', $row))          $row['started_at'] = null;
        if (array_key_exists('completed_at', $row))        $row['completed_at'] = null;
        if (array_key_exists('completed_by', $row))        $row['completed_by'] = null;
        if (array_key_exists('started_by', $row))          $row['started_by'] = null;
        if (array_key_exists('actual_duration', $row))     $row['actual_duration'] = null;
        if (array_key_exists('employee_approved', $row))   $row['employee_approved'] = null;
        if (array_key_exists('employee_approved_at', $row))$row['employee_approved_at'] = null;
        if (array_key_exists('on_hold_reason', $row))      $row['on_hold_reason'] = null;
        if (array_key_exists('on_hold_timestamp', $row))   $row['on_hold_timestamp'] = null;
        if (array_key_exists('reassigned_at', $row))       $row['reassigned_at'] = null;
        if (array_key_exists('reassignment_reason', $row)) $row['reassignment_reason'] = null;
        if (array_key_exists('optimization_run_id', $row)) $row['optimization_run_id'] = null;
        if (array_key_exists('assigned_by_generation', $row)) $row['assigned_by_generation'] = null;

        $insert->execute($row);
        $inserted++;
    }
    return $inserted;
}

try {
    $db->beginTransaction();
    // Idempotent: wipe any existing rows for these dates first (hard delete)
    $del = $db->prepare("DELETE FROM tasks WHERE scheduled_date IN (?, ?)");
    $del->execute([$date15, $date20]);
    $deleted = $del->rowCount();
    echo "Removed $deleted existing rows on those dates.\n";

    $a = seedDate($insert, $template, $locations, $date15, 15);
    $b = seedDate($insert, $template, $locations, $date20, 20);
    $db->commit();
    echo "Inserted $a tasks for $date15\n";
    echo "Inserted $b tasks for $date20\n";
    echo "\nDone. Reload the comparison page — both dates will appear in the dropdown.\n";
} catch (Throwable $e) {
    $db->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
