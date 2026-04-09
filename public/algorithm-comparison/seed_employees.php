<?php
/**
 * Seeder: insert 7 sample employees (with linked users) so the comparison
 * tool can run setups with up to 20 active employees.
 *
 * Strategy: clone an existing user + employee row's column shape so every
 * NOT NULL column is satisfied. Only identity fields (name, email, full_name)
 * and the FK linking employee.user_id are overwritten per row.
 *
 * Run from browser:
 *   http://localhost/opticrew/public/algorithm-comparison/seed_employees.php
 *
 * Idempotent: re-running deletes any previously seeded "Sim Employee" rows
 * and reinserts them, so you always end up with exactly 7 extras.
 */

header('Content-Type: text/plain');

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

$NUM_TO_SEED = 7;
$SIM_PREFIX  = 'Sim Employee';
$SIM_EMAIL   = 'sim.employee.%d@opticrew.local';

// ─── Templates ───
$userTemplate = $db->query("
    SELECT * FROM users
    WHERE role = 'employee'
    ORDER BY id ASC LIMIT 1
")->fetch();

if (!$userTemplate) exit("ERROR: No existing employee user to use as a template.\n");

$empTemplate = $db->query("
    SELECT e.* FROM employees e
    JOIN users u ON u.id = e.user_id
    WHERE e.is_active = 1 AND e.deleted_at IS NULL
    ORDER BY e.id ASC LIMIT 1
")->fetch();

if (!$empTemplate) exit("ERROR: No existing active employee to use as a template.\n");

unset($userTemplate['id'], $userTemplate['created_at'], $userTemplate['updated_at']);
unset($empTemplate['id'], $empTemplate['created_at'], $empTemplate['updated_at'], $empTemplate['deleted_at']);

// ─── Idempotent: skip slots that already exist (NO deletes) ───
$db->beginTransaction();
try {
    $existing = $db->query("SELECT email FROM users WHERE email LIKE 'sim.employee.%@opticrew.local'")
                   ->fetchAll(PDO::FETCH_COLUMN);
    $existingSet = array_flip($existing);

    // ─── Build prepared inserts from template column shape ───
    $userCols = array_keys($userTemplate);
    $userSql  = "INSERT INTO users (" . implode(',', $userCols) . ") VALUES (" .
                implode(',', array_map(fn($c) => ":$c", $userCols)) . ")";
    $userInsert = $db->prepare($userSql);

    $empCols = array_keys($empTemplate);
    $empSql  = "INSERT INTO employees (" . implode(',', $empCols) . ") VALUES (" .
               implode(',', array_map(fn($c) => ":$c", $empCols)) . ")";
    $empInsert = $db->prepare($empSql);

    $hashed = password_hash('password', PASSWORD_BCRYPT);

    $inserted = 0;
    for ($i = 1; $i <= $NUM_TO_SEED; $i++) {
        $email = sprintf($SIM_EMAIL, $i);
        if (isset($existingSet[$email])) continue; // already seeded, skip — never delete

        // user row
        $u = $userTemplate;
        $u['name']     = "$SIM_PREFIX $i";
        $u['email']    = $email;
        if (array_key_exists('username', $u)) $u['username'] = "sim_employee_$i";
        $u['password'] = $hashed;
        $u['role']     = 'employee';
        if (array_key_exists('email_verified_at', $u)) $u['email_verified_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('remember_token', $u))    $u['remember_token']    = null;
        $userInsert->execute($u);
        $newUserId = (int)$db->lastInsertId();

        // employee row
        $e = $empTemplate;
        $e['user_id']   = $newUserId;
        if (array_key_exists('full_name', $e))            $e['full_name'] = "$SIM_PREFIX $i";
        if (array_key_exists('is_active', $e))            $e['is_active'] = 1;
        // Alternate driver / non-driver to keep team formation realistic
        if (array_key_exists('has_driving_license', $e))  $e['has_driving_license'] = ($i % 2 === 0) ? 1 : 0;
        $empInsert->execute($e);
        $inserted++;
    }

    $db->commit();
    echo "Inserted $inserted new simulated employees (skipped " . (count($existingSet)) . " already present).\n";

    $total = (int)$db->query("SELECT COUNT(*) FROM employees WHERE is_active = 1 AND deleted_at IS NULL")->fetchColumn();
    echo "Total active employees now: $total\n";
    echo "\nDone. The comparison tool will now accept up to $total in the 'No. of Employees' field.\n";
    echo "Re-running this script is safe — it skips any sim.employee.* rows that already exist and never deletes anything.\n";
} catch (Throwable $e) {
    $db->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
