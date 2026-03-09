<?php
// GET /api/dashboard/stats.php
require_once __DIR__ . '/../../helpers.php';
require_auth();

$db = db();

$total     = $db->query("SELECT COUNT(*) AS n FROM users")->fetch_assoc()['n'];
$elderly   = $db->query("SELECT COUNT(*) AS n FROM users WHERE role='Elderly'")->fetch_assoc()['n'];
$caregivers = $db->query("SELECT COUNT(*) AS n FROM users WHERE role='Caregiver'")->fetch_assoc()['n'];
$emergency = $db->query("SELECT COUNT(*) AS n FROM alerts")->fetch_assoc()['n'];

json_out([
    'totalUsers'    => (int)$total,
    'elderly'       => (int)$elderly,
    'caregivers'    => (int)$caregivers,
    'emergencyLogs' => (int)$emergency,
]);
