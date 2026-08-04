<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); exit;
}

$qr_hash = $data['qr_hash'] ?? '';

try {
    $pdo->beginTransaction();

    // Find the reservation and lock it
    $stmt = $pdo->prepare("SELECT id, inventory_id FROM reservations WHERE qr_code_hash = ? AND status = 'reserved' FOR UPDATE");
    $stmt->execute([$qr_hash]);
    $reservation = $stmt->fetch();

    if ($reservation) {
        // Mark claimed and set timestamp
        $updateRes = $pdo->prepare("UPDATE reservations SET status = 'claimed', claimed_at = NOW() WHERE id = ?");
        $updateRes->execute([$reservation['id']]);

        // Deduct from inventory
        $updateInv = $pdo->prepare("UPDATE inventory SET remaining_quantity = remaining_quantity - 1 WHERE id = ?");
        $updateInv->execute([$reservation['inventory_id']]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Invalid or already claimed QR Code.');
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>