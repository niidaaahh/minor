<?php
require_once "database.php";
$conn = getDBConnection();

parse_str(file_get_contents("php://input"), $put_vars);
$id = intval($_GET['id'] ?? $put_vars['id'] ?? 0);

if ($id > 0) {
    $conn->query("UPDATE alerts SET status='Resolved' WHERE id=$id");
}
$conn->close();
echo json_encode(["success"=>true]);
?>