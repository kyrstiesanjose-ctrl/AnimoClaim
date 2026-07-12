<?php
require_once('../config/database.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

$events = [];

// Inside api/get_events.php
$query = "SELECT id, title, organizer_id AS organizer, location, image_url, category FROM events WHERE is_active = 1";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

echo json_encode($events);
exit();
?>