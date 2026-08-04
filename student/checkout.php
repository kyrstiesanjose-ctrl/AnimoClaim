<?php
// student/checkout.php
require_once '../config/database.php';
requireLogin('student');

$reservation_id = filter_input(INPUT_GET, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
$claimer_id = $_SESSION['user_id'] ?? null;

if (!$reservation_id || !$claimer_id) {
    header("Location: tickets.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT
        r.reservation_id, r.quantity, r.status AS reservation_status,
        i.item_id, i.category, i.description, i.price,
        e.event_title AS title, e.distribution_location AS location, e.event_date,
        ts.start_time, ts.end_time,
        c.email
    FROM reservations r
    JOIN items i ON r.item_id = i.item_id
    JOIN events e ON i.event_id = e.event_id
    JOIN time_slots ts ON r.slot_id = ts.slot_id
    JOIN claimers c ON r.claimer_id = c.claimer_id
    WHERE r.reservation_id = ? AND r.claimer_id = ?
    LIMIT 1
");
$stmt->execute([$reservation_id, $claimer_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    header("Location: tickets.php");
    exit();
}

if ($reservation['reservation_status'] !== 'pending_payment') {
    // Already paid (or cancelled/expired) — nothing to check out.
    header("Location: tickets.php");
    exit();
}

$total_amount = round(((float) $reservation['price']) * ((int) $reservation['quantity']), 2);
$currentPage = 'tickets';

require_once '../views/student/checkout_view.php';
