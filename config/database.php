<?php
// Start the session for authentication
session_start();

// Database credentials for default XAMPP
$host = '127.0.0.1';
$db   = 'animo_claim';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // This is the exact variable your index.php is looking for!
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If the database connection fails, it will halt and show you exactly why
    die("Database Connection Failed: " . $e->getMessage());
}

// Generate CSRF Token for security if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper auth redirect function used across the app
function requireLogin($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /claim/index.php");
        exit;
    }
    if ($role && $_SESSION['role'] !== $role) {
        $redirect = $_SESSION['role'] === 'student' ? '/claim/student/index.php' : '/claim/organizer/dashboard.php';
        header("Location: " . $redirect);
        exit;
    }
}
?>