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
    $stmt = $pdo->prepare("SELECT event_id, current_reservations, max_capacity FROM event_time_slots WHERE id = ? FOR UPDATE");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch();

    if (!$slot || $slot['current_reservations'] >= $slot['max_capacity']) {
        throw new Exception("Slot is full or invalid.");
    }
    
    // Get the inventory ID for this event
    $invStmt = $pdo->prepare("SELECT id FROM inventory WHERE event_id = ? LIMIT 1");
    $invStmt->execute([$slot['event_id']]);
    $inventory_id = $invStmt->fetchColumn();

    if (!$inventory_id) {
        throw new Exception("No inventory linked to this event.");
    }

    $qr_hash = 'AC-' . rand(100,999) . '-' . strtoupper(substr(uniqid(), -6));
    
    // Insert with inventory_id
    $ins = $pdo->prepare("INSERT INTO reservations (user_id, time_slot_id, inventory_id, qr_code_hash) VALUES (?, ?, ?, ?)");
    $ins->execute([$user_id, $slot_id, $inventory_id, $qr_hash]);
    
    // Update slot reservations count
    $upd = $pdo->prepare("UPDATE event_time_slots SET current_reservations = current_reservations + 1 WHERE id = ?");
    $upd->execute([$slot_id]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'qr_hash' => $qr_hash]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>