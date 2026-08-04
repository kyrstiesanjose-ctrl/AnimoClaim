<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm">
        <div>
            <h3 class="font-black text-sm uppercase tracking-wider text-[#0e0f0c] wise-heading">Distribution Audit Logs</h3>
            <p class="text-[11px] text-gray-500 font-medium">Monitor attendee compliance logs and manage student no-show penalties.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7">
            <div class="bg-white p-6 rounded-[32px] border border-[#0e0f0c]/12 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#0e0f0c]">Recent Transaction Audit Logs</h4>
                    <span class="text-[10px] text-gray-400 font-bold font-mono"><?php echo count($reservations); ?> records</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-[450px] overflow-y-auto pr-1 hide-scrollbar">
                    <?php foreach($reservations as $item): ?>
                        <div class="py-3.5 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-xs text-[#0e0f0c]"><?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?></p>
                                <p class="text-[10px] text-gray-500 mt-0.5 font-mono font-medium">
                                    <?php echo htmlspecialchars($item['title']); ?> • <?php echo date('M d, h:i A', strtotime($item['start_time'])); ?>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php 
                                    $badgeClass = 'bg-gray-100 text-gray-600';
                                    if ($item['status'] === 'claimed') $badgeClass = 'bg-[#e2f6d5] text-[#163300] border border-[#9fe870]/40';
                                    if ($item['status'] === 'reserved') $badgeClass = 'bg-blue-50 text-blue-700 animate-pulse border border-blue-200';
                                    if ($item['status'] === 'expired') $badgeClass = 'bg-red-50 text-red-600 border border-red-200';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-mono font-black uppercase tracking-widest <?php echo $badgeClass; ?>">
                                    <?php echo $item['status']; ?>
                                </span>
                                <?php if($item['status'] === 'reserved'): ?>
                                    <button onclick="triggerNoShow(<?php echo $item['id']; ?>, <?php echo $item['user_id']; ?>)" class="p-1.5 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors cursor-pointer" title="Issue Strike">
                                        <i data-lucide="flame" class="w-4 h-4"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="bg-[#0e0f0c] p-6 rounded-[32px] text-white border border-white/10 shadow-xl space-y-4">
                <div>
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#9fe870] flex items-center gap-1.5 font-mono">
                        <i data-lucide="flame" class="w-4 h-4 text-amber-400"></i> Student Strikes Management
                    </h4>
                </div>
                <div class="divide-y divide-white/10 space-y-3 pt-2 max-h-[350px] overflow-y-auto pr-2 hide-scrollbar">
                    <?php foreach($students as $student): 
                        $strikes = (int)$student['strikes'];
                        $isSuspended = $strikes >= 3;
                    ?>
                        <div class="pt-3 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-xs text-white"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                                <p class="text-[9px] text-[#9fe870]/70 font-mono mt-0.5"><?php echo htmlspecialchars($student['dlsu_id']); ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1 bg-white/5 p-1 rounded-xl border border-white/10">
                                    <button onclick="adjustStrike(<?php echo $student['id']; ?>, 'remove')" class="w-6 h-6 bg-white/10 hover:bg-white/20 flex items-center justify-center rounded-lg text-xs cursor-pointer font-bold transition-all">-</button>
                                    <span class="font-mono text-xs font-black w-6 text-center text-[#9fe870]"><?php echo $strikes; ?></span>
                                    <button onclick="adjustStrike(<?php echo $student['id']; ?>, 'add')" class="w-6 h-6 bg-white/10 hover:bg-white/20 flex items-center justify-center rounded-lg text-xs cursor-pointer font-bold transition-all">+</button>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase font-mono tracking-wider <?php echo $isSuspended ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-[#9fe870]/20 text-[#9fe870] border border-[#9fe870]/30'; ?>">
                                    <?php echo $isSuspended ? 'Suspended' : 'Clear'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function triggerNoShow(reservationId, userId) {
        if(!confirm("Mark this reservation as expired and issue a strike?")) return;
        const res = await fetch('/claim/api/manage_strikes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'no-show', reservation_id: reservationId, user_id: userId, csrf_token: csrfToken })
        });
        const data = await res.json();
        if(data.success) window.location.reload();
        else alert("Error: " + data.message);
    }

    async function adjustStrike(userId, action) {
        const res = await fetch('/claim/api/manage_strikes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, user_id: userId, csrf_token: csrfToken })
        });
        const data = await res.json();
        if(data.success) window.location.reload();
        else alert("Error: " + data.message);
    }
</script>

<?php require_once '../includes/footer.php'; ?>