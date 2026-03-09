<?php
include "database.php";

$conn = getDBConnection();

$sql = "SELECT ml.id, m.name, ml.scheduled_time, ml.status
        FROM medication_logs ml
        JOIN medicines m ON ml.medicine_id = m.id
        WHERE DATE(ml.scheduled_time) = CURDATE()";

$result = $conn->query($sql);

$meds = [];

while($row = $result->fetch_assoc()){
    $meds[] = $row;
}

echo json_encode($meds);
?>