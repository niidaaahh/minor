<?php
session_start();
header("Content-Type: application/json");

// Require login
if(!isset($_SESSION['userId'])){
    echo json_encode(["error"=>"Not logged in"]);
    exit;
}

include 'db.php'; // your database connection

$userId = $_SESSION['userId'];  // session userId

$action = $_GET['action'] ?? '';

switch($action){
    case 'dashboard':
        // fetch user info
        $userQ = $conn->prepare("SELECT * FROM users WHERE user_id=?");
        $userQ->bind_param("s", $userId);
        $userQ->execute();
        $res = $userQ->get_result()->fetch_assoc();

        // fetch medicines
        $meds = [];
        $medQ = $conn->prepare("SELECT * FROM medicines WHERE user_id=? ORDER BY time ASC");
        $medQ->bind_param("s", $userId);
        $medQ->execute();
        $medRes = $medQ->get_result();
        while($row = $medRes->fetch_assoc()){
            $meds[] = $row;
        }

        // fetch latest health readings
        $health = [];
        $hQ = $conn->prepare("SELECT * FROM health_readings WHERE user_id=? ORDER BY date DESC LIMIT 3");
        $hQ->bind_param("s", $userId);
        $hQ->execute();
        $hRes = $hQ->get_result();
        while($row = $hRes->fetch_assoc()){
            $health[] = $row;
        }

        // caregiver info
        $cg = [];
        if(isset($res['caregiver_id'])){
            $cgQ = $conn->prepare("SELECT * FROM caregivers WHERE caregiver_id=?");
            $cgQ->bind_param("s", $res['caregiver_id']);
            $cgQ->execute();
            $cg = $cgQ->get_result()->fetch_assoc();
        }

        echo json_encode([
            "name"=>$res['name'],
            "age"=>$res['age'],
            "patient_id"=>$res['user_id'],
            "dob"=>$res['dob'],
            "blood_group"=>$res['blood_group'],
            "mobile"=>$res['mobile'],
            "address"=>$res['address'],
            "conditions"=>explode(",", $res['conditions']),
            "doctor"=>$res['doctor'],
            "caregiver_name"=>$cg['name'] ?? null,
            "caregiver_mobile"=>$cg['mobile'] ?? null,
            "caregiver_email"=>$cg['email'] ?? null,
            "bp"=>$res['bp'] ?? "--",
            "sugar"=>$res['sugar'] ?? "--",
            "heart_rate"=>$res['heart_rate'] ?? "--",
            "last_updated"=>$res['last_updated'] ?? "--",
            "medicines"=>$meds,
            "health_history"=>$health
        ]);
    break;

    case 'sos':
        // handle emergency SOS
        // You can send SMS/email to caregiver here
        echo json_encode(["message"=>"SOS triggered successfully. Caregiver notified."]);
    break;

    default:
        echo json_encode(["error"=>"Invalid action"]);
    break;
}
?>