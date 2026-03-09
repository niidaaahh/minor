<?php
header("Content-Type: application/json");
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$conn = getDBConnection();

$sql = "
    SELECT sa.id, sa.triggered_at, sa.alert_type, sa.message,
           sa.status, sa.latitude, sa.longitude,
           u.full_name, u.username
    FROM sos_alerts sa
    LEFT JOIN users u ON u.id = sa.user_id
    ORDER BY sa.triggered_at DESC
    LIMIT 100
";

$result = $conn->query($sql);
$alerts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alerts[] = [
            "id"           => $row["id"],
            "triggered_at" => $row["triggered_at"],
            "patient_name" => $row["full_name"],
            "username"     => $row["username"],
            "alert_type"   => $row["alert_type"],
            "message"      => $row["message"],
            "status"       => $row["status"],
            "severity"     => "High"
        ];
    }
}

echo json_encode(["data" => $alerts]);
$conn->close();
?>