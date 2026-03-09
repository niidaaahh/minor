<?php
header('Content-Type: application/json');
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$conn    = getDBConnection();

$stmt = $conn->prepare("
    SELECT
        m.id             AS log_id,
        m.medicine_name  AS name,
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
    WHERE m.user_id = ?
    AND m.is_active = 1
    ORDER BY m.id ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$meds = [];
while ($row = $result->fetch_assoc()) {
    $meds[] = $row;
}

echo json_encode($meds);
$conn->close();
?>