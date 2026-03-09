<?php
// DELETE /api/users/delete.php?id=5
require_once __DIR__ . '/../../helpers.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    json_out(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_out(['success' => false, 'message' => 'User ID is required.'], 400);

$db   = db();
$stmt = $db->prepare('DELETE FROM users WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $stmt->close();
    json_out(['success' => false, 'message' => 'User not found.'], 404);
}
$stmt->close();

json_out(['success' => true, 'message' => 'User deleted.']);
