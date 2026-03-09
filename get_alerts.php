<?php
// elderly-care-backend/get_alerts.php
session_start();
header("Content-Type: application/json");
require_once "database.php";
$conn = getDBConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'caregiver') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$caregiver_id = (int)$_SESSION['user_id'];

// Get the one assigned patient
$patient_res = $conn->query("
    SELECT u.id, u.full_name
    FROM caregiver_assignments ca
    JOIN users u ON u.id = ca.user_id
    WHERE ca.caregiver_id = $caregiver_id
      AND ca.is_active    = 1
    LIMIT 1
");
$patient    = $patient_res ? $patient_res->fetch_assoc() : null;
$patient_id = (int)($patient['id'] ?? 0);

if ($patient_id === 0) {
    echo json_encode(["patient" => null, "alerts" => []]);
    exit;
}

// Fetch alerts for this patient, newest first
$sql = "
    SELECT
        id,
        alert_type,
        message,
        latitude,
        longitude,
        status,
        triggered_at,
        resolved_at,
        resolution_notes
    FROM sos_alerts
    WHERE user_id = $patient_id
    ORDER BY triggered_at DESC
    LIMIT 50
";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => $conn->error]);
    exit;
}

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
}

echo json_encode([
    "patient" => $patient,
    "alerts"  => $alerts,
]);

$conn->close();
?>