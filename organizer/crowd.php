<?php
require_once '../config/database.php';
requireLogin('organizer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim   Organizer Crowd Monitor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2fed9;
            margin: 0;
            padding: 20px;
            color: #1c261b;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #1c261b;
            color: #ffffff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        h2 {
            color: #c6f135;
            margin-top: 0;
            text-transform: uppercase;
            font-size: 20px;
        }
        .video-box {
            position: relative;
            width: 100%;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            border: 2px solid #333;
            margin-bottom: 20px;
        }
        .video-box img {
            width: 100%;
            display: block;
        }
        .metrics-card {
            background: #0f2419;
            border: 1px solid #c6f135;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .metric-item {
            text-align: center;
        }
        .metric-value {
            font-size: 28px;
            font-weight: bold;
            color: #c6f135;
            font-family: monospace;
        }
        .metric-label {
            font-size: 11px;
            color: #a0a0a0;
            text-transform: uppercase;
        }
        .status-badge {
            background: #22c55e;
            color: #1c261b;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2>Organizer Overhead Camera Feed</h2>
        <span class="status-badge">Live System Connected</span>
    </div>

    <!-- Live Python Camera Stream -->
    <div class="video-box">
        <img src="http://localhost:5000/video_feed" alt="Live Camera Stream Offline" onerror="this.src='https://via.placeholder.com/800x450/000000/ffffff?text=Camera+Stream+Offline+(Run+Python+Script)';" />
    </div>

    <!-- Live Telemetry Card -->
    <div class="metrics-card">
        <div class="metric-item" style="text-align: left;">
            <div class="metric-label">Monitored Location</div>
            <div id="location-name" style="font-size: 16px; font-weight: bold; margin-top: 4px;">John L. Gokongwei Jr. Innovation Center</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Current Headcount</div>
            <div id="headcount-val" class="metric-value">0</div>
        </div>
        <div class="metric-item">
            <div class="metric-label">Crowd Density</div>
            <div id="density-val" class="metric-value">0%</div>
        </div>
    </div>
</div>

<script>
function updateOrganizerTraffic() {
    fetch('../api/get_traffic.php')
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success' && response.data.length > 0) {
                const data = response.data[0];
                document.getElementById('location-name').innerText = data.location_name;
                document.getElementById('headcount-val').innerText = data.current_headcount + " people";
                document.getElementById('density-val').innerText = data.density_percentage + "%";
            }
        })
        .catch(err => console.error("Could not fetch traffic API:", err));
}

// Poll API every 2 seconds
setInterval(updateOrganizerTraffic, 2000);
updateOrganizerTraffic();
</script>

</body>
</html>