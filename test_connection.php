<?php
include 'config/database.php';

$conn = getDBConnection();

if ($conn) {
    echo "Database connected successfully!";
}
?>