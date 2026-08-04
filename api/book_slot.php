<?php
require_once '../config/database.php';
requireLogin('student');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); exit;
}

$slot_id = (int)$data['time_slot_id'];
$item_id = (int)($data['item_id'] ?? 0);
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

   // Resolve the reserved item. The client must specify which item,
// since an event can have multiple (e.g. General Admission,
// Upper Box, Lower Box). Using the first item is no longer valid.
    if ($item_id > 0) {
        $itemStmt = $pdo->prepare("SELECT item_id, remaining_balance FROM items WHERE item_id = ? AND event_id = ? FOR UPDATE");
        $itemStmt->execute([$item_id, $slot['event_id']]);
        $item = $itemStmt->fetch();
    } else {
        // Back-compat: no item_id sent (older client, or an event with a
        // single item) — fall back to "the only item for this event".
        $itemStmt = $pdo->prepare("SELECT item_id, remaining_balance FROM items WHERE event_id = ? FOR UPDATE");
        $itemStmt->execute([$slot['event_id']]);
        $item = $itemStmt->fetch();
    }

    if (!$item) {
        throw new Exception("Invalid ticket/item selected.");
    }
    if ($item['remaining_balance'] <= 0) {
        throw new Exception("That section/item is sold out.");
    }

    // Insert reservation
    $ins = $pdo->prepare("INSERT INTO reservations (claimer_id, item_id, slot_id) VALUES (?, ?, ?)");
    $ins->execute([$user_id, $item['item_id'], $slot_id]);

    // Deduct one seat/unit from this specific item's remaining stock
    $updItem = $pdo->prepare("UPDATE items SET remaining_balance = remaining_balance - 1 WHERE item_id = ?");
    $updItem->execute([$item['item_id']]);

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