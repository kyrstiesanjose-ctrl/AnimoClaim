<?php
require_once('../config/database.php');

// Get all users
$query = mysqli_query($conn, "SELECT id, password FROM users");
while ($user = mysqli_fetch_assoc($query)) {
    // Check if it's already a hash (starts with $2y$)
    if (strpos($user['password'], '$2y$') !== 0) {
        $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
        $uid = $user['id'];
        mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE id = '$uid'");
        echo "User $uid rehashed successfully.<br>";
    }
}
?>