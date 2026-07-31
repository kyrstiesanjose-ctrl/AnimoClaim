<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">
    <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="font-black text-sm uppercase tracking-wider text-[#0e0f0c] wise-heading">Overhead Vision Framework</h3>
            <p class="text-[11px] text-gray-500 font-medium">Live overhead camera feed tracking real-time headcount and crowd density.</p>
        </div>
        <div class="flex gap-2">
            <button id="camToggleBtn" onclick="toggleCamera()" class="h-11 px-5 rounded-full text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200 wise-btn">
                Disconnect Camera
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 flex flex-col gap-4">
            <div class="relative w-full aspect-video rounded-[32px] overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex items-center justify-center" id="camStreamContainer">
                <img id="liveFeedImg" src="http://localhost:5000/video_feed" alt="Live YOLO stream" class="w-full h-full object-cover" onerror="handleStreamError()" />
                
                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center bg-black/75 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-[10px] font-mono">
                    <div class="text-white/70">
                        Headcount: <span class="text-white font-bold" id="headcountDisplay">--</span> • 
                        Density: <span class="text-white font-bold" id="densityDisplay">--</span>
                    </div>
                    <span class="text-[#9fe870] font-bold" id="locationDisplay">Connecting...</span>
                </div>
            </div>
            
            <div id="camOfflineContainer" class="hidden relative w-full aspect-video rounded-[32px] overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex flex-col items-center justify-center text-center text-white/40 space-y-2 p-6">
                <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-red-500 animate-pulse"></i>
                <p class="text-xs uppercase font-mono tracking-widest font-bold text-red-400">Stream Disconnected</p>
                <p class="text-[10px] text-white/40 max-w-xs">Make sure yolo_traffic_bridge.py is running on this machine.</p>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm space-y-3">
                <h4 class="font-bold text-xs uppercase text-[#0e0f0c] tracking-wider flex items-center gap-1.5">
                    <i data-lucide="gauge" class="w-4 h-4 text-[#163300]"></i> Live Telemetry
                </h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Current headcount:</span>
                        <span class="font-black text-[#0e0f0c] font-mono" id="telemetryHeadcount">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Crowd density:</span>
                        <span class="font-black text-[#0e0f0c] font-mono" id="telemetryDensity">--</span>
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
        btn.className = 'h-11 px-5 rounded-full text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200 wise-btn';
        stream.classList.remove('hidden');
        offline.classList.add('hidden');
        img.src = 'http://localhost:5000/video_feed?' + Date.now();
    } else {
        btn.textContent = 'Connect Camera';
        btn.className = 'h-11 px-5 rounded-full text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-green-100 text-green-800 border border-green-200 wise-btn';
        stream.classList.add('hidden');
        offline.classList.remove('hidden');
        img.src = '';
    }
}

function handleStreamError() {
    if (!cameraActive) return;
    document.getElementById('camStreamContainer').classList.add('hidden');
    document.getElementById('camOfflineContainer').classList.remove('hidden');
}

function updateTelemetry() {
    fetch('/claim/api/get_traffic.php')
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success' && response.data.length > 0) {
                const data = response.data[0];
                document.getElementById('headcountDisplay').textContent = data.current_headcount + ' people';
                document.getElementById('densityDisplay').textContent = data.density_percentage + '%';
                document.getElementById('locationDisplay').textContent = data.location_name;
                document.getElementById('telemetryHeadcount').textContent = data.current_headcount + ' people';
                document.getElementById('telemetryDensity').textContent = data.density_percentage + '%';
            }
        });
}

setInterval(updateTelemetry, 2000);
updateTelemetry();
</script>

<?php require_once '../includes/footer.php'; ?>