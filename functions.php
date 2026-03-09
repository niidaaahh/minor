<?php
include 'database.php';

/**
 * Get all active users with profile info
 */
function getUsers() {
    $conn = getDBConnection();
    $sql = "SELECT u.id, u.username, u.full_name, u.role, u.email, u.phone,
                   up.date_of_birth, up.gender, up.address
            FROM users u
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE u.is_active = 1";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $conn->close();
    return $users;
}

/**
 * Get caregivers assigned to a specific user
 */
function getUserCaregivers($user_id) {
    $conn = getDBConnection();
    $sql = "SELECT c.id, c.username, c.full_name, c.email, c.phone
            FROM caregiver_assignments ca
            JOIN users c ON ca.caregiver_id = c.id
            WHERE ca.user_id = $user_id AND ca.is_active = 1";
    $result = $conn->query($sql);
    $caregivers = [];
    while ($row = $result->fetch_assoc()) {
        $caregivers[] = $row;
    }
    $conn->close();
    return $caregivers;
}

/**
 * Get latest health readings for a user
 */
function getHealthReadings($user_id, $limit = 10) {
    $conn = getDBConnection();
    $sql = "SELECT reading_type, value, unit, systolic, diastolic, recorded_at
            FROM health_readings
            WHERE user_id = $user_id
            ORDER BY recorded_at DESC
            LIMIT $limit";
    $result = $conn->query($sql);
    $readings = [];
    while ($row = $result->fetch_assoc()) {
        $readings[] = $row;
    }
    $conn->close();
    return $readings;
}

/**
 * Get medications and latest logs for a user
 */
function getMedications($user_id) {
    $conn = getDBConnection();
    $sql = "SELECT m.id, m.medicine_name, m.dosage, m.frequency, m.start_date, m.end_date, ml.scheduled_time, ml.status
            FROM medicines m
            LEFT JOIN medication_logs ml ON m.id = ml.medicine_id
            WHERE m.user_id = $user_id
            ORDER BY ml.scheduled_time DESC";
    $result = $conn->query($sql);
    $medications = [];
    while ($row = $result->fetch_assoc()) {
        $medications[] = $row;
    }
    $conn->close();
    return $medications;
}

/**
 * Get SOS alerts for a user
 */
function getSOSAlerts($user_id) {
    $conn = getDBConnection();
    $sql = "SELECT id, alert_type, message, status, triggered_at, resolved_at
            FROM sos_alerts
            WHERE user_id = $user_id
            ORDER BY triggered_at DESC";
    $result = $conn->query($sql);
    $alerts = [];
    while ($row = $result->fetch_assoc()) {
        $alerts[] = $row;
    }
    $conn->close();
    return $alerts;
}

/**
 * Get a single user's basic info
 */
function getUserInfo($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($fullName, $email, $phone);
    $user = ["full_name" => "--", "email" => "--", "phone" => "--"];
    if ($stmt->fetch()) {
        $user = ["full_name" => $fullName, "email" => $email, "phone" => $phone];
    }
    $stmt->close();
    $conn->close();
    return $user;
}
?>