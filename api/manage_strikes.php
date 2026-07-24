<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$user_id = (int)($data['user_id'] ?? 0);
$action = $data['action'] ?? '';

try {
    $pdo->beginTransaction();
    
    if ($action === 'add') {
        // Manual organizer strike addition (linking to reservation 0 as a manual override)
        $ins = $pdo->prepare("INSERT INTO strike_logs (user_id, reservation_id, status) VALUES (?, 0, 'Manual Organizer Strike')");
        $ins->execute([$user_id]);
    } elseif ($action === 'remove') {
        // Remove the most recent strike
        $del = $pdo->prepare("DELETE FROM strike_logs WHERE user_id = ? ORDER BY strike_date DESC LIMIT 1");
        $del->execute([$user_id]);
    } elseif ($action === 'no-show') {
        $reservation_id = (int)($data['reservation_id'] ?? 0);
        
        $exp = $pdo->prepare("UPDATE reservations SET status = 'expired' WHERE id = ? AND user_id = ?");
        $exp->execute([$reservation_id, $user_id]);
        
        $ins = $pdo->prepare("INSERT INTO strike_logs (user_id, reservation_id, status) VALUES (?, ?, 'Unexcused No-Show')");
        $ins->execute([$user_id, $reservation_id]);
    } else {
        throw new Exception("Invalid action passed.");
    }
    
    // Count new total strikes
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM strike_logs WHERE user_id = ?");
    $countStmt->execute([$user_id]);
    $current_strikes = $countStmt->fetchColumn();

    // Auto-suspend if necessary
    $status = ($current_strikes >= 3) ? 'suspended' : 'active';
    $upd = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $upd->execute([$status, $user_id]);
    
    $pdo->commit();
    echo json_encode(['success' => true, 'new_strikes' => $current_strikes]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>