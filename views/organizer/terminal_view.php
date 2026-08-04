<?php require_once '../includes/header.php'; ?>

<div class="space-y-5">

    <!-- TOP ROW: Scanner left, Status right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- LEFT: Input Panel -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-[#0e0f0c] p-6 rounded-[28px] text-white border border-white/10 shadow-xl space-y-4 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black text-[#9fe870] uppercase tracking-wider font-mono">Onsite QR Scan Terminal</label>
                    <span class="text-[10px] font-mono bg-white/10 px-3 py-1 rounded-full text-white/70">Terminal #01 Active</span>
                </div>
                <div class="relative">
                    <i data-lucide="qr-code" class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9fe870] w-5 h-5"></i>
                    <input type="text" id="scanInput"
                        placeholder="Enter or scan Student ID / RFID"
                        class="w-full pl-12 pr-4 h-14 rounded-2xl bg-white/5 border border-[#9fe870]/40 focus:border-[#9fe870] focus:ring-2 focus:ring-[#9fe870]/20 outline-none text-white text-xs font-mono font-bold placeholder:text-white/30" />
                </div>
                <button onclick="processScan()" class="w-full h-14 bg-[#9fe870] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-lg wise-btn cursor-pointer flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> Verify & Scan ID
                </button>
            </div>
        </div>

        <!-- RIGHT: Status Panel -->
        <div class="lg:col-span-5">
            <div class="bg-[#0e0f0c] p-6 rounded-[28px] text-white border border-white/10 shadow-xl h-full flex flex-col justify-between min-h-[220px]">
                <p class="text-xs font-black text-[#9fe870] uppercase tracking-wider font-mono mb-2">Status</p>

                <!-- Status display -->
                <div id="statusDisplay" class="flex-1 flex flex-col items-center justify-center gap-2 text-center">
                    <p class="text-white/20 text-xs font-mono uppercase tracking-widest">Awaiting scan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM: Claim History Panel -->
    <div class="bg-[#0e0f0c] rounded-[28px] text-white border border-white/10 shadow-xl overflow-hidden">
        <div class="px-6 pt-5 pb-3 border-b border-white/10 flex items-center justify-between">
            <p class="text-xs font-black text-[#9fe870] uppercase tracking-wider font-mono">Claim History</p>
            <span id="historyCount" class="text-[10px] font-mono bg-white/10 px-3 py-1 rounded-full text-white/50">0 records</span>
        </div>

        <div class="overflow-x-auto max-h-72 overflow-y-auto">
            <table class="w-full text-xs font-mono">
                <thead class="sticky top-0 bg-[#0e0f0c] border-b border-white/10">
                    <tr>
                        <th class="text-left px-5 py-3 text-white/40 font-bold uppercase tracking-widest text-[10px] border-r border-white/5">User</th>
                        <th class="text-left px-5 py-3 text-white/40 font-bold uppercase tracking-widest text-[10px] border-r border-white/5">Event</th>
                        <th class="text-left px-5 py-3 text-white/40 font-bold uppercase tracking-widest text-[10px] border-r border-white/5">Scheduled Slot</th>
                        <th class="text-left px-5 py-3 text-white/40 font-bold uppercase tracking-widest text-[10px] border-r border-white/5">Status</th>
                        <th class="text-left px-5 py-3 text-white/40 font-bold uppercase tracking-widest text-[10px]">Reason</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-10 text-white/20 text-xs">No scan history yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// ── RFID global keydown capture ──────────────────────────────
let rfidBuffer = '';
let rfidTimer  = null;

document.addEventListener('keydown', function(e) {
    if (document.activeElement.id === 'scanInput') return;
    if (e.key === 'Enter') {
        if (rfidBuffer.length > 3) triggerScan(rfidBuffer.trim());
        rfidBuffer = '';
        clearTimeout(rfidTimer);
        return;
    }
    if (e.key.length === 1) {
        rfidBuffer += e.key;
        clearTimeout(rfidTimer);
        rfidTimer = setTimeout(() => { rfidBuffer = ''; }, 500);
    }
});

// ── Manual button click ──────────────────────────────────────
function processScan() {
    const val = document.getElementById('scanInput').value.trim();
    if (!val) return;
    triggerScan(val);
    document.getElementById('scanInput').value = '';
}

// ── Core scan function ───────────────────────────────────────
async function triggerScan(input) {
    setStatus('loading');

    try {
        const res = await fetch('/claim/api/rfid_scan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ scan_input: input, csrf_token: csrfToken })
        });
        const data = await res.json();

        if (!data.success) {
            setStatus('error', null, data.message);
            return;
        }

        setStatus(
            data.result, 
            data.student_name, 
            data.reason, 
            data.event_name, 
            data.allow_early_claim, 
            data.reservation_id, 
            data.current_density
        );
        loadHistory();

    } catch (err) {
        setStatus('error', null, 'Network error');
    }
}

