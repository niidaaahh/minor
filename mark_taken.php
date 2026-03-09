<?php
include "database.php";

$conn = getDBConnection();

$id = $_POST['id'];

$sql = "UPDATE medication_logs
        SET status='Taken',
        taken_time = NOW()
        WHERE id = $id";

if($conn->query($sql)){
    echo "success";
}else{
    echo "error";
}
?>