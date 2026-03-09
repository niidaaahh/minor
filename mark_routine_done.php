<?php
header('Content-Type: application/json');
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$input   = json_decode(file_get_contents('php://input'), true) ?? [];
$log_id  = (int)($input['log_id'] ?? 0);

if ($log_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid log_id']);
    exit;
}

$conn = getDBConnection();

// Verify this log belongs to this user
$check = $conn->prepare("
    SELECT id, status
    FROM routine_logs
    WHERE id = ? AND user_id = ?
");
$check->bind_param("ii", $log_id, $user_id);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Log not found']);
    exit;
}

if ($row['status'] === 'done') {
    echo json_encode(['success' => true, 'message' => 'Already marked done']);
    exit;
}

$upd = $conn->prepare("
    UPDATE routine_logs
    SET status       = 'done',
        completed_at = NOW()
    WHERE id = ?
");
$upd->bind_param("i", $log_id);
$upd->execute();

echo json_encode(['success' => true]);
$conn->close();
?>