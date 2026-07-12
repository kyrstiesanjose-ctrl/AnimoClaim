<?php
session_start();

require_once('../config/database.php');

// Basic validation
if (!isset($_POST['dlsu_id']) || !isset($_POST['password'])) {
    header("Location: ../index.php?error=missing_fields");
    exit();
}

$dlsu_id = mysqli_real_escape_string($conn, $_POST['dlsu_id']);
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE dlsu_id = '$dlsu_id' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    // Check account status
    if ($user['status'] !== 'active') {
        header("Location: ../index.php?error=inactive_account");
        exit();
    }

    // Set session variables
    $_SESSION['logged_in']  = true;
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['dlsu_id']    = $user['dlsu_id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['role']       = $user['role'];

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($user['role'] === 'organizer') {
        header("Location: ../organizer/dashboard.php");
    } else {
        header("Location: ../student/events.php");
    }
    exit();

} else {
    header("Location: ../index.php?error=invalid_credentials");
    exit();
}
?>