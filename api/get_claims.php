<?php
require_once '../config/database.php';
requireLogin('student');
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT
        e.event_title AS title,
        e.distribution_location AS location,
        t.start_time,
        r.reservation_id
    FROM reservations r
    JOIN time_slots t ON r.slot_id = t.slot_id
    JOIN events e ON t.event_id = e.event_id
    WHERE r.claimer_id = ? AND r.status = 'reserved'
    ORDER BY t.start_time ASC
");
$stmt->execute([$user_id]);
$claims = $stmt->fetchAll();

$response = array_map(function ($claim) {
    return [
        'title' => $claim['title'],
        'location' => $claim['location'],
        'formatted_date' => date('M j, Y', strtotime($claim['start_time'])),
        'formatted_time' => date('g:i A', strtotime($claim['start_time'])),
        'reservation_id' => $claim['reservation_id'],
    ];
}, $claims);

echo json_encode($response);