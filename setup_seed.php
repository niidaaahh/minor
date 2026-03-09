<?php
/**
 * setup_seed.php
 * Run this ONCE via browser: http://localhost/elderly-care/setup_seed.php
 * Then DELETE this file from the server for security.
 */

$secret_key = $_GET['key'] ?? '';
if ($secret_key !== 'setup2024') {
    die('Access denied. Add ?key=setup2024 to the URL.');
}

require_once __DIR__ . '/config/database.php';

$conn = getDBConnection();

$accounts = [
    ['admin',      'Admin@1234',     'System Administrator', 'admin',     'admin@elderlycare.local',     '0000000001'],
    ['caregiver1', 'Caregiver@1234', 'Sarah Johnson',        'caregiver', 'sarah@elderlycare.local',     '0000000002'],
    ['user1',      'User@1234',      'Robert Thompson',      'user',      'robert@elderlycare.local',    '0000000003'],
];

echo "<h2>Elderly Care – Seed Setup</h2><pre>";

foreach ($accounts as [$username, $password, $full_name, $role, $email, $phone]) {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $stmt = $conn->prepare("UPDATE users SET password_hash=?, full_name=?, role=?, email=?, phone=? WHERE username=?");
        $stmt->bind_param("ssssss", $hash, $full_name, $role, $email, $phone, $username);
        $stmt->execute();
        $stmt->close();
        echo "✅ Updated: $username (role: $role, password: $password)\n";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, full_name, role, email, phone) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $username, $hash, $full_name, $role, $email, $phone);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();
        echo "✅ Created: $username (id: $new_id, role: $role, password: $password)\n";
    }
}

// Create user profile for user1
$stmt = $conn->prepare("SELECT id FROM users WHERE username='user1'");
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($user) {
    $uid = $user['id'];
    $stmt = $conn->prepare("
        INSERT INTO user_profiles (user_id, date_of_birth, gender, blood_type, medical_notes)
        VALUES (?, '1950-04-15', 'male', 'O+', 'Diabetic, hypertension history. Regular monitoring required.')
        ON DUPLICATE KEY UPDATE updated_at=NOW()
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->close();
    echo "✅ Profile created for user1\n";
}

// Caregiver assignment
$stmt = $conn->prepare("SELECT id FROM users WHERE username='user1'");
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM users WHERE username='caregiver1'");
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($u && $c) {
    $stmt = $conn->prepare("INSERT IGNORE INTO caregiver_assignments (user_id, caregiver_id) VALUES (?,?)");
    $stmt->bind_param("ii", $u['id'], $c['id']);
    $stmt->execute();
    $stmt->close();
    echo "✅ Caregiver assigned: {$c['id']} → user {$u['id']}\n";
}

// Sample medicines
$meds = [
    ['Metformin',  '500mg', 'Twice daily', '08:00,20:00', 'Take with meals',      'Dr. Smith'],
    ['Lisinopril', '10mg',  'Once daily',  '09:00',       'Take in the morning',  'Dr. Smith'],
    ['Aspirin',    '81mg',  'Once daily',  '08:00',       'Take with food',       'Dr. Jones'],
];

if ($u && $c) {
    foreach ($meds as [$name, $dosage, $freq, $times, $instr, $dr]) {
        $stmt = $conn->prepare("
            INSERT IGNORE INTO medicines (user_id, medicine_name, dosage, frequency, scheduled_times, instructions, prescribing_doctor, added_by)
            VALUES (?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param("issssssi", $u['id'], $name, $dosage, $freq, $times, $instr, $dr, $c['id']);
        $stmt->execute();
        $stmt->close();
    }
    echo "✅ Sample medicines added for user1\n";
}

$conn->close();
echo "\n🎉 Setup complete! DELETE this file (setup_seed.php) now.\n";
echo "</pre>";
?>
