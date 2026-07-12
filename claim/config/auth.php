<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); exit();
}
// Role-based protection
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, '/organizer/') !== false && $_SESSION['role'] !== 'organizer' && $_SESSION['role'] !== 'admin') {
    header("Location: ../student/events.php"); exit();
}
?>