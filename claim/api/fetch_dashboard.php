<?php
session_start();
require_once('../config/database.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

// Use the logged-in user's ID. Falls back to 2 for local testing if no session.
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 2;

$response = [
    'user' => [
        'name' => 'User',
        'dlsu_id' => '',
        'program' => '',
        'college' => ''
    ],
    'stats' => ['total_claims' => 0, 'active_claims' => 0],
    'active_claims' => [],
    'history' => []
];

// 1. User info
$user_query = "SELECT dlsu_id, first_name, last_name, program, college FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
if ($user_result && ($row = mysqli_fetch_assoc($user_result))) {
    $response['user']['name'] = trim($row['first_name'] . ' ' . $row['last_name']);
    $response['user']['dlsu_id'] = $row['dlsu_id'];
    $response['user']['program'] = $row['program'] ?? '';
    $response['user']['college'] = $row['college'] ?? '';
}

// 2. Active claims (reserved, not yet claimed)
$active_query = "
    SELECT r.qr_code_hash, e.title, e.location, ts.start_time
    FROM reservations r
    JOIN event_time_slots ts ON r.time_slot_id = ts.id
    JOIN events e ON ts.event_id = e.id
    WHERE r.user_id = $user_id AND r.status = 'reserved'
    ORDER BY ts.start_time ASC
";
$active_result = mysqli_query($conn, $active_query);
if ($active_result) {
    while ($row = mysqli_fetch_assoc($active_result)) {
        $date = new DateTime($row['start_time']);
        $response['active_claims'][] = [
            'claim_code' => $row['qr_code_hash'],
            'event_title' => $row['title'],
            'location' => $row['location'],
            'claim_date' => $date->format('M d, h:i A')
        ];
    }
}

// 3. History (claimed / expired)
$history_query = "
    SELECT e.title, r.status
    FROM reservations r
    JOIN event_time_slots ts ON r.time_slot_id = ts.id
    JOIN events e ON ts.event_id = e.id
    WHERE r.user_id = $user_id AND r.status != 'reserved'
    ORDER BY ts.start_time DESC
";
$history_result = mysqli_query($conn, $history_query);
if ($history_result) {
    while ($row = mysqli_fetch_assoc($history_result)) {
        $response['history'][] = [
            'event_title' => $row['title'],
            'status' => $row['status']
        ];
    }
}

$response['stats']['active_claims'] = count($response['active_claims']);
$response['stats']['total_claims'] = $response['stats']['active_claims'] + count($response['history']);

echo json_encode($response);
exit();
?>