<?php
// student/index.php

require_once '../config/database.php';
requireLogin('student');

try {
    // Fetch and map database columns to match what views/student/index_view.php expects
    $stmt = $pdo->query("
        SELECT 
            e.event_id,
            e.event_title AS title,
            e.distribution_location AS location,
            e.event_date AS first_slot_time,
            i.item_id,
            i.category,
            i.description,
            i.price,
            i.total_inventory,
            i.remaining_balance AS remaining_qty
        FROM events e 
        LEFT JOIN items i ON e.event_id = i.event_id 
        ORDER BY e.event_date ASC
    ");
    $events = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Load the student view template
require_once '../views/student/index_view.php';
?>