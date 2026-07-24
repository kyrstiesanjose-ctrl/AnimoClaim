<?php
require_once '../config/database.php';
requireLogin('student');
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT
        e.title,
        e.location,
        e.image_url,
        t.start_time,
        r.qr_code_hash
    FROM reservations r
    JOIN event_time_slots t ON r.time_slot_id = t.id
    JOIN events e ON t.event_id = e.id
    WHERE r.user_id = ? AND r.status = 'reserved'
    ORDER BY t.start_time ASC
");
$stmt->execute([$user_id]);
$claims = $stmt->fetchAll();

$response = array_map(function ($claim) {
    return [
        'title' => $claim['title'],
        'location' => $claim['location'],
        'image_url' => $claim['image_url'] ? '/claim/assets/pictures/' . $claim['image_url'] : null,
        'formatted_date' => date('M j, Y', strtotime($claim['start_time'])),
        'formatted_time' => date('g:i A', strtotime($claim['start_time'])),
        'qr_code_hash' => $claim['qr_code_hash'],
    ];
}, $claims);

echo json_encode($response);