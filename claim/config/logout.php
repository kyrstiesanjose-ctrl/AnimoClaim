<?php
// 1. Initialize the session
session_start();

// 2. Unset all session variables
$_SESSION = [];

// 3. Destroy the session cookie
// This is the most secure way to ensure the session is gone from the server
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destroy the session
session_destroy();

// 5. Redirect to your login page
header("Location: ../index.php");
exit();
?>