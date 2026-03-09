<?php
session_start();
header('Content-Type: application/json');
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = getDBConnection();

$stmt = $conn->prepare("
    SELECT u.id AS user_id, u.username, u.full_name, u.email, u.phone,
           p.date_of_birth, p.gender, p.address, p.emergency_contact_name,
           p.emergency_contact_phone, p.blood_type, p.allergies, p.medical_notes
    FROM users u
    JOIN user_profiles p ON u.id = p.user_id
    WHERE u.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo json_encode(["error" => "Profile not found"]);
    exit;
}

echo json_encode($patient);
$conn->close();
?>