<?php
// config/database.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$db   = 'animo_claim';
$user = 'root';
$pass = ''; 
$port = '3307';
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
        // Force lowercase to prevent "Student" vs "student" bugs
        $currentRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
        $requiredRole = strtolower($requiredRole);
        
        // Boot Organizers/Admins out of Student views
        if ($requiredRole === 'student' && $currentRole !== 'student') {
            header("Location: /claim/organizer/dashboard.php");
            exit();
        }

        // Boot Students out of Organizer views
        if ($requiredRole === 'organizer' && $currentRole !== 'organizer' && $currentRole !== 'admin') {
            header("Location: /claim/student/index.php");
            exit();
        }
    }
}