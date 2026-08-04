<?php require_once '../includes/header.php'; ?>

<div class="space-y-5 animate-fade-in pb-28">
    <!-- Header with Campus Switcher Bar -->
    <div class="bg-white p-5 rounded-[32px] border border-[#0e0f0c]/12 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-sm">
        <div>
            <h2 class="text-xl wise-heading text-[#0e0f0c] uppercase flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-[#163300]"></i>
                Live Campus Density Map
            </h2>
            <p class="text-xs text-gray-500 font-semibold mt-1">Real-time building density telemetry</p>
        </div>
        
        <div class="w-full sm:w-auto">
            <select 
                onchange="window.location.href='?campus='+this.value"
                class="w-full sm:w-auto h-12 px-5 bg-gray-50 border border-[#0e0f0c]/12 rounded-full text-xs font-bold text-[#0e0f0c] outline-none focus:ring-2 focus:ring-[#9fe870] cursor-pointer"
            >
                <option value="manila" <?php echo $selectedCampus === 'manila' ? 'selected' : ''; ?>>DLSU Manila Campus</option>
                <option value="laguna" <?php echo $selectedCampus === 'laguna' ? 'selected' : ''; ?>>DLSU Laguna Campus</option>
            </select>
        </div>
    </div>

    <!-- Predictive Alerts -->
    <?php if (count($heavyTrafficBldgs) > 0): ?>
        <?php foreach ($heavyTrafficBldgs as $b): ?>
            <div class="flex gap-3 bg-red-50 p-4 rounded-[24px] border border-red-200 items-start">
                <i data-lucide="alert-triangle" class="text-red-500 w-5 h-5 flex-shrink-0 mt-0.5 animate-bounce"></i>
                <div>
                    <p class="text-xs font-bold text-red-950">Heavy Queue Alert: <?php echo htmlspecialchars($b['location_name']); ?></p>
                    <p class="text-[11px] text-red-800 leading-snug mt-0.5 font-medium">Building density is at <?php echo $b['density_percentage']; ?>% (<?php echo $b['current_headcount']; ?> headcount). Expect potential delays at pickup counters.</p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="flex gap-3 bg-white p-4 rounded-[24px] border border-[#0e0f0c]/12 items-start shadow-sm">
            <i data-lucide="check-circle-2" class="text-green-600 w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-extrabold text-[#0e0f0c]">Traffic Prediction Engine: Smooth Flow</p>
                <p class="text-[11px] text-gray-600 leading-snug mt-0.5 font-semibold">Pickup counters are operating smoothly without bottlenecks across distribution zones.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Map Canvas & Telemetry Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Left Map View -->
        <div class="lg:col-span-7 flex flex-col gap-3">
            <div class="relative w-full rounded-[32px] overflow-hidden bg-white border border-[#0e0f0c]/12 h-[380px] md:h-[420px] shadow-sm">
                <div id="campus-map" class="w-full h-full z-10"></div>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-md w-fit rounded-full border border-[#0e0f0c]/12 text-[11px] font-bold text-[#0e0f0c] shadow-sm">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                <span>YOLO OpenCV Vision Telemetry Sync Active</span>
            </div>
        </div>

        <!-- Right Telemetry Panel -->
        <div class="lg:col-span-5">
            <div class="bg-[#0e0f0c] rounded-[32px] border border-white/10 p-5 text-white flex flex-col justify-between space-y-4 shadow-lg">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-xs text-[#9fe870] uppercase tracking-wider">Building Headcount</h3>
                        <div class="relative max-w-36">
                            <input 
                                type="text" 
                                id="telemetrySearch"
                                placeholder="Search..." 
                                class="w-full px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-[10px] text-white outline-none focus:border-[#9fe870] font-semibold placeholder:text-white/40"
                            />
                        </div>
                    </div>
                    
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1 hide-scrollbar" id="telemetryList">
                        <?php if (empty($trafficLogs)): ?>
                            <p class="text-white/40 text-center text-xs py-8">No buildings matching search.</p>
                        <?php else: ?>
                            <?php foreach ($trafficLogs as $log): 
                                $color = '#9fe870'; 
                                if ($log['density_percentage'] >= 85) $color = '#ef4444';
                                else if ($log['density_percentage'] >= 60) $color = '#f59e0b';
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
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] font-black text-white z-10"><?php echo $log['density_percentage']; ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="pt-3 border-t border-white/10 flex justify-between items-center text-[11px] font-semibold">
                    <span class="text-white/60">Total Campus Headcount:</span>
                    <span class="font-extrabold text-[#9fe870]"><?php echo $totalHeadcount; ?> Active</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    if (window.lucide) lucide.createIcons();

    document.getElementById('telemetrySearch')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.telemetry-row');
        
        rows.forEach(row => {
            const location = (row.getAttribute('data-location') || '').toLowerCase();
            if (location.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    const campus = "<?php echo $selectedCampus; ?>";
    const center = campus === 'laguna' ? [14.2612, 121.0427] : [14.5648, 120.9932];
    
    if (window.L) {
        let map = L.map('campus-map', { zoomControl: false }).setView(center, 17);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);
        
        // Add zoom control to top-right
        L.control.zoom({ position: 'topright' }).addTo(map);

        let layerGroup = L.layerGroup().addTo(map);

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

        const trafficData = <?php echo json_encode($trafficLogs); ?>;
        
        trafficData.forEach(log => {
            const coords = buildingCoords[log.location_name];
            if (coords) {
                let strokeColor = '#16a34a';
                let fillColor = '#9fe870';
                
                if (log.density_percentage >= 85) {
                    strokeColor = '#dc2626';
                    fillColor = '#f87171';
                } else if (log.density_percentage >= 60) {
                    strokeColor = '#d97706';
                    fillColor = '#fbbf24';
                }

                L.circle(coords, { 
                    color: strokeColor,
                    weight: 3,
                    fillColor: fillColor,
                    fillOpacity: 0.6,
                    radius: 15 + (log.density_percentage * 0.4)
                }).bindPopup(`<b>${log.location_name}</b><br/>Density: ${log.density_percentage}%<br/>Headcount: ${log.current_headcount}`)
                  .addTo(layerGroup);
            }
        });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>