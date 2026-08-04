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

// Booked tickets awaiting payment confirmation, listed separately so students can complete checkout.
$stmt_pending = $pdo->prepare("
    SELECT r.reservation_id, r.quantity, e.event_title AS title, e.distribution_location AS location,
           t.start_time, i.price, i.description
    FROM reservations r
    JOIN items i ON r.item_id = i.item_id
    JOIN time_slots t ON r.slot_id = t.slot_id
    JOIN events e ON t.event_id = e.event_id
    WHERE r.claimer_id = ? AND r.status = 'pending_payment'
    ORDER BY t.start_time ASC
");
$stmt_pending->execute([$_SESSION['user_id']]);
$pendingPayments = $stmt_pending->fetchAll();

$currentPage = 'tickets';

require_once '../views/student/tickets_view.php';
?>
