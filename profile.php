<?php
include "database.php";

$conn = getDBConnection();

$userId = $_GET['userId'];

$sql = "SELECT * FROM user_profiles WHERE user_id='$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo "No profile found";
}
?>