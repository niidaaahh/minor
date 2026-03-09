<?php
header('Content-Type: application/json');
session_start();
require_once 'database.php';
require_once 'send_sms.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';
$conn      = getDBConnection();

// Get low stock medicines
$stmt = $conn->prepare("
    SELECT medicine_name, stock_count, dosage
    FROM medicines
    WHERE user_id   = ?
    AND   is_active  = 1
    AND   stock_count < 5
    ORDER BY stock_count ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$low = [];
while ($row = $result->fetch_assoc()) {
    $low[] = $row;
}

if (empty($low)) {
    echo json_encode(['success' => true, 'message' => 'No low stock medicines']);
    exit;
}

// Get assigned caregiver phone
$cg = $conn->prepare("
    SELECT u.phone, u.full_name
    FROM caregiver_assignments ca
    JOIN users u ON u.id = ca.caregiver_id
    WHERE ca.user_id   = ?
    AND   ca.is_active = 1
    LIMIT 1
");
$cg->bind_param("i", $user_id);
$cg->execute();
$caregiver = $cg->get_result()->fetch_assoc();

if ($caregiver && !empty($caregiver['phone'])) {
    $list = implode(', ', array_map(fn($m) =>
        $m['medicine_name'] . ' (' . $m['stock_count'] . ' left)', $low));

    $sms  = "LOW STOCK ALERT! ";
    $sms .= "Patient: $full_name. ";
    $sms .= "Medicines running low: $list. ";
    $sms .= "Please refill soon.";

    sendSMS($caregiver['phone'], $sms);
}

echo json_encode([
    'success'   => true,
    'notified'  => count($low),
    'medicines' => $low,
]);

$conn->close();
?>