<?php 
require_once '../config/database.php';
requireLogin('organizer');
require_once '../includes/header.php';
?>
<div class="space-y-6">
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="font-black text-sm uppercase tracking-wider text-gray-900">Overhead Vision Framework (OpenCV & YOLOv8 Nano)</h3>
                <p class="text-[11px] text-gray-500">Live overhead camera feed tracking real-time headcount and crowd density.</p>
            </div>
            <div class="flex gap-2">
                <button id="camToggleBtn" onclick="toggleCamera()" class="h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200">
                    Disconnect Camera
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Real YOLO Camera Stream -->
        <div class="lg:col-span-8 flex flex-col gap-4">
            <div class="relative w-full aspect-video rounded-3xl overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex items-center justify-center" id="camStreamContainer">
                <img id="liveFeedImg" src="http://localhost:5000/video_feed" alt="Live YOLO camera stream" class="w-full h-full object-cover"
                     onerror="handleStreamError()" />

                <div class="absolute top-4 left-4 z-10 flex gap-2">
                    <span class="bg-red-600 text-white font-mono text-[9px] font-black uppercase px-2.5 py-1 rounded-md animate-pulse flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE AI STREAM
                    </span>
                </div>

                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center bg-black/65 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/5 text-[10px] font-mono">
                    <div class="text-white/60">
                        Headcount: <span class="text-white font-bold" id="headcountDisplay">--</span>
                        Density: <span class="text-white font-bold" id="densityDisplay">--</span>
                    </div>
                    <span class="text-[#c6f135] font-bold" id="locationDisplay">Connecting...</span>
                </div>
            </div>

            <div id="camOfflineContainer" class="hidden relative w-full aspect-video rounded-3xl overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex flex-col items-center justify-center text-center text-white/40 space-y-2">
                <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-red-500 animate-pulse"></i>
                <p class="text-xs uppercase font-mono tracking-widest font-bold text-red-400">Stream Disconnected</p>
                <p class="text-[10px] text-white/30 max-w-xs">Couldn't reach the vision bridge. Make sure yolo_traffic_bridge.py is running on this machine, then toggle 'Connect Camera' to retry.</p>
            </div>

            <div class="flex gap-2 text-[10px] font-mono font-bold text-gray-500">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-ping mt-1.5 flex-shrink-0"></span>
                <span>Live feed and headcount are pulled directly from the YOLO bridge running on this machine.</span>
            </div>
        </div>

        <!-- Right: Real telemetry -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm space-y-3">
                <h4 class="font-bold text-xs uppercase text-gray-900 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="gauge" class="w-4 h-4 text-blue-600"></i> Live Telemetry
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-500">Current headcount:</span>
                        <span class="font-bold text-gray-900 font-mono" id="telemetryHeadcount">--</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-500">Crowd density:</span>
                        <span class="font-bold text-gray-900 font-mono" id="telemetryDensity">--</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-500">Monitored location:</span>
                        <span class="font-bold text-gray-900 font-mono" id="telemetryLocation">--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cameraActive = true;

function toggleCamera() {
    cameraActive = !cameraActive;
    const btn = document.getElementById('camToggleBtn');
    const stream = document.getElementById('camStreamContainer');
    const offline = document.getElementById('camOfflineContainer');
    const img = document.getElementById('liveFeedImg');

    if (cameraActive) {
        btn.textContent = 'Disconnect Camera';
        btn.className = 'h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200';
        stream.classList.remove('hidden');
        offline.classList.add('hidden');
        img.src = 'http://localhost:5000/video_feed?' + Date.now(); // force reconnect
    } else {
        btn.textContent = 'Connect Camera';
        btn.className = 'h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-green-100 text-green-700 border border-green-200';
        stream.classList.add('hidden');
        offline.classList.remove('hidden');
        img.src = '';
    }
}

function handleStreamError() {
    if (!cameraActive) return; // already manually disconnected
    document.getElementById('camStreamContainer').classList.add('hidden');
    document.getElementById('camOfflineContainer').classList.remove('hidden');
}

function updateTelemetry() {
    fetch('../api/get_traffic.php')
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success' && response.data.length > 0) {
                const data = response.data[0];
                document.getElementById('headcountDisplay').textContent = data.current_headcount + ' people';
                document.getElementById('densityDisplay').textContent = data.density_percentage + '%';
                document.getElementById('locationDisplay').textContent = data.location_name;
                document.getElementById('telemetryHeadcount').textContent = data.current_headcount + ' people';
                document.getElementById('telemetryDensity').textContent = data.density_percentage + '%';
                document.getElementById('telemetryLocation').textContent = data.location_name;
            }
        })
        .catch(err => console.error('Could not fetch traffic telemetry:', err));
}

setInterval(updateTelemetry, 2000);
updateTelemetry();
</script>

<?php require_once '../includes/footer.php'; ?>