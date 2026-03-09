<?php
require_once __DIR__ . '/elderly-care-backend/helpers.php';

if(isset($_SESSION['user'])){
    header("Location: admin-dashboard.html");
    exit;
}
?>