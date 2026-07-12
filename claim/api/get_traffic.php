<?php
require_once('../config/database.php');
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

date_default_timezone_set('Asia/Manila');
$current_time_str = date('Y-m-d H:i:s');

$response = [
    'traffic' => [],
    'alerts' => []
];

// 1. Fetch Live Vision Telemetry, sorted by highest density first
$traffic_query = "SELECT campus, location_name, current_headcount, density_percentage FROM crowd_traffic_logs ORDER BY density_percentage DESC";
$traffic_result = mysqli_query($conn, $traffic_query);

// Map full building names to clean UI abbreviations
$short_names = [
    'Henry Sy Sr. Hall' => 'HENRY SY',
    'Enrique Razon Sports Center' => 'RAZON',
    'Gokongwei Hall' => 'GOKONGWEI',
    'St. La Salle Hall' => 'LA SALLE',
    'Velasco Hall' => 'VELASCO',
    'Milagros R. Del Rosario Bldg' => 'MRR',
    'John L. Gokongwei Jr. Innovation Center' => 'JGIC',
    'Richard L. Lee Engineering Block' => 'LEE',
    'Dr. George S.K. Ty Bldg' => 'GEORGE TY',
    'St. Matthew Gymnasium' => 'MATTHEW',
    'Enrique K. Razon Jr. Hall' => 'RAZON JR'
];

if ($traffic_result) {
    while ($row = mysqli_fetch_assoc($traffic_result)) {
        $percentage = (int)$row['density_percentage'];
        
        $full_name = $row['location_name'];
        // Use mapped name, or default to first word if not found
        $location_code = isset($short_names[$full_name]) ? $short_names[$full_name] : strtoupper(explode(' ', $full_name)[0]); 

        $traffic_data = [
            'campus' => $row['campus'],
            'location_code' => $location_code,
            'location_name' => $full_name,
            'percentage' => $percentage,
            'color' => '#4CAF50' 
        ];
        
        if ($percentage >= 85) {
            $traffic_data['color'] = '#F44336'; 
        } elseif ($percentage >= 60) {
            $traffic_data['color'] = '#FFB300'; 
        }
        
        $response['traffic'][] = $traffic_data;
    }
}

// 2. Predictive Engine Analyze Upcoming Reservation Densities
$prediction_query = "
    SELECT 
        e.location,
        e.title,
        ts.start_time,
        ts.max_capacity,
        ts.current_reservations,
        (ts.current_reservations / ts.max_capacity) * 100 AS reservation_density
    FROM event_time_slots ts
    JOIN events e ON ts.event_id = e.id
    WHERE ts.start_time >= '$current_time_str' 
      AND ts.start_time <= DATE_ADD('$current_time_str', INTERVAL 2 HOUR)
    HAVING reservation_density >= 80
    ORDER BY ts.start_time ASC
    LIMIT 1
";

$prediction_result = mysqli_query($conn, $prediction_query);

if ($prediction_result && mysqli_num_rows($prediction_result) > 0) {
    $predictive_row = mysqli_fetch_assoc($prediction_result);
    $formatted_time = date('g:i A', strtotime($predictive_row['start_time']));
    
    $response['alerts'][] = [
        'type' => 'prediction',
        'location' => $predictive_row['location'],
        'message' => "Heavy traffic expected in " . $predictive_row['location'] . " at " . $formatted_time . " due to high reservation volumes for " . $predictive_row['title'] . ". You may want to arrive early."
    ];
} else {
    $response['alerts'][] = [
        'type' => 'nominal',
        'location' => 'All Locations',
        'message' => "Traffic conditions are normal across remaining distribution windows. No upcoming booking bottlenecks detected."
    ];
}

echo json_encode($response);
exit();
?>