// ── Status panel renderer ────────────────────────────────────
function setStatus(result, name, reason, event, allowEarly, reservationId, density) {
    const box = document.getElementById('statusDisplay');

    if (result === 'loading') {
        box.innerHTML = `<p class="text-white/40 text-xs font-mono animate-pulse uppercase tracking-widest">Reading...</p>`;
        return;
    }
    if (result === 'error') {
        box.innerHTML = `
            <p class="text-red-400 text-3xl font-black tracking-tight">INVALID</p>
            <div class="w-full border-t border-white/10 pt-2 text-center">
                <p class="text-red-400/80 text-[11px] font-mono">${reason || 'Unknown error'}</p>
            </div>`;
        return;
    }

    if (result === 'VALID') {
        box.innerHTML = `
            <p class="text-[#9fe870] text-3xl font-black tracking-tight">VALID CLAIM</p>
            <p class="text-white/80 text-xs font-bold font-mono">${name || ''}</p>
            ${event ? `<p class="text-white/40 text-[10px] font-mono">${event}</p>` : ''}
            ${reservationId ? `
                <button onclick="approveClaim(${reservationId}, false)" 
                        class="mt-2 w-full py-2.5 bg-[#9fe870] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-md hover:bg-[#8edb5f] transition-all cursor-pointer">
                    Approve Claim
                </button>` : ''}
        `;
    } else if (result === 'EARLY_ARRIVAL') {
        if (allowEarly) {
            // Density < 60% — Allow Early Claim button active
            box.innerHTML = `
                <p class="text-amber-400 text-2xl font-black tracking-tight">EARLY ARRIVAL</p>
                <p class="text-white/80 text-xs font-bold font-mono">${name || ''}</p>
                <p class="text-amber-400/80 text-[10px] font-mono">${reason}</p>
                
                <button onclick="approveClaim(${reservationId}, true)" 
                        class="mt-2 w-full py-2.5 bg-amber-400 text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-md hover:bg-amber-300 transition-all cursor-pointer">
                    ⚡ Allow Early Claim
                </button>
            `;
        } else {
            // Density >= 60% — Station Congested, Block Early Claim
            box.innerHTML = `
                <p class="text-red-400 text-2xl font-black tracking-tight">STATION CONGESTED</p>
                <p class="text-white/80 text-xs font-bold font-mono">${name || ''}</p>
                <div class="w-full border-t border-white/10 pt-2 text-center">
                    <p class="text-red-400/80 text-[11px] font-mono">${reason}</p>
                    <p class="text-white/40 text-[10px] font-mono mt-1">Station is congested (≥60% density). Ask student to return during their scheduled slot.</p>
                </div>
            `;
        }
    } else {
        box.innerHTML = `
            <p class="text-red-400 text-3xl font-black tracking-tight">INVALID</p>
            ${name ? `<p class="text-white/80 text-xs font-bold font-mono">${name}</p>` : ''}
            <div class="w-full border-t border-white/10 pt-2 text-center">
                <p class="text-red-400/80 text-[11px] font-mono">${reason || '—'}</p>
            </div>`;
    }
    if (window.lucide) lucide.createIcons();
}

// ── Approve Claim Action ─────────────────────────────────────
async function approveClaim(reservationId, overrideEarly) {
    const actionLabel = overrideEarly ? "early claim" : "claim";
    if (!confirm(`Confirm approval for this ${actionLabel}?`)) return;

    try {
        const res = await fetch('/claim/api/approve_claim.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                reservation_id: reservationId, 
                override_early: overrideEarly, 
                csrf_token: csrfToken 
            })
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('statusDisplay').innerHTML = `
                <p class="text-[#9fe870] text-2xl font-black tracking-tight">CLAIMED ✅</p>
                <p class="text-white/60 text-xs font-mono mt-1">${data.message}</p>
            `;
            loadHistory();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve claim.'));
        }
    } catch (e) {
        alert('Network error while approving claim.');
    }
}

// ── History loader ───────────────────────────────────────────
async function loadHistory() {
    try {
        const res  = await fetch('/claim/api/get_scan_logs.php');
        const data = await res.json();
        if (!data.success) return;

        const tbody = document.getElementById('historyTableBody');
        document.getElementById('historyCount').innerText = data.logs.length + ' records';

        if (data.logs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-white/20 text-xs">No scan history yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.logs.map(log => {
            const isValid  = log.status === 'VALID' || log.status === 'CLAIMED';
            const color    = isValid ? 'text-[#9fe870]' : 'text-red-400';
            const reason   = log.reason || '—';
            return `
            <tr class="border-b border-white/5 hover:bg-white/3 transition-colors">
                <td class="px-5 py-3 text-white/80 border-r border-white/5">${log.user_name}</td>
                <td class="px-5 py-3 text-white/60 border-r border-white/5">${log.event_name}</td>
                <td class="px-5 py-3 text-white/50 border-r border-white/5">${log.scheduled_slot}</td>
                <td class="px-5 py-3 border-r border-white/5 font-bold ${color}">${log.status}</td>
                <td class="px-5 py-3 text-white/40">${reason}</td>
            </tr>`;
        }).join('');

    } catch(e) {
        console.error('History load failed', e);
    }
}

// Load history on page open
loadHistory();
</script>

<?php require_once '../includes/footer.php'; ?>