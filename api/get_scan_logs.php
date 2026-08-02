<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            CONCAT(u.first_name, ' ', u.last_name) AS user_name,
            sl.event_name,
            CONCAT(
                DATE_FORMAT(sl.slot_start, '%b %d, %Y — %h:%i %p'),
                ' to ',
                DATE_FORMAT(sl.slot_end, '%h:%i %p')
            ) AS scheduled_slot,
            sl.status,
            sl.reason,
            sl.scanned_at
        FROM scan_logs sl
        JOIN users u ON u.id = sl.user_id
        ORDER BY sl.scanned_at DESC
        LIMIT 50
    ");
    $logs = $stmt->fetchAll();
    echo json_encode(['success' => true, 'logs' => $logs]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>