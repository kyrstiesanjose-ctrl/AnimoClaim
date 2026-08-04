<?php 
require_once '../config/database.php';
requireLogin('student');

$stmt = $pdo->prepare("
    SELECT r.*, e.event_title AS title, e.distribution_location AS location, t.start_time 
    FROM reservations r 
    JOIN time_slots t ON r.slot_id = t.slot_id 
    JOIN events e ON t.event_id = e.event_id 
    WHERE r.claimer_id = ? AND r.status = 'reserved'
");
$stmt->execute([$_SESSION['user_id']]);
$claims = $stmt->fetchAll();

$currentPage = 'tickets';

require_once '../views/student/tickets_view.php';
?>