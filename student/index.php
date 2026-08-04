<?php
// student/index.php

require_once '../config/database.php';
requireLogin('student');

// Fetch events and items using the correct column name (remaining_balance)
try {
    $stmt = $pdo->query("
        SELECT e.*, i.*, i.remaining_balance AS remaining_quantity 
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