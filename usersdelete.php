<?php
require_once "database.php";
$conn = getDBConnection();

$id = intval($_GET['id']);
$conn->query("DELETE FROM users WHERE id=$id");
$conn->close();
echo json_encode(["success"=>true]);
?>