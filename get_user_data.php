<?php
include "database.php";

$conn = getDBConnection();

$sql = "SELECT u.name, p.age, p.relation
        FROM users u
        JOIN user_profiles p ON u.id = p.user_id
        WHERE u.role='user'
        LIMIT 1";

$result = $conn->query($sql);

echo json_encode($result->fetch_assoc());
?>