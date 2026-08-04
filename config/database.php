<?php
// config/database.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'ccscloud.dlsu.edu.ph';
$db   = 'animo_claim_db'; 
$user = 'CBDBADM01';
$pass = 'y9pSAee2MURj'; 
$port = '22003';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Global authentication function
function requireLogin($requiredRole = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: /claim/index.php");
        exit();
    }

    if ($requiredRole) {
        $currentRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
        $requiredRole = strtolower($requiredRole);
        
        if ($requiredRole === 'student' && $currentRole !== 'student') {
            header("Location: /claim/organizer/dashboard.php");
            exit();
        }

        if ($requiredRole === 'organizer' && $currentRole !== 'organizer' && $currentRole !== 'admin') {
            header("Location: /claim/student/index.php");
            exit();
        }
    }
}
?>