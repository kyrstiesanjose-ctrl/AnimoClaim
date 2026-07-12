<?php
session_start();
require_once('../config/database.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

// Default to user 2 (Dhens) for testing if not logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

$claims = [];

// Query to get only active reservations
$query = "
    SELECT 
        r.qr_code_hash, 
        e.title, 
        e.location, 
        e.image_url,
        ts.start_time, 
        ts.end_time 
    FROM reservations r
    JOIN event_time_slots ts ON r.time_slot_id = ts.id
    JOIN events e ON ts.event_id = e.id
    WHERE r.user_id = $user_id AND r.status = 'reserved'
    ORDER BY ts.start_time ASC
";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Format the times for the ticket UI
        $start = new DateTime($row['start_time']);
        $end = new DateTime($row['end_time']);
        
        $row['formatted_date'] = $start->format('l, F j, Y'); // e.g., Monday, March 16, 2026
        $row['formatted_time'] = $start->format('g:i A') . ' - ' . $end->format('g:i A'); // e.g., 9:00 AM - 11:00 AM
        
        $claims[] = $row;
    }
}

echo json_encode($claims);
exit();
?>