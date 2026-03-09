<?php

function getDBConnection() {

    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "elderly_care_db";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>