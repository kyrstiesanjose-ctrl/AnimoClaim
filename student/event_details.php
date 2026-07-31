<?php 
require_once '../config/database.php';
requireLogin('student');

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_active = 1");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: /claim/student/index.php");
    exit;
}

$invStmt = $pdo->prepare("SELECT SUM(total_quantity) as total_qty, SUM(remaining_quantity) as remaining_qty FROM inventory WHERE event_id = ?");
$invStmt->execute([$event_id]);
$inventory = $invStmt->fetch();

$total_qty = (int)($inventory['total_qty'] ?? 0);
$remain_qty = (int)($inventory['remaining_qty'] ?? 0);
$capacity_percent = $total_qty > 0 ? round(($remain_qty / $total_qty) * 100) : 0;

$slotStmt = $pdo->prepare("SELECT * FROM event_time_slots WHERE event_id = ? ORDER BY start_time ASC");
$slotStmt->execute([$event_id]);
$time_slots = $slotStmt->fetchAll();

$resStmt = $pdo->prepare("
    SELECT t.id 
    FROM reservations r
    JOIN event_time_slots t ON r.time_slot_id = t.id
    WHERE r.user_id = ? AND t.event_id = ? AND r.status = 'reserved'
");
$resStmt->execute([$_SESSION['user_id'], $event_id]);
$userReservedSlots = $resStmt->fetchAll(PDO::FETCH_COLUMN);
$hasReservedEvent = count($userReservedSlots) > 0;

$strikeStmt = $pdo->prepare("SELECT COUNT(*) FROM strike_logs WHERE user_id = ?");
$strikeStmt->execute([$_SESSION['user_id']]);
$strikes = $strikeStmt->fetchColumn();
$isSuspended = $strikes >= 3;

$currentPage = 'event_details';

require_once '../views/student/event_details_view.php';
?>