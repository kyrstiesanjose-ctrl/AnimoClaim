<?php
session_start();
require_once('../config/database.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

// We will use the logged-in user's ID. For testing, we default to 2 (Dhens) if the session is not set.
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

$response = [
    'user' => [],
    'stats' => ['total_claims' => 0, 'active_claims' => 0],
    'active_reservations' => [],
    'history' => []
];

// Query 1: Get User Info
$user_query = "SELECT dlsu_id, first_name, last_name, email FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
if ($user_result) {
    $response['user'] = mysqli_fetch_assoc($user_result);
}

// Query 2: Get Active Reservations (My Claims)
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
        // Format the date for the frontend
        $date = new DateTime($row['start_time']);
        $row['formatted_time'] = $date->format('M d, h:i A');
        $response['active_reservations'][] = $row;
    }
}
$response['stats']['active_claims'] = count($response['active_reservations']);

// Query 3: Get History
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
        $response['history'][] = $row;
    }
}

$response['stats']['total_claims'] = $response['stats']['active_claims'] + count($response['history']);

echo json_encode($response);
exit();
?>