<?php
// elderly-care-backend/alertsresolve.php
header("Content-Type: application/json");
session_start();
require_once "database.php";

$conn = getDBConnection();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid ID"]);
    exit;
}

$conn->query("UPDATE sos_alerts SET status='resolved', resolved_at=NOW() WHERE id=$id");

echo json_encode(["success" => true]);

$conn->close();
?>