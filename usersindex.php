<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'database.php';

$conn = getDBConnection();

$result = $conn->query("SELECT id, full_name, role, phone, is_active FROM users ORDER BY id ASC");

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "id" => (int)$row['id'],
        "full_name" => $row['full_name'],
        "role" => $row['role'],
        "phone" => $row['phone'],
        "is_active" => (int)$row['is_active']
    ];
}

echo json_encode([
    "data" => $users
]);

$conn->close();
?>