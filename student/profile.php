<?php 
require_once '../config/database.php';
requireLogin('student');

$user_id = $_SESSION['user_id'];

// Get User Profile & Strike Count
$stmt = $pdo->prepare("
    SELECT u.*, (SELECT COUNT(*) FROM strike_logs WHERE user_id = u.id) as total_strikes 
    FROM users u WHERE u.id = ?
");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

// Get ALL reservations[cite: 25]
$resStmt = $pdo->prepare("
    SELECT r.*, e.title, e.location, t.start_time 
    FROM reservations r 
    JOIN event_time_slots t ON r.time_slot_id = t.id 
    JOIN events e ON t.event_id = e.id 
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$resStmt->execute([$user_id]);
$allReservations = $resStmt->fetchAll();

$activeReservations = array_filter($allReservations, function($r) { return $r['status'] === 'reserved'; });
$pastReservations = array_filter($allReservations, function($r) { return $r['status'] !== 'reserved'; });
$strikesCount = (int)$currentUser['total_strikes'];

$currentPage = 'profile';

// Load the view
require_once '../views/student/profile_view.php';
?>