<?php require_once '../includes/header.php'; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <div class="lg:col-span-8 space-y-4">
        <div class="bg-[#0e0f0c] p-6 rounded-[28px] text-white border border-white/10 shadow-xl space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-black text-[#9fe870] uppercase tracking-wider font-mono">Onsite QR Scan Terminal</label>
                <span class="text-[10px] font-mono bg-white/10 px-3 py-1 rounded-full text-white/70">Terminal #01 Active</span>
            </div>
            <div class="relative">
                <i data-lucide="qr-code" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9fe870] w-5 h-5"></i>
                <input type="text" id="scanInput" placeholder="Enter or scan ticket hash (e.g., AC-501-9F3B2E1D)" class="w-full pl-12 pr-4 h-14 rounded-2xl bg-white/5 border border-[#9fe870]/40 focus:border-[#9fe870] focus:ring-2 focus:ring-[#9fe870]/20 outline-none text-white text-xs font-mono font-bold placeholder:text-white/30" />
            </div>
            <button onclick="processScan()" class="w-full h-14 bg-[#9fe870] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-lg wise-btn cursor-pointer flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i> Verify & Approve Claim
            </button>
        </div>
        <div id="scanResultBox" class="hidden p-5 rounded-2xl border text-xs font-mono font-bold shadow-sm transition-all"></div>
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