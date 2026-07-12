<?php
// rehash_passwords.php - run once, then delete
$conn = new mysqli("localhost", "root", "", "your_db_name");

$result = $conn->query("SELECT id, password FROM users");
while ($row = $result->fetch_assoc()) {
    $plainPassword = $row['password']; // assuming still plain-text
    $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
    $conn->query("UPDATE users SET password = '$hashed' WHERE id = " . $row['id']);
}
echo "Done rehashing.";