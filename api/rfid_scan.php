<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); exit;
}

$scan_input = trim($data['scan_input'] ?? '');
if (!$scan_input) {
    echo json_encode(['success' => false, 'message' => 'No input received']); exit;
}

try {
    $stmt = $pdo->prepare("CALL sp_rfid_scan(?)");
    $stmt->execute([$scan_input]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'No response from scanner']); exit;
    }

    echo json_encode([
        'success'      => true,
        'result'       => $row['result'],
        'reason'       => $row['reason'],
        'student_name' => $row['student_name'],
        'event_name'   => $row['event_name'],
        'slot_time'    => $row['slot_time'],
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>