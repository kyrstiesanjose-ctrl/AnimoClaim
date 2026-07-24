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
                <p class="text-[11px] text-gray-500">Simulate overhead cameras tracking physical item counts vs digital claims to monitor queue length and crowd density.</p>
            </div>
            <div class="flex gap-2">
                <button id="camToggleBtn" onclick="toggleCamera()" class="h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200">
                    Disconnect Camera
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Simulated YOLO Camera Stream -->
        <div class="lg:col-span-8 flex flex-col gap-4">
            <div class="relative w-full aspect-video rounded-3xl overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex items-center justify-center" id="camStreamContainer">
                <!-- Live stream backdrop graphic -->
                <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#22c55e_1px,transparent_1px)] [background-size:16px_16px] animate-pulse"></div>
                
                <div class="absolute top-4 left-4 z-10 flex gap-2">
                    <span class="bg-red-600 text-white font-mono text-[9px] font-black uppercase px-2.5 py-1 rounded-md animate-pulse flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span> LIVE AI STREAM
                    </span>
                    <span class="bg-white/10 backdrop-blur-md text-white font-mono text-[9px] px-2 py-1 rounded-md">
                        99.2% YOLOv8 CONFIDENCE
                    </span>
                </div>

                <!-- Simulated Bounding Boxes Overlay -->
                <div class="absolute inset-0 p-8 flex flex-col justify-around text-white">
                    <div class="flex justify-between">
                        <div class="border-2 border-green-500 bg-green-500/10 p-2 rounded-lg w-28 h-20 relative animate-bounce" style="animation-duration: 3s;">
                            <span class="absolute -top-4 left-0 bg-green-500 text-[8px] font-black px-1 rounded uppercase tracking-wider">Claimant 98%</span>
                            <span class="text-[9px] font-mono leading-tight block">CCS Student</span>
                            <span class="text-[8px] text-green-300 block">ID: 12011111</span>
                        </div>
                        <div class="border-2 border-blue-500 bg-blue-500/10 p-2 rounded-lg w-32 h-20 relative">
                            <span class="absolute -top-4 left-0 bg-blue-500 text-[8px] font-black px-1 rounded uppercase tracking-wider">Stock Box 99%</span>
                            <span class="text-[9px] font-mono leading-tight block">Kits Counted: <span id="kitsCountDisplay">12</span></span>
                            <span class="text-[8px] text-blue-300 block">Location: Gokongwei Hall</span>
                        </div>
                    </div>
                    <div class="flex justify-around">
                        <div class="border-2 border-purple-500 bg-purple-500/10 p-2 rounded-lg w-24 h-16 relative">
                            <span class="absolute -top-4 left-0 bg-purple-500 text-[8px] font-black px-1 rounded uppercase tracking-wider">Staff 95%</span>
                            <span class="text-[9px] font-mono leading-tight block">Verification terminal</span>
                        </div>
                        <div class="border-2 border-green-500 bg-green-500/10 p-2 rounded-lg w-24 h-20 relative animate-bounce" style="animation-duration: 4s;">
                            <span class="absolute -top-4 left-0 bg-green-500 text-[8px] font-black px-1 rounded uppercase tracking-wider">Claimant 91%</span>
                            <span class="text-[9px] font-mono leading-tight block">COB Student</span>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center bg-black/65 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/5 text-[10px] font-mono">
                    <div class="text-white/60">
                        Queue Length: <span class="text-white font-bold" id="queueLengthDisplay">6 people</span> 
                        Density: <span class="text-white font-bold" id="densityDisplay">Medium (72%)</span>
                    </div>
                    <span class="text-[#c6f135] font-bold">Henry Sy Hall Entrance Cam</span>
                </div>
            </div>
            
            <div id="camOfflineContainer" class="hidden relative w-full aspect-video rounded-3xl overflow-hidden bg-gray-950 border border-gray-800 shadow-xl flex flex-col items-center justify-center text-center text-white/40 space-y-2">
                <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto text-red-500 animate-pulse"></i>
                <p class="text-xs uppercase font-mono tracking-widest font-bold text-red-400">Stream Disconnected</p>
                <p class="text-[10px] text-white/30 max-w-xs">Vision system is currently offline. Toggle 'Connect Camera' above to resume YOLO tracking.</p>
            </div>

            <div class="flex gap-2 text-[10px] font-mono font-bold text-gray-500">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-ping mt-1.5 flex-shrink-0"></span>
                <span>CV Counting Pipeline synchronized: physical kits on feed are dynamically cross-checked with active reservation claimed logs.</span>
            </div>
        </div>

        <!-- Right interactive simulation console -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-[#1c261b] p-5 rounded-3xl text-white border border-white/5 space-y-5">
                <div>
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#c6f135]">Simulation Controls</h4>
                    <p class="text-[10px] text-white/50 mt-1">Adjust variables to test CV telemetry alerts and congestion updates.</p>
                </div>
                
                <div class="space-y-2 text-[10px]">
                    <div class="flex justify-between font-bold">
                        <span class="uppercase text-white/70">Simulated Queue Headcount</span>
                        <span class="font-mono text-[#c6f135]" id="queueSliderVal">6 people</span>
                    </div>
                    <input type="range" min="1" max="25" value="6" id="queueSlider" class="w-full accent-[#c6f135]" oninput="updateQueue(this.value)"/>
                </div>
                
                <div class="space-y-2 text-[10px]">
                    <div class="flex justify-between font-bold">
                        <span class="uppercase text-white/70">Physical Kits counted on feed</span>
                        <span class="font-mono text-[#c6f135]" id="kitSliderVal">12 packages</span>
                    </div>
                    <input type="range" min="1" max="40" value="12" id="kitSlider" class="w-full accent-[#c6f135]" oninput="updateKits(this.value)"/>
                </div>

                <div class="pt-2 border-t border-white/5 flex items-center justify-between">
                    <div class="text-[10px]">
                        <span class="font-bold text-white block uppercase">Force Inventory Mismatch</span>
                        <span class="text-white/40 block mt-0.5 text-[9px]">Simulate physical counts != claims logs</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="mismatchToggle" class="sr-only peer" onchange="toggleMismatch(this.checked)"/>
                        <div class="w-9 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>

                <div id="mismatchAlert" class="hidden bg-red-950/40 border border-red-500/20 p-3 rounded-xl flex gap-2 text-[10px] text-red-300 leading-normal">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold uppercase tracking-wide text-red-400">Inventory Mismatch Detected</p>
                        <p class="mt-0.5">Physical items counted on camera does not align with remaining active digital claims records. Audit logs generated.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm space-y-3">
                <h4 class="font-bold text-xs uppercase text-gray-900 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="gauge" class="w-4 h-4 text-blue-600"></i> Density Predictor Output
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-500">Predicted waiting time:</span>
                        <span class="font-bold text-gray-900 font-mono" id="waitTimeDisplay">4.8 mins</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-500">Fulfillment Efficiency Rate:</span>
                        <span class="font-bold text-green-600">Nominal (&lt; 1 min per user)</span>
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
    
    if (cameraActive) {
        btn.textContent = 'Disconnect Camera';
        btn.className = 'h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-red-100 text-red-600 border border-red-200';
        stream.classList.remove('hidden');
        offline.classList.add('hidden');
    } else {
        btn.textContent = 'Connect Camera';
        btn.className = 'h-10 px-4 rounded-xl text-xs font-mono font-black uppercase tracking-wider transition-all cursor-pointer bg-green-100 text-green-700 border border-green-200';
        stream.classList.add('hidden');
        offline.classList.remove('hidden');
    }
}

function updateQueue(val) {
    document.getElementById('queueSliderVal').textContent = `${val} people`;
    document.getElementById('queueLengthDisplay').textContent = `${val} people`;
    document.getElementById('densityDisplay').textContent = val > 15 ? `High (${val * 12}%)` : `Medium (${val * 12}%)`;
    document.getElementById('waitTimeDisplay').textContent = `${(val * 0.8).toFixed(1)} mins`;
}

function updateKits(val) {
    document.getElementById('kitSliderVal').textContent = `${val} packages`;
    document.getElementById('kitsCountDisplay').textContent = val;
}

function toggleMismatch(isChecked) {
    const alertBox = document.getElementById('mismatchAlert');
    if (isChecked) {
        alertBox.classList.remove('hidden');
    } else {
        alertBox.classList.add('hidden');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>