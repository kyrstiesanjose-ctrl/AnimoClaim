<?php 
require_once '../config/database.php';
requireLogin('organizer');

$error_msg = null;
$success_msg = null;

// Grab flash messages from create or delete actions
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Safely fetch dashboard metrics & campaigns
try {
    $activeCampaigns = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= NOW()")->fetchColumn();
    $totalReserved = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'reserved'")->fetchColumn();
    $itemsClaimed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'claimed'")->fetchColumn();
    $totalExpired = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'expired'")->fetchColumn();

    $totalReservationsEver = $totalReserved + $itemsClaimed + $totalExpired;
    $noShowRate = $totalReservationsEver > 0 ? round(($totalExpired / $totalReservationsEver) * 100) : 0;

    $campaignsStmt = $pdo->query("
        SELECT e.event_id AS id, 
               e.event_title AS title, 
               e.event_date AS created_at, 
               e.distribution_location AS location,
               (SELECT category FROM items WHERE event_id = e.event_id LIMIT 1) AS category,
            COALESCE((SELECT SUM(capacity) FROM time_slots WHERE event_id = e.event_id), 0) as total_capacity,
            COALESCE((SELECT SUM(current_reservations) FROM time_slots WHERE event_id = e.event_id), 0) as total_reservations,
            (SELECT COUNT(*) FROM time_slots WHERE event_id = e.event_id) as slot_count
        FROM events e
        WHERE e.event_date >= NOW()
        ORDER BY e.event_date ASC
    ");
    $campaigns = $campaignsStmt->fetchAll();
} catch (Exception $e) {
    $activeCampaigns = 0;
    $totalReserved = 0;
    $itemsClaimed = 0;
    $totalExpired = 0;
    $noShowRate = 0;
    $campaigns = [];
}

$currentPage = 'dashboard';
require_once '../views/organizer/dashboard_view.php';
?>