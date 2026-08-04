<?php 
require_once '../config/database.php';
requireLogin('student');

$user_id = $_SESSION['user_id'];

// Get User Profile & Strike Count
$stmt = $pdo->prepare("
    SELECT c.*, (SELECT COUNT(*) FROM strike_logs WHERE claimer_id = c.claimer_id) as total_strikes 
    FROM claimers c WHERE c.claimer_id = ?
");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

// Get ALL reservations
$resStmt = $pdo->prepare("
    SELECT r.*, e.event_title AS title, e.distribution_location AS location, t.start_time 
    FROM reservations r 
    JOIN time_slots t ON r.slot_id = t.slot_id 
    JOIN events e ON t.event_id = e.event_id 
    WHERE r.claimer_id = ?
    ORDER BY r.reservation_id DESC
");
$resStmt->execute([$user_id]);
$allReservations = $resStmt->fetchAll();

$activeReservations = array_filter($allReservations, function($r) { return $r['status'] === 'reserved'; });
$pastReservations = array_filter($allReservations, function($r) { return $r['status'] !== 'reserved'; });
$strikesCount = (int)$currentUser['total_strikes'];

$currentPage = 'profile';

require_once '../views/student/profile_view.php';
?>