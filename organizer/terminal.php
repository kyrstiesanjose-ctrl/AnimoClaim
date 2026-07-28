<?php
require_once '../config/database.php';
requireLogin('organizer');
require_once '../includes/header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <div class="lg:col-span-8 space-y-4">
        <!-- QR Scan Terminal Box -->
        <div class="bg-[#0e0f0c] p-6 rounded-[28px] text-white border border-white/10 shadow-xl space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-black text-[#9fe870] uppercase tracking-wider font-mono">Onsite QR Scan Terminal</label>
                <span class="text-[10px] font-mono bg-white/10 px-3 py-1 rounded-full text-white/70">Terminal #01 Active</span>
            </div>

            <div class="relative">
                <i data-lucide="qr-code" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9fe870] w-5 h-5"></i>
                <input
                    type="text"
                    id="scanInput"
                    placeholder="Enter or scan ticket hash (e.g., AC-501-9F3B2E1D)"
                    class="w-full pl-12 pr-4 h-14 rounded-2xl bg-white/5 border border-[#9fe870]/40 focus:border-[#9fe870] focus:ring-2 focus:ring-[#9fe870]/20 outline-none text-white text-xs font-mono font-bold placeholder:text-white/30"
                />
            </div>

            <button onclick="processScan()" class="w-full h-14 bg-[#9fe870] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-lg wise-btn cursor-pointer flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                Verify & Approve Claim
            </button>
        </div>

        <div id="scanResultBox" class="hidden p-5 rounded-2xl border text-xs font-mono font-bold shadow-sm transition-all"></div>
    </div>

    <div class="lg:col-span-4 space-y-4">
        <!-- RFID Tap Visual -->
        <div class="bg-white rounded-[28px] border border-gray-200/80 shadow-sm p-6 flex flex-col items-center justify-center gap-3 relative overflow-hidden h-56">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-24 h-24 rounded-full border-2 border-[#9fe870] rfid-pulse-ring-1 absolute"></div>
                <div class="w-24 h-24 rounded-full border-2 border-[#9fe870] rfid-pulse-ring-2 absolute"></div>
            </div>
            <div class="relative w-20 h-13 rfid-card-anim">
                <div class="w-20 h-13 rounded-lg bg-gradient-to-br from-[#163300] to-[#0e0f0c] shadow-lg relative overflow-hidden border border-[#9fe870]/30">
                    <div class="absolute w-full h-0.5 bg-[#9fe870]/80 rfid-scan-line"></div>
                    <i data-lucide="wifi" class="absolute top-1.5 right-1.5 w-3 h-3 text-[#9fe870] rfid-wifi-anim"></i>
                    <div class="absolute bottom-1.5 left-1.5 w-6 h-1 bg-[#9fe870]/40 rounded-full"></div>
                </div>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 relative z-10 mt-2">Tap Student ID to Scan</p>
        </div>

        <div class="bg-white p-5 rounded-[28px] border border-gray-200/80 shadow-sm space-y-3">
            <h4 class="font-extrabold text-xs uppercase tracking-wider text-[#0e0f0c] flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#163300]"></i> Verification Protocol
            </h4>
            <p class="text-xs text-gray-500 leading-relaxed font-medium">
                Scan or enter the QR code hash presented on student tickets. Approval marks the ticket as claimed and updates inventory in real time.
            </p>
        </div>
    </div>
</div>

<script>
async function processScan() {
    const hash = document.getElementById('scanInput').value.trim();
    if(!hash) return;
    const resultBox = document.getElementById('scanResultBox');

    try {
        const res = await fetch('/claim/api/approve_claim.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_hash: hash, csrf_token: csrfToken })
        });
        const data = await res.json();

        resultBox.classList.remove('hidden', 'bg-green-100', 'border-green-400', 'text-green-800', 'bg-red-100', 'border-red-400', 'text-red-800');
        if (data.success) {
            resultBox.classList.add('bg-green-100', 'border-green-500', 'text-green-900');
            resultBox.innerHTML = `<span class="flex items-center gap-2">Claim Approved Successfully! Ticket: ${hash}</span>`;
            if (window.confetti) {
                confetti({ particleCount: 60, spread: 60, origin: { y: 0.7 } });
            }
        } else {
            resultBox.classList.add('bg-red-100', 'border-red-400', 'text-red-800');
            resultBox.innerText = `Error: ${data.message || 'Invalid or already claimed ticket.'}`;
        }
        document.getElementById('scanInput').value = '';
    } catch(err) {
        alert('Network or processing error occurred.');
    }
}
</script>
<?php require_once '../includes/footer.php'; ?>