<?php
$conn = new mysqli("localhost", "root", "", "animo_claim"); // match your db config exactly

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Give everyone the same test password for now — change later
$plainPassword = "password123";
$hashed = password_hash($plainPassword, PASSWORD_DEFAULT);

$conn->query("UPDATE users SET password = '$hashed'");

echo "All passwords reset to: $plainPassword (hashed in DB)";
$conn->close();