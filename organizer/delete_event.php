<?php
require_once '../config/database.php';
requireLogin('organizer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];
    $claimer_id = $_SESSION['user_id'];

    try {
        // 1. Verify that the logged-in organizer's organization owns this event
        $orgCheck = $pdo->prepare("
            SELECT e.event_id 
            FROM events e
            JOIN organizers o ON e.org_id = o.org_id
            WHERE e.event_id = ? AND o.claimer_id = ?
        ");
        $orgCheck->execute([$event_id, $claimer_id]);
        if (!$orgCheck->fetch()) {
            throw new Exception("Unauthorized action or event not found.");
        }

        // 2. Strict Check: Ensure NO payments exist for this event
        $paymentCheck = $pdo->prepare("
            SELECT COUNT(*) 
            FROM payments p
            JOIN reservations r ON p.reservation_id = r.reservation_id
            JOIN items i ON r.item_id = i.item_id
            WHERE i.event_id = ?
        ");
        $paymentCheck->execute([$event_id]);
        $paymentCount = $paymentCheck->fetchColumn();

        if ($paymentCount > 0) {
            throw new Exception("Cannot delete this campaign because student payments have already been processed.");
        }

        // 3. Proceed with deletion (Cascade handles items, slots, and unpaid reservations)
        $deleteStmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
        $deleteStmt->execute([$event_id]);

        $_SESSION['success_msg'] = "Campaign successfully cancelled and deleted.";
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Deletion failed: " . $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}