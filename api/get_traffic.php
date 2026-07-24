<?php
// claim/api/get_traffic.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cacheFile = __DIR__ . '/traffic_cache.json';

if (file_exists($cacheFile)) {
    $trafficData = json_decode(file_get_contents($cacheFile), true);
    echo json_encode([
        'status' => 'success',
        'data' => [$trafficData]
    ]);
} else {
    // Default fallback values if stream hasn't started yet
    echo json_encode([
        'status' => 'success',
        'data' => [
            [
                'campus' => 'laguna',
                'location_name' => 'John L. Gokongwei Jr. Innovation Center',
                'current_headcount' => 0,
                'density_percentage' => 0
            ]
        ]
    ]);
}
?>