<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = trim($_POST['fname']);
    $lname     = trim($_POST['lname']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password !== $cpassword) {
        echo "❌ Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssss", $fname, $lname, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: ../login.html");
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
