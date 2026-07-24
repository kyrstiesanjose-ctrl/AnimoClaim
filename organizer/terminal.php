<?php 
require_once '../config/database.php';
requireLogin('organizer');
require_once '../includes/header.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-7">
        <div class="bg-[#1c261b] p-6 rounded-3xl text-white border border-white/5 space-y-4">
            <label class="block text-[10px] font-black text-[#c6f135] uppercase tracking-widest mb-1.5 ml-1">Barcode Scanner Emulation</label>
            <div class="relative">
                <i data-lucide="qr-code" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#c6f135] w-5 h-5"></i>
                <input type="text" id="scanInput" placeholder="Enter Ticket Hash (e.g., AC-501-9F3B2E1D)" class="w-full pl-12 pr-4 h-14 rounded-2xl bg-[#0f2419] border border-[#c6f135]/30 focus:border-[#c6f135] focus:ring-1 outline-none text-white text-sm font-mono font-bold" />
            </div>
            <button onclick="processScan()" class="w-full h-12 bg-[#c6f135] text-[#1c261b] font-black text-xs uppercase rounded-xl shadow-md mt-4">Confirm Claim</button>
        </div>
    </div>
</div>
<script>
async function processScan() {
    const hash = document.getElementById('scanInput').value.trim();
    if(!hash) return;
    const res = await fetch('/claim/api/approve_claim.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ qr_hash: hash, csrf_token: csrfToken })
    });
    const data = await res.json();
    alert(data.success ? 'Claim Approved Successfully!' : data.message);
    document.getElementById('scanInput').value = '';
}
</script>
<?php require_once '../includes/footer.php'; ?>