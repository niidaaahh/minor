<?php
header('Content-Type: application/json');
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$conn    = getDBConnection();
$today   = date('Y-m-d');

// Check if today's logs already exist for this user
$check = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM routine_logs
    WHERE user_id = ?
    AND DATE(scheduled_time) = ?
");
$check->bind_param("is", $user_id, $today);
$check->execute();
$row = $check->get_result()->fetch_assoc();

// If no logs exist for today, generate them from routines table
if ((int)$row['cnt'] === 0) {
    $routines = $conn->query("
        SELECT id, scheduled_time
        FROM routines
        WHERE active = 1
        ORDER BY scheduled_time ASC
    ");

    $ins = $conn->prepare("
        INSERT IGNORE INTO routine_logs
            (user_id, routine_id, scheduled_time, status)
        VALUES
            (?, ?, ?, 'pending')
    ");

    while ($r = $routines->fetch_assoc()) {
        $scheduled = $today . ' ' . $r['scheduled_time'];
        $ins->bind_param("iis", $user_id, $r['id'], $scheduled);
        $ins->execute();
    }
}

// Fetch today's logs joined with routine names
$stmt = $conn->prepare("
    SELECT
        r.id,
        r.name,
        TIME_FORMAT(r.scheduled_time, '%H:%i') AS scheduled_time,
        rl.status,
        rl.id AS log_id
    FROM routine_logs rl
    JOIN routines r ON r.id = rl.routine_id
    WHERE rl.user_id = ?
    AND DATE(rl.scheduled_time) = ?
    AND r.active = 1
    ORDER BY r.scheduled_time ASC
");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();

$out = [];
while ($row = $result->fetch_assoc()) {
    $out[] = [
        'id'             => (int)$row['id'],
        'name'           => $row['name'],
        'scheduled_time' => $row['scheduled_time'],
        'status'         => $row['status'],
        'log_id'         => (int)$row['log_id'],
    ];
}

echo json_encode($out);
$conn->close();
?>