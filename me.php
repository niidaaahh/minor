<?php
// GET /api/auth/me.php  – check session
require_once __DIR__ . '/../../helpers.php';

start_session();

if (empty($_SESSION['admin_id'])) {
    json_out(['success' => false, 'message' => 'Not authenticated.'], 401);
}

json_out([
    'success'  => true,
    'fullName' => $_SESSION['full_name'] ?? 'Super Admin',
    'username' => $_SESSION['username']  ?? '',
]);
