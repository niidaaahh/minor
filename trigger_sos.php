<?php
header("Content-Type: application/json");
session_start();
require_once "database.php";
require_once "send_sms.php"; // ← add this

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';

$input     = json_decode(file_get_contents('php://input'), true) ?? [];
$latitude  = isset($input['latitude'])  ? (float)$input['latitude']  : null;
$longitude = isset($input['longitude']) ? (float)$input['longitude'] : null;

$conn    = getDBConnection();
$message = "Emergency button pressed by " . $full_name;

// Insert SOS alert
$stmt = $conn->prepare("
    INSERT INTO sos_alerts
        (user_id, alert_type, message, latitude, longitude, status, triggered_at)
    VALUES
        (?, 'sos', ?, ?, ?, 'active', NOW())
");
$stmt->bind_param("isdd", $user_id, $message, $latitude, $longitude);
$stmt->execute();
$alert_id = (int)$conn->insert_id;

// Get assigned caregivers with phone numbers
$cg = $conn->prepare("
    SELECT u.id AS caregiver_id, u.phone, u.full_name
    FROM caregiver_assignments ca
    JOIN users u ON u.id = ca.caregiver_id
    WHERE ca.user_id = ? AND ca.is_active = 1
");
$cg->bind_param("i", $user_id);
$cg->execute();
$cg_result = $cg->get_result();

$notif = $conn->prepare("
    INSERT IGNORE INTO sos_notifications
        (alert_id, caregiver_id, notified_at, is_acknowledged)
    VALUES
        (?, ?, NOW(), 0)
");

while ($row = $cg_result->fetch_assoc()) {
    $cg_id    = (int)$row['caregiver_id'];
    $cg_phone = $row['phone'];
    $cg_name  = $row['full_name'];

    // Insert notification row
    $notif->bind_param("ii", $alert_id, $cg_id);
    $notif->execute();

    // Send SMS if phone exists
    if (!empty($cg_phone)) {
        $sms  = "SOS ALERT! ";
        $sms .= "Patient: $full_name. ";
        $sms .= "Time: " . date('d-m-Y H:i:s') . ". ";
        if ($latitude && $longitude) {
            $sms .= "Location: https://maps.google.com/?q=$latitude,$longitude. ";
        }
        $sms .= "Please respond immediately.";

        sendSMS($cg_phone, $sms);
    }
}

echo json_encode([
    "success" => true,
    "message" => "Emergency Alert Sent! Caregiver has been notified."
]);

$conn->close();
?>