<?php
include 'includes/db.php';  // Adjust path if needed

// Change these to your desired admin username and password
$username = 'admin';
$password = 'admin123';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert or update admin user in database
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = ?");
$stmt->bind_param("sss", $username, $hashed_password, $hashed_password);

if ($stmt->execute()) {
    echo "Admin user created or updated successfully.<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
