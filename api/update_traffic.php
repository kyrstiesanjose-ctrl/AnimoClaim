<?php
// claim/api/update_traffic.php

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

    // Save live traffic metrics to cache JSON
    file_put_contents(__DIR__ . '/traffic_cache.json', json_encode($dataToSave));

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