<?php
require_once '../config/database.php';
requireLogin('student');

$stmt = $pdo->query("
    SELECT e.*,
           COALESCE((SELECT SUM(remaining_quantity) FROM inventory WHERE event_id = e.id), 0) as remaining_qty,
           (SELECT start_time FROM event_time_slots WHERE event_id = e.id ORDER BY start_time ASC LIMIT 1) as first_slot_time
    FROM events e
    WHERE e.is_active = 1
");
$events = $stmt->fetchAll();

$currentPage = 'index';
require_once '../views/student/index_view.php';
?>