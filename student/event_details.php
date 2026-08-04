<?php 
require_once '../config/database.php';
requireLogin('student');

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("
    SELECT event_id AS id, event_title AS title, distribution_location AS location, 
           event_date, NULL AS image_url
    FROM events WHERE event_id = ?
");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: /claim/student/index.php");
    exit;
}

// Description + stock totals now come from items (inventory table is just an adjustment log)
$itemStmt = $pdo->prepare("
    SELECT description, SUM(total_inventory) as total_qty, SUM(remaining_balance) as remain_qty
    FROM items WHERE event_id = ?
");
$itemStmt->execute([$event_id]);
$itemData = $itemStmt->fetch();

$event['description'] = $itemData['description'] ?? 'No description available.';
$total_qty = (int)($itemData['total_qty'] ?? 0);
$remain_qty = (int)($itemData['remain_qty'] ?? 0);
$capacity_percent = $total_qty > 0 ? round(($remain_qty / $total_qty) * 100) : 0;

$slotStmt = $pdo->prepare("
    SELECT slot_id AS id, start_time, end_time, capacity AS max_capacity, current_reservations 
    FROM time_slots WHERE event_id = ? ORDER BY start_time ASC
");
$slotStmt->execute([$event_id]);
$time_slots = $slotStmt->fetchAll();

$resStmt = $pdo->prepare("
    SELECT t.slot_id 
    FROM reservations r
    JOIN time_slots t ON r.slot_id = t.slot_id
    WHERE r.claimer_id = ? AND t.event_id = ? AND r.status = 'reserved'
");
$resStmt->execute([$_SESSION['user_id'], $event_id]);
$userReservedSlots = $resStmt->fetchAll(PDO::FETCH_COLUMN);
$hasReservedEvent = count($userReservedSlots) > 0;

$strikeStmt = $pdo->prepare("SELECT COUNT(*) FROM strike_logs WHERE claimer_id = ?");
$strikeStmt->execute([$_SESSION['user_id']]);
$strikes = $strikeStmt->fetchColumn();
$isSuspended = $strikes >= 3;

$currentPage = 'event_details';

require_once '../views/student/event_details_view.php';
?>