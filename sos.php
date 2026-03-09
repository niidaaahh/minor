<?php

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data["userId"];

// get user info
$userQuery = "SELECT * FROM users WHERE username='$userId'";
$userResult = $conn->query($userQuery);

if($userResult->num_rows == 0){
    echo json_encode(["message"=>"User not found"]);
    exit();
}

$user = $userResult->fetch_assoc();
$user_db_id = $user["id"];


// get caregiver
$caregiverQuery = "
SELECT u.phone 
FROM caregiver_assignments ca
JOIN users u ON ca.caregiver_id = u.id
WHERE ca.user_id = '$user_db_id'
";

$caregiverResult = $conn->query($caregiverQuery);

if($caregiverResult->num_rows == 0){
    echo json_encode(["message"=>"No caregiver assigned"]);
    exit();
}

$caregiver = $caregiverResult->fetch_assoc();
$phone = $caregiver["phone"];


// save alert
$insert = "
INSERT INTO sos_alerts (user_id, created_at)
VALUES ('$user_db_id', NOW())
";

$conn->query($insert);


// response
echo json_encode([
"message"=>"SOS sent to caregiver",
"caregiver_phone"=>$phone
]);

?>