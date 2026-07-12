<?php 
require_once('../config/auth.php'); 
$current_page = 'map'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim - Live Crowd Levels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f2fed9;
        }
        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: radial-gradient(at 0% 0%, rgba(209, 232, 207, 0.4) 0px, transparent 50%),
                        radial-gradient(at 100% 100%, rgba(237, 249, 212, 0.5) 0px, transparent 50%),
                        radial-gradient(at 80% 20%, rgba(163, 222, 254, 0.1) 0px, transparent 50%);
        }
    </style>
</head>

    <header class="w-full bg-[#1A2419] px-6 py-6 flex justify-between items-center rounded-b-[24px] md:rounded-bl-none md:rounded-br-[32px] shadow-md z-40 relative md:fixed md:top-0 md:left-72 md:w-[calc(100%-18rem)]">
        <h1 class="text-[24px] font-black text-[#c6f135] tracking-tight">Maps</h1>
        <button class="relative w-10 h-10 flex items-center justify-center rounded-full text-[#c6f135] hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined">notifications</span>
        </button>
    </header>


<body class="md:pl-72 min-h-screen text-[#191C19] relative">
    
    <div class="mesh-gradient"></div>

    <?php include('../components/sidebar_student.php'); ?>

    <main class="pt-24 pb-28 px-4 md:px-8 max-w-7xl mx-auto space-y-8">
        
        <div class="flex items-center gap-3 max-w-xl mx-auto md:mx-0">
            <div class="relative flex-grow">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">search</span>
                <input class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 shadow-sm rounded-full text-sm focus:ring-2 focus:ring-[#1c261b]/20 outline-none placeholder:text-gray-400 transition-all duration-300" placeholder="Search locations, buildings, or halls..." type="text">
            </div>
            <button class="w-12 h-12 flex items-center justify-center bg-[#1c261b] text-[#EDF9D4] rounded-2xl shadow-lg active:scale-95 transition-transform flex-shrink-0">
                <span class="material-symbols-outlined">tune</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="flex flex-col gap-4">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-[#1c261b] tracking-tight">Live Crowd Levels</h2>
                        <p class="text-gray-600 text-sm mt-1">Real-time density heatmap of the campus premises.</p>
                    </div>
                    
                    <select id="campus-selector" class="bg-white border border-gray-200 text-[#1c261b] text-sm rounded-xl focus:ring-2 focus:ring-[#c6f135] focus:outline-none block p-2.5 shadow-sm font-bold cursor-pointer transition-all">
                        <option value="manila">Manila Campus</option>
                        <option value="laguna">Laguna Campus</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 px-3 py-1.5 bg-[#EDF9D4] w-fit rounded-full border border-gray-200 shadow-sm">
                    <span class="w-2 h-2 bg-[#4CAF50] rounded-full animate-pulse"></span>
                    <span class="text-[11px] font-bold text-[#1c261b] uppercase tracking-wider">Last updated live</span>
                </div>

                <div class="relative w-full rounded-3xl overflow-hidden shadow-xl bg-white border border-gray-200 h-80 lg:h-[400px]">
                    <div class="absolute inset-0 z-0">
                        <div id="campus-map" class="w-full h-full z-0"></div>
                    </div>
                    
                    <div class="absolute bottom-4 right-4 flex flex-col gap-2 z-[400]">
                        <button id="zoom-in" class="w-10 h-10 bg-white/90 backdrop-blur-md rounded-xl flex items-center justify-center shadow-md text-[#1c261b] hover:bg-gray-50">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                        <button id="zoom-out" class="w-10 h-10 bg-white/90 backdrop-blur-md rounded-xl flex items-center justify-center shadow-md text-[#1c261b] hover:bg-gray-50">
                            <span class="material-symbols-outlined">remove</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:pt-[76px]">
                <div class="bg-[#1c261b] rounded-3xl shadow-xl border border-white/10 p-6">
                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex flex-col">
                            <h3 class="text-xl font-bold text-white">Traffic Prediction</h3>
                            <p class="text-[#c6f135] text-[10px] font-bold uppercase tracking-widest mt-1">Predictive Engine Sources</p>
                        </div>

                        <div id="alerts-container"></div>
                    </div>
                    
                    <div id="traffic-container" class="space-y-5 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        <p class="text-white/50 text-center py-4 text-sm">Loading live traffic data...</p>
                    </div>

                    <button class="w-full py-3.5 bg-[#C6F135] text-[#1c261b] rounded-full font-bold text-sm shadow-lg active:scale-95 transition-transform mt-8 hover:bg-[#b5e024]">
                        View Detailed Analytics
                    </button>
                </div>
            </div>

        </div>
    </main>

    <?php include('../components/bottom_nav_student.php'); ?>

    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            // Setup Campus Configurations with the EXACT new database names
            const campusConfigs = {
                'manila': {
                    center: [14.5648, 120.9932],
                    zoom: 17,
                    buildings: {
                        'Henry Sy Sr. Hall': [14.5645, 120.9935],
                        'Enrique Razon Sports Center': [14.5662, 120.9925],
                        'Gokongwei Hall': [14.5650, 120.9940],
                        'St. La Salle Hall': [14.5640, 120.9930],
                        'Velasco Hall': [14.5655, 120.9938]
                    }
                },
                'laguna': {
                    center: [14.2612, 121.0427],
                    zoom: 17,
                    buildings: {
                        'Milagros R. Del Rosario Bldg': [14.2618, 121.0420],
                        'John L. Gokongwei Jr. Innovation Center': [14.2610, 121.0422],
                        'Richard L. Lee Engineering Block': [14.2615, 121.0430],
                        'Dr. George S.K. Ty Bldg': [14.2622, 121.0425],
                        'St. Matthew Gymnasium': [14.2625, 121.0415],
                        'Enrique K. Razon Jr. Hall': [14.2605, 121.0428]
                    }
                }
            };

            let currentCampus = 'manila';
            let allTrafficData = [];
            let activeMapLayer = L.layerGroup(); 

            // Initialize Leaflet Map
            const map = L.map('campus-map', { zoomControl: false }).setView(campusConfigs[currentCampus].center, campusConfigs[currentCampus].zoom);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            activeMapLayer.addTo(map);

            // Connect zoom buttons
            document.getElementById('zoom-in').addEventListener('click', () => map.zoomIn());
            document.getElementById('zoom-out').addEventListener('click', () => map.zoomOut());

            // Render Function: Updates Map and Sidebar based on selected campus
            function renderCampusData() {
                const trafficContainer = document.getElementById('traffic-container');
                const alertsContainer = document.getElementById('alerts-container');
                const config = campusConfigs[currentCampus];

                // Clear old data
                alertsContainer.innerHTML = '';
                trafficContainer.innerHTML = '';
                activeMapLayer.clearLayers(); 

                // Fly map to new campus smoothly
                map.flyTo(config.center, config.zoom, { duration: 1.5 });

                // Filter data for current campus
                const campusTraffic = allTrafficData.filter(loc => loc.campus === currentCampus);

                if (campusTraffic.length === 0) {
                    trafficContainer.innerHTML = '<p class="text-white/50 text-center py-4 text-sm">No live traffic data available for this campus.</p>';
                    return;
                }

                campusTraffic.forEach(loc => {
                    // Draw Sidebar Progress Bars
                    trafficContainer.innerHTML += `
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 w-24">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: ${loc.color}"></span>
                                <span class="font-bold text-[11px] text-white truncate" title="${loc.location_name}">${loc.location_code}</span>
                            </div>
                            <div class="flex-grow bg-white/10 rounded-full overflow-hidden relative h-6">
                                <div class="absolute inset-0 transition-all duration-1000" style="width: ${loc.percentage}%; background-color: ${loc.color}"></div>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-white z-10 drop-shadow-md">${loc.percentage}%</span>
                            </div>
                        </div>
                    `;

                    // Draw Live Circles on Map
                    const coords = config.buildings[loc.location_name];
                    if (coords) {
                        L.circle(coords, {
                            color: loc.color,
                            fillColor: loc.color,
                            fillOpacity: 0.5,
                            radius: 20 + (loc.percentage / 3) 
                        }).bindPopup(`<b>${loc.location_name}</b><br>Density: ${loc.percentage}%`)
                          .addTo(activeMapLayer); 
                    }
                });

                // Re-render relevant alerts
                const campusAlerts = campusTraffic.filter(loc => loc.percentage >= 85);
                campusAlerts.forEach(loc => {
                    alertsContainer.innerHTML += `
                        <div class="flex gap-3 items-start bg-red-500/20 p-4 rounded-2xl border border-red-500/30 mt-2 animate-pulse">
                            <span class="material-symbols-outlined text-red-400 text-sm mt-0.5">warning</span>
                            <p class="text-[13px] text-red-100 font-medium leading-tight">Heavy traffic expected in ${loc.location_name}. You may arrive earlier than your time slot.</p>
                        </div>
                    `;
                });
            }

            // Fetch Live Traffic Data Once
            fetch('../api/get_traffic.php')
                .then(response => response.json())
                .then(data => {
                    allTrafficData = data.traffic || [];
                    renderCampusData(); 
                })
                .catch(error => {
                    console.error('Error fetching traffic:', error);
                    document.getElementById('traffic-container').innerHTML = '<p class="text-red-400 text-sm">Failed to load traffic data.</p>';
                });

            // Dropdown Event Listener
            document.getElementById('campus-selector').addEventListener('change', (e) => {
                currentCampus = e.target.value;
                renderCampusData();
            });

            // UI Micro interactions
            document.querySelectorAll('button:not(#zoom-in):not(#zoom-out)').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    let ripple = document.createElement('div');
                    ripple.className = 'absolute bg-white/20 rounded-full animate-ping pointer-events-none';
                    ripple.style.width = '20px';
                    ripple.style.height = '20px';
                    
                    const rect = btn.getBoundingClientRect();
                    ripple.style.left = (e.clientX - rect.left - 10) + 'px';
                    ripple.style.top = (e.clientY - rect.top - 10) + 'px';
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 1000);
                });
            });
        });
    </script>
    <script src="../assets/javascript/main.js" defer></script>
</body>
</html>