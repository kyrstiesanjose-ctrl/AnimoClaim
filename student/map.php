<?php 
require_once '../config/database.php';
requireLogin('student');
require_once '../includes/header.php';

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

// DEDUPLICATE: Keep only one entry per building (the highest density one due to our ORDER BY)
$uniqueLogs = [];
foreach ($allLogs as $log) {
    if (!isset($uniqueLogs[$log['location_name']])) {
        $uniqueLogs[$log['location_name']] = $log;
    }
}
$trafficLogs = array_values($uniqueLogs);

// Find any buildings with >85% traffic for the alerts (using the cleaned data)
$heavyTrafficBldgs = array_filter($trafficLogs, function($log) {
    return $log['density_percentage'] >= 85;
});

// Calculate the total active headcount for the bottom of the telemetry logs
$totalHeadcount = array_sum(array_column($trafficLogs, 'current_headcount'));
?>

<div class="space-y-6">
    <!-- Top Header and Campus Selector -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3">
        <div>
            <h2 class="text-xl font-black text-[#1c261b] tracking-tight">Live Crowd Levels</h2>
            <p class="text-gray-600 text-xs mt-0.5">Real-time building density heatmap of the campus premises.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <select 
                onchange="window.location.href='?campus='+this.value"
                class="bg-white border border-gray-200 text-[#1c261b] text-xs rounded-xl focus:ring-2 focus:ring-[#c6f135] focus:outline-none block p-2.5 shadow-sm font-bold cursor-pointer transition-all flex-1 sm:flex-none"
            >
                <option value="manila" <?php echo $selectedCampus === 'manila' ? 'selected' : ''; ?>>DLSU Manila Campus</option>
                <option value="laguna" <?php echo $selectedCampus === 'laguna' ? 'selected' : ''; ?>>DLSU Laguna Campus</option>
            </select>
        </div>
    </div>

    <!-- Predictive Alerts -->
    <?php if (count($heavyTrafficBldgs) > 0): ?>
        <?php foreach ($heavyTrafficBldgs as $b): ?>
            <div class="flex gap-3 items-start bg-red-50 p-4 rounded-2xl border border-red-200 animate-pulse">
                <i data-lucide="alert-triangle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-red-950">Heavy Traffic Alert: <?php echo htmlspecialchars($b['location_name']); ?></p>
                    <p class="text-[11px] text-red-800 leading-normal mt-0.5">Building density is at <?php echo $b['density_percentage']; ?>% (<?php echo $b['current_headcount']; ?> headcount). Standard distributions might experience delays. We advise arriving early or picking alternative times.</p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="flex gap-3 items-start bg-[#EDF9D4] p-4 rounded-2xl border border-gray-200">
            <i data-lucide="check-circle-2" class="text-[#7ba82a] w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-bold text-gray-900">Traffic Prediction Engine: Normal</p>
                <p class="text-[11px] text-gray-600 leading-normal mt-0.5">Traffic is nominal across remaining distribution windows. No crowd bottlenecks detected. Your slot reservation arrivals are clean.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- MAP CANVAS & SIDEBAR CO-LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Map Canvas -->
        <div class="lg:col-span-7 flex flex-col gap-3">
            <div class="relative w-full rounded-3xl overflow-hidden shadow-lg bg-white border border-gray-200 h-80 md:h-[400px]">
                <div id="campus-map" class="w-full h-full z-0"></div>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white/50 w-fit rounded-full border border-gray-200">
                <span class="w-2.5 h-2.5 bg-[#4CAF50] rounded-full animate-pulse"></span>
                <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wider">Live Vision Camera Feeds Connected</span>
            </div>
        </div>

        <!-- Right: Interactive Telemetry Sidebar -->
        <div class="lg:col-span-5">
            <div class="bg-[#1c261b] rounded-3xl shadow-xl border border-white/5 p-5 text-white flex flex-col h-full justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-sm text-[#c6f135] uppercase tracking-widest font-mono">Telemetry Logs</h3>
                        <div class="relative max-w-40">
                            <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-white/30 w-3.5 h-3.5"></i>
                            <input 
                                type="text" 
                                id="telemetrySearch"
                                placeholder="Search building..." 
                                class="w-full pl-8 pr-2 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] outline-none text-white focus:border-[#c6f135] focus:ring-0 placeholder:text-white/20"
                            />
                        </div>
                    </div>
                    
                    <div class="space-y-4 max-h-64 overflow-y-auto pr-1 hide-scrollbar" id="telemetryList">
                        <?php if (count($trafficLogs) === 0): ?>
                            <p class="text-white/40 text-center text-xs py-8">No buildings found matching search.</p>
                        <?php else: ?>
                            <?php foreach ($trafficLogs as $log): 
                                // Color selection matching the new map styles
                                $color = '#22c55e'; // Green 500
                                if ($log['density_percentage'] >= 85) $color = '#ef4444'; // Red 500
                                else if ($log['density_percentage'] >= 60) $color = '#f59e0b'; // Amber 500
                            ?>
                                <div class="telemetry-row flex items-center gap-3" data-location="<?php echo htmlspecialchars($log['location_name']); ?>">
                                    <div class="w-24 flex items-center gap-1.5 flex-shrink-0">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: <?php echo $color; ?>"></span>
                                        <span class="font-bold text-[10px] text-white truncate" title="<?php echo htmlspecialchars($log['location_name']); ?>">
                                            <?php echo htmlspecialchars($log['location_name']); ?>
                                        </span>
                                    </div>
                                    <div class="flex-1 bg-white/10 rounded-full h-5 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 transition-all duration-1000" style="width: <?php echo $log['density_percentage']; ?>%; background-color: <?php echo $color; ?>"></div>
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] font-mono font-black text-white z-10"><?php echo $log['density_percentage']; ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="pt-4 mt-4 border-t border-white/5 flex flex-col gap-2">
                    <div class="flex justify-between items-center text-[10px] text-white/50">
                        <span>Campus head count logs</span>
                        <span class="font-mono font-bold text-white"><?php echo $totalHeadcount; ?> Active</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    // 1. Live Search Filtering Logic
    document.getElementById('telemetrySearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.telemetry-row');
        
        rows.forEach(row => {
            const location = row.getAttribute('data-location').toLowerCase();
            if (location.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // 2. Initialize Leaflet Map
    const campus = "<?php echo $selectedCampus; ?>";
    const center = campus === 'laguna' ? [14.2612, 121.0427] : [14.5648, 120.9932];
    
    let map = L.map('campus-map').setView(center, 17);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
    let layerGroup = L.layerGroup().addTo(map);

    // Hardcoded building coordinates for the map
    const buildingCoords = {
        'Henry Sy Sr. Hall': [14.5645, 120.9935],
        'Enrique Razon Sports Center': [14.5662, 120.9925],
        'Gokongwei Hall': [14.5650, 120.9940],
        'St. La Salle Hall': [14.5640, 120.9930],
        'Velasco Hall': [14.5655, 120.9938],
        'Milagros R. Del Rosario Bldg': [14.2618, 121.0420],
        'John L. Gokongwei Jr. Innovation Center': [14.2610, 121.0422],
        'Richard L. Lee Engineering Block': [14.2615, 121.0430],
        'Dr. George S.K. Ty Bldg': [14.2622, 121.0425],
        'St. Matthew Gymnasium': [14.2625, 121.0415],
        'Enrique K. Razon Jr. Hall': [14.2605, 121.0428]
    };

    // Grab the deduplicated logs from PHP
    const trafficData = <?php echo json_encode($trafficLogs); ?>;
    
    trafficData.forEach(log => {
        const coords = buildingCoords[log.location_name];
        if (coords) {
            // Enhanced differentiation of colors
            let strokeColor = '#16a34a'; // Darker green border
            let fillColor = '#4ade80';   // Lighter green fill
            
            if (log.density_percentage >= 85) {
                strokeColor = '#dc2626'; // Dark red border
                fillColor = '#f87171';   // Light red fill
            } else if (log.density_percentage >= 60) {
                strokeColor = '#d97706'; // Dark amber border
                fillColor = '#fbbf24';   // Light amber fill
            }

            L.circle(coords, { 
                color: strokeColor,
                weight: 3, // Thicker border for better visibility
                fillColor: fillColor,
                fillOpacity: 0.6, // Slightly more opaque
                radius: 15 + (log.density_percentage * 0.4) // Scaled radius
            }).bindPopup(`<b>${log.location_name}</b><br/>Density: ${log.density_percentage}%<br/>Headcount: ${log.current_headcount}`)
              .addTo(layerGroup);
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>