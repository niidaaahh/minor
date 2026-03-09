<?php
header('Content-Type: application/json');
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$caregiver_id = (int)$_SESSION['user_id'];
$conn         = getDBConnection();

// Get assigned patient
$res        = $conn->query("
    SELECT user_id FROM caregiver_assignments
    WHERE caregiver_id = $caregiver_id AND is_active = 1
    LIMIT 1
");
$row        = $res ? $res->fetch_assoc() : null;
$patient_id = (int)($row['user_id'] ?? 0);

if ($patient_id === 0) {
    echo json_encode(['medicines' => [], 'low_stock' => []]);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.medicine_name AS name,
        m.dosage,
        COALESCE(m.stock_count, 0) AS stock_count
    FROM medicines m
    WHERE m.user_id   = ?
    AND   m.is_active = 1
    ORDER BY m.medicine_name ASC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$medicines = [];
$low_stock  = [];

while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
    if ((int)$row['stock_count'] < 5) {
        $low_stock[] = $row['name'];
    }
}

echo json_encode([
    'medicines' => $medicines,
    'low_stock' => $low_stock,
]);

$conn->close();
?>