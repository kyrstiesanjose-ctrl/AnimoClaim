<?php
// student/event_details.php

require_once '../config/database.php';
requireLogin('student');

// Get the event ID and the logged-in user's ID
$event_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$claimer_id = $_SESSION['user_id'] ?? null;

if (!$event_id || !$claimer_id) {
    header("Location: index.php");
    exit();
}

try {
    // 1. Fetch Event Details
    $stmt = $pdo->prepare("
        SELECT 
            e.event_id,
            e.event_title AS title,
            e.event_date,
            e.distribution_location AS location,
            e.target_audience,
            e.image_url,
            o.org_name
        FROM events e
        LEFT JOIN organizations o ON e.org_id = o.org_id
        WHERE e.event_id = ?
        LIMIT 1
    ");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        die("Event not found.");
    }

    // 2. Fetch Items & Calculate Total Quantities
    $stmt_items = $pdo->prepare("SELECT * FROM items WHERE event_id = ?");
    $stmt_items->execute([$event_id]);
    $items = $stmt_items->fetchAll();

    $remain_qty = 0;
    $total_qty = 0;
    
    foreach ($items as $item) {
        $remain_qty += $item['remaining_balance'];
        $total_qty += $item['total_inventory'];
    }
    
    // Calculate percentage (preventing division by zero)
    $capacity_percent = ($total_qty > 0) ? round(($remain_qty / $total_qty) * 100) : 0;

    // The 'events' table lacks a description column, so we pull it from the
    // first item — except for Ticket events with multiple seat tiers, where
    // "the first item's description" (e.g. "General Admission Seat") would
    // misleadingly describe the whole event instead of just one tier.
    $isTicketEvent = !empty($items) && $items[0]['category'] === 'Ticket';
    if ($isTicketEvent) {
        $event['description'] = 'Choose your preferred seat section below. Ticket prices vary by section.';
    } else {
        $event['description'] = !empty($items) ? $items[0]['description'] : 'Join us for this exciting event!';
    }

    // 3. Fetch Time Slots with the exact aliases the view expects (id, max_capacity)
    $stmt_slots = $pdo->prepare("
        SELECT 
            slot_id AS id,
            start_time,
            end_time,
            capacity AS max_capacity,
            current_reservations
        FROM time_slots
        WHERE event_id = ?
        ORDER BY start_time ASC
    ");
    $stmt_slots->execute([$event_id]);
    $time_slots = $stmt_slots->fetchAll();

    // 4. Fetch User Reservations (Fixes the Fatal in_array Error)
    $stmt_res = $pdo->prepare("
        SELECT r.slot_id 
        FROM reservations r
        JOIN time_slots ts ON r.slot_id = ts.slot_id
        WHERE r.claimer_id = ? AND ts.event_id = ? AND r.status IN ('reserved', 'pending_payment', 'claimed')
    ");
    $stmt_res->execute([$claimer_id, $event_id]);
    
    // Fetch as a flat array of slot IDs
    $userReservedSlots = $stmt_res->fetchAll(PDO::FETCH_COLUMN);
    
    // Ensure it is always an array so in_array() doesn't throw a TypeError
    if (!$userReservedSlots) {
        $userReservedSlots = []; 
    }
    
    $hasReservedEvent = (count($userReservedSlots) > 0);

    // 5. Suspension check (the view's <script> block reads $isSuspended —
    //    leaving it undefined throws a warning that gets printed straight
    //    into the inline <script>, breaking its JS syntax entirely, which
    //    is why clicks stop doing anything anywhere on the page)
    $stmt_claimer = $pdo->prepare("SELECT status, strikes FROM claimers WHERE claimer_id = ? LIMIT 1");
    $stmt_claimer->execute([$claimer_id]);
    $claimer = $stmt_claimer->fetch();
    $isSuspended = $claimer && ($claimer['status'] === 'Suspended' || (int)$claimer['strikes'] >= 3);

} catch (\PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Load the view template
require_once '../views/student/event_details_view.php';
?>