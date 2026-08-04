<?php 
require_once '../config/database.php';
requireLogin('student');

// Check which campus is selected (default to manila)
$selectedCampus = $_GET['campus'] ?? 'manila';

// Fetch the logs, ordered by highest density first
$stmt = $pdo->prepare("
    SELECT * FROM crowd_traffic_logs 
    WHERE campus = ? 
    ORDER BY density_percentage DESC
");
$stmt->execute([$selectedCampus]);
$allLogs = $stmt->fetchAll();

// DEDUPLICATE: Keep only one entry per building (the highest density one due to our ORDER BY)[cite: 24]
$uniqueLogs = [];
foreach ($allLogs as $log) {
    if (!isset($uniqueLogs[$log['location_name']])) {
        $uniqueLogs[$log['location_name']] = $log;
    }
}
$trafficLogs = array_values($uniqueLogs);

// Find any buildings with >85% traffic for the alerts (using the cleaned data)[cite: 24]
$heavyTrafficBldgs = array_filter($trafficLogs, function($log) {
    return $log['density_percentage'] >= 85;
});

// Calculate the total active headcount for the bottom of the telemetry logs[cite: 24]
$totalHeadcount = array_sum(array_column($trafficLogs, 'current_headcount'));

$currentPage = 'map';

// Load the view
require_once '../views/student/map_view.php';
?>