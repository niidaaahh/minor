<?php
session_start();
header("Content-Type: application/json");
require_once "database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$caregiver_id = (int)$_SESSION['user_id'];
$conn = getDBConnection();

// Get assigned patient
$res = $conn->query("
    SELECT user_id FROM caregiver_assignments
    WHERE caregiver_id = $caregiver_id AND is_active = 1
    LIMIT 1
");
$row        = $res ? $res->fetch_assoc() : null;
$patient_id = (int)($row['user_id'] ?? 0);

if ($patient_id === 0) {
    echo json_encode([]);
    exit;
}

// Get all active medicines with latest log status
$sql = "
    SELECT
        m.id              AS log_id,
        m.medicine_name   AS name,
        m.dosage,
        m.scheduled_times AS scheduled_time,
        COALESCE(
            (SELECT ml.status
             FROM medication_logs ml
             WHERE ml.medicine_id = m.id
             ORDER BY ml.scheduled_time DESC
             LIMIT 1),
        'pending') AS status
    FROM medicines m
    WHERE m.user_id   = $patient_id
    AND   m.is_active = 1
    ORDER BY m.id ASC
";

$result = $conn->query($sql);
$medicines = [];
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}

echo json_encode($medicines);
$conn->close();
?>