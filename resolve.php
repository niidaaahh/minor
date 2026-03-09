<?php
// PUT /api/alerts/resolve.php?id=3
require_once __DIR__ . '/../../helpers.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    json_out(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_out(['success' => false, 'message' => 'Alert ID is required.'], 400);

$db   = db();
$stmt = $db->prepare("UPDATE alerts SET status='Resolved', resolved_at=NOW() WHERE id=? AND status='Open'");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $stmt->close();
    json_out(['success' => false, 'message' => 'Alert not found or already resolved.'], 404);
}
$stmt->close();

$row = $db->query("SELECT * FROM alerts WHERE id=$id")->fetch_assoc();
json_out(['success' => true, 'data' => $row]);
