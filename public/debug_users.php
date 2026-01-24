<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Direct database connection
$conn = new mysqli('127.0.0.1', 'root', '', 'opticrew');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get users with company/manager-like roles
$sql = "SELECT id, name, email, role, deleted_at FROM users
        WHERE email LIKE '%kakslauttanen%'
        OR role IN ('company', 'manager', 'admin', 'contracted_client')
        LIMIT 20";

$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Also show the column definition
$sql2 = "SHOW COLUMNS FROM users LIKE 'role'";
$result2 = $conn->query($sql2);
$roleColumn = $result2->fetch_assoc();

echo json_encode([
    'users' => $users,
    'role_column_definition' => $roleColumn,
    'count' => count($users)
], JSON_PRETTY_PRINT);

$conn->close();
