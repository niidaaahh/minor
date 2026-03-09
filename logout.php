<?php
// GET /api/auth/logout.php
require_once __DIR__ . '/../../helpers.php';

start_session();
session_unset();
session_destroy();
setcookie(SESSION_NAME, '', time() - 3600, '/');

json_out(['success' => true]);
