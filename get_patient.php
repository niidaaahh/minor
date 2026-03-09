<?php
// elderly-care-backend/get_patient.php
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

// Get caregiver's own name
$cg_res = $conn->query("SELECT full_name FROM users WHERE id = $caregiver_id LIMIT 1");
$cg     = $cg_res ? $cg_res->fetch_assoc() : null;

// Get the one assigned patient with their profile
$sql = "
    SELECT
        u.id,
        u.full_name,
        up.date_of_birth,
        up.gender,
        up.address,
        up.emergency_contact_name,
        up.emergency_contact_phone,
        up.blood_type,
        up.allergies,
        up.medical_notes
    FROM caregiver_assignments ca
    JOIN users u ON u.id = ca.user_id
    LEFT JOIN user_profiles up ON up.user_id = u.id
    WHERE ca.caregiver_id = $caregiver_id
      AND ca.is_active    = 1
    LIMIT 1
";

$result  = $conn->query($sql);
$patient = $result ? $result->fetch_assoc() : null;

if (!$patient) {
    echo json_encode(["error" => "No assigned patient found"]);
    exit;
}

// Calculate age
$age = null;
if (!empty($patient['date_of_birth'])) {
    $dob = new DateTime($patient['date_of_birth']);
    $age = (int)(new DateTime())->diff($dob)->y;
}

// Pick avatar emoji based on gender
$avatar = '👴';
if (!empty($patient['gender'])) {
    $avatar = strtolower($patient['gender']) === 'female' ? '👵' : '👴';
}

echo json_encode([
    "caregiver_name" => $cg['full_name'] ?? 'Caregiver',
    "full_name"      => $patient['full_name'],
    "age"            => $age,
    "avatar"         => $avatar,
    "blood_type"     => $patient['blood_type'],
    "allergies"      => $patient['allergies'],
    "medical_notes"  => $patient['medical_notes'],
    "address"        => $patient['address'],
    "emergency_contact_name"  => $patient['emergency_contact_name'],
    "emergency_contact_phone" => $patient['emergency_contact_phone'],
]);

$conn->close();
?>