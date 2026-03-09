<?php
header('Content-Type: application/json');
require 'database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['full_name']) || empty($input['username'])) {
    echo json_encode(['error' => 'Full Name and Username are required']);
    exit;
}

$full_name = $input['full_name'];
$username  = $input['username'];
$phone     = $input['phone'] ?? '';
$role      = $input['role'] ?? 'user';
$status    = $input['status'] === 'Inactive' ? 0 : 1;

// Default password (can be random or fixed, e.g. '1234')
$password = $input['password'] ?? '1234'; // plain text

$conn = getDBConnection();

$stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role, phone, is_active) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssi", $username, $password, $full_name, $role, $phone, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>