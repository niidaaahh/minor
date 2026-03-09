<?php
include 'database.php';

$conn = getDBConnection();

// Check if admin already exists
$result = $conn->query("SELECT * FROM users WHERE username='admin'");
if ($result->num_rows > 0) {
    echo "Admin user already exists!";
} else {
    // Insert admin user
    $sql = "INSERT INTO users (username, password, full_name, role, email, phone) 
            VALUES ('admin', 'admin123', 'Admin User', 'admin', 'admin@example.com', '1234567890')";
    if ($conn->query($sql) === TRUE) {
        echo "Admin user created successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>