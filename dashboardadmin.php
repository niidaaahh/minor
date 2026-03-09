<?php
include 'functions.php'; // Include the functions

$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 0;

$userInfo = getUserInfo($userId);
$readings = getHealthReadings($userId);

echo json_encode([
    "name" => $userInfo['full_name'],
    "bp" => "--",       // you can format using $readings
    "sugar" => "--",
    "heart_rate" => "--"
]);
?>