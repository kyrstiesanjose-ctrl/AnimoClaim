<?php
// index.php (Root)

require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'organizer' || $_SESSION['role'] === 'admin') {
        header("Location: organizer/dashboard.php");
    } else {
        header("Location: student/index.php");
    }
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provided_token = $_POST['csrf_token'] ?? '';
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $provided_token)) {
        $error = "Invalid session token. Please refresh and try again.";
    } elseif (empty($login_id) || empty($password)) {
        $error = "Please enter both your ID/Email and Password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE dlsu_id = ? OR email = ? LIMIT 1");
        $stmt->execute([$login_id, $login_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user'] = [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'dlsu_id' => $user['dlsu_id'],
                'role' => $user['role']
            ];

            if ($user['role'] === 'organizer' || $user['role'] === 'admin') {
                header("Location: organizer/dashboard.php");
            } else {
                header("Location: student/index.php");
            }
            exit();
        } else {
            $error = "Invalid credentials. Please try again.";
        }
    }
}

// Load the visual template
require_once 'views/index_view.php';
?>