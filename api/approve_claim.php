<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); 
    exit;
}

$qr_hash = trim($data['qr_hash'] ?? '');
$reservation_id = (int)($data['reservation_id'] ?? 0);
$override_early = (bool)($data['override_early'] ?? false);

try {
    $pdo->beginTransaction();

    // Lock and retrieve reservation record
    if ($reservation_id > 0) {
        $stmt = $pdo->prepare("SELECT reservation_id, item_id, inventory_id FROM reservations WHERE reservation_id = ? AND status = 'reserved' FOR UPDATE");
        $stmt->execute([$reservation_id]);
    } else if ($qr_hash !== '') {
        $stmt = $pdo->prepare("SELECT reservation_id, item_id, inventory_id FROM reservations WHERE qr_code_hash = ? AND status = 'reserved' FOR UPDATE");
        $stmt->execute([$qr_hash]);
    } else {
        throw new Exception('Missing reservation identifier.');
    }

    $reservation = $stmt->fetch();

    if ($reservation) {
        $resId = $reservation['reservation_id'];

        // Mark as claimed and timestamp
        $updateRes = $pdo->prepare("UPDATE reservations SET status = 'claimed', claimed_at = NOW() WHERE reservation_id = ?");
        $updateRes->execute([$resId]);

        // Deduct inventory balance
        if (!empty($reservation['item_id'])) {
            $updateInv = $pdo->prepare("UPDATE items SET remaining_balance = remaining_balance - 1 WHERE item_id = ? AND remaining_balance > 0");
            $updateInv->execute([$reservation['item_id']]);
        } elseif (!empty($reservation['inventory_id'])) {
            $updateInv = $pdo->prepare("UPDATE inventory SET remaining_quantity = remaining_quantity - 1 WHERE id = ?");
            $updateInv->execute([$reservation['inventory_id']]);
        }

        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => $override_early ? 'Early claim approved!' : 'Claim approved!'
        ]);
    } else {
        throw new Exception('Invalid or already claimed reservation.');
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>