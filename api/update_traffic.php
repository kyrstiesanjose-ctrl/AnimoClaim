<?php
// claim/api/update_traffic.php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$inputData = json_decode(file_get_contents('php://input'), true);

if (isset($inputData['current_headcount'])) {
    $location = $inputData['location_name'] ?? 'John L. Gokongwei Jr. Innovation Center';
    $headcount = intval($inputData['current_headcount']);
    $maxCapacity = isset($inputData['max_capacity']) ? intval($inputData['max_capacity']) : 30;
    
    // Calculate crowd density percentage
    $density = min(100, round(($headcount / $maxCapacity) * 100));

    $dataToSave = [
        'campus' => $inputData['campus'] ?? 'laguna',
        'location_name' => $location,
        'current_headcount' => $headcount,
        'density_percentage' => $density,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Save live traffic metrics to cache JSON (fast, for the live-polling dashboard)
    file_put_contents(__DIR__ . '/traffic_cache.json', json_encode($dataToSave));

    // Also log to the database, but throttled to once every 60 seconds per location,
    // so we get a history for the map's density heatmap without flooding the table.
    $lastLogStmt = $pdo->prepare("
        SELECT recorded_at FROM crowd_traffic_logs
        WHERE location_name = ?
        ORDER BY recorded_at DESC
        LIMIT 1
    ");
    $lastLogStmt->execute([$location]);
    $lastLog = $lastLogStmt->fetchColumn();

    $shouldLog = !$lastLog || (strtotime($dataToSave['timestamp']) - strtotime($lastLog)) >= 60;

    if ($shouldLog) {
        $insertStmt = $pdo->prepare("
            INSERT INTO crowd_traffic_logs (campus, location_name, current_headcount, density_percentage, recorded_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertStmt->execute([
            $dataToSave['campus'],
            $dataToSave['location_name'],
            $dataToSave['current_headcount'],
            $dataToSave['density_percentage'],
            $dataToSave['timestamp'],
        ]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Traffic log updated successfully',
        'data' => $dataToSave
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data payload'
    ]);
}
?>