<?php 
require_once '../config/database.php';
requireLogin('student');

$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.location, t.start_time 
    FROM reservations r 
    JOIN event_time_slots t ON r.time_slot_id = t.id 
    JOIN events e ON t.event_id = e.id 
    WHERE r.user_id = ? AND r.status = 'reserved'
");
$stmt->execute([$_SESSION['user_id']]);
$claims = $stmt->fetchAll();

$currentPage = 'tickets';

// Load the view[cite: 29]
require_once '../views/student/tickets_view.php';
?>