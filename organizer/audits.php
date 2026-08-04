<?php 
require_once '../config/database.php';
requireLogin('organizer');

// 1. Fetch Reservations Log (Added r.reservation_id AS id)
$logStmt = $pdo->query("
    SELECT 
        r.*, 
        r.reservation_id AS id,
        c.first_name, 
        c.last_name, 
        c.claimer_id AS dlsu_id, 
        c.claimer_id AS user_id,
        e.event_title AS title, 
        ts.start_time
    FROM reservations r
    JOIN claimers c ON r.claimer_id = c.claimer_id
    JOIN time_slots ts ON r.slot_id = ts.slot_id
    JOIN events e ON ts.event_id = e.event_id
    ORDER BY r.reservation_id DESC
");
$reservations = $logStmt->fetchAll();

// 2. Fetch Students and Claimers List
$studentStmt = $pdo->query("
    SELECT 
        c.*, 
        c.claimer_id AS id, 
        c.claimer_id AS dlsu_id
    FROM claimers c 
    LEFT JOIN organizers o ON c.claimer_id = o.claimer_id
    WHERE o.organizer_id IS NULL
    ORDER BY c.first_name ASC
");
$students = $studentStmt->fetchAll();

$currentPage = 'audits';

require_once '../views/organizer/audits_view.php';
?>