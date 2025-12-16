<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "beautyshop";  // make sure this DB exists

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(" Connection failed: " . $conn->connect_error);
}
?>
