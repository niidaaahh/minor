<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['userId']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $conn = getDBConnection();

    // Prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user) {
        // Plain text password (replace with password_verify if hashed)
        if ($password === $user['password']) {

            // Store session variables
            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['username'] = $user['username']; // needed for get_user_profile.php

            // Redirect based on role
            switch ($role) {
                case "user":
                    header("Location: ../elderly-care.html"); // stays the same
                    break;
                case "caregiver":
                    header("Location: ../caregiver-home.html"); // corrected path
                    break;
                case "admin":
                    header("Location: ../admin-dashboard.html"); // stays the same
                    break;
                default:
                    echo "Unknown role";
                    exit;
            }
            exit;
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found or role mismatch";
    }

    $stmt->close();
    $conn->close();
}
?>