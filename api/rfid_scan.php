<?php
require_once '../config/database.php';
requireLogin('organizer');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']); 
    exit;
}

$scan_input = trim($data['scan_input'] ?? '');
if (!$scan_input) {
    echo json_encode(['success' => false, 'message' => 'No input received']); 
    exit;
}

// Hardcoded density threshold for allowing early claims (60%)
define('MAX_EARLY_CLAIM_DENSITY', 60);

// Fetch live traffic telemetry cache
$cacheFile = __DIR__ . '/traffic_cache.json';
$currentDensity = 0;

if (file_exists($cacheFile)) {
    $trafficData = json_decode(file_get_contents($cacheFile), true);
    $currentDensity = (int)($trafficData['density_percentage'] ?? 0);
}

try {
    $stmt = $pdo->prepare("CALL sp_rfid_scan(?)");
    $stmt->execute([$scan_input]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'No response from scanner']); 
        exit;
    }

    $result = $row['result'] ?? 'INVALID';
    $isEarlyArrival = ($result === 'EARLY_ARRIVAL');
    $isStationCongested = ($currentDensity >= MAX_EARLY_CLAIM_DENSITY);
    
    // Only allow early claim override if station density is under 60%
    $allowEarlyClaim = $isEarlyArrival && !$isStationCongested;

    $reason = $row['reason'] ?? '';
    if ($isEarlyArrival) {
        $reason = $isStationCongested 
            ? "Early Arrival — Station Congested ({$currentDensity}% density)" 
            : "Early Arrival — Low Station Density ({$currentDensity}% density)";
    }

    echo json_encode([
        'success'           => true,
        'result'            => $result,
        'reason'            => $reason,
        'student_name'      => $row['student_name'] ?? '',
        'event_name'        => $row['event_name'] ?? '',
        'slot_time'         => $row['slot_time'] ?? '',
        'reservation_id'    => $row['reservation_id'] ?? $row['id'] ?? null,
        'allow_early_claim' => $allowEarlyClaim,
        'current_density'   => $currentDensity
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>