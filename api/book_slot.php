<?php
require_once '../config/database.php';
requireLogin('student');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); exit;
}

$slot_id = (int)$data['time_slot_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();
    
    // Lock slot for validation
    $stmt = $pdo->prepare("SELECT event_id, current_reservations, capacity FROM time_slots WHERE slot_id = ? FOR UPDATE");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch();

    if (!$slot || $slot['current_reservations'] >= $slot['capacity']) {
        throw new Exception("Slot is full or invalid.");
    }
    
    // Get the item for this event
    $itemStmt = $pdo->prepare("SELECT item_id FROM items WHERE event_id = ? LIMIT 1");
    $itemStmt->execute([$slot['event_id']]);
    $item_id = $itemStmt->fetchColumn();

    if (!$item_id) {
        throw new Exception("No item linked to this event.");
    }
    
    // Insert reservation
    $ins = $pdo->prepare("INSERT INTO reservations (claimer_id, item_id, slot_id) VALUES (?, ?, ?)");
    $ins->execute([$user_id, $item_id, $slot_id]);
    
    // Update slot reservations count
    $upd = $pdo->prepare("UPDATE time_slots SET current_reservations = current_reservations + 1 WHERE slot_id = ?");
    $upd->execute([$slot_id]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'reservation_id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>