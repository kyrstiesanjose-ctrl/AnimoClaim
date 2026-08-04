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
        // Updated to use claimer_id instead of dlsu_id
        $stmt = $pdo->prepare("
            SELECT c.*, o.role_level 
            FROM claimers c 
            LEFT JOIN organizers o ON c.claimer_id = o.claimer_id 
            WHERE c.claimer_id = ? OR c.email = ? 
            LIMIT 1
        ");
        $stmt->execute([$login_id, $login_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $role = 'student';
            if (!empty($user['role_level'])) {
                $role = (strtolower($user['role_level']) === 'administrator') ? 'admin' : 'organizer';
            }

            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['claimer_id'];
            $_SESSION['role'] = $role;
            $_SESSION['user'] = [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'claimer_id' => $user['claimer_id'],
                'role' => $role
            ];

            if ($role === 'organizer' || $role === 'admin') {
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