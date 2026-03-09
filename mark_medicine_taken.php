<?php
header("Content-Type: application/json");
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$input   = json_decode(file_get_contents('php://input'), true) ?? [];
$med_id  = (int)($input['log_id'] ?? 0);

if ($med_id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid medicine id"]);
    exit;
}

$conn = getDBConnection();

// Verify medicine belongs to this user
$check = $conn->prepare("
    SELECT id FROM medicines
    WHERE id = ? AND user_id = ? AND is_active = 1
");
$check->bind_param("ii", $med_id, $user_id);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "error" => "Medicine not found"]);
    exit;
}

// Check if a log already exists for today
$existing = $conn->prepare("
    SELECT id FROM medication_logs
    WHERE medicine_id = ?
    AND DATE(scheduled_time) = CURDATE()
    ORDER BY scheduled_time DESC
    LIMIT 1
");
$existing->bind_param("i", $med_id);
$existing->execute();
$log = $existing->get_result()->fetch_assoc();

if ($log) {
    // Update existing log
    $upd = $conn->prepare("
        UPDATE medication_logs
        SET status = 'taken', taken_time = NOW()
        WHERE id = ?
    ");
    $upd->bind_param("i", $log['id']);
    $upd->execute();
} else {
    // Insert new log for today
    $ins = $conn->prepare("
        INSERT INTO medication_logs
            (medicine_id, scheduled_time, taken_time, status)
        VALUES
            (?, NOW(), NOW(), 'taken')
    ");
    $ins->bind_param("i", $med_id);
    $ins->execute();
}

echo json_encode(["success" => true, "message" => "Medicine marked as taken"]);
$conn->close();
?>