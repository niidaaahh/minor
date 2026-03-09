<?php
header("Content-Type: application/json");
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$conn  = getDBConnection();
$stats = [];

$stats['totalUsers']    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$stats['elderly']       = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$stats['caregivers']    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='caregiver'")->fetch_assoc()['c'];
$stats['emergencyLogs'] = $conn->query("SELECT COUNT(*) AS c FROM sos_alerts WHERE status='active'")->fetch_assoc()['c'];

echo json_encode($stats);
$conn->close();
?>