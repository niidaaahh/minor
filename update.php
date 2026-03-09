<?php
// PUT /api/users/update.php?id=5
require_once __DIR__ . '/../../helpers.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    json_out(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_out(['success' => false, 'message' => 'User ID is required.'], 400);

$data   = body();
$fields = [];
$types  = '';
$vals   = [];

if (isset($data['name']))   { $fields[] = 'name=?';   $types .= 's'; $vals[] = trim($data['name']); }
if (isset($data['role']))   { $fields[] = 'role=?';   $types .= 's'; $vals[] = trim($data['role']); }
if (isset($data['phone']))  { $fields[] = 'phone=?';  $types .= 's'; $vals[] = trim($data['phone']); }
if (isset($data['status'])) { $fields[] = 'status=?'; $types .= 's'; $vals[] = trim($data['status']); }

if (!$fields) json_out(['success' => false, 'message' => 'Nothing to update.'], 400);

$vals[] = $id;
$types .= 'i';

$db   = db();
$sql  = 'UPDATE users SET ' . implode(',', $fields) . ' WHERE id=?';
$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$vals);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $stmt->close();
    json_out(['success' => false, 'message' => 'User not found.'], 404);
}
$stmt->close();

$row = $db->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
json_out(['success' => true, 'data' => $row]);